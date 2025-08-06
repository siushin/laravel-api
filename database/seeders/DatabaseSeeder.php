<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(1)->create();

        $user = User::query()->first();
        $user->username = env('APP_ADMIN', 'admin');
        $user->phone = env('APP_ADMIN_PHONE', '');
        $user->password = Hash::make(env('APP_ADMIN_PASSWORD', 'admin'));
        $user->nick_name = env('APP_ADMIN_NAME', '超级管理员');
        $user->email = env('APP_EMAIL', '');
        $user->save();

        $this->call([
            DictionarySeeder::class,
            OrganizationSeeder::class,
        ]);
    }
}
