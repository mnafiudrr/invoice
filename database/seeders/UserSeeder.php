<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('app.owner.email')],
            [
                'name' => config('app.owner.name'),
                'password' => Hash::make(config('app.owner.password')),
            ]
        );
    }
}
