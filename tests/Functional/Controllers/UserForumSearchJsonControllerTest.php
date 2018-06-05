<?php

namespace Tests;

class UserForumSearchJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp()
    {
        $this->setDefaultConnection('mysql');

        parent::setUp();
    }

    public function test_search_index()
    {
        $user = $this->fakeCurrentUserCloak();
        $category = $this->fakeCategory();
        $thread = $this->fakeThread($category->getId(), $user->getId());
        $posts = [];

        $postCount = 20;

        for ($i = 0; $i < $postCount; $i++) { 
            $posts[] = $this->fakePost($thread->getId(), $user->getId());
        }

        $page = 1;
        $limit = 3;

        $command = $this->app->make(\Railroad\Railforums\Commands\CreateSearchIndexes::class);
        $command->handle();

        // $this->artisan('command:createSearchIndexes'); // TODO - make it work and remove the above

        $response = $this->call('GET', self::API_PREFIX . '/search', [
            'page' => $page,
            'limit' => $limit,
            'term' => $this->faker->sentence()
        ]);

        // TODO - assert response
        $this->assertTrue(true);
    }
}
