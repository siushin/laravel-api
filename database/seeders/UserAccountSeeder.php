<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * 数据填充：用户账号
 */
class UserAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建管理员账号
        Admin::factory(1)->create();
        $admin = Admin::query()->first();
        $admin->update([
            'username' => env('APP_ADMIN', 'admin'),
            'mobile' => env('APP_ADMIN_MOBILE', ''),
            'password' => Hash::make(env('APP_ADMIN_PASSWORD', 'admin')),
            'real_name' => env('APP_ADMIN_NAME', '超级管理员'),
            'email' => env('APP_EMAIL', ''),
        ]);

        // 创建用户账号
        User::factory(100)->create();
        $user = User::query()->first();
        $user->update([
            'username' => 'user001',
            'password' => Hash::make('123456'),
            'real_name' => '用户001',
        ]);
    }
}
