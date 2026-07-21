<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = strtolower((string) config('auth.admin_email', 'mzuglnd@proton.me'));

        // Only one administrator is allowed.
        User::query()->where('email', '!=', $adminEmail)->delete();

        User::query()->updateOrCreate(
            ['email' => $adminEmail],
            [
                'name'     => 'Umzugland Admin',
                'role'     => 'administrator',
                'password' => Hash::make('Vyndonnermaster325'),
            ]
        );

        $this->call(UmzuglandContentSeeder::class);
    }
}
