<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'chatify'], function () {
        Route::get('test', function () {
            echo "OK";
        });
    });
});

