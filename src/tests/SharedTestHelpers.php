<?php

namespace Tests;

use App\Auth\Role;
use App\Auth\User;

trait SharedTestHelpers
{
    protected $admin;
    protected $editor;

    /**
     * Set the current user context to be an admin.
     */
    public function asAdmin()
    {
        return $this->actingAs($this->getAdmin());
    }

    /**
     * Get the current admin user.
     */
    public function getAdmin(): User
    {
        if (is_null($this->admin)) {
            $adminRole = Role::getSystemRole('admin');
            $this->admin = $adminRole->users->first();
        }

        return $this->admin;
    }
}
