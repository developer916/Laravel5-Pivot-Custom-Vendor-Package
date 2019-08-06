<?php namespace Pivotal\Csv;

use Illuminate\Support\ServiceProvider;

class CsvServiceProvider extends ServiceProvider {

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
		$this->package('pivotal/csv');
		include __DIR__.'/../../events.php';
		include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerReader();
		$this->registerCsvFactory();
	}

	/**
	 * Bind the reader to the app object
	 */
	public function registerReader()
	{
		$this->app->bind('reader', function() {
			return new Reader();
		});
	}


	/**
	 * Bind the csv factory to the app object
	 */
	public function registerCsvFactory()
	{
		$this->app->singleton('csv', function() {

			$validator = \App::make('validator');
			$reader = \App::make('reader');
			return new CsvFactory($validator,$reader);
		});

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('Pivotal\Csv\Csv');
	}

}
