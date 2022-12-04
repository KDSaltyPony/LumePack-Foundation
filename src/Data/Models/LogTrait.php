<?php
/**
 * LogTrait class file
 *
 * PHP Version 7.2.19
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models;

use Illuminate\Database\Eloquent\Model;
use LumePack\Foundation\Data\Models\Auth\User;
use LumePack\Foundation\Data\Models\Log\Log;

/**
 * LogTrait
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
trait LogTrait
{
    /**
     * Boot the trait
     *
     * @return void
     */
    protected static function bootLogTrait()
    {
        // retrieved, creating, created, updating, updated, saving, saved, deleting, deleted, trashed, forceDeleted, restoring, restored, and replicating.
        // TODO: log on login / logout / refresh / user actions ???
        // TODO: log on request input / output

        static::created(function (Model $model) {
            if (!is_null($model->log_uid)) {
                $log = new Log();

                $log->code = 'DB-CREATED';
                $log->data = $model;
                $log->save();
            }
        });

        static::updated(function (Model $model) {
            if (!is_null($model->log_uid)) {
                $log = new Log();

                $log->code = 'DB-UPDATED';
                $log->data = $model;
                $log->save();
            }
        });

        static::deleted(function (Model $model) {
            if (!is_null($model->log_uid)) {
                $log = new Log();

                $log->code = 'DB-DELETED';
                $log->data = $model;
                $log->save();

                if ($model instanceof User) {
                    // TODO: remove personnal data on user delete with complex expressions
                    // WHERE data.body.data.id ou data.body.data.*.id = $model->id && un autre truc pour trouver le user??? email ???
                    // dump(Log::where(
                    //     'data.body.data.id', $model->id
                    // )->get()->toArray());
                    $logs = Log::where(
                        'data.uid', 'LIKE', '%User%'
                    )->where(
                        'data.attributes.id', $model->id
                    )->get();

                    foreach ($logs as $log) {
                        $data = $log->data;

                        if (array_key_exists('login', $data['attributes'])) {
                            $data['attributes']['login'] = $model->login;
                        }

                        if (array_key_exists('email', $data['attributes'])) {
                            $data['attributes']['email'] = $model->email;
                        }

                        if (array_key_exists('original', $data)) {
                            if (array_key_exists('login', $data['original'])) {
                                $data['original']['login'] = $model->login;
                            }

                            if (array_key_exists('email', $data['original'])) {
                                $data['original']['email'] = $model->email;
                            }
                        }

                        $log->data = $data;
                        $log->save();
                    }
                }
            }
        });
    }
}
