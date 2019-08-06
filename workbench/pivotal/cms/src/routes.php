<?php
Route::group(array('before' => 'auth'), function() {
    Route::group(array('prefix' => 'resource'), function() {
        Route::get('/', array('as' => 'csv.resource.index', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@index'));
        Route::get('/byquestion', array('as' => 'csv.resource.byquestion', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@byQuestion'));
        Route::get('/general', array('as' => 'csv.resource.general', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@general'));
        Route::get('/q1', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q1'));
        Route::get('/q2', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q2'));
        Route::get('/q3', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q3'));
        Route::get('/q4', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q4'));
        Route::get('/q5', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q5'));
        Route::get('/q6', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q6'));
        Route::get('/q7', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q7'));
        Route::get('/q8', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q8'));
        Route::get('/q9', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q9'));
        Route::get('/q10', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q10'));
        Route::get('/q11', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q11'));
        Route::get('/q12', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q12'));
        Route::get('/q13', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q13'));
        Route::get('/q14', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q14'));
        Route::get('/q15', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q15'));
        Route::get('/q16', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q16'));
        Route::get('/q17', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q17'));
        Route::get('/q18', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q18'));
        Route::get('/q19', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q19'));
        Route::get('/q20', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q20'));
        Route::get('/q21', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q21'));
        Route::get('/q22', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q22'));
        Route::get('/q23', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q23'));
        Route::get('/q24', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q24'));
        Route::get('/q25', array('as' => 'resource.q1', 'uses' => 'Pivotal\Cms\Controllers\ResourceController@q25'));

    });
});