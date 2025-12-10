<?php

namespace Modules\Base\Database\Seeders;

use Illuminate\Database\Seeder;

class BaseDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Modules\Base\Database\Seeders\DictionarySeeder::class,
            \Modules\Base\Database\Seeders\OrganizationSeeder::class,
            \Modules\Base\Database\Seeders\AccountSeeder::class,
        ]);
    }
}
