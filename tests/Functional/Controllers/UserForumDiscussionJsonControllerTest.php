<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Permissions\Exceptions\NotAllowedException;
use Railroad\Railforums\Services\ConfigService;

class UserForumDiscussionJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp()
    {
        $this->setDefaultConnection('testbench');

        parent::setUp();
    }

    public function test_discussions_index_with_permission()
    {
        $discussions = [];

        for ($i = 0; $i < 20; $i++) {
            /** @var array $category */
            $category = $this->fakeCategory();

            $discussions[] = $category;

            $thread1 = $this->fakeThread($category['id'], rand(1,7));
            $thread2 = $this->fakeThread($category['id'], rand(10,15));

            $post = $this->fakePost($thread1['id'], rand(2,5));
            $post2 = $this->fakePost($thread1['id'], rand(6,15));
        }



        $discussions = collect($discussions)->sortBy('created_at')->toArray();

        $payload = [
            'amount' => 5,
            'page' => 1
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);
           $response = $this->call(
            'GET',
            self::API_PREFIX . '/discussions/index',
            $payload
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        $results = $response->decodeResponseJson();

        $this->assertEquals(count($results['results']), $payload['amount']);

        // assert reponse entities have the requested category
        foreach ($results['results'] as $index=>$category) {
            $this->assertEquals($category['id'], $discussions[$index]['id']);
        }
    }

    public function test_discussions_index_without_permission()
    {
        $payload = [
            'amount' => 10,
            'page' => 1
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to index-discussions')
            );

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/discussions/index',
            $payload
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_discussion_show_with_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->actingAs($user)->call(
            'GET',
            self::API_PREFIX . '/discussions/show/' . $category['id']
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertArraySubset(
            [
                'title' => $category['title'],
                'description' => $category['description'],
                'topic' => $category['topic'],
            ],
            $response->decodeResponseJson()
        );
    }

    public function test_discussion_show_without_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to show-discussions')
            );

        $response = $this->actingAs($user)->call(
            'GET',
            self::API_PREFIX . '/discussions/show/' . $category['id']
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_discussion_show_with_decorated_data()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();
        $category2 = $this->fakeCategory();

        /** @var array $thread */
        $thread1 = $this->fakeThread($category['id'], $user['id']);
        $thread = $this->fakeThread($category2['id'], $user['id']);

        /** @var array $post */
        for($i=0; $i<15; $i++) {
            $post = $this->fakePost($thread['id'], $user['id']);
        }

        for($i=0; $i<5; $i++) {
            $post = $this->fakePost($thread1['id'], $user['id']);
        }

        $dateTime =
            Carbon::instance($this->faker->dateTime)
                ->toDateTimeString();

        $threadFollow = [
            'thread_id' => $thread['id'],
            'follower_id' => $user['id'],
            'followed_on' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $this->databaseManager->table(ConfigService::$tableThreadFollows)
            ->insertGetId($threadFollow);

        $threadRead = [
            'thread_id' => $thread['id'],
            'reader_id' => $user['id'],
            'read_on' => $dateTime,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];

        $this->databaseManager->table(ConfigService::$tableThreadReads)
            ->insertGetId($threadRead);

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response =
            $this->actingAs($user)
                ->call(
                    'GET',
                    self::API_PREFIX . '/discussions/show/' . $category['id']
                );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());


    }

    public function test_discussion_show_not_exists()
    {
        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'GET',
            self::API_PREFIX . '/discussion/show/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

}
