<?php

namespace Tests;

use App\Models\User;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Contracts\Role;

trait ProvidesUser
{
    /** @var User $user */
    protected $user;

    public function getUser()
    {
        if (!$this->user) {
            $this->initUser();
        }

        return $this->user;
    }

    public function asProvidedUser()
    {
        if (!$this->user) {
            $this->initUser();
        }

        return $this->actingAs($this->user);
    }

    /**
     * @param Role[] $roles
     * @param Permission $permissions
     */
    public function initUser($attributes = [], $roles = [], $permissions = [])
    {
        /** @var User $user */
        $this->user = User::factory()->createOne($attributes);
    }
}
