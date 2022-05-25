<?php

namespace Tests;

use Railroad\Permissions\Exceptions\NotAllowedException;
use Railroad\Railforums\Services\ConfigService;

class UserForumSignaturesControllerTest extends TestCase
{
    protected function setUp(): void
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
                    '/signature/store',
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
                    '/signature/store',
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
            '/signature/store',
            []
        );

        // assert the session has the error messages
        $response->assertSessionHasErrors(
            ['signature']
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
            '/signature/update/' . $signature['user_id'],
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
                    '/signature/update/' . $signature['user_id'],
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
                    '/signature/update/' . $signature['id'],
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
    }

    public function test_signature_update_not_found()
    {
        $newSignature = $this->faker->sentence();

        $this->permissionServiceMock->method('canOrThrow')
            ->willReturn(true);

        $response = $this->call(
            'PATCH',
            '/signature/update/' . rand(0, 32767),
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
            '/signature/delete/' . $signature['id']
        );

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
                    '/signature/delete/' . $signature['id']
                );

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
                    '/signature/delete/' . $signature['id']
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
            '/signature/delete/' . rand(0, 32767)
        );

        // assert response status code
        $this->assertEquals(404, $response->getStatusCode());
    }
}
