<?php

namespace App\Services\User;

use App\Http\Requests\User\CreateRequest;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function register(array $userData): array
    {
        $encryptedPassword = Hash::make($userData['password']);
        $userData = [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $encryptedPassword
        ];

        return $this->userRepository->create($userData);
    }

    public function login(array $credentials): string
    {
        $user = $this->userRepository->findByEmail($credentials['email']);
        if(!$user){
            throw ValidationException::withMessages(['Invalid credentials']);
        }

        $passwordCheck = Hash::check($credentials['password'], $user->password);
        if(!$passwordCheck){
            throw ValidationException::withMessages(['Invalid credentials']);
        }

        if($user->role === 'admin'){
            return $user->createToken($credentials['device_name'], ['*'])->plainTextToken;
        }

        return $user->createToken($credentials['device_name'], ['user'])->plainTextToken;
    }

}
