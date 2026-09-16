<?php
/**
 * HasMfaMethods class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
 * @author   KDSaltyPony <kallofdragon@gmail.com>, Franz Vetter <fvetter@diatem.net> <fvetter@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models;

use App\Data\Models\Auth\User;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\Auth\MfaMethod;
use LumePack\Foundation\Mail\BaseMail;
use PragmaRX\Google2FA\Google2FA;

/**
 * HasMfa
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
 * @author   KDSaltyPony <kallofdragon@gmail.com>, Franz Vetter <fvetter@diatem.net> <fvetter@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
trait HasMfa
{
    /**
     * The access token the user is using for the current request.
     *
     * @var MfaMethod
     */
    protected $mfa_method;

    /**
     * The MFA exempted roles.
     *
     * @var array
     */
    protected array $exempted_roles;

    /**
     * The MFA exempted permissions.
     *
     * @var array
     */
    protected array $exempted_permissions;

    /**
     * The engine to create MFA tokens.
     *
     * @var MfaMethod
     */
    protected Google2FA $engine;

    // /**
    //  * Boot the trait
    //  *
    //  * @return void
    //  */
    // protected static function bootHasMfaTrait(Google2FA $engine)
    public function initializeHasMfa()
    {
        $this->exempted_roles = config('mfa.exempted_roles', []);

        if (count($this->exempted_roles) === 1 && $this->exempted_roles[0] === '') {
            $this->exempted_roles = [];
        }

        $this->exempted_permissions = config('mfa.exempted_permissions', []);

        if (count($this->exempted_permissions) === 1 && $this->exempted_permissions[0] === '') {
            $this->exempted_permissions = [];
        }

        $this->engine = new Google2FA();
        $this->engine->setOneTimePasswordLength(config('mfa.digits', 6));
        $this->engine->setKeyRegeneration(config('mfa.timeout_sec', 30));
    }

    /**
     * Get the access tokens that belong to model.
     *
     * @return HasMany
     */
    public function mfaMethods(): HasMany
    {
        return $this->hasMany(MfaMethod::class);
    }

    /**
     * Get a specific mfa methods.
     *
     * @param string $method The method to get
     *
     * @return MfaMethod|null
     */
    public function mfaMethod(string $method): ?MfaMethod
    {
        return $this->mfaMethods()->where('method', $method)->first();
    }

    /**
     * Get the enabled mfa methods.
     *
     * @return array
     */
    public function enabledMfaMethods(): array
    {
        return $this->mfaMethods()->where('enabled', true)->pluck('method')->all();
    }

    /**
     * Check if the user is exempted from MFA.
     *
     * @return bool
     */
    public function isMfaExempted(): bool
    {
        return (
            $this->roles()->whereIn(
                'uid', config('mfa.exempted_roles')
            )->exists() ||
            $this->roles()->whereHas('permissions', function ($query) {
                $query->whereIn('uid', config('mfa.exempted_permissions'));
            })->exists()
        );
    }

    /**
     * Check if the method is an enabled method.
     *
     * @return bool
     */
    public function isMfaSet(): bool
    {
        return !empty($this->enabledMfaMethods());
    }

    /**
     * Check if the user has to setup MFA.
     *
     * @return bool
     */
    public function isMfaSetupMendatory(): bool
    {
        return config('mfa.is_mendatory') && !$this->isMfaExempted();
    }

    /**
     * Check if we can disable a method.
     *
     * @return bool
     */
    public function canDisableMfa(): bool
    {
        return (
            !$this->isMfaSetupMendatory() ||
            $this->mfaMethods()->where('enabled', true)->count() > 1
        );
    }

    /**
     * Create a TOTP secret (localy)
     *
     * @return string
     */
    public function pendingTokenCreate(): string
    {
        $token = Str::random(64);
        $ttl = config('user-totp.pending_ttl');

        Cache::store()->put($this->key('pending', 'token', $token), [
            'user_id' => $this->id, 'label' => $this->email
        ], $ttl);

        return $token;
    }

    /**
     * Remove a TOTP secret (localy)
     *
     * @return void
     */
    public function pendingTokenDelete(string $token): void
    {
        $this->store()->forget($this->key('pending', 'token', $token));
        $this->store()->forget($this->key('pending', 'attempts', $token));
    }

    /**
     * Fail a TOTP secret (localy)
     *
     * @return void
     */
    public function pendingTokenFail(string $token): void
    {
        $ttl = config('user-totp.pending_ttl');
        $key_attempts = $this->key('pending', 'attempts', $token);
        $attempts = Cache::store()->get($key_attempts);

        if ($attempts >= config('mfa.pending_attempts')) {
            $this->pendingTokenDelete($token);
        } else {
            Cache::store()->put($key_attempts, $attempts + 1, $ttl);
        }
    }

    /**
     * Retrive a user based on a local TOTP token
     *
     * @return User|null
     */
    public static function pendingTokenRetriveUser(string $token): ?User
    {
        $payload = Cache::store()->get(self::sKey('pending', 'token', $token));
        $user = null;

        $user_model = config('crud.user_model');

        if (!is_null($payload)) {
            $user = $user_model::where('id', $payload['user_id']);
            $relations = empty($relations)? config('query.relations', []): $relations;

            foreach ($relations as $relation) {
                $user->with($relation);
            }

            $user = $user->first();
        }

        return $user;
    }

    /**
     * Create a TOTP secret (localy)
     *
     * @return string
     */
    public function totpSecret(): string
    {
        return $this->engine->generateSecretKey();
    }

    /**
     * Generate standard URI used to create a TOTP QR Code
     *
     * @param string $holder The holder of the QR Code
     * @param string $secret The secret to transform into URI
     *
     * @return string
     */
    public function totpQrCodeUrl(string $holder, string $secret): string
    {
        return $this->engine->getQRCodeUrl(
            config('app.name'), $holder, $secret
        );
    }

    /**
     * Send an email with a code
     *
     * @return void
     */
    public function emailCode(): void
    {
        $digits = config('mfa.digits');
        $ttl = config('mfa.timeout_min') * 60;
        $code = strval(random_int((10 ** ($digits - 1)), (10 ** $digits) - 1));

        Cache::store()->put(
            $this->key('email', 'code'), Hash::make($code), $ttl
        );
        Cache::store()->forget($this->key('email', 'attempts'));

        Mail::send(new BaseMail('foundation::emails.mfa.code', [
            'subject' => trans('foundation::mail.subject_mfa', [
                'app' => env('APP_NAME')
            ]),
            'user'    => $this,
            'code'    => $code,
            'ttl'     => $ttl
        ]));
    }

    /**
     * Check a code
     *
     * @param string      $method The method to get
     * @param string      $code   The code to check
     * @param string|null $secret The secret associated
     *
     * @return bool
     */
    public function verify(
        string $method, string $code, string $secret = null
    ): bool
    {
        return match ($method) {
            'totp' => $this->engine->verifyKey($secret, $code),
            'email' => $this->cacheCodeCheck($method, $code),
            default => false
        };
    }

    /**
     * Check a code in cache
     *
     * @param string $method The method of mfa
     * @param string $code   The code to check
     *
     * @return bool
     */
    protected function cacheCodeCheck(string $method, string $code): bool
    {
        $is_valid = false;
        // $ttl = config('mfa.timeout_min') * 60;
        $key_code = $this->key($method, 'code');
        $key_attempts = $this->key($method, 'attempts');
        $hashed = Cache::store()->get($key_code);

        if (!is_null($hashed)) {
            $attempts = Cache::store()->get($key_attempts);

            if ($attempts >= config('mfa.email_attempts')) {
                Cache::store()->forget($key_code);
                Cache::store()->forget($key_attempts);
            } else {
                if (Hash::check($code, $hashed)) {
                    $is_valid = true;

                    Cache::store()->forget($key_code);
                    Cache::store()->forget($key_attempts);
                } else {
                    Cache::store()->put($key_attempts, $attempts + 1);
                }
            }
        }

        return $is_valid;
    }

    /**
     * Get the cache store
     *
     * @return CacheRepository
     */
    protected function store(): CacheRepository
    {
        return Cache::store();
    }

    /**
     * Create a key to store elements in cache (3 parts)
     *
     * @param string      $method The method of mfa to store data for
     * @param string      $type   The type of information to store
     * @param string|null $uid    The uid to diferentiate stored data (default user id)
     *
     * @return string
     */
    protected function key(
        string $method, string $type, string $uid = null
    ): string
    {
        return self::sKey($method, $type, (is_null($uid)? $this->id: $uid));
    }

    /**
     * Create a key to store elements in cache (static version of the key() function)
     *
     * @param string $method The method of mfa to store data for
     * @param string $type   The type of information to store
     * @param string $uid    The uid to diferentiate stored data (no default)
     *
     * @return string
     */
    protected static function sKey(
        string $method, string $type, string $uid
    ): string
    {
        return "{$method}:{$type}:{$uid}";
    }
}
