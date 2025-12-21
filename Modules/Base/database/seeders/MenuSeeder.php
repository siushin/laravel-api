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
        DB::table('gpa_menu')->insert([
            'menu_id'      => $workbenchId,
            'account_type' => $accountType,
            'menu_name'    => '工作台',
            'menu_key'     => 'dashboard.workplace',
            'menu_path'    => '/workbench',
            'menu_icon'    => 'AppstoreOutlined',
            'menu_type'    => 'menu',
            'parent_id'    => 0,
            'component'    => './Dashboard/Workplace',
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

        $userManagementId = generateId();
        DB::table('gpa_menu')->insert([
            'menu_id'      => $userManagementId,
            'account_type' => $accountType,
            'menu_name'    => '用户管理',
            'menu_key'     => 'user.management',
            'menu_path'    => '/user',
            'menu_icon'    => 'TeamOutlined',
            'menu_type'    => 'menu',
            'parent_id'    => 0,
            'component'    => null,
            'redirect'     => '/user/account',
            'layout'       => true,
            'access'       => 'canAdmin',
            'wrappers'     => null,
            'sort'         => 2,
            'status'       => 1,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $notificationId = generateId();
        DB::table('gpa_menu')->insert([
            'menu_id'      => $notificationId,
            'account_type' => $accountType,
            'menu_name'    => '通知管理',
            'menu_key'     => 'notification.management',
            'menu_path'    => '/notification',
            'menu_icon'    => 'BellOutlined',
            'menu_type'    => 'menu',
            'parent_id'    => 0,
            'component'    => null,
            'redirect'     => '/notification/system',
            'layout'       => true,
            'access'       => 'canAdmin',
            'wrappers'     => null,
            'sort'         => 3,
            'status'       => 1,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $appManagementId = generateId();
        DB::table('gpa_menu')->insert([
            'menu_id'      => $appManagementId,
            'account_type' => $accountType,
            'menu_name'    => '应用管理',
            'menu_key'     => 'app.management',
            'menu_path'    => '/app',
            'menu_icon'    => 'AppstoreOutlined',
            'menu_type'    => 'menu',
            'parent_id'    => 0,
            'component'    => null,
            'redirect'     => '/app/market',
            'layout'       => true,
            'access'       => 'canAdmin',
            'wrappers'     => null,
            'sort'         => 4,
            'status'       => 1,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $organizationId = generateId();
        DB::table('gpa_menu')->insert([
            'menu_id'      => $organizationId,
            'account_type' => $accountType,
            'menu_name'    => '组织架构管理',
            'menu_key'     => 'organization.management',
            'menu_path'    => '/organization',
            'menu_icon'    => 'ApartmentOutlined',
            'menu_type'    => 'menu',
            'parent_id'    => 0,
            'component'    => null,
            'redirect'     => '/organization/company',
            'layout'       => true,
            'access'       => 'canAdmin',
            'wrappers'     => null,
            'sort'         => 5,
            'status'       => 1,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $systemId = generateId();
        DB::table('gpa_menu')->insert([
            'menu_id'      => $systemId,
            'account_type' => $accountType,
            'menu_name'    => '系统管理',
            'menu_key'     => 'system.management',
            'menu_path'    => '/system',
            'menu_icon'    => 'SettingOutlined',
            'menu_type'    => 'menu',
            'parent_id'    => 0,
            'component'    => null,
            'redirect'     => '/system/admin',
            'layout'       => true,
            'access'       => 'canAdmin',
            'wrappers'     => null,
            'sort'         => 6,
            'status'       => 1,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        // 插入应用管理的子菜单
        $appManagementChildren = [
            [
                'menu_name' => '应用市场',
                'menu_key'  => 'app.market',
                'menu_path' => '/app/market',
                'menu_icon' => 'ShopOutlined',
                'component' => './App/Market',
                'sort'      => 1,
            ],
            [
                'menu_name' => '我的应用',
                'menu_key'  => 'app.my',
                'menu_path' => '/app/my',
                'menu_icon' => 'AppstoreOutlined',
                'component' => './App/My',
                'sort'      => 2,
            ],
        ];

        foreach ($appManagementChildren as $child) {
            DB::table('gpa_menu')->insert([
                'menu_id'      => generateId(),
                'account_type' => $accountType,
                'menu_name'    => $child['menu_name'],
                'menu_key'     => $child['menu_key'],
                'menu_path'    => $child['menu_path'],
                'menu_icon'    => $child['menu_icon'],
                'menu_type'    => 'menu',
                'parent_id'    => $appManagementId,
                'component'    => $child['component'],
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

        // 插入通知管理的子菜单
        $notificationChildren = [
            [
                'menu_name' => '系统通知',
                'menu_key'  => 'notification.system',
                'menu_path' => '/notification/system',
                'menu_icon' => 'NotificationOutlined',
                'component' => './Notification/System',
                'sort'      => 1,
            ],
            [
                'menu_name' => '站内信',
                'menu_key'  => 'notification.message',
                'menu_path' => '/notification/message',
                'menu_icon' => 'MessageOutlined',
                'component' => './Notification/Message',
                'sort'      => 2,
            ],
            [
                'menu_name' => '公告管理',
                'menu_key'  => 'notification.announcement',
                'menu_path' => '/notification/announcement',
                'menu_icon' => 'SoundOutlined',
                'component' => './Notification/Announcement',
                'sort'      => 3,
            ],
        ];

        foreach ($notificationChildren as $child) {
            DB::table('gpa_menu')->insert([
                'menu_id'      => generateId(),
                'account_type' => $accountType,
                'menu_name'    => $child['menu_name'],
                'menu_key'     => $child['menu_key'],
                'menu_path'    => $child['menu_path'],
                'menu_icon'    => $child['menu_icon'],
                'menu_type'    => 'menu',
                'parent_id'    => $notificationId,
                'component'    => $child['component'],
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

        // 插入组织架构管理的子菜单
        $organizationChildren = [
            [
                'menu_name' => '公司管理',
                'menu_key'  => 'organization.company',
                'menu_path' => '/organization/company',
                'menu_icon' => 'BankOutlined',
                'component' => './Organization/Company',
                'sort'      => 1,
            ],
            [
                'menu_name' => '部门管理',
                'menu_key'  => 'organization.department',
                'menu_path' => '/organization/department',
                'menu_icon' => 'PartitionOutlined',
                'component' => './Organization/Department',
                'sort'      => 2,
            ],
            [
                'menu_name' => '职位管理',
                'menu_key'  => 'organization.position',
                'menu_path' => '/organization/position',
                'menu_icon' => 'SolutionOutlined',
                'component' => './Organization/Position',
                'sort'      => 3,
            ],
            [
                'menu_name' => '岗位管理',
                'menu_key'  => 'organization.job',
                'menu_path' => '/organization/job',
                'menu_icon' => 'IdcardOutlined',
                'component' => './Organization/Job',
                'sort'      => 4,
            ],
        ];

        foreach ($organizationChildren as $child) {
            DB::table('gpa_menu')->insert([
                'menu_id'      => generateId(),
                'account_type' => $accountType,
                'menu_name'    => $child['menu_name'],
                'menu_key'     => $child['menu_key'],
                'menu_path'    => $child['menu_path'],
                'menu_icon'    => $child['menu_icon'],
                'menu_type'    => 'menu',
                'parent_id'    => $organizationId,
                'component'    => $child['component'],
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

        // 插入角色权限父菜单（作为系统管理的子菜单）
        $rbacId = generateId();
        DB::table('gpa_menu')->insert([
            'menu_id'      => $rbacId,
            'account_type' => $accountType,
            'menu_name'    => '角色权限',
            'menu_key'     => 'system.rbac',
            'menu_path'    => '/system/rbac',
            'menu_icon'    => 'SafetyOutlined',
            'menu_type'    => 'menu',
            'parent_id'    => $systemId,
            'component'    => null,
            'redirect'     => '/system/rbac/role',
            'layout'       => true,
            'access'       => 'canAdmin',
            'wrappers'     => null,
            'sort'         => 1,
            'status'       => 1,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        // 插入角色权限的子菜单
        $rbacChildren = [
            [
                'menu_name' => '角色管理',
                'menu_key'  => 'system.rbac.role',
                'menu_path' => '/system/rbac/role',
                'menu_icon' => 'ProfileOutlined',
                'component' => './System/Role',
                'sort'      => 1,
            ],
            [
                'menu_name' => '菜单管理',
                'menu_key'  => 'system.rbac.menu',
                'menu_path' => '/system/rbac/menu',
                'menu_icon' => 'BlockOutlined',
                'component' => './System/Menu',
                'sort'      => 2,
            ],
            [
                'menu_name' => '用户角色关联',
                'menu_key'  => 'system.rbac.userRole',
                'menu_path' => '/system/rbac/user-role',
                'menu_icon' => 'UsergroupAddOutlined',
                'component' => './System/UserRole',
                'sort'      => 3,
            ],
            [
                'menu_name' => '角色菜单关联',
                'menu_key'  => 'system.rbac.roleMenu',
                'menu_path' => '/system/rbac/role-menu',
                'menu_icon' => 'UnorderedListOutlined',
                'component' => './System/RoleMenu',
                'sort'      => 4,
            ],
        ];

        foreach ($rbacChildren as $child) {
            DB::table('gpa_menu')->insert([
                'menu_id'      => generateId(),
                'account_type' => $accountType,
                'menu_name'    => $child['menu_name'],
                'menu_key'     => $child['menu_key'],
                'menu_path'    => $child['menu_path'],
                'menu_icon'    => $child['menu_icon'],
                'menu_type'    => 'menu',
                'parent_id'    => $rbacId,
                'component'    => $child['component'],
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

        // 插入系统管理的其他子菜单
        $systemChildren = [
            [
                'menu_name' => '管理员管理',
                'menu_key'  => 'system.admin',
                'menu_path' => '/system/admin',
                'menu_icon' => 'UserOutlined',
                'component' => './System/Admin',
                'sort'      => 2,
            ],
            [
                'menu_name' => '组织架构',
                'menu_key'  => 'system.organization',
                'menu_path' => '/system/organization',
                'menu_icon' => 'ApartmentOutlined',
                'component' => './System/Organization',
                'sort'      => 3,
            ],
            [
                'menu_name' => '数据字典',
                'menu_key'  => 'system.dict',
                'menu_path' => '/system/dict',
                'menu_icon' => 'BookOutlined',
                'component' => './System/Dict',
                'sort'      => 4,
            ],
            [
                'menu_name' => '系统日志',
                'menu_key'  => 'system.log',
                'menu_path' => '/system/log',
                'menu_icon' => 'FileTextOutlined',
                'component' => './System/Log',
                'sort'      => 5,
            ],
        ];

        foreach ($systemChildren as $child) {
            DB::table('gpa_menu')->insert([
                'menu_id'      => generateId(),
                'account_type' => $accountType,
                'menu_name'    => $child['menu_name'],
                'menu_key'     => $child['menu_key'],
                'menu_path'    => $child['menu_path'],
                'menu_icon'    => $child['menu_icon'],
                'menu_type'    => 'menu',
                'parent_id'    => $systemId,
                'component'    => $child['component'],
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

        // 插入用户管理的子菜单
        $userManagementChildren = [
            [
                'menu_name' => '账号管理',
                'menu_key'  => 'user.account',
                'menu_path' => '/user/account',
                'menu_icon' => 'IdcardOutlined',
                'component' => './User/Account',
                'sort'      => 1,
            ],
            [
                'menu_name' => '用户管理',
                'menu_key'  => 'user.user',
                'menu_path' => '/user/user',
                'menu_icon' => 'UserOutlined',
                'component' => './User/User',
                'sort'      => 2,
            ],
            [
                'menu_name' => '账号资料',
                'menu_key'  => 'user.profile',
                'menu_path' => '/user/profile',
                'menu_icon' => 'ProfileOutlined',
                'component' => './User/Profile',
                'sort'      => 3,
            ],
            [
                'menu_name' => '社交绑定',
                'menu_key'  => 'user.social',
                'menu_path' => '/user/social',
                'menu_icon' => 'ShareAltOutlined',
                'component' => './User/Social',
                'sort'      => 4,
            ],
        ];

        foreach ($userManagementChildren as $child) {
            DB::table('gpa_menu')->insert([
                'menu_id'      => generateId(),
                'account_type' => $accountType,
                'menu_name'    => $child['menu_name'],
                'menu_key'     => $child['menu_key'],
                'menu_path'    => $child['menu_path'],
                'menu_icon'    => $child['menu_icon'],
                'menu_type'    => 'menu',
                'parent_id'    => $userManagementId,
                'component'    => $child['component'],
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
        $baseModule = DB::table('gpa_module')
            ->where('module_identifier', 'base')
            ->orWhere('module_name', 'Base')
            ->first();

        if (!$baseModule) {
            // 如果Base模块不存在，创建它
            $baseModuleId = generateId();
            DB::table('gpa_module')->insert([
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
        $menuIds = DB::table('gpa_menu')
            ->where('account_type', AccountTypeEnum::Admin->value)
            ->pluck('menu_id')
            ->toArray();

        // 关联菜单到Base模块
        $now = now();
        $moduleMenuData = [];
        foreach ($menuIds as $menuId) {
            // 检查是否已存在关联
            $exists = DB::table('gpa_module_menu')
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
            DB::table('gpa_module_menu')->insert($moduleMenuData);
        }
    }
}
