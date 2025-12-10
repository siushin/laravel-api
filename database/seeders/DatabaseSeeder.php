<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 调用 Base 模块的 seeder
        $this->call(\Modules\Base\Database\Seeders\BaseDatabaseSeeder::class);
    }
}
