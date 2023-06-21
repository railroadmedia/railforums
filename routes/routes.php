<?php

Route::group(
    [
        'middleware' => config('railforums.controller_middleware', []),
    ],
    function () {
        // discussions
        Route::put(
            'discussion/store',
            \Railroad\Railforums\Controllers\UserForumDiscussionController::class.'@store'
        )->name('railforums.discussion.store');
        Route::patch(
            'discussion/update/{id}',
            \Railroad\Railforums\Controllers\UserForumDiscussionController::class.'@update'
        )->name('railforums.discussion.update');
        Route::delete(
            'discussion/delete/{id}',
            \Railroad\Railforums\Controllers\UserForumDiscussionController::class.'@delete'
        )->name('railforums.discussion.delete');

        // threads
        Route::put(
            'thread/store',
            \Railroad\Railforums\Controllers\UserForumThreadController::class.'@store'
        )->name('railforums.thread.store');

        Route::patch(
            'thread/update/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadController::class.'@update'
        )->name('railforums.thread.update');

        // threads follow
        Route::put(
            'thread/follow/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadController::class.'@follow'
        )->name('railforums.thread.follow');

        Route::delete(
            'thread/unfollow/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadController::class.'@unfollow'
        )->name('railforums.thread.unfollow');

        // threads read
        Route::put(
            'thread/read/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadController::class.'@read'
        )->name('railforums.thread.read');

        // threads delete
        Route::delete(
            'thread/delete/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadController::class.'@delete'
        )->name('railforums.thread.delete');


        // posts
        Route::put(
            'post/store',
            \Railroad\Railforums\Controllers\UserForumPostController::class.'@store'
        )->name('railforums.post.store');

        Route::patch(
            'post/update/{id}',
            \Railroad\Railforums\Controllers\UserForumPostController::class.'@update'
        )->name('railforums.post.update');

        // post likes
        Route::put(
            'post/like/{id}',
            \Railroad\Railforums\Controllers\UserForumPostController::class.'@like'
        )->name('railforums.post.like');

        Route::delete(
            'post/unlike/{id}',
            \Railroad\Railforums\Controllers\UserForumPostController::class.'@unlike'
        )->name('railforums.post.unlike');

        Route::group(
            [
                'middleware' => config('railforums.route_middleware_logged_in_groups', []),
            ],
            function () {
                //user signatures
                Route::put(
                    'signature/store',
                    \Railroad\Railforums\Controllers\UserForumSignaturesController::class.'@store'
                )
                    ->middleware([
                        \Railroad\Railforums\Middleware\HTMLSanitization::class,
                    ])
                    ->name('railforums.signature.store');

                Route::patch(
                    'signature/update/{id}',
                    \Railroad\Railforums\Controllers\UserForumSignaturesController::class.'@update'
                )
                    ->middleware([
                        \Railroad\Railforums\Middleware\HTMLSanitization::class,
                    ])
                    ->name('railforums.signature.update');

                Route::delete(
                    'signature/delete/{id}',
                    \Railroad\Railforums\Controllers\UserForumSignaturesController::class.'@delete'
                )
                    ->name('railforums.discussions.delete');
            }
        );
    }
);

Route::group(
    [
        'prefix' => 'forums/',
        'middleware' => config('railforums.controller_middleware', []),
    ],
    function () {
        Route::get(
            'thread/index',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class.'@index'
        )->name('railforums.thread.index');

        Route::get(
            'thread/show/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class.'@show'
        )->name('railforums.mobile.thread.show');

        Route::put(
            'thread/store',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class.'@store'
        )->name('railforums.api.thread.store');

        Route::patch(
            'thread/update/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class.'@update'
        )->name('railforums.api.thread.update');

        Route::delete(
            'thread/delete/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class.'@delete'
        )->name('railforums.api.thread.delete');

        Route::put(
            'thread/follow/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class.'@follow'
        )->name('railforums.api.thread.follow');

        Route::delete(
            'thread/unfollow/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class.'@unfollow'
        )->name('railforums.api.thread.unfollow');

        Route::put(
            'thread/read/{id}',
            \Railroad\Railforums\Controllers\UserForumThreadJsonController::class.'@read'
        )->name('railforums.api.thread.read');

        // post api
        Route::get(
            'post/index',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class.'@index'
        )->name('railforums.api.post.index');

        Route::get(
            'post/show/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class.'@show'
        )->name('railforums.api.post.show');

        Route::put(
            'post/store',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class.'@store'
        )->name('railforums.api.post.store');

        Route::patch(
            'post/update/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class.'@update'
        )->name('railforums.api.post.update');

        Route::delete(
            'post/delete/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class.'@delete'
        )->name('railforums.api.post.delete');

        Route::put(
            'post/report/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class.'@report'
        )->name('railforums.api.post.report');

        // post likes
        Route::put(
            'post/like/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class.'@like'
        )->name('railforums.api.post.like');

        Route::delete(
            'post/unlike/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class.'@unlike'
        )->name('railforums.api.post.unlike');

        // search
        Route::get(
            'search',
            \Railroad\Railforums\Controllers\UserForumSearchJsonController::class.'@index'
        )->name('railforums.api.search.index');

        //categories
        Route::put(
            'discussions/store',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class.'@store'
        )->name('railforums.api.discussion.store');

        Route::get(
            'discussions/show/{id}',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class.'@show'
        )->name('railforums.api.discussion.show');

        Route::get(
            'discussions/index',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class.'@index'
        )->name('railforums.api.discussions.index');

        Route::patch(
            'discussions/update/{id}',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class.'@update'
        )->name('railforums.api.discussions.update');

        Route::delete(
            'discussions/delete/{id}',
            \Railroad\Railforums\Controllers\UserForumDiscussionJsonController::class.'@delete'
        )->name('railforums.api.discussions.delete');

        //user signatures
        Route::put(
            'signature/store',
            \Railroad\Railforums\Controllers\UserForumSignaturesJsonController::class.'@store'
        )->name('railforums.api.signature.store');

        Route::patch(
            'signature/update/{id}',
            \Railroad\Railforums\Controllers\UserForumSignaturesJsonController::class.'@update'
        )->name('railforums.api.signature.update');

        Route::delete(
            'signature/delete/{id}',
            \Railroad\Railforums\Controllers\UserForumSignaturesJsonController::class.'@delete'
        )->name('railforums.api.signature.delete');

        //post-likes
        Route::get(
            '/post-likes/{id}',
            \Railroad\Railforums\Controllers\UserForumPostJsonController::class. '@getPostLikes'
        )->name('railforums.post-likes.index');
    }
);
