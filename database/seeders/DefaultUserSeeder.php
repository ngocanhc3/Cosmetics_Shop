<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'user@cosme.local'],
            ['name' => 'Demo User', 'password' => 'user123', 'is_active' => true]
        );

        if (! $user->hasRole('user')) {
            $user->assignRole('user');
        }
    }
}
