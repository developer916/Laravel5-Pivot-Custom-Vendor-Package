<?php
Route::group(array('before' => 'auth'), function () {
    Route::group(array('prefix' => 'admin'), function () {

        Route::get('/user/import_meta/{school_id}', array('uses' => 'Pivotal\Admin\Controllers\UserController@importMeta', 'as' => 'admin.user.import.meta'));
        Route::post('/user/import_meta/{school_id}', array('uses' => 'Pivotal\Admin\Controllers\UserController@importMetaPost', 'as' => 'admin.user.import.meta.post'));

    });
});