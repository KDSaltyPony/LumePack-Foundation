<?php
/**
 * DefaultRole class file
 *
 * PHP Version 7.2.19
 *
 * @category Command
 * @package  LumePack\Foundation\Commands
 * @author   Loïc Gérard <lgerard@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Commands;

use Illuminate\Console\Command;
use LumePack\Foundation\Data\Models\Auth\Role;
use LumePack\Foundation\Data\Models\Auth\User;
use LumePack\Foundation\Data\Repositories\Auth\RoleRepository;
use LumePack\Foundation\Data\Models\Auth\Permission;

/**
 * DefaultRole
 *
 * @category Command
 * @package  LumePack\Foundation\Commands
 * @author   Loïc Gérard <lgerard@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class DefaultRole extends Command
{
    /*
    In order to call the cmd : php artisan defaultrole:create --roleuid=admin --rolename="Admin (Default role)" --email=admin@example.com --password=password
    */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'defaultrole:create {--roleuid=admin : The UID of the role} {--rolename="Admin (Default role)" : The name of the role} {--email= : The email for the user} {--password= : The password for the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default role and user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $repo = new RoleRepository();
        $permissions = [
            'LPFAR_AUTHROLE',
            'LPFAR_AUTHROLE_ADD',
            'LPFAR_AUTHROLE_EDIT',
            'LPFAR_AUTHROLE_DELETE',
            'LPFAP_AUTHPERMISSION'
        ];
        $role_uid = $this->option('roleuid');
        $role_name = $this->option('rolename');
        $email = $this->option('email');
        $password = $this->option('password');

        if (empty($email) || empty($password) || empty($role_uid) || empty($role_name)) {
            $this->error('Arguments email, password, name, roleuid and rolename are required');

            return self::FAILURE;
        }

        $this->info("Creating default role: {$role_name}");

        // Create role
        $repo->create([ 'uid' => $role_uid, 'name' => $role_name ]);
        $role = Role::where('uid', $role_uid)->first();

        $this->info('Role created');

        $repo->update([
            'permissions' => Permission::whereIn(
                'uid', $permissions
            )->get()->map->only([ 'id' ])->toArray()
        ], $role->id);

        $this->info('Role updated with permissions');

        try {
            $user = User::create([
                'login'             => $email,
                'email'             => $email,
                'email_verified_at' => now(),
                'password'          => $password
            ]);

            $user->roles()->attach($role->id);

            $this->info("User created with email: {$email}");
            $this->info("User linked to role: {$role_name}");
        } catch (\Exception $e) {
            $this->error("Error creating user: {$e->getMessage()}");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}