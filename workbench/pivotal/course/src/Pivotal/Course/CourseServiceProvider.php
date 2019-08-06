<?php namespace Pivotal\Course;

use Illuminate\Support\ServiceProvider;
use Pivotal\Course\Models\Course;
use Pivotal\Repositories\CourseRepository;

class CourseServiceProvider extends ServiceProvider
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
        $this->package('pivotal/course','course');
        include __DIR__ . '/../../events.php';
        include __DIR__ . '/../../routes.php';
        include __DIR__ . '/../../filters.php';

        $this->bootModelBinding();
    }

    public function bootModelBinding()
    {
        \Route::bind('course', function($id){
            $schoolModel = \App::make('courseControllerModel');
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
        $this->registerCourseRepository();
        $this->registerCourseFactory();
        $this->registerCourseControllerModel();
    }

    public function registerCourseRepository()
    {
        $this->app->bind('courseRepository', function() {

            $model = new Course();

            return new CourseRepository($model);
        });
    }

    public function registerCourseFactory()
    {
        $this->app->singleton('courseFactory', function() {
            $repository = \App::make('courseRepository');
            return new CourseFactory($repository);
        });
    }

    public function registerCourseControllerModel()
    {
        $this->app->singleton('courseControllerModel', function() {
            $courseFactory =  \App::make('courseFactory');
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
