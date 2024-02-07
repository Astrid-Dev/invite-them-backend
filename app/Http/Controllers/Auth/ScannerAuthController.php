<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginFormRequest;
use App\Http\Requests\UserLoginFormRequest;
use App\Http\Requests\UserRegisterFormRequest;
use App\Models\User;
use Illuminate\Http\Response;

class ScannerAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:scanner', ['except' => ['login']]);
    }

    public function login(UserLoginFormRequest $request): Response
    {
        return $this->makeLogin(['pseudo' => $request->get('pseudo'), 'password' => $request->get('password')]);
    }

    protected function makeLogin($data): Response
    {
        if (! $token = auth('scanner')->attempt($data)) {
            return Response(['error' => 'Incorrect credentials'], 401);
        }

        return $this->createNewToken($token);
    }

    public function refreshToken():Response
    {
        return $this->createNewToken(auth('scanner')->refresh());
    }

    protected function createNewToken($token): Response
    {
        return Response([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('scanner')->factory()->getTTL(),
            'scanner' => auth('scanner')->user()
        ]);
    }

    public function logout(): Response
    {
        auth('scanner')->logout();
        return Response(['message' => 'Scanner successfully signed out']);
    }
}
