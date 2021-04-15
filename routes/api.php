<?php

Route::group(
    [
        'prefix' => 'forums/api/',
        'middleware' => config('railcontent.api_middleware',[]),
    ],
    function () {
        // thread api
        Route::get(
            'thread/index',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@index'
        );

        Route::get(
            'thread/show/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@show'
        );

        Route::put(
            'thread/store',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@store'
        );

        Route::patch(
            'thread/update/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@update'
        );

        Route::delete(
            'thread/delete/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@delete'
        );

        Route::put(
            'thread/follow/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@follow'
        );

        Route::delete(
            'thread/unfollow/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@unfollow'
        );

        Route::put(
            'thread/read/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@read'
        );

        // post api
        Route::get(
            'post/index',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@index'
        )->name('railforums.api.post.index');

        Route::get(
            'post/show/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@show'
        );

        Route::put(
            'post/store',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@store'
        );

        Route::patch(
            'post/update/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@update'
        );

        Route::delete(
            'post/delete/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@delete'
        );

        Route::put(
            'post/report/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@report'
        );

        // post likes
        Route::put(
            'post/like/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@like'
        );

        Route::delete(
            'post/unlike/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@unlike'
        );

        // search
        Route::get(
            'search',
            \Railroad\Railforums\Controllers\UserForumSearchJsonController::class . '@index'
        );

        //categories
        Route::put(
            'discussions/store',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class . '@store'
        );

        Route::get(
            'discussions/show/{id}',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class . '@show'
        );

        Route::get(
            'discussions/index',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class . '@index'
        );

        Route::patch(
            'discussions/update/{id}',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class . '@update'
        );

        Route::delete(
            'discussions/delete/{id}',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class . '@delete'
        );

        //user signatures
        Route::put(
            'signature/store',
            \Railroad\Railforums\Controllers\UserForumSignaturesJsonController::class . '@store'
        );
        Route::patch(
            'signature/update/{id}',
            \Railroad\Railforums\Controllers\UserForumSignaturesJsonController::class . '@update'
        );
        Route::delete(
            'signature/delete/{id}',
            \Railroad\Railforums\Controllers\UserForumSignaturesJsonController::class . '@delete'
        );

    });