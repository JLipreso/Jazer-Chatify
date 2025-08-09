<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Jazer\Chatify\Http\Events\ChatEvent;

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'chatify'], function () {

        Route::group(['prefix' => 'message'], function () {
            Route::post('send', [Jazer\Chatify\Http\Controllers\Message\Send::class, 'send']);
        });

        Route::group(['prefix' => 'attachment'], function () {

        });

        Route::group(['prefix' => 'convo'], function () {

        });

    });
});

