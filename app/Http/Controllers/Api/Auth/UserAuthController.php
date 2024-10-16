<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Services\ServiceUser;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    protected ServiceUser $serviceUser;
    public function __construct(ServiceUser $serviceUser)
    {
        $this->serviceUser = $serviceUser;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function create(CreateUserRequest $request)
    {
        try {
            $result = $this->serviceUser->create($request);
        } catch (\Exception $e) {
            $result = [
                'message' => $e->getMessage(),
                'status' => $e->getCode()
            ];
        }
        return response()->json($result, $result['status'], [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Display the specified resource.
     */
    public function login(LoginRequest $request)
    {
        try {
            $result = $this->serviceUser->login($request);
        } catch (\Exception $e) {
            $result = [
                'message' => $e->getMessage(),
                'status' => $e->getCode()
            ];
        }
        return response()->json($result, $result['status'], [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Update the specified resource in storage.
     */
    public function logout(Request $request, string $id)
    {
        //
    }
}
