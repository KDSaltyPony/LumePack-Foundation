<?php
/**
 * DataValidate class file
 *
 * PHP Version 7.2.19
 *
 * @category Middleware
 * @package  LumePack\Foundation\Http\Middleware
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Http\Middleware;

use Closure;
use Exception;
use LumePack\Foundation\Services\ValidatorService;
use LumePack\Foundation\Services\ResponseService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * DataValidate
 *
 * @category Middleware
 * @package  LumePack\Foundation\Http\Middleware
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class DataValidate
{
    /**
     * The methods that should be tested
     * (allow the middleware to be set on named routes)
     *
     * @var array $_methods
     */
    private $_methods = [ 'POST', 'PUT', 'PATCH' ];

    /**
     * An instance of a child of ValidatorService that contains
     * the validation rules
     *
     * @var ValidatorService $_validator
     */
    private $_validator = null;

    /**
     * Handle an incoming request.
     *
     * @param Request $request   The request to validate
     * @param Closure $next      The controller method passed in routes
     * @param string  $validator The validator name
     * @param string  $namespace The validator namespace (default App)
     *
     * @return Response
     */
    public function handle(
        Request $request,
        Closure $next,
        string $validator,
        string $namespace = 'app'
    ): Response
    {
        if (in_array($request->getMethod(), $this->_methods)) {
            $this->_setValidator($request, nsval($validator), nsval($namespace));

            if (!$this->_validator->isValidated()) {
                return (new ResponseService(
                    $this->_validator->getErrors(), 400
                ))->format();
            }
        }

        return $next($request);
    }

    /**
     * Check if the Validator exist, if yes call it, if not throw an error.
     *
     * @param Request $request   The request to validate
     * @param string  $validator The validator name
     * @param string  $namespace The validator namespace
     *
     * @return void
     */
    private function _setValidator(
        Request $request, string $validator, string $namespace
    ): void
    {
        $validator = "{$namespace}\\Data\\Validators{$validator}Validator";

        if (!class_exists($validator)) {
            throw new Exception("{$validator} not found");
        }

        $this->_validator = new $validator(
            $request->all(),
            intval($request->route('uid'))
        );
    }
}
