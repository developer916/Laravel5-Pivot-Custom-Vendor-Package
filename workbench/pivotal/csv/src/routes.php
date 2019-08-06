<?php
Route::group(array('before' => 'auth'), function() {
    Route::group(array('before' => 'editor'), function() {
        // user routes
        Route::get('/school/importcsv/{school}', array('uses' => 'Pivotal\Csv\Controllers\ImportCSVController@view', 'as' => 'school.editcsv'));
        Route::post('/school/importcsv/{school}', array('uses' => 'Pivotal\Csv\Controllers\ImportCSVController@process', 'as' => 'school.savecsv', 'before' => 'csrf'));
    });
});