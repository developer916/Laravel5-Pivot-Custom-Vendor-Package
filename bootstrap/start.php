<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application;

/*
|--------------------------------------------------------------------------
| Detect The Application Environment
|--------------------------------------------------------------------------
|
| Laravel takes a dead simple approach to your application environments
| so you can just specify a machine name for the host that matches a
| given environment, then we will automatically detect it for you.
|
*/
//If We've set the environment within the env file just use it
if (getenv('APP_ENV')) {
    $env = $app->detectEnvironment(function () {
        return getenv('APP_ENV');
    });
} else {
    //If we have not set the environment within the env file
    if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['PHP_SELF'])) {
        $url = parse_url('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'], PHP_URL_HOST);
        if (substr_count($url, '.') >= 2) {
            $array = explode('.', $url);
            $subdomain = $array[0];
        }
    }

    //If we have a subdomain and it's not www
    if (isset($subdomain) && $subdomain != 'www') {
        //If the subdomain has the word stage in it
        if (strpos($subdomain, 'stage') !== false) {
            $env = $app->detectEnvironment(function () {
                return 'staging';
            });
        } else {
            $env = $app->detectEnvironment(array(
                'andrew' => array('dev-pc'),
                'evan' => array('Evans-MacBook-Pro.local'),
                'dev' => array('web.dev.scaffoldlms.com'),
                'john' => array('Lenovo-PC'),
                'james' => array('JB-VAIO'),
                'tri' => array('tri-pc'),
                'live' => array('ip-172-31-3-196'),
                'ifrond' => array('ifrond')
            ));
        }
    } else {
        //Otherwise if we are not using a subdomain or the subdomain is www
        $env = $app->detectEnvironment(array(
            'andrew' => array('dev-pc'),
            'evan' => array('Evans-MacBook-Pro.local'),
            'dev' => array('web.dev.scaffoldlms.com'),
            'john' => array('Lenovo-PC'),
            'james' => array('JB-VAIO'),
            'tri' => array('tri-pc'),
            'live' => array('ip-172-31-3-196'),
            'ifrond' => array('ifrond')
        ));
    }
}


/*
|--------------------------------------------------------------------------
| Bind Paths
|--------------------------------------------------------------------------
|
| Here we are binding the paths configured in paths.php to the app. You
| should not be changing these here. If you need to change these you
| may do so within the paths.php file and they will be bound here.
|
*/

$app->bindInstallPaths(require __DIR__ . '/paths.php');

/*
|--------------------------------------------------------------------------
| Load The Application
|--------------------------------------------------------------------------
|
| Here we will load this Illuminate application. We will keep this in a
| separate location so we can isolate the creation of an application
| from the actual running of the application with a given request.
|
*/

$framework = $app['path.base'] .
    '/vendor/laravel/framework/src';

require $framework . '/Illuminate/Foundation/start.php';

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
