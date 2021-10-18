<?php
/**
 * InheritanceModel class file
 *
 * PHP Version 7.2.0
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * InheritanceModel
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
trait InheritanceModel
{
    // /**
    //  * Initialize the trait
    //  *
    //  * @return void
    //  */
    // protected function initializeInheritanceModel()
    // {
    //     dd('test');
    // }

    /**
     * Boot the trait
     *
     * @return void
     */
    protected static function bootInheritanceModel()
    {
        static::retrieved(function(Model $model) {
            $settings = $model->inheritanceModel();
            $pkey = array_key_exists(
                'parent_relation', $settings
            )? $settings['parent_relation']: 'parent';
            $parent = $model->$pkey;

            // TODO
            // foreach (array_merge(
            //     $model->merge, $model->inherited
            // ) as $relation) {
            //     if (!($model->$relation instanceof Collection)) {
            //         // TODO throw error
            //     }
            // }

            if (!is_null($parent)) {
                // if (array_key_exists('merged', $settings)) {
                //     foreach ($settings['merged'] as $relation) {
                //         foreach ($parent->$relation as $value) {
                //             $model->setRelation($relation, $value);
                //             // $model->$relation->add($value);
                //         }
                //     }
                // }

                // if (array_key_exists('inherited', $settings)) {
                //     foreach ($settings['inherited'] as $relation => $reverse) {
                //         $type = substr(
                //             strrchr(get_class($model->$relation()), '\\'), 1
                //         );

                //         foreach ($parent->$relation as $value) {
                //             switch ($type) {
                //                 case 'BelongsToMany':
                //                     // $model->$relation()->attach($value);
                //                     break;

                //                 case 'HasMany':
                //                     $value->$reverse()->associate($model);
                //                     break;
                //             }
                //         }
                //         $model->$relation = $parent->$relation;
                //     }
                // }

                // if (array_key_exists('inherited', $settings)) {
                //     foreach ($settings['inherited'] as $relation => $reverse) {
                //         $type = substr(
                //             strrchr(get_class($model->$relation()), '\\'), 1
                //         );

                //         foreach ($parent->$relation as $value) {
                //             switch ($type) {
                //                 case 'BelongsToMany':
                //                     $model->setRelation($relation, $value);
                //                     // $model->$relation()->attach($value);
                //                     break;

                //                 case 'HasMany':
                //                     $model->setRelation($relation, $value);
                //                     // $value->$reverse()->associate($model);
                //                     break;
                //             }
                //         }
                //         $model->$relation = $parent->$relation;
                //     }
                // }

                foreach ($model->attributes as $attribute => $value) {
                    if (
                        (
                            !array_key_exists('ignored', $settings) || (
                                array_key_exists('ignored', $settings) &&
                                !in_array($attribute, $settings['ignored'])
                            )
                        ) && is_null($value)
                    ) {
                        $model->$attribute = $parent->$attribute;
                    }
                }
            }

            return $model;
        });

        static::saving(function(Model $model) {
            $settings = $model->inheritanceModel();
            $pkey = array_key_exists(
                'parent_relation', $settings
            )? $settings['parent_relation']: 'parent';
            $ckey = array_key_exists(
                'children_relation', $settings
            )? $settings['children_relation']: 'children';
            $parent = $model->$pkey;
            $children = $model->$ckey;

            // foreach (array_merge(
            //     $model->merge, $model->inherited
            // ) as $relation) {
            //     if (!($model->$relation instanceof Collection)) {
            //         // TODO throw error
            //     }
            // }

            if (!is_null($parent)) {
                // if (array_key_exists('merged', $settings)) {
                //     foreach ($settings['merged'] as $relation) {
                //         // TODO No need if already exist in parent
                //     }
                // }

                // if (array_key_exists('inherited', $settings)) {
                //     foreach ($settings['inherited'] as $relation => $reverse) {
                //         $type = substr(
                //             strrchr(get_class($model->$relation()), '\\'), 1
                //         );

                //         switch ($type) {
                //             case 'BelongsToMany':
                //                 $model->$relation()->sync();
                //                 break;

                //             case 'HasMany':
                //                 foreach ($parent->$relation as $value) {
                //                     $value->$reverse()->associate($parent);
                //                 }
                //                 break;
                //         }

                //         $model->$relation = $parent->$relation;
                //     }
                // }

                foreach ($model->attributes as $attribute => $value) {
                    // if attribute in ignored array, can't force null
                    if (
                        array_key_exists('ignored', $settings) &&
                        !in_array($attribute, $settings['ignored']) &&
                        $attribute !== 'id' &&
                        $attribute !== "{$model->getTable()}_id"
                    ) {
                        // if attribute in locked array, can't overwrite so store null
                        // if attribute not in unlocked array, can't overwrite so store null
                        // if attribute value same as parent, can't overwrite so store null
                        if (
                            (
                                array_key_exists('locked', $settings) &&
                                in_array($attribute, $settings['locked'])
                            ) || (
                                array_key_exists('unlocked', $settings) &&
                                !in_array($attribute, $settings['unlocked'])
                            ) || $value === $parent->$attribute
                        ) {
                            $attribute = ucfirst(Str::camel($attribute));
                            $method = "set{$attribute}";
                            $model->$method(null);
                        }
                    }
                }
            }

            // if ($children->count() > 0) {
            //     if (array_key_exists('merged', $settings)) {
            //         foreach ($settings['merged'] as $relation) {
            //             // TODO Remove duplicate in children
            //         }
            //     }
            // }

            return $model;
        });
    }

    /**
     * -------------------------------------------------------------------------
     * Mutators
     * -------------------------------------------------------------------------
     */

    // public function toArray()
    // {
    //     $model = parent::toArray();
    //     $settings = $this->inheritanceModel();

    //     if (!is_null($this->parent)) {
    //         $parent = $this->parent->toArray();

    //         if (array_key_exists('inherited', $settings)) {
    //             foreach ($settings['inherited'] as $relation) {
    //                 if (array_key_exists($relation, $model)) {
    //                     $model[$relation] = $parent[$relation];
    //                 }
    //             }
    //         }
    //     }

    //     return $model;
    // }
}
