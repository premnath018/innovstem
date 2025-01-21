<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function saveResetToken(User $user, string $token, $expiry)
    {
        $user->reset_token = $token;
        $user->reset_token_expiry = $expiry;
        $user->save();
    }

    public function updatePassword(User $user, string $password)
    {
        $user->password = $password;
        $user->reset_token = null;
        $user->reset_token_expiry = null;
        $user->save();
    }
}
