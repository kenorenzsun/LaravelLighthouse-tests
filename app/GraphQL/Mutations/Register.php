<?php

namespace App\GraphQL\Mutations;

use App\Models\User;

final class Register
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $user = User::create([
            'name' => $args['name'],
            'password' => bcrypt($args['password']),
            'email' => $args['email']
        ]);

        return $user;
    }
}
