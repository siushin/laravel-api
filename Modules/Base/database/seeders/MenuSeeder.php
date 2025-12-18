<?php

namespace Modules\Base\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Base\Enums\AccountTypeEnum;

/**
 * 数据填充：菜单
 */
class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $accountType = AccountTypeEnum::Admin->value;

        // 先插入父菜单
        $workbenchId = generateId();
        DB::table('sys_menu')->insert([
            'menu_id'      => $workbenchId,
            'account_type' => $accountType,
            'menu_name'    => '工作台',
            'name'         => 'dashboard.workplace',
            'menu_path'    => '/workbench',
            'component'    => './Dashboard/Workplace',
            'menu_icon'    => 'AppstoreOutlined',
            'menu_type'    => 'menu',
            'parent_id'    => 0,
            'redirect'     => null,
            'layout'       => true,
            'access'       => 'canAdmin',
            'wrappers'     => null,
            'is_required'  => 1,
            'sort'         => 1,
            'status'       => 1,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $notificationId = generateId();
        DB::table('sys_menu')->insert([
            'menu_id'      => $notificationId,
            'account_type' => $accountType,
            'menu_name'    => '通知管理',
            'name'         => 'notification.management',
            'menu_path'    => '/notification',
            'component'    => null,
            'menu_icon'    => 'BellOutlined',
            'menu_type'    => 'menu',
            'parent_id'    => 0,
            'redirect'     => '/notification/system',
            'layout'       => true,
            'access'       => 'canAdmin',
            'wrappers'     => null,
            'sort'         => 2,
            'status'       => 1,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $systemId = generateId();
        DB::table('sys_menu')->insert([
            'menu_id'      => $systemId,
            'account_type' => $accountType,
            'menu_name'    => '系统管理',
            'name'         => 'system.management',
            'menu_path'    => '/system',
            'component'    => null,
            'menu_icon'    => 'SettingOutlined',
            'menu_type'    => 'menu',
            'parent_id'    => 0,
            'redirect'     => '/system/user',
            'layout'       => true,
            'access'       => 'canAdmin',
            'wrappers'     => null,
            'sort'         => 3,
            'status'       => 1,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        // 插入通知管理的子菜单
        $notificationChildren = [
            [
                'menu_name' => '系统通知',
                'name'      => 'notification.system',
                'menu_path' => '/notification/system',
                'component' => './Notification/System',
                'menu_icon' => 'NotificationOutlined',
                'sort'      => 1,
            ],
            [
                'menu_name' => '站内信',
                'name'      => 'notification.message',
                'menu_path' => '/notification/message',
                'component' => './Notification/Message',
                'menu_icon' => 'MessageOutlined',
                'sort'      => 2,
            ],
            [
                'menu_name' => '公告管理',
                'name'      => 'notification.announcement',
                'menu_path' => '/notification/announcement',
                'component' => './Notification/Announcement',
                'menu_icon' => 'SoundOutlined',
                'sort'      => 3,
            ],
        ];

        foreach ($notificationChildren as $child) {
            DB::table('sys_menu')->insert([
                'menu_id'      => generateId(),
                'account_type' => $accountType,
                'menu_name'    => $child['menu_name'],
                'name'         => $child['name'],
                'menu_path'    => $child['menu_path'],
                'component'    => $child['component'],
                'menu_icon'    => $child['menu_icon'],
                'menu_type'    => 'menu',
                'parent_id'    => $notificationId,
                'redirect'     => null,
                'layout'       => true,
                'access'       => 'canAdmin',
                'wrappers'     => null,
                'sort'         => $child['sort'],
                'status'       => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        // 插入系统管理的子菜单
        $systemChildren = [
            [
                'menu_name' => '用户管理',
                'name'      => 'system.user',
                'menu_path' => '/system/user',
                'component' => './System/User',
                'menu_icon' => 'UserOutlined',
                'sort'      => 1,
            ],
            [
                'menu_name' => '角色管理',
                'name'      => 'system.role',
                'menu_path' => '/system/role',
                'component' => './System/Role',
                'menu_icon' => 'ProfileOutlined',
                'sort'      => 2,
            ],
            [
                'menu_name' => '菜单管理',
                'name'      => 'system.menu',
                'menu_path' => '/system/menu',
                'component' => './System/Menu',
                'menu_icon' => 'BlockOutlined',
                'sort'      => 3,
            ],
            [
                'menu_name' => '组织架构',
                'name'      => 'system.organization',
                'menu_path' => '/system/organization',
                'component' => './System/Organization',
                'menu_icon' => 'ApartmentOutlined',
                'sort'      => 4,
            ],
            [
                'menu_name' => '数据字典',
                'name'      => 'system.dict',
                'menu_path' => '/system/dict',
                'component' => './System/Dict',
                'menu_icon' => 'BookOutlined',
                'sort'      => 5,
            ],
            [
                'menu_name' => '系统日志',
                'name'      => 'system.log',
                'menu_path' => '/system/log',
                'component' => './System/Log',
                'menu_icon' => 'FileTextOutlined',
                'sort'      => 6,
            ],
        ];

        foreach ($systemChildren as $child) {
            DB::table('sys_menu')->insert([
                'menu_id'      => generateId(),
                'account_type' => $accountType,
                'menu_name'    => $child['menu_name'],
                'name'         => $child['name'],
                'menu_path'    => $child['menu_path'],
                'component'    => $child['component'],
                'menu_icon'    => $child['menu_icon'],
                'menu_type'    => 'menu',
                'parent_id'    => $systemId,
                'redirect'     => null,
                'layout'       => true,
                'access'       => 'canAdmin',
                'wrappers'     => null,
                'sort'         => $child['sort'],
                'status'       => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        // 关联菜单到Base模块
        $this->associateMenusToBaseModule();
    }

    /**
     * 关联菜单到Base模块
     */
    private function associateMenusToBaseModule(): void
    {
        // 查找Base模块
        $baseModule = DB::table('sys_module')
            ->where('module_identifier', 'base')
            ->orWhere('module_name', 'Base')
            ->first();

        if (!$baseModule) {
            // 如果Base模块不存在，创建它
            $baseModuleId = generateId();
            DB::table('sys_module')->insert([
                'module_id'          => $baseModuleId,
                'module_identifier'  => 'base',
                'module_name'        => 'Base',
                'module_alias'       => '基础服务',
                'module_description' => 'LaravelAPI 基础服务，勿删除！',
                'status'             => 1,
                'priority'           => 0,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        } else {
            $baseModuleId = $baseModule->module_id;
        }

        // 获取所有Base模块的Admin菜单
        $menuIds = DB::table('sys_menu')
            ->where('account_type', AccountTypeEnum::Admin->value)
            ->pluck('menu_id')
            ->toArray();

        // 关联菜单到Base模块
        $now = now();
        $moduleMenuData = [];
        foreach ($menuIds as $menuId) {
            // 检查是否已存在关联
            $exists = DB::table('sys_module_menu')
                ->where('module_id', $baseModuleId)
                ->where('menu_id', $menuId)
                ->exists();

            if (!$exists) {
                $moduleMenuData[] = [
                    'id'         => generateId(),
                    'module_id'  => $baseModuleId,
                    'menu_id'    => $menuId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($moduleMenuData)) {
            DB::table('sys_module_menu')->insert($moduleMenuData);
        }
    }
}
