<?php
Route::group(array('before' => 'guest'), function() {

    // auth routes
    Route::get('/login', array('uses' => 'Pivotal\User\Controllers\UserController@login', 'as' => 'user.login'));
    Route::post('/login', array('uses' => 'Pivotal\User\Controllers\UserController@authenticate', 'as' => 'user.authenticate'));
    Route::get('/password/remind', array('uses' => 'Pivotal\User\Controllers\ReminderController@view', 'as' => 'remind.view'));
    Route::post('/password/remind/send', array('uses' => 'Pivotal\User\Controllers\ReminderController@send', 'as' => 'remind.send'));
    Route::get('/password/reset/{token}', array('uses' => 'Pivotal\User\Controllers\ReminderController@reset_view', 'as' => 'reset.view'));
    Route::post('/password/reset/process', array('uses' => 'Pivotal\User\Controllers\ReminderController@reset_process', 'as' => 'reset.process'));
});


Route::group(array('before' => 'auth'), function() {
    // auth routes
    Route::get('/logout', array('uses' => 'Pivotal\User\Controllers\UserController@logout', 'as' => 'user.logout'));
    Route::get('/logout_as', array('uses' => 'Pivotal\User\Controllers\UserController@logout_as'));

    Route::get('/user/login/{user_id}', array('as' => 'user.login.as', 'uses' => 'Pivotal\User\Controllers\UserController@login_as'));


    // requires user to be PIVOT ADMIN
    Route::group(array('before' => 'administrator'), function() {
        Route::get('/users', array('uses' => 'Pivotal\User\Controllers\UserController@index', 'as' => 'user.index'));
        Route::get('/user/login_as/{user_id}', array('as' => 'admin.login.as', 'uses' => 'Pivotal\User\Controllers\UserController@login_as'));
    });
    Route::group(array('before' => 'user'), function() {
        Route::get('/user/view/{user}', array('uses' => 'Pivotal\User\Controllers\UserController@view', 'as' => 'user.view'));
    });
    Route::group(array('before' => 'editor'), function() {
        // user routes
        Route::get('/user/create/{school?}', array('uses' => 'Pivotal\User\Controllers\UserController@create', 'as' => 'user.create'));
        Route::get('/user/edit/{user}', array('uses' => 'Pivotal\User\Controllers\UserController@edit', 'as' => 'user.edit'));
        Route::post('/user/save/{data}', array('uses' => 'Pivotal\User\Controllers\UserController@save', 'as' => 'user.save', 'before' => 'csrf'));
        Route::get('/user/delete/{user}', array('uses' => 'Pivotal\User\Controllers\UserController@delete', 'as' => 'user.delete'));
    });
});