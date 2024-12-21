<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTAuthController extends Controller
{
    // User registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                [
                    'errorMessage' => 'Please check the input data.',
                    'errors' => $validator->errors()
                ],
                400
            );
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);


        return $this->successResponse([
            "token" => $token,
            "user" => $user
        ], 201);
    }

    // User login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse(['errorMessage' => 'Invalid credentials'], 401);
            }

            // Get the authenticated user.
            $user = auth()->user();

            // (optional) Attach the role to the token.
            // $token = JWTAuth::claims(['role' => $user->role])->fromUser($user);
            $token = JWTAuth::fromUser($user);

            return $this->successResponse([
                "token" => $token,
                "user" => $user
            ]);
        } catch (JWTException $e) {
            dd($e);
            return $this->errorResponse(['errorMessage' => 'Could not create token'], 500);
        }
    }

    // Get authenticated user
    public function me()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->errorResponse(['errorMessage' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            return $this->errorResponse(['errorMessage' => 'Invalid token'], 400);
        }

        return $this->successResponse([
            "user" => $user
        ]);
    }

    // User logout
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return $this->successResponse([
            "loggedOut" => true
        ]);
    }
}
