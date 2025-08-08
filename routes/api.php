<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'chatify'], function () {

        Route::group(['prefix' => 'message'], function () {
            Route::get('send', [Jazer\Chatify\Http\Controllers\Message\Send::class, 'send']);
        });

        Route::group(['prefix' => 'attachment'], function () {

        });

        Route::group(['prefix' => 'convo'], function () {

        });
    });
});

