<?php

namespace Jmhc\Admin;

use Jmhc\Admin\UEditor\UEditor;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class UEditorProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UEditor::class, function ($app) {
            return new UEditor($app->make('Illuminate\Http\Request'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * 获取由提供者提供的服务。
     *
     * @return array
     */
    public function provides()
    {
        return [UEditor::class];
    }
}
