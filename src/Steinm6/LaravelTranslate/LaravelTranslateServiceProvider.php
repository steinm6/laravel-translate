<?php

namespace Steinm6\LaravelTranslate;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Steinm6\LaravelTranslate\Commands\TranslateCommand;

class LaravelTranslateServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('steinm6/laravel-translate');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app->bind('translate', function () {
            return new Translate;
        });

        $this->app->bind('translateExtract', function () {
            return new TranslateCommand();
        });

        $this->commands('translateExtract');


        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Translate', 'Steinm6\LaravelTranslate\Facades\Translate');
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
        return array('translate');
	}

}
