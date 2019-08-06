<?php
Route::group(array('before' => 'auth'), function() {
    // requires user to be PIVOT ADMIN
    Route::group(array('before' => 'administrator'), function() {
        Route::get('/schools', array('uses' => 'Pivotal\School\Controllers\SchoolController@index', 'as' => 'school.index'));
    });
    Route::group(array('before' => 'school'), function() {
        Route::get('/school/view/{school}', array('uses' => 'Pivotal\School\Controllers\SchoolController@view', 'as' => 'school.view'));
    });

    Route::group(array('before' => 'editor'), function() {
        // school routes
        Route::get('/school/edit/{school?}', array('uses' => 'Pivotal\School\Controllers\SchoolController@edit', 'as' => 'school.edit'));
        Route::get('/school/delete/{school}', array('uses' => 'Pivotal\School\Controllers\SchoolController@delete', 'as' => 'school.delete'));
        Route::post('/school/save/{school?}', array('uses' => 'Pivotal\School\Controllers\SchoolController@save', 'as' => 'school.save', 'before' => 'csrf'));
    });
});