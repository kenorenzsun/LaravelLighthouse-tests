<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Error;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

final class Login
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {

        // Plain Laravel: Auth::guard()
        // Laravel Sanctum: Auth::guard(Arr::first(config('sanctum.guard')))
        $guard = Auth::guard(Arr::first(config('sanctum.guard')));

        if (!$guard->attempt($args)) {
            throw new Error('Invalid credentials.');
        }

        $user = $guard->user();

        return [
            "user" => $user,
            "accessToken" => $user->createToken("API_TOKEN")->plainTextToken
        ];
    }
}
