<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginFormRequest;
use App\Http\Requests\UserLoginFormRequest;
use App\Http\Requests\UserRegisterFormRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Response;

class UserAuthController extends Controller
{

    public function login(UserLoginFormRequest $request): Response
    {
        return $this->makeLogin([
            'pseudo' => $request->get('pseudo'),
            'password' => $request->get('password')
        ], $request->get('event_code'));
    }

    public function register(UserRegisterFormRequest $request):Response
    {
        $user = User::query()
            ->create(array_merge(
                $request->validated(),
                ['password' => bcrypt($request->password)]
            ));

        return self::makeLogin(['pseudo' => $user->pseudo, 'password' => $request->password]);
    }

    protected function makeLogin($data, $eventCode = null): Response
    {
        if (! $token = auth('api')->attempt($data)) {
            return Response(['error' => 'Incorrect credentials'], 401);
        }

        if ($eventCode) {
            $eventExists = Event::query()
                ->whereHas('scanners', function ($query) {
                    $query->where('user_id', auth('api')->id());
                })
                ->where('code', $eventCode)
                ->exists();
            if (!$eventExists) {
                auth('api')->logout();
                return Response(['error' => 'Event not found'], 404);
            }
        }

        return $this->createNewToken($token, $eventCode);
    }

    public function refreshToken():Response
    {
        return $this->createNewToken(auth('api')->refresh());
    }

    protected function createNewToken($token, $eventCode): Response
    {
        $user = auth('api')->user();
        if (!empty($eventCode)) {
            $user->current_event = Event::query()->where('code', $eventCode)->first();
        }
        return Response([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => !empty($eventCode) ? (24 * 60 * 60) : auth('api')->factory()->getTTL(),
            'user' => $user
        ]);
    }

    public function logout(): Response
    {
        auth('api')->logout();
        return Response(['message' => 'User successfully signed out']);
    }
}
