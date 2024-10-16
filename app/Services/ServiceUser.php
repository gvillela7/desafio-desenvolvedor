<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ServiceUser
{
    public function __construct()
    {
    }

    public function create(Request $request): array
    {
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);
        return ['data' => $user, 'status' => Response::HTTP_CREATED];
    }

    public function login(Request $request): ?array
    {
        $user = User::where('email', $request->get('email'))->first();
        if (!$user || !Hash::check($request->get('password'), $user->password)) {
            return [
                'message' => 'Invalid Credentials',
                'status' => Response::HTTP_UNAUTHORIZED
            ];
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'access_token' => $token,
            'status' => Response::HTTP_OK
        ];
    }
}
