<?php

use App\Models\User;
use App\Services\User\AuthService;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);
it('registers a user with valid data', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'user@test.com',
        'password' => 'password123',
    ];

    $userRepository = Mockery::mock(UserRepository::class);
    $userRepository->shouldReceive('create')->once()->andReturn($userData);
    $authService = new AuthService($userRepository);
    $result = $authService->register($userData);

    expect($result)->toBe($userData);
});

it('throws an exception when password is missing', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com'
    ];

    $userRepository = $this->createMock(UserRepository::class);
    $authService = new AuthService($userRepository);

    $this->expectException(ErrorException::class);

    $authService->register($userData);
});

it('throws an exception when invalid credentials are provided', function () {
    $user = User::factory()->make();
    $credentials = [
        'email' => $user->email,
        'password' => 'wrong_password',
        'device_name' => 'test'
    ];

    $userRepository = $this->createMock(UserRepository::class);
    $userRepository->expects($this->once())->method('findByEmail')->willReturn($user);

    $authService = new AuthService($userRepository);

    $this->expectException(ValidationException::class);

    $authService->login($credentials);
});

it('throws an exception when user does not exist', function () {
    $credentials = [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
        'device_name' => 'test'
    ];

    $userRepository = $this->createMock(UserRepository::class);
    $userRepository->expects($this->once())->method('findByEmail')->willReturn(null);

    $authService = new AuthService($userRepository);

    $this->expectException(ValidationException::class);

    $authService->login($credentials);
});
