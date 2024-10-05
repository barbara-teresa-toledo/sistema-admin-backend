<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Requests\User\CreateRequest;
use App\Services\User\AuthService;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);
it('registers a user successfully', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'user@test.com',
        'password' => 'password123',
    ];

    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('register')->once()->andReturn($userData);

    $authController = new AuthController($authService);
    $requestMock = Mockery::mock(CreateRequest::class);
    $requestMock->shouldReceive('validated')->once()->andReturn($userData);
    $response = $authController->register($requestMock);

    expect($response->getStatusCode())->toBe(Response::HTTP_CREATED);
});
