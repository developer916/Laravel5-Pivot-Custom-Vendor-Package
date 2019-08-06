<?php namespace Pivotal\Department;

use Illuminate\Support\ServiceProvider;
use Pivotal\Department\Models\Department;
use Pivotal\Department\Repositories\DepartmentRepository;

class DepartmentServiceProvider extends ServiceProvider
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
        $this->package('pivotal/department','department');
        include __DIR__ . '/../../events.php';
        include __DIR__ . '/../../routes.php';
        include __DIR__ . '/../../filters.php';

        $this->bootModelBinding();
    }

    public function bootModelBinding()
    {
        \Route::bind('department', function($id){
            $departmentModel = \App::make('departmentControllerModel');
            return $departmentModel->find($id);
        });

    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerDepartmentRepository();
        $this->registerDepartmentFactory();
        $this->registerDepartmentControllerModel();
    }

    public function registerDepartmentRepository()
    {
        $this->app->bind('departmentRepository', function () {
            $model = new Department();
            return new DepartmentRepository($model);
        });
    }

    public function registerDepartmentFactory()
    {
        $this->app->singleton('departmentFactory', function () {
            $repository = \App::make('departmentRepository');
            return new DepartmentFactory($repository);
        });
    }

    public function registerDepartmentControllerModel()
    {
        $this->app->singleton('departmentControllerModel', function() {
            $departmentFactory =  \App::make('departmentFactory');
            return $departmentFactory;
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
