<?php
/**
 * OrderTrait class file
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

/**
 * OrderTrait
 *
 * @category Model
 * @package  LumePack\Foundation\Data\Models
 * @author   Louis Jaeger <ljaeger@diatem.net>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
trait OrderTrait
{
    /**
     * Boot the trait
     *
     * @return void
     */
    protected static function bootOrderTrait()
    {
        static::creating(function (BaseModel $model) {
            if (
                is_null($model->order) &&
                $model->is_ordered &&
                $model->order_grouped_by
            ) {
                // dump("creating start");
                $class = get_class($model);
                $group = $model->order_grouped_by;
                // dump("class: {$class}");
                // dump("group: {$group}");

                $model->order = $class::where(
                    $group, $model->$group
                )->where('deleted_at', null)->count() + 1;
                // dump("creating {$model->order}");
            }
        });

        static::created(function (BaseModel $model) {
            if ($model->is_ordered && $model->order_grouped_by) {
                // dump("created start");
                $class = get_class($model);
                $group = $model->order_grouped_by;
                // dump("created: {$class}");
                // dump("created: {$group}");
                $others = $class::where($group, $model->$group)->where(
                    'order', '>=', $model->order
                )->where(
                    'id', '<>', $model->id
                )->where('deleted_at', null)->get();

                foreach ($others as $other) {
                    $other->order += 1;
                    $other->saveQuietly();
                }
                // dump("created {$model->order}");
                // dump($model->toArray());
            }
        });

        static::updating(function (BaseModel $model) {
            if ($model->is_ordered && $model->order_grouped_by) {
                // dump("updating start");
                $old_order = $model->getOriginal('order');

                // if (!is_null($model->deleted_at)) {
                //     $model->order = 0;
                // }

                if (!is_null($model->order) && $model->order !== $old_order) {
                    $is_asc = $model->order > $old_order;
                    $class = get_class($model);
                    $group = $model->order_grouped_by;

                    $others = $class::where($group, $model->$group)->where(
                        'order', ($is_asc? '>': '<'), $old_order
                    )->where(
                        'order', ($is_asc? '<=': '>='), $model->order
                    )->where(
                        'id', '<>', $model->id
                    )->where('deleted_at', null)->get();

                    foreach ($others as $other) {
                        $other->order += ($is_asc? -1: 1);
                        $other->saveQuietly();
                    }
                } else {
                    $model->order = $old_order;
                }
                // dump("updating {$model->order}");
            }
        });

        static::deleted(function (BaseModel $model) {
            if ($model->is_ordered && $model->order_grouped_by) {
                // dump("deleted start");
                $class = get_class($model);
                $group = $model->order_grouped_by;
                $others = $class::where($group, $model->$group)->where(
                    'order', '>=', $model->order
                )->where('deleted_at', null)->get();

                foreach ($others as $other) {
                    $other->order -= 1;
                    $other->saveQuietly();
                }
                // dump("deleted {$model->order}");
            }
        });
    }
}
