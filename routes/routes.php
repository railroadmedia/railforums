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

// posts
Route::put(
    'post/store',
    \Railroad\Railforums\Controllers\UserForumPostController::class . '@store'
)->name('railforums.post.store');

Route::patch(
    'post/update/{id}',
    \Railroad\Railforums\Controllers\UserForumPostController::class . '@update'
)->name('railforums.post.update');

// -----------------------
Route::group(
    [
        'prefix' => 'forums/',
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
    });
