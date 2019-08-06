<?php namespace Pivotal\Survey;

use Illuminate\Support\ServiceProvider;
use Pivotal\School\Models\School;
use Pivotal\School\Repositories\SchoolRepository;
use Pivotal\Survey\Models\Survey;

class SurveyServiceProvider extends ServiceProvider
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
        $this->package('pivotal/survey','survey');
        include __DIR__ . '/../../events.php';
        include __DIR__ . '/../../routes.php';
        include __DIR__ . '/../../filters.php';

        $this->bootModelBinding();
    }

    public function bootModelBinding()
    {

    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSurveyRepository();
        $this->registerSurveyFactory();
    }

    public function registerSurveyRepository()
    {
        $this->app->bind('surveyRepository', function () {
            $model = new Survey();
            return new SchoolRepository($model);
        });
    }

    public function registerSurveyFactory()
    {
        $this->app->bind('surveyFactory', function () {
            $repository = \App::make('surveyRepository');
            return new SurveyFactory($repository);
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
