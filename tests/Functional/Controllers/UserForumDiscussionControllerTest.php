<?php

namespace Tests;

use Carbon\Carbon;
use Railroad\Railforums\Services\ConfigService;
use Railroad\Permissions\Exceptions\NotAllowedException;

class UserForumDiscussionControllerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }


    public function test_discussion_store_with_permission()
    {
        $user = $this->fakeUser();

        $discussionData = [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->text
        ];

        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->actingAs($user)->call(
            'PUT',
            '/discussion/store',
            $discussionData
        );

        // assert the discussion data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableCategories,
            [
                'title' => $discussionData['title'],
                'description' => $discussionData['description']
            ]
        );
    }

    public function test_discussion_store_without_permission()
    {
        $user = $this->fakeUser();

        $discussionData = [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->text
        ];

        $this->permissionServiceMock->method('canOrThrow')->willThrowException(
            new NotAllowedException('You are not allowed to create-discussions')
        );

        $response = $this->actingAs($user)->call(
            'PUT',
            '/discussion/store',
            $discussionData
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());

        // assert the discussion data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableCategories,
            [
                'title' => $discussionData['title'],
                'description' => $discussionData['description']
            ]
        );
    }

    public function test_discussion_store_validation_fail()
    {
        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->actingAs()->call(
            'PUT',
            '/discussion/store',
            []
        );

        // assert the session has the error messages
        $response->assertSessionHasErrors(
            ['title']
        );
    }

    public function test_discussion_update_with_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $newTitle = $this->faker->sentence();

        $this->permissionServiceMock->method('can')->willReturn(true);
        $this->permissionServiceMock
            ->method('columns')
            ->willReturn(['title' => $newTitle]);

        $response = $this->actingAs($user)->call(
            'PATCH',
            '/discussion/update/' . $category['id'],
            ['title' => $newTitle]
        );

        // assert the post data was saved in the db
        $this->assertDatabaseHas(
            'forum_categories',
            [
                'id' => $category['id'],
                'title' => $newTitle
            ]
        );
    }

    public function test_discussion_update_without_permission()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $newTitle = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')->willThrowException(
            new NotAllowedException('You are not allowed to update-discussions')
        );

        $response = $this->actingAs($user)->call(
            'PATCH',
            '/discussion/update/' . $category['id'],
            ['title' => $newTitle]
        );

        // assert the post data was not saved in the db
        $this->assertDatabaseMissing(
            'forum_categories',
            [
                'id' => $category['id'],
                'title' => $newTitle
            ]
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_discussion_update_validation_fail()
    {
        $user = $this->fakeUser();

        /** @var array $category */
        $category = $this->fakeCategory();

        $this->permissionServiceMock->method('can')->willReturn(true);

        $response = $this->actingAs($user)->call(
            'PATCH',
            '/discussion/update/' . $category['id'],
            ['title' => '']
        );

        // assert response status code
        $this->assertEquals(302, $response->getStatusCode());

        // assert validation fail
        $response->assertSessionHasErrors(['title']);
    }

    public function test_discussion_update_not_found()
    {
        $newDescription= $this->faker->sentence();

        $this->permissionServiceMock->method('can')->willReturn(true);
        $this->permissionServiceMock
            ->method('columns')
            ->willReturn(['description' => $newDescription]);

        $response = $this->actingAs()->call(
            'PATCH',
            '/discussion/update/' . rand(0, 32767),
            ['description' => $newDescription]
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }
}
