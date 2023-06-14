<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

final class Logout
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $user = auth()->user();
        $user->tokens()->delete();

        return $user;
    }
}
