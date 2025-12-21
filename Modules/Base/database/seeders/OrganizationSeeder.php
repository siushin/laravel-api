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

        // 清空表
        DB::table('sys_post')->truncate();
        DB::table('sys_position')->truncate();
        DB::table('sys_department')->truncate();
        DB::table('sys_company')->truncate();
        DB::table('sys_organization')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1'); // 开启外键检查

        // 重置自增ID
        DB::statement('ALTER TABLE sys_organization AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE sys_company AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE sys_department AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE sys_position AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE sys_post AUTO_INCREMENT = 1');

        $now = now();

        // 组织架构数据
        $organizationData = [
            [
                'organization_name'     => '中国',
                'organization_pid'      => 0,
                'full_organization_pid' => ',1,',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'organization_name'     => '广东省',
                'organization_pid'      => 1,
                'full_organization_pid' => ',1,2,',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'organization_name'     => '深圳市',
                'organization_pid'      => 2,
                'full_organization_pid' => ',1,2,3,',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'organization_name'     => '广州市',
                'organization_pid'      => 2,
                'full_organization_pid' => ',1,2,4,',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'organization_name'     => '北京市',
                'organization_pid'      => 1,
                'full_organization_pid' => ',1,5,',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
        ];
        DB::table('sys_organization')->insert($organizationData);

        // 公司数据（关联组织架构）
        $companyData = [
            [
                'company_name'    => '深圳科技有限公司',
                'company_code'    => 'SZ-TECH-001',
                'organization_id' => 3, // 关联深圳市
                'legal_person'    => '张三',
                'contact_phone'   => '0755-12345678',
                'contact_email'   => 'contact@sztech.com',
                'address'         => '深圳市南山区科技园',
                'description'     => '专注于软件开发和技术服务的高科技公司',
                'status'          => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'company_name'    => '广州贸易有限公司',
                'company_code'    => 'GZ-TRADE-001',
                'organization_id' => 4, // 关联广州市
                'legal_person'    => '李四',
                'contact_phone'   => '020-87654321',
                'contact_email'   => 'contact@gztrade.com',
                'address'         => '广州市天河区CBD',
                'description'     => '专业从事国际贸易和物流服务',
                'status'          => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'company_name'    => '北京金融投资有限公司',
                'company_code'    => 'BJ-FINANCE-001',
                'organization_id' => 5, // 关联北京市
                'legal_person'    => '王五',
                'contact_phone'   => '010-11223344',
                'contact_email'   => 'contact@bjfinance.com',
                'address'         => '北京市朝阳区金融街',
                'description'     => '专业的金融投资和资产管理公司',
                'status'          => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ];
        DB::table('sys_company')->insert($companyData);

        // 部门数据（关联公司）
        $departmentData = [
            // 深圳科技有限公司的部门
            [
                'department_name' => '技术研发部',
                'department_code' => 'TECH-RD-001',
                'company_id'      => 1,
                'parent_id'       => 0,
                'full_parent_id'  => ',1,',
                'manager_id'      => null,
                'description'     => '负责产品研发和技术创新',
                'status'          => 1,
                'sort_order'      => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'department_name' => '前端开发组',
                'department_code' => 'TECH-FRONT-001',
                'company_id'      => 1,
                'parent_id'       => 1,
                'full_parent_id'  => ',1,2,',
                'manager_id'      => null,
                'description'     => '负责前端界面开发和用户体验优化',
                'status'          => 1,
                'sort_order'      => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'department_name' => '后端开发组',
                'department_code' => 'TECH-BACK-001',
                'company_id'      => 1,
                'parent_id'       => 1,
                'full_parent_id'  => ',1,3,',
                'manager_id'      => null,
                'description'     => '负责后端服务开发和系统架构设计',
                'status'          => 1,
                'sort_order'      => 2,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'department_name' => '市场销售部',
                'department_code' => 'TECH-SALES-001',
                'company_id'      => 1,
                'parent_id'       => 0,
                'full_parent_id'  => ',4,',
                'manager_id'      => null,
                'description'     => '负责市场推广和产品销售',
                'status'          => 1,
                'sort_order'      => 2,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'department_name' => '人力资源部',
                'department_code' => 'TECH-HR-001',
                'company_id'      => 1,
                'parent_id'       => 0,
                'full_parent_id'  => ',5,',
                'manager_id'      => null,
                'description'     => '负责人力资源管理和员工发展',
                'status'          => 1,
                'sort_order'      => 3,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            // 广州贸易有限公司的部门
            [
                'department_name' => '国际贸易部',
                'department_code' => 'GZ-INTL-001',
                'company_id'      => 2,
                'parent_id'       => 0,
                'full_parent_id'  => ',6,',
                'manager_id'      => null,
                'description'     => '负责国际贸易业务拓展',
                'status'          => 1,
                'sort_order'      => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'department_name' => '物流管理部',
                'department_code' => 'GZ-LOGISTICS-001',
                'company_id'      => 2,
                'parent_id'       => 0,
                'full_parent_id'  => ',7,',
                'manager_id'      => null,
                'description'     => '负责物流运输和仓储管理',
                'status'          => 1,
                'sort_order'      => 2,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ];
        DB::table('sys_department')->insert($departmentData);

        // 职位数据（关联部门）
        $positionData = [
            // 技术研发部相关职位
            [
                'position_name'    => '高级前端工程师',
                'position_code'    => 'POS-FRONT-SENIOR',
                'department_id'    => 2, // 前端开发组
                'job_description'  => '负责复杂前端项目的架构设计和开发工作',
                'job_requirements' => '5年以上前端开发经验，精通React/Vue等框架',
                'status'           => 1,
                'sort_order'       => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'position_name'    => '中级前端工程师',
                'position_code'    => 'POS-FRONT-MID',
                'department_id'    => 2,
                'job_description'  => '负责前端功能模块的开发与维护',
                'job_requirements' => '3年以上前端开发经验，熟悉主流前端框架',
                'status'           => 1,
                'sort_order'       => 2,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'position_name'    => '高级后端工程师',
                'position_code'    => 'POS-BACK-SENIOR',
                'department_id'    => 3, // 后端开发组
                'job_description'  => '负责后端系统架构设计和核心功能开发',
                'job_requirements' => '5年以上后端开发经验，精通Laravel/PHP等技术栈',
                'status'           => 1,
                'sort_order'       => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'position_name'    => '中级后端工程师',
                'position_code'    => 'POS-BACK-MID',
                'department_id'    => 3,
                'job_description'  => '负责后端业务逻辑开发和API接口设计',
                'job_requirements' => '3年以上后端开发经验，熟悉Laravel框架',
                'status'           => 1,
                'sort_order'       => 2,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'position_name'    => '技术总监',
                'position_code'    => 'POS-TECH-CTO',
                'department_id'    => 1, // 技术研发部
                'job_description'  => '负责技术团队管理和技术战略规划',
                'job_requirements' => '10年以上技术管理经验，具备丰富的团队管理能力',
                'status'           => 1,
                'sort_order'       => 0,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            // 市场销售部相关职位
            [
                'position_name'    => '销售经理',
                'position_code'    => 'POS-SALES-MANAGER',
                'department_id'    => 4, // 市场销售部
                'job_description'  => '负责销售团队管理和业务拓展',
                'job_requirements' => '5年以上销售管理经验，具备良好的沟通能力',
                'status'           => 1,
                'sort_order'       => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'position_name'    => '销售专员',
                'position_code'    => 'POS-SALES-SPECIALIST',
                'department_id'    => 4,
                'job_description'  => '负责客户开发和产品销售',
                'job_requirements' => '2年以上销售经验，具备良好的客户服务意识',
                'status'           => 1,
                'sort_order'       => 2,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            // 人力资源部相关职位
            [
                'position_name'    => 'HR经理',
                'position_code'    => 'POS-HR-MANAGER',
                'department_id'    => 5, // 人力资源部
                'job_description'  => '负责人力资源规划、招聘和员工关系管理',
                'job_requirements' => '5年以上HR管理经验，熟悉人力资源各模块',
                'status'           => 1,
                'sort_order'       => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            // 国际贸易部相关职位
            [
                'position_name'    => '国际贸易经理',
                'position_code'    => 'POS-INTL-MANAGER',
                'department_id'    => 6, // 国际贸易部
                'job_description'  => '负责国际贸易业务拓展和客户关系维护',
                'job_requirements' => '5年以上国际贸易经验，英语流利',
                'status'           => 1,
                'sort_order'       => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
        ];
        DB::table('sys_position')->insert($positionData);

        // 岗位数据（关联职位和部门）
        $postData = [
            [
                'post_name'         => 'React前端开发',
                'post_code'         => 'POST-REACT-DEV',
                'position_id'       => 1, // 高级前端工程师
                'department_id'     => 2, // 前端开发组
                'post_description'  => '负责React项目的开发和维护，参与技术方案设计',
                'post_requirements' => '熟练掌握React、TypeScript、Redux等技术',
                'status'            => 1,
                'sort_order'        => 1,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'post_name'         => 'Vue前端开发',
                'post_code'         => 'POST-VUE-DEV',
                'position_id'       => 2, // 中级前端工程师
                'department_id'     => 2,
                'post_description'  => '负责Vue项目的功能开发和bug修复',
                'post_requirements' => '熟悉Vue3、Element Plus等前端技术',
                'status'            => 1,
                'sort_order'        => 2,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'post_name'         => 'Laravel后端开发',
                'post_code'         => 'POST-LARAVEL-DEV',
                'position_id'       => 3, // 高级后端工程师
                'department_id'     => 3, // 后端开发组
                'post_description'  => '负责Laravel后端系统的架构设计和核心功能开发',
                'post_requirements' => '精通Laravel框架，熟悉MySQL、Redis等',
                'status'            => 1,
                'sort_order'        => 1,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'post_name'         => 'API接口开发',
                'post_code'         => 'POST-API-DEV',
                'position_id'       => 4, // 中级后端工程师
                'department_id'     => 3,
                'post_description'  => '负责RESTful API接口的设计和开发',
                'post_requirements' => '熟悉RESTful API设计规范，了解微服务架构',
                'status'            => 1,
                'sort_order'        => 2,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'post_name'         => '技术团队管理',
                'post_code'         => 'POST-TECH-LEAD',
                'position_id'       => 5, // 技术总监
                'department_id'     => 1, // 技术研发部
                'post_description'  => '负责技术团队的管理和技术决策',
                'post_requirements' => '具备丰富的技术管理经验和团队领导能力',
                'status'            => 1,
                'sort_order'        => 1,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'post_name'         => '企业客户销售',
                'post_code'         => 'POST-B2B-SALES',
                'position_id'       => 6, // 销售经理
                'department_id'     => 4, // 市场销售部
                'post_description'  => '负责企业级客户的开发和维护',
                'post_requirements' => '具备B2B销售经验，良好的客户沟通能力',
                'status'            => 1,
                'sort_order'        => 1,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'post_name'         => '在线销售专员',
                'post_code'         => 'POST-ONLINE-SALES',
                'position_id'       => 7, // 销售专员
                'department_id'     => 4,
                'post_description'  => '负责在线销售渠道的开发和维护',
                'post_requirements' => '熟悉电商平台运营，具备网络销售经验',
                'status'            => 1,
                'sort_order'        => 2,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'post_name'         => '招聘专员',
                'post_code'         => 'POST-RECRUITMENT',
                'position_id'       => 8, // HR经理
                'department_id'     => 5, // 人力资源部
                'post_description'  => '负责人才招聘和面试组织工作',
                'post_requirements' => '熟悉招聘流程，具备良好的沟通和判断能力',
                'status'            => 1,
                'sort_order'        => 1,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'post_name'         => '欧美市场拓展',
                'post_code'         => 'POST-EU-US-MARKET',
                'position_id'       => 9, // 国际贸易经理
                'department_id'     => 6, // 国际贸易部
                'post_description'  => '负责欧美市场的业务拓展和客户开发',
                'post_requirements' => '英语流利，熟悉欧美市场，具备国际贸易经验',
                'status'            => 1,
                'sort_order'        => 1,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];
        DB::table('sys_post')->insert($postData);
    }
}
