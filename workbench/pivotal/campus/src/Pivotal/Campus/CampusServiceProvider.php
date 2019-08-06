<?php namespace Pivotal\Campus;

use Illuminate\Support\ServiceProvider;
use Pivotal\Campus\Models\Campus;
use Pivotal\Campus\Repositories\CampusRepository;

class CampusServiceProvider extends ServiceProvider
{

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
        $this->package('pivotal/campus','campus');
        include __DIR__ . '/../../events.php';
        include __DIR__ . '/../../routes.php';
        include __DIR__ . '/../../filters.php';

        $this->bootModelBinding();
    }

    public function bootModelBinding()
    {
        \Route::bind('campus', function($id){
            $campusModel = \App::make('campusControllerModel');
            return $campusModel->find($id);
        });

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCampusRepository();
        $this->registerCampusFactory();
        $this->registerCampusControllerModel();
    }

    public function registerCampusRepository()
    {
        $this->app->bind('campusRepository', function() {

            $model = new Campus();

            return new CampusRepository($model);
        });
    }

    public function registerCampusFactory()
    {
        $this->app->singleton('campusFactory', function() {
            $repository = \App::make('campusRepository');
            return new CampusFactory($repository);
        });
    }

    public function registerCampusControllerModel()
    {
        $this->app->singleton('campusControllerModel', function() {
            $campusFactory =  \App::make('campusFactory');
            return $campusFactory;
        });
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
