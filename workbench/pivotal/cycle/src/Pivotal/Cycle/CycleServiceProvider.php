<?php namespace Pivotal\Cycle;

use Illuminate\Support\ServiceProvider;
use Pivotal\Cycle\Models\Cycle;
use Pivotal\Cycle\Repositories\CycleRepository;


class CycleServiceProvider extends ServiceProvider
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
        $this->package('pivotal/cycle','cycle');
        include __DIR__ . '/../../events.php';
        include __DIR__ . '/../../routes.php';
        include __DIR__ . '/../../filters.php';
        $this->bootModelBinding();
    }

    public function bootModelBinding()
    {
        \Route::bind('cycle', function($id){
            $cycleModel = \App::make('cycleControllerModel');
            return $cycleModel->find($id);
        });

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCycleRepository();
        $this->registerCycleFactory();
        $this->registerCycleControllerModel();
    }

    public function registerCycleRepository()
    {
        $this->app->bind('cycleRepository', function () {
            $model = new Cycle();
            return new CycleRepository($model);
        });
    }

    public function registerCycleFactory()
    {
        $this->app->bind('cycleFactory', function () {
            $repository = \App::make('cycleRepository');
            return new CycleFactory($repository);
        });
    }

    public function registerCycleControllerModel()
    {
        $this->app->singleton('cycleControllerModel', function() {
            $courseFactory =  \App::make('cycleFactory');
            return $courseFactory;
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
