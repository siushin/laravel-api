<?php

namespace Database\Factories;

use App\Enums\AccountTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

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
        return [
            'id'          => generateId(),
            'username'    => fake()->unique()->word(),
            'password'    => static::$password ??= Hash::make('123456'),
            'account_type' => fake()->randomElement(AccountTypeEnum::cases()),
        ];
    }
}
