<?php
Route::group(array('before' => 'auth'), function() {
    Route::group(array('before' => 'course'), function() {
        Route::get('/class/view/{course}', array('uses' => 'Pivotal\Course\Controllers\CourseController@view', 'as' => 'class.view'));
    });

    Route::group(array('before' => 'editor'), function() {
        Route::get('/class/edit/{school}/{department?}/{course?}', array('uses' => 'Pivotal\Course\Controllers\CourseController@edit', 'as' => 'class.edit'));
        Route::post('/class/save/{school}/{course?}', array('uses' => 'Pivotal\Course\Controllers\CourseController@save', 'as' => 'class.save', 'before' => 'csrf'));
        Route::get('/class/delete/{course}', array('uses' => 'Pivotal\Course\Controllers\CourseController@delete', 'as' => 'class.delete'));
    });
});