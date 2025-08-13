<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $generateMobileNumber = function () {
            $firstChar = ['13', '14', '15', '16', '17', '18', '19']; // 常见的手机号码前缀
            $firstChar = $firstChar[array_rand($firstChar)]; // 随机选择一个前缀

            // 生成后10位数字
            $randomNumber = mt_rand(1000000000, 9999999999); // 使用mt_rand生成一个更大的随机数范围

            // 截取后9位数字
            $randomNumber = str_pad(substr($randomNumber, -9), 9, '0', STR_PAD_LEFT); // 确保总是9位数字，如果不足则左侧补0

            // 组合前缀和后9位数字
            return $firstChar . $randomNumber;
        };

        return [
            'username' => fake()->unique()->word(),
            'real_name' => fake()->name(),
            'mobile' => $generateMobileNumber(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('123456'),
        ];
    }
}
