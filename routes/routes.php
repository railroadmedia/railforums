<?php
// threads
Route::put(
    'thread/store',
    \Railroad\Railforums\Controllers\UserForumThreadController::class . '@store'
)->name('railforums.thread.store');

Route::patch(
    'thread/update/{id}',
    \Railroad\Railforums\Controllers\UserForumThreadController::class . '@update'
)->name('railforums.thread.update');

// threads follow
Route::put(
    'thread/follow/{id}',
    \Railroad\Railforums\Controllers\UserForumThreadController::class . '@follow'
)->name('railforums.thread.follow');

Route::delete(
    'thread/unfollow/{id}',
    \Railroad\Railforums\Controllers\UserForumThreadController::class . '@unfollow'
)->name('railforums.thread.unfollow');

// threads read
Route::put(
    'thread/read/{id}',
    \Railroad\Railforums\Controllers\UserForumThreadController::class . '@read'
)->name('railforums.thread.read');

// threads delete
Route::delete(
    'thread/delete/{id}',
    \Railroad\Railforums\Controllers\UserForumThreadController::class . '@delete'
)->name('railforums.thread.delete');


// posts
Route::put(
    'post/store',
    \Railroad\Railforums\Controllers\UserForumPostController::class . '@store'
)->name('railforums.post.store');

Route::patch(
    'post/update/{id}',
    \Railroad\Railforums\Controllers\UserForumPostController::class . '@update'
)->name('railforums.post.update');

// post likes
Route::put(
    'post/like/{id}',
    \Railroad\Railforums\Controllers\UserForumPostController::class . '@like'
)->name('railforums.post.like');

Route::delete(
    'post/unlike/{id}',
    \Railroad\Railforums\Controllers\UserForumPostController::class . '@unlike'
)->name('railforums.post.unlike');

// -----------------------
Route::group(
    [
        'prefix' => 'forums/',
        'middleware' => config('railcontent.api_middleware',[]),
    ],
    function () {
        // thread api
        Route::get(
            'thread/index',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@index'
        )->name('railforums.api.thread.index');

        Route::get(
            'thread/show/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@show'
        )->name('railforums.api.thread.show');

        Route::put(
            'thread/store',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@store'
        )->name('railforums.api.thread.store');

        Route::patch(
            'thread/update/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@update'
        )->name('railforums.api.thread.update');

        Route::delete(
            'thread/delete/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@delete'
        )->name('railforums.api.thread.delete');

        Route::put(
            'thread/follow/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@follow'
        )->name('railforums.thread.follow');

        Route::delete(
            'thread/unfollow/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@unfollow'
        )->name('railforums.thread.unfollow');

        Route::put(
            'thread/read/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class . '@read'
        )->name('railforums.thread.read');

        // post api
        Route::get(
            'post/index',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@index'
        )->name('railforums.api.post.index');

        Route::get(
            'post/show/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@show'
        )->name('railforums.api.post.show');

        Route::put(
            'post/store',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@store'
        )->name('railforums.api.post.store');

        Route::patch(
            'post/update/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@update'
        )->name('railforums.api.post.update');

        Route::delete(
            'post/delete/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@delete'
        )->name('railforums.api.post.delete');

        Route::put(
            'post/report/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@report'
        )->name('railforums.api.post.report');

        // post likes
        Route::put(
            'post/like/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@like'
        )->name('railforums.api.post.like');

        Route::delete(
            'post/unlike/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class . '@unlike'
        )->name('railforums.api.post.unlike');

        // search
        Route::get(
            'search',
            \Railroad\Railforums\Controllers\UserForumSearchJsonController::class . '@index'
        )->name('railforums.api.search.index');

        //categories
        Route::put(
            'discussions/store',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class . '@store'
        )->name('railforums.api.discussion.store');

        Route::get(
            'discussions/show/{id}',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class . '@show'
        )->name('railforums.api.discussion.show');

        Route::get(
            'discussions/index',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class . '@index'
        )->name('railforums.api.discussions.index');
    });
