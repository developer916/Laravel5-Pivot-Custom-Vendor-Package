<?php namespace Pivotal\School;

use Illuminate\Support\ServiceProvider;
use Pivotal\School\Models\School;
use Pivotal\School\Repositories\SchoolRepository;

class SchoolServiceProvider extends ServiceProvider
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
        $this->package('pivotal/school','school');
        include __DIR__ . '/../../events.php';
        include __DIR__ . '/../../routes.php';
        include __DIR__ . '/../../filters.php';

        $this->bootModelBinding();
    }

    public function bootModelBinding()
    {
        \Route::bind('school', function($id){
            $schoolModel = \App::make('schoolControllerModel');
            return $schoolModel->find($id);
        });

    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSchoolRepository();
        $this->registerSchoolFactory();
        $this->registerSchoolControllerModel();
    }

    public function registerSchoolRepository()
    {
        $this->app->bind('schoolRepository', function () {
            $model = new School();
            return new SchoolRepository($model);
        });
    }

    public function registerSchoolFactory()
    {
        $this->app->bind('schoolFactory', function () {
            $repository = \App::make('schoolRepository');
            return new SchoolFactory($repository);
        });
    }

    public function registerSchoolControllerModel()
    {
        $this->app->singleton('schoolControllerModel', function() {
            $schoolFactory =  \App::make('schoolFactory');
            return $schoolFactory;
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
