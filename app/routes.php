<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::get('/', function() {

    if (!Auth::check()) {
        // not logged in
        return Redirect::to("/login");
    }

    switch(Auth::user()->role) {
        case User::PIVOT_ADMIN:
            return Redirect::to("/schools");
            break;
        case User::SCHOOL_ADMIN:
            return Redirect::to("/school/view/".Auth::user()->school_id);
            break;
        case User::DEPARTMENT_HEAD:
            return Redirect::to("/department/view/".Auth::user()->department_id);
            break;
        case User::TEACHER:
            return Redirect::to("/user/view/".Auth::user()->id);
            break;
    }

    // should never get here
    App::abort(404);
});

// fetches the necessary data for the nav bar
View::composer('layout-basic', function($view) {

    if (Auth::guest()) {
        return;
    }

    $data = array();

    switch(Auth::user()->role) {
        case User::SCHOOL_ADMIN:
            $data['nav']['departments'] = Auth::user()->school->departments;
            $data['nav']['classes'] = Auth::user()->school->classesForYear();
            $data['nav']['teachers'] = Auth::user()->school->teachers;
            $data['nav']['cycles'] = Auth::user()->school->cycles;
            break;

        case User::DEPARTMENT_HEAD:
            $data['nav']['departments'] = [Auth::user()->department];
            $data['nav']['classes'] = Auth::user()->department->classesForYear();
            $data['nav']['teachers'] = Auth::user()->department->teachers();
            $cycles = Cycle::whereHas('classes', function($q) {
                $q->where('department_id', '=', Auth::user()->department->id);
            })->orderBy('start_date', 'DESC')->get();
            $data['nav']['cycles'] = array();
            foreach ($cycles as $cycle) {
                $data['nav']['cycles'][] = $cycle;
            }
            break;

        case User::TEACHER:
             $depts = \Department::whereHas('classes', function($q) {
                $q->where('teacher_id', '=', Auth::user()->id);
            })->get();
            $data['nav']['departments'] = array();
            foreach ($depts as $dept) {
                $data['nav']['departments'][] = $dept;
            }
            $data['nav']['classes'] = Auth::user()->classesForYear();
            $data['nav']['teachers'] = [];
            $cycles = Cycle::whereHas('classes', function($q) {
                $q->where('teacher_id', '=', Auth::user()->id);
                $q->where('classes.start_year', '=', \DB::raw('YEAR(cycles.start_date)'));
            })->orderBy('start_date', 'DESC')->get();
            $data['nav']['cycles'] = array();
            foreach ($cycles as $cycle) {
                $data['nav']['cycles'][] = $cycle;
            }
            break;
    }
    $view->with($data);
});

View::composer('layout', function($view) {

    if (Auth::guest()) {
        return;
    }

    $data = array();

    switch(Auth::user()->role) {
        case User::SCHOOL_ADMIN:
            $data['nav']['departments'] = Auth::user()->school->departments;
            $data['nav']['classes'] = Auth::user()->school->classesForYear();
            $data['nav']['teachers'] = Auth::user()->school->teachers;
            $data['nav']['cycles'] = Auth::user()->school->cycles;
            break;

        case User::DEPARTMENT_HEAD:
            $data['nav']['departments'] = [Auth::user()->department];
            $data['nav']['classes'] = Auth::user()->department->classesForYear();
            $data['nav']['teachers'] = Auth::user()->department->teachers();
            $data['nav']['cycles'] = Cycle::whereHas('classes', function($q) {
                $q->where('department_id', '=', Auth::user()->department->id);
            })->orderBy('start_date', 'DESC')->get();
            break;

        case User::TEACHER:
            $data['nav']['departments'] = Department::whereHas('classes', function($q) {
                $q->where('teacher_id', '=', Auth::user()->id);
            })->get();
            $data['nav']['classes'] = Auth::user()->classesForYear();
            $data['nav']['teachers'] = [];
            $cycles = Cycle::whereHas('classes', function($q) {
                $q->where('teacher_id', '=', Auth::user()->id);
                $q->where('classes.start_year', '=', \DB::raw('YEAR(cycles.start_date)'));
            })->orderBy('start_date', 'DESC')->get();
            $data['nav']['cycles'] = array();
            foreach ($cycles as $cycle) {
                $data['nav']['cycles'][] = $cycle;
            }
            break;
    }

    $view->with($data);
});

// create the model binding for URL params
//Route::model('class', 'aClass');
//Route::model('department', 'Department');
//Route::model('school', 'School');
//Route::model('user', 'User');
//Route::model('cycle', 'Cycle');

// requires user to be NOT logged in
//Route::group(array('before' => 'guest'), function() {
//
//    // auth routes
//    Route::get('/login', array('uses' => 'UsersController@login', 'as' => 'user.login'));
//    Route::post('/login', array('uses' => 'UsersController@authenticate', 'as' => 'user.authenticate'));
//    Route::get('/password/remind', array('uses' => 'RemindersController@view', 'as' => 'remind.view'));
//    Route::post('/password/remind/send', array('uses' => 'RemindersController@send', 'as' => 'remind.send'));
//    Route::get('/password/reset/{token}', array('uses' => 'RemindersController@reset_view', 'as' => 'reset.view'));
//    Route::post('/password/reset/process', array('uses' => 'RemindersController@reset_process', 'as' => 'reset.process'));
//});

// requires user to be LOGGED IN
Route::group(array('before' => 'auth'), function() {

//    // auth routes
//    Route::get('/logout', array('uses' => 'UsersController@logout', 'as' => 'user.logout'));
//    Route::get('/logout_as', array('uses' => 'UsersController@logout_as'));

    // This is a static view page only
    Route::get('/help', array('uses' => 'HelpController@view'));

//    // requires user to be PIVOT ADMIN
//    Route::group(array('before' => 'administrator'), function() {
//        Route::get('/users', array('uses' => 'UsersController@index', 'as' => 'user.index'));
//        Route::get('/user/login_as/{user_id}', array('uses' => 'UsersController@login_as'));
//    });

    // requires user belong to the SCHOOL, or be PIVOT ADMIN
    Route::group(array('before' => 'school'), function() {

        // view routes
//        Route::get('/user/view/{user}', array('uses' => 'UsersController@view', 'as' => 'user.view'));

        // report routes
        //question breakdown
//        Route::get('reports/question_break_down_teacher/{user}/{cycle}/{mode?}', array('uses' =>'QuestionBreakdownController@teacher_page', 'before' => 'auth', 'as' => 'report.question_breakdown'));
//        Route::get('reports/question_break_down_school_admin/{user}/{cycle}/{mode?}', array('uses' =>'QuestionBreakdownController@principal_page', 'before' => 'auth', 'as' => 'report.question_breakdown'));
//        Route::get('reports/question_break_down_department_head/{department}/{cycle}/{mode?}', array('uses' =>'QuestionBreakdownController@department_head_page', 'before' => 'auth', 'as' => 'report.question_breakdown'));

        // scatter plot
//        Route::get('reports/scatter_plot_school_admin/{user}/{cycle}/{mode?}', array('uses' =>'ScatterPlotController@principal_page', 'before' => 'auth', 'as' => 'report.scatter_plot'));
//        Route::get('reports/scatter_plot_department_head/{department}/{cycle}/{mode?}', array('uses' =>'ScatterPlotController@department_head_page', 'before' => 'auth', 'as' => 'report.scatter_plot'));

		//bar graph
//        Route::get('reports/bar_graph_school_admin/{user}/{cycle}/{mode?}', array('uses' =>'BarGraphController@principal_page', 'before' => 'auth', 'as' => 'report.bar_graph'));
//        Route::get('reports/bar_graph_department_head/{department}/{cycle}/{mode?}', array('uses' =>'BarGraphController@department_head_page', 'before' => 'auth', 'as' => 'report.bar_graph'));

        // heatmap
//        Route::get('reports/heatmap_school_admin/{user}/{cycle}', array('uses' =>'HeatmapController@principal_page', 'before' => 'auth', 'as' => 'report.heat_map'));
//        Route::get('reports/heatmap_department_head/{department}/{cycle}', array('uses' =>'HeatmapController@department_head_page', 'before' => 'auth', 'as' => 'report.heat_map'));
//        Route::get('reports/heatmap_teacher/{user}/{cycle}', array('uses' =>'HeatmapController@teacher_page', 'before' => 'auth', 'as' => 'report.heat_map'));

        //comparison table
//        Route::get('reports/comparison_table_school_admin/{user}/{cycle}/{mode?}', array('uses' =>'ComparisonTableController@principal_page', 'before' => 'auth', 'as' => 'report.comparison_table'));


        // Route::get('/quicklook/{user}/{department}', array('uses' => 'QuicklookSummary@show_page', 'as' => 'report.quicklook'));
        // requires user to be SCHOOL ADMIN, or PIVOT ADMIN
        Route::group(array('before' => 'editor'), function() {


//            Route::get('/school/importcsv/{school}', array('uses' => 'ImportCSVController@view', 'as' => 'school.editcsv'));
//            Route::post('/school/importcsv/{school}', array('uses' => 'ImportCSVController@process', 'as' => 'school.savecsv', 'before' => 'csrf'));

            // user routes
//            Route::get('/user/create/{school?}', array('uses' => 'UsersController@create', 'as' => 'user.create'));
//            Route::get('/user/edit/{user}', array('uses' => 'UsersController@edit', 'as' => 'user.edit'));
//            Route::post('/user/save/{data}', array('uses' => 'UsersController@save', 'as' => 'user.save', 'before' => 'csrf'));
//            Route::get('/user/delete/{user}', array('uses' => 'UsersController@delete', 'as' => 'user.delete'));
        });
    });
});


Route::get('/test', array('uses' => 'TestController@index', 'as' => 'test.index'));
Route::get('/test/resend-teachers-welcome-mails', array('uses' => 'TestController@resendTeachersWelcomeMails', 'as' => 'test.resendTeachersWelcomeMails'));
//Route::get('/test/mailgun', array('uses' => 'TestController@testMailgun', 'as' => 'test.testMailgun'));