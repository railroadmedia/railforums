<?php

namespace Tests;

use Railroad\Permissions\Exceptions\NotAllowedException;
use Railroad\Railforums\Services\ConfigService;

class UserForumSignaturesJsonControllerTest extends TestCase
{
    const API_PREFIX = '/forums';

    protected function setUp()
    {
        $this->setDefaultConnection('testbench');

        parent::setUp();
    }

    public function test_signature_store_with_permission()
    {
        $user = $this->fakeUser();

        $signatureData = [
            'signature' => $this->faker->paragraph(),
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response =
            $this->actingAs($user)
                ->call(
                    'PUT',
                    self::API_PREFIX . '/signature/store',
                    $signatureData
                );

        // assert the signature data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableUserSignatures,
            [
                'signature' => $signatureData['signature'],
                'user_id' => $user['id'],
            ]
        );
    }

    public function test_signature_store_without_permission()
    {
        $user = $this->fakeUser();

        $signatureData = [
            'signature' => $this->faker->paragraph(),
        ];

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to create-user-signature')
            );

        $response =
            $this->actingAs($user)
                ->call(
                    'PUT',
                    self::API_PREFIX . '/signature/store',
                    $signatureData
                );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());

        // assert the signature data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableUserSignatures,
            [
                'signaature' => $signatureData['signature'],
                'user_id' => $user['id'],
            ]
        );
    }

    public function test_signature_store_validation_fail()
    {
        $response = $this->call(
            'PUT',
            self::API_PREFIX . '/signature/store',
            []
        );

        // assert response status code
        $this->assertEquals(422, $response->getStatusCode());

        // assert response validation error messages
        $this->assertEquals(
            [
                [
                    "source" => "signature",
                    "detail" => "The signature field is required.",
                ],
            ],
            $response->decodeResponseJson()['errors']
        );
    }

    public function test_signature_update_with_permission()
    {
        $signature = $this->fakeSignature();

        $newSignature = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/signature/update/' . $signature['id'],
            ['signature' => $newSignature]
        );

        // assert the signature data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableUserSignatures,
            [
                'id' => $signature['id'],
                'signature' => $newSignature,
            ]
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertEquals($newSignature, $response->decodeResponseJson('signature'));
        $this->assertEquals($signature['id'], $response->decodeResponseJson('id'));
    }

    public function test_signature_update_without_permission()
    {
        $user = $this->fakeUser();

        $signature = $this->fakeSignature();

        $newSignature = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to update-user-signature')
            );

        $response =
            $this->actingAs($user)
                ->call(
                    'PATCH',
                    self::API_PREFIX . '/signature/update/' . $signature['id'],
                    ['signature' => $newSignature]
                );

        // assert the signature data was not saved in the db
        $this->assertDatabaseMissing(
            ConfigService::$tableUserSignatures,
            [
                'id' => $signature['id'],
                'signature' => $newSignature,
            ]
        );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_signature_update_same_user()
    {
        $user = $this->fakeUser();

        $signature = $this->fakeSignature($user['id']);

        $newSignature = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to update-user-signature')
            );

        $response =
            $this->actingAs($user)
                ->call(
                    'PATCH',
                    self::API_PREFIX . '/signature/update/' . $signature['id'],
                    ['signature' => $newSignature]
                );

        // assert the signature data was saved in the db
        $this->assertDatabaseHas(
            ConfigService::$tableUserSignatures,
            [
                'id' => $signature['id'],
                'signature' => $newSignature,
            ]
        );

        // assert response status code
        $this->assertEquals(200, $response->getStatusCode());

        // assert response data
        $this->assertEquals($newSignature, $response->decodeResponseJson('signature'));
        $this->assertEquals($signature['id'], $response->decodeResponseJson('id'));
    }

    public function test_signature_update_not_found()
    {
        $newSignature = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'PATCH',
            self::API_PREFIX . '/signature/update/' . rand(0, 32767),
            ['signature' => $newSignature]
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_signature_delete()
    {
        $signature = $this->fakeSignature();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/signature/delete/' . $signature['id']
        );

        // assert response status code
        $this->assertEquals(204, $response->getStatusCode());

        // assert the signature data was deleted
        $this->assertDatabaseMissing(
            ConfigService::$tableUserSignatures,
            [
                'id' => $signature['id'],
            ]
        );
    }

    public function test_signature_delete_same_user()
    {
        $user = $this->fakeUser();
        $signature = $this->fakeSignature($user['id']);

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to delete-user-signature')
            );

        $response =
            $this->actingAs($user)
                ->call(
                    'DELETE',
                    self::API_PREFIX . '/signature/delete/' . $signature['id']
                );

        // assert response status code
        $this->assertEquals(204, $response->getStatusCode());

        // assert the signature data was deleted
        $this->assertDatabaseMissing(
            ConfigService::$tableUserSignatures,
            [
                'id' => $signature['id'],
            ]
        );
    }

    public function test_signature_delete_without_permission()
    {
        $user = $this->fakeUser();

        $signature = $this->fakeSignature();

        $this->permissionServiceMock->method('canOrThrow')
            ->willThrowException(
                new NotAllowedException('You are not allowed to delete-user-signature')
            );

        $response =
            $this->actingAs($user)
                ->call(
                    'DELETE',
                    self::API_PREFIX . '/signature/delete/' . $signature['id']
                );

        // assert response status code
        $this->assertEquals(403, $response->getStatusCode());

        // assert the signature data was not deleted from db
        $this->assertDatabaseHas(
            ConfigService::$tableUserSignatures,
            [
                'id' => $signature['id'],
            ]
        );
    }

    public function test_signature_delete_not_found()
    {
        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'DELETE',
            self::API_PREFIX . '/signature/delete/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }
}
