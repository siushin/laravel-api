<?php

namespace Modules\Base\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 数据填充：组织架构
 */
class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0'); // 关闭外键检查
        DB::table('sys_organization')->truncate(); // 清空表
        DB::statement('SET FOREIGN_KEY_CHECKS=1'); // 开启外键检查
        DB::statement('ALTER TABLE sys_organization AUTO_INCREMENT = 1'); // 重置自增ID

        $now = now();
        $data = [
            [
                'organization_name' => '中国',
                'organization_pid' => 0,
                'full_organization_pid' => ',1,',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'organization_name' => '广东省',
                'organization_pid' => 1,
                'full_organization_pid' => ',1,2,',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'organization_name' => '深圳市',
                'organization_pid' => 2,
                'full_organization_pid' => ',1,2,3,',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        DB::table('sys_organization')->insert($data);
    }
}
