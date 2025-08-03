<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $generatePhoneNumber = function () {
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
            'nick_name' => fake()->name(),
            'gender' => '未知',
            'phone' => $generatePhoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('123456'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
