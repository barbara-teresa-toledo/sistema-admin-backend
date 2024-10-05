<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateRequest;
use App\Models\User;
use App\Services\User\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/register",
     *      summary="Register a new user",
     *      tags={"Authentication"},
     *      @OA\RequestBody(
     *          required=true,
     *          description="User data",
     *          @OA\JsonContent(
     *              required={"name", "email", "password", "password_confirmation"},
     *              @OA\Property(property="name", type="string", maxLength=255),
     *              @OA\Property(property="email", type="string", format="email", maxLength=255),
     *              @OA\Property(property="password", type="string", minLength=8, maxLength=255),
     *              @OA\Property(property="password_confirmation", type="string", maxLength=255),
     *              ),
     *          ),
     *          @OA\Response(
     *              response=201,
     *              description="User registered successfully",
     *          ),
     *          @OA\Response(
     *              response=500,
     *              description="Internal Server Error",
     *              @OA\JsonContent(
     *                  type="object",
     *                  @OA\Property(property="message", type="string"),
     *              ),
     *          ),
     *          security={}
     * )
     */
    public function register(CreateRequest $request)
    {
        try {
            $userData = $request->validated();
            $user = $this->authService->register($userData);

            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials",
     *         @OA\JsonContent(
     *             required={"email", "password", "device_name"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="device_name", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     security={}
     * )
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only(['email', 'password', 'device_name']);
            $token = $this->authService->login($credentials);

            return response()->json(['token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="User logout",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     security={}
     * )
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }
}
