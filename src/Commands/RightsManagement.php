<?php
/**
 * Permissions class file
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
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\Auth\Permission;
use LumePack\Foundation\Data\Models\Route;
use LumePack\Foundation\Data\Repositories\Auth\PermissionRepository;
use LumePack\Foundation\Data\Models\Auth\Role;
use LumePack\Foundation\Data\Models\Auth\User;
use LumePack\Foundation\Data\Repositories\Auth\RoleRepository;

/**
 * Permissions
 *
 * @category Command
 * @package  LumePack\Foundation\Commands
 * @author   Loïc Gérard <lgerard@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class RightsManagement extends Command
{
    /*
    In order to call the cmd :
    php artisan rightsmanagement --action=create-role --roleuid=roleuid --rolename=rolename [--withdefaultpermissions=true]
    php artisan rightsmanagement --action=create-user --name=nomutilisateur --surname=prenom --email=email@email.fr --password=mypassword --roleuid=roleuid
    php artisan rightsmanagement --action=create-users --file=users.csv
    php artisan rightsmanagement --action=create-permissions
    */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rightsmanagement {--action= : Action to perform (create-role, create-user, create-users, create-permissions)} {--roleuid= : Role UID} {--rolename= : Role name} {--name= : User name} {--surname= : User surname} {--email= : User email} {--password= : User password} {--file= : path to the CSV file containing the users to create} {--withdefaultpermissions= : if true, the role is created with default permissions}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Users, roles and permissions management.';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $action = $this->option('action');

        if(empty($action)){
            $this->error('Argument --action (Values : create-role, create-user, create-users or create-permissions) is required !');
        }else if($action == 'create-role'){
            return $this->execute_createRole();
        }else if($action == 'create-user'){
            return $this->execute_createUser();
        }else if($action == 'create-users'){
            return $this->execute_createUsers();
        }else if($action == 'create-permissions'){
            return $this->execute_createPermissions();
        }else{
            $this->error('Action '.$action.' is not supported !');
        }
    }

    private function execute_createRole()
    {
        $roleuid = $this->option('roleuid');
        $rolename = $this->option('rolename');
        $withdefaultpermissions = $this->option('withdefaultpermissions');
        if(empty($roleuid) || empty($rolename)){
            $this->error('Action create-role require the following parameters : roleuid, rolename');
            return self::FAILURE;
        }

        // Create role
        $repo = new RoleRepository();
        $repo->create([ 'uid' => $roleuid, 'name' => $rolename ]);
        $role = Role::where('uid', $roleuid)->first();
        $this->info("Role {$roleuid} created");

        if($withdefaultpermissions == "true"){

            $permissions = [
                'LPFAR_AUTHROLE',
                'LPFAR_AUTHROLE_ADD',
                'LPFAR_AUTHROLE_EDIT',
                'LPFAR_AUTHROLE_DELETE',
                'LPFAP_AUTHPERMISSION'
            ];

            $repo->update([
                'permissions' => Permission::whereIn(
                    'uid', $permissions
                )->get()->map->only([ 'id' ])->toArray()
            ], $role->id);

            $this->info('Role updated with default permissions');

        }


        return self::SUCCESS;
    }

    private function execute_createUser()
    {
        $roleuid = $this->option('roleuid');
        $password = $this->option('password');
        $email = $this->option('email');
        $name = $this->option('name');
        $surname = $this->option('surname');
        if(empty($roleuid) || empty($email) || empty($password) || empty($name) || empty($surname)){
            $this->error('Action create-role requires the following parameters : roleuid, email, name, surname and password');
            return self::FAILURE;
        }

        $r = $this->createUser($email, $password, $name, $surname, $roleuid);
        if($r){
            return self::SUCCESS;
        }
        return self::FAILURE;
    }

    private function createUser($email, $password, $name, $surname, $roleuid){
        $role = Role::where('uid', $roleuid)->first();
        if(!$role){
            $this->error("Role {$roleuid} does not exists !");
            return FALSE;
        }

        try {
            $user = User::create([
                'login'             => $email,
                'email'             => $email,
                'email_verified_at' => now(),
                'password'          => $password,
                'name'              => $name,
                'surname'           => $surname
            ]);

            $user->roles()->attach($role->id);

            $this->info("User created with email: {$email}");
            return TRUE;
        } catch (\Exception $e) {
            $this->error("Error creating user: {$e->getMessage()}");
            return FALSE;
        }
    }

    private function execute_createUsers(){
        $filepath = $this->option('file');
        if(empty($filepath)){
            $this->error('Action create-users requires the following parameter : file');
            return self::FAILURE;
        }
        if(!is_file($filepath)){
            $this->error("File {$filepath} does not exists !");
            return self::FAILURE;
        }

        $fichier = fopen($filepath, 'r');
        $entete = fgetcsv($fichier, 0, ';');

        if($entete != ["email","name","surname","roleuid","password"]){
            $this->error('CSV file MUST respects those columns names and orders : email, name, surname, roleuid, password');
            return self::FAILURE;
        }

        $g = True;
        while (($ligne = fgetcsv($fichier, 0, ';')) !== false) {
            $r = $this->createUser(
                $ligne[0],
                $ligne[4],
                $ligne[1],
                $ligne[2],
                $ligne[3]
            );
            if(!$r){
                $g = False;
            }
        }
        fclose($fichier);

        if($g){
            return self::SUCCESS;
        }
        return self::FAILURE;
    }

    private function execute_createPermissions(){
        $routes = Route::getRoutes();
        $exceptions = config('permissions')['route_exceptions'];
        $additions = config('permissions')['additional'];
        $permissions = [];
        $repo = new PermissionRepository();

        // foreach ($exceptions as $exception) {
        //     $exception = explode(':', $exception);
        //     $exceptions[Str::after($exception[0], 'api/')] = array_key_exists(1, $exception)? Str::upper($exception[1]): '*';
        // }

        foreach ($routes as $route) {
            if (count($route['permissions']) === 0) {
                $is_exception = false;

                // if (array_key_exists(Str::after($route['uri'], 'api/'), $exceptions)) {
                //     $is_exception = true;
                // }

                foreach($exceptions as $exception){
                    $method = Str::afterLast($exception, ':');

                    if ($method === $exception) {
                        $method = '*';
                    }

                    if (
                        Str::lower(
                            Str::after(Str::beforeLast($exception, ':'), 'api/')
                        ) === Str::lower(Str::after($route['uri'], 'api/')) &&
                        ($method == '*' || in_array($method, $route['methods']))
                    ) {
                        $is_exception = true;
                    }
                }

                if (!$is_exception) {
                    if (array_key_exists($route['uid'], $permissions)) {
                        $permissions[$route['uid']]['routes'][] = [
                            'route'  => $route['uri'],
                            'method' => $route['methods'][0]
                        ];
                    } else {
                        $permissions[$route['uid']] = [
                            'uid'    => $route['uid'],
                            'routes' => [
                                [
                                    'route'  => $route['uri'],
                                    'method' => $route['methods'][0]
                                ]
                            ]
                        ];
                    }
                }
            }
        }

        foreach($additions as $addition){
            if (Permission::where('uid', '=', $addition)->count() == 0) {
                $permissions[$addition] = [ 'uid' => $addition ];
            }
        }

        if (count($permissions) === 0) {
            $this->info('No missing permission');
        }

        foreach($permissions as $permission){
            if (array_key_exists('routes', $permission)) {
                $routes = '';

                foreach ($permission['routes'] as $key => $route) {
                    if ($key > 0) {
                        $routes .= ' and ';
                    }

                    $routes .= $route['route'].':'.$route['method'];
                }

                $this->info("Create permission {$permission['uid']} for route(s) {$routes}");
            } else {
                $this->info("Create permission {$permission['uid']}");
            }

            $repo->create([
                'uid'                => $permission['uid'],
                'name'               => $permission['uid'],
                'permission_type_id' => 1
            ]);
        }

        return self::SUCCESS;
    }
}
