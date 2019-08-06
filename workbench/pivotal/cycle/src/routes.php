<?php
Route::group(array('before' => 'auth'), function() {
    Route::group(array('before' => 'cycle'), function() {
        Route::get('/cycle/view/{cycle}', array('uses' => 'Pivotal\Cycle\Controllers\CycleController@view', 'as' => 'cycle.view'));
        Route::get('/cycle/departmentview/{cycle}/{department}', array('uses' => 'Pivotal\Cycle\Controllers\CycleController@view', 'as' => 'cycle.view'));
    });

    Route::group(array('before' => 'editor'), function() {
        //Cycle edit routes
        Route::get('/cycle/edit/{school}/{cycle?}', array('uses' => 'Pivotal\Cycle\Controllers\CycleController@edit', 'as' => 'cycle.edit'));
        Route::post('/cycle/save/{school}/{cycle?}', array('uses' => 'Pivotal\Cycle\Controllers\CycleController@save', 'as' => 'cycle.save', 'before' => 'csrf'));
        Route::get('/cycle/delete/{cycle}', array('uses' => 'Pivotal\Cycle\Controllers\CycleController@delete', 'as' => 'cycle.delete'));

        // cycle edit class routes
        Route::get('/cycle/class/edit/{cycle}', array('uses' => 'Pivotal\Cycle\Controllers\CycleController@edit_class', 'as' => 'cycle_class.edit'));
        Route::post('/cycle/class/save/{cycle}', array('uses' => 'Pivotal\Cycle\Controllers\CycleController@save_class', 'as' => 'cycle_class.save', 'before' => 'csrf'));
        Route::get('/cycle/class/delete/{cycle}/{course}', array('uses' => 'Pivotal\Cycle\Controllers\CycleController@delete_class', 'as' => 'cycle_class.delete'));

        // cycle edit class routes
        Route::get('/cycle/class/fake/{cycle}/{course}', array('uses' => 'Pivotal\Cycle\Controllers\CycleController@fake_class', 'as' => 'cycle_class.delete'));
    });

    //Route::get('/cycle/test/send_pre_notifications', array('uses' => 'Pivotal\Cycle\Controllers\CycleController@send_pre_notifications', 'as' => 'cycle_class.send_pre_notifications'));
    //Route::get('/cycle/test/send_pre_notifications_once', array('uses' => 'Pivotal\Cycle\Controllers\CycleController@send_pre_notifications_once', 'as' => 'cycle_class.send_pre_notifications_once'));

});