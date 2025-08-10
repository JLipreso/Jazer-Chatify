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
            Route::post('create', [Jazer\Chatify\Http\Controllers\Convo\ConvoController::class, 'createConvo']);
            Route::put('update/{convo_refid}', [Jazer\Chatify\Http\Controllers\Convo\ConvoController::class, 'updateConvo']);
            Route::patch('update/{convo_refid}', [Jazer\Chatify\Http\Controllers\Convo\ConvoController::class, 'updateConvo']);
            Route::delete('delete/{convo_refid}', [Jazer\Chatify\Http\Controllers\Convo\ConvoController::class, 'deleteConvo']);
            Route::get('fetch/{user_refid}', [Jazer\Chatify\Http\Controllers\Convo\ConvoController::class, 'fetchConvo']);
            Route::get('single/{convo_refid}', [Jazer\Chatify\Http\Controllers\Convo\ConvoController::class, 'fetchSingleConvo']);
        });

    });
});

