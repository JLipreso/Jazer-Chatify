<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Jazer\Chatify\Http\Events\ChatEvent;

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'chatify'], function () {
        Route::group(['prefix' => 'message'], function () {
            Route::post('create', [Jazer\Chatify\Http\Controllers\Message\MessageController::class, 'createMessage']);
            Route::put('update/{convo_refid}/{dataid}', [Jazer\Chatify\Http\Controllers\Message\MessageController::class, 'updateMessage']);
            Route::patch('update/{messageId}', [Jazer\Chatify\Http\Controllers\Message\MessageController::class, 'updateMessage']);
            Route::delete('delete/{messageId}', [Jazer\Chatify\Http\Controllers\Message\MessageController::class, 'deleteMessage']);
            Route::get('fetch/{convo_refid}', [Jazer\Chatify\Http\Controllers\Message\MessageController::class, 'fetchMessages']);
            Route::get('single/{messageId}', [Jazer\Chatify\Http\Controllers\Message\MessageController::class, 'fetchSingleMessage']);
            Route::patch('pin/{messageId}', [Jazer\Chatify\Http\Controllers\Message\MessageController::class, 'togglePinMessage']);
            Route::get('pinned/{convo_refid}', [Jazer\Chatify\Http\Controllers\Message\MessageController::class, 'fetchPinnedMessages']);
            Route::get('search/{convo_refid}', [Jazer\Chatify\Http\Controllers\Message\MessageController::class, 'searchMessages']);
            Route::get('count/{convo_refid}', [Jazer\Chatify\Http\Controllers\Message\MessageController::class, 'getMessageCount']);
            Route::delete('bulk-delete', [Jazer\Chatify\Http\Controllers\Message\MessageController::class, 'bulkDeleteMessages']);
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

