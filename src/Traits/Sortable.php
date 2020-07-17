<?php


namespace Jmhc\Admin\Traits;


trait Sortable
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($model) {
            $model->weigh = $model->id;
            $model->save();
        });
    }
}
