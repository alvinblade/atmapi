<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait AuthTrait
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    protected function getUser(string $username, string $password): bool|User
    {
        if ($username && $password) {
            $user = $this->user->whereUsername($username)->first();
            if ($user) {
                return Hash::check($password, $user->password) ? $user : false;
            }
        }
        return false;
    }

    protected function checkUser(string $username, string $password): User|bool
    {
        if ($user = $this->getUser($username, $password)) {
            return $user;
        }

        return false;
    }
}
