<?php namespace Angel\News;

use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider {

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
		$this->package('angel/news');
		
		include __DIR__ . '../../../routes.php';

		$bindings = array(
			// Models
			'News'        => '\Angel\News\News',
	
			// Controllers
			'NewsController'             => '\Angel\News\NewsController',
			'AdminNewsController'        => '\Angel\News\AdminNewsController'
		);
		
		foreach ($bindings as $name=>$class) {
			$this->app->singleton($name, function() use ($class) {
				return new $class;
			});
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
