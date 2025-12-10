<?php

namespace Database\Factories;

use App\Enums\AccountTypeEnum;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
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
