<?php

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
    if(app()->environment() == 'live')
    {
        //return Redirect::secure(Request::path());
        URL::forceSchema('https');
    }
});


App::after(function($request, $response)
{
    //
});




/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
    if (Auth::guest())
    {
        if (Request::ajax())
        {
            return Response::make('Unauthorized', 401);
        }
        return Redirect::guest('login');
    }
});


Route::filter('auth.basic', function()
{
    return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
    if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
    if (Session::token() !== Input::get('_token'))
    {
        throw new Illuminate\Session\TokenMismatchException;
    }
});

/*
|--------------------------------------------------------------------------
| Pivot custom filters
|--------------------------------------------------------------------------
|
*/

/**
 * Must belong to the school in question
 */
//@todo this needs to be turned on and refactored
Route::filter('school', function($route, $request) {
    // admins can do anything
//    if (!Auth::user()->administrator) {
//
//        // get the most specific paramater to check the permissions against
//        if ($user = $route->getParameter('user')) {
//
//            if (! UsersController::can_access($user)) {
//                throw new AccessDeniedHttpException();
//            }
//
//        } elseif ($cycle = $route->getParameter('cycle')) {
//
//            if (! Pivotal\Cycle\Controllers\CycleController::can_access($cycle)) {
//                throw new AccessDeniedHttpException();
//            }
//
//        } elseif ($class = $route->getParameter('class')) {
//
//            if (! ClassesController::can_access($class)) {
//                throw new AccessDeniedHttpException();
//            }
//
//        } elseif ($department = $route->getParameter('department')) {
//
//            if (! DepartmentsController::can_access($department)) {
//                throw new AccessDeniedHttpException();
//            }
//
//        } elseif ($school = $route->getParameter('school')) {
//
//            if(is_null($school))
//            {
//                $school = School::where('id','=',Auth::user()->school_id)->first();
//            }
//
//            if (! Pivotal\School\Controllers\SchoolController::can_access($school)) {
//                throw new AccessDeniedHttpException();
//            }
//
//        } elseif ($data = $route->getParameter('data')) {
//            $data = json_decode($data);
//            if ($data->school_id != Auth::user()->school->id || !Pivotal\School\Controllers\SchoolController::can_access(Auth::user()->school)) {
//                throw new AccessDeniedHttpException();
//            }
//
//        } else {
//            // can't determine the context of the request
//            throw new BadRequestHttpException();
//        }
//    }
});

/**
 * Must be a Pivot Admin
 */
Route::filter('administrator', function() {
    if (!Auth::user()->isAdministrator()) {
        throw new AccessDeniedHttpException();
    }
});

/**
 * Must have a role that can edit
 */
Route::filter('editor', function() {
    if (!Auth::user()->isEditor()) {
        throw new AccessDeniedHttpException();
    }
});