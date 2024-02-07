<?php

namespace App\Http\Controllers;

use App\Traits\HasApiRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, HasApiRequest;

    public static function routeParam(string $key): object|string|null
    {
        return request()->route($key);
    }
}
