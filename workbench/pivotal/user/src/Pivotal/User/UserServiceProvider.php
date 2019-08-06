<?php namespace Pivotal\User;

use Illuminate\Support\ServiceProvider;
use Pivotal\User\Repositories\TeacherRepository;
use Pivotal\User\Models\User;
use Pivotal\User\Repositories\UserRepository;
use Pivotal\User\UserFactory;

class UserServiceProvider extends ServiceProvider
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
        $this->package('pivotal/user');
        include __DIR__ . '/../../events.php';
        include __DIR__ . '/../../routes.php';
        include __DIR__ . '/../../filters.php';

        $this->bootModelBinding();

    }

    public function bootModelBinding()
    {
        \Route::bind('user', function($id){
            $userModel = \App::make('userControllerModel');
            return $userModel->find($id);
        });

    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerUserRepository();
        $this->registerUserFactory();
        $this->registerUserControllerModel();

        $this->registerTeacherRepository();

    }

    public function registerUserRepository()
    {
        $this->app->bind('userRepository', function () {
            $model = new User();
            return new UserRepository($model);
        });
    }

    public function registerTeacherRepository()
    {
        $this->app->bind('teacher', function () {
            $model = new User();
            return new TeacherRepository($model);
        });
    }

    public function registerUserFactory()
    {
        $this->app->singleton('userFactory', function () {
            $repository = \App::make('userRepository');
            return new UserFactory($repository);
        });
    }

    public function registerUserControllerModel()
    {
        $this->app->singleton('userControllerModel', function() {
            $userFactory =  \App::make('userFactory');
            return $userFactory;
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
