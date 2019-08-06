<?php
Route::group(array('before' => 'auth'), function() {
    Route::group(array('before' => 'department'), function() {
        Route::get('/department/view/{department}', array('uses' => 'Pivotal\Department\Controllers\DepartmentController@view', 'as' => 'department.view'));
    });

    Route::group(array('before' => 'editor'), function() {
        //Cycle edit routes
        // department routes
        Route::get('/department/edit/{school}/{department?}', array('uses' => 'Pivotal\Department\Controllers\DepartmentController@edit', 'as' => 'department.edit'));
        Route::post('/department/save/{school}/{department?}', array('uses' => 'Pivotal\Department\Controllers\DepartmentController@save', 'as' => 'department.save', 'before' => 'csrf'));
        Route::get('/department/delete/{department}', array('uses' => 'Pivotal\Department\Controllers\DepartmentController@delete', 'as' => 'department.delete'));
    });
});