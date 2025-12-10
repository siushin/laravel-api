<?php

namespace Database\Seeders;

use App\Enums\AccountTypeEnum;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserSocial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Siushin\LaravelTool\Enums\GenderTypeEnum;
use Siushin\LaravelTool\Enums\SocialTypeEnum;

/**
 * 数据填充：用户账号
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 生成手机号的辅助函数
        $generateMobileNumber = function () {
            $firstChar = ['13', '14', '15', '16', '17', '18', '19'];
            $firstChar = $firstChar[array_rand($firstChar)];
            $randomNumber = mt_rand(1000000000, 9999999999);
            $randomNumber = str_pad(substr($randomNumber, -9), 9, '0', STR_PAD_LEFT);
            return $firstChar . $randomNumber;
        };

        // 生成随机姓名（中文名或英文名）
        $generateRandomName = function () {
            if (fake()->boolean()) {
                // 生成英文名
                return fake()->name();
            } else {
                // 生成中文名
                $surnames = ['王', '李', '张', '刘', '陈', '杨', '赵', '黄', '周', '吴', '徐', '孙', '胡', '朱', '高', '林', '何', '郭', '马', '罗', '梁', '宋', '郑', '谢', '韩', '唐', '冯', '于', '董', '萧'];
                $givenNames = ['伟', '芳', '娜', '秀英', '敏', '静', '丽', '强', '磊', '军', '洋', '勇', '艳', '杰', '娟', '涛', '明', '超', '秀兰', '霞', '平', '刚', '桂英', '建华', '文', '华', '建国', '红', '志强', '秀', '敏', '静', '丽', '强', '磊', '军', '洋', '勇', '艳', '杰', '娟', '涛', '明', '超', '秀兰', '霞', '平', '刚', '桂英'];
                return $surnames[array_rand($surnames)] . $givenNames[array_rand($givenNames)];
            }
        };

        // 创建用户账号
        $users = User::factory(100)->create();

        // 为每个用户创建关联数据
        foreach ($users as $user) {
            // 创建用户资料
            UserProfile::query()->create([
                'id' => generateId(),
                'user_id' => $user->id,
                'real_name' => $generateRandomName(),
                'gender' => fake()->randomElement(array_column(GenderTypeEnum::cases(), 'name')),
                'avatar' => null,
            ]);

            // 创建社交网络数据（手机号）
            UserSocial::query()->create([
                'id' => generateId(),
                'user_id' => $user->id,
                'social_type' => SocialTypeEnum::Mobile->value,
                'social_account' => $generateMobileNumber(),
                'social_name' => null,
                'avatar' => null,
                'is_verified' => fake()->boolean(30), // 30% 概率已验证
                'verified_at' => fake()->boolean(30) ? now() : null,
            ]);

            // 随机决定是否创建邮箱数据
            if (fake()->boolean()) {
                UserSocial::query()->create([
                    'id' => generateId(),
                    'user_id' => $user->id,
                    'social_type' => SocialTypeEnum::Email->value,
                    'social_account' => fake()->unique()->safeEmail(),
                    'social_name' => null,
                    'avatar' => null,
                    'is_verified' => fake()->boolean(50), // 50% 概率已验证
                    'verified_at' => fake()->boolean(50) ? now() : null,
                ]);
            }

            // 根据账号类型创建对应的附属信息
            if ($user->account_type === AccountTypeEnum::Admin) {
                Admin::query()->create([
                    'id' => generateId(),
                    'user_id' => $user->id,
                    'company_id' => null,
                    'department_id' => null,
                ]);
            } elseif ($user->account_type === AccountTypeEnum::Customer) {
                Customer::query()->create([
                    'id' => generateId(),
                    'user_id' => $user->id,
                ]);
            }
        }

        // 初始化超级管理员账号（按创建时间取第一条记录，不管是什么用户类型）
        $adminUser = User::query()
            ->orderBy('created_at')
            ->first();

        if ($adminUser) {
            $wasCustomer = $adminUser->account_type === AccountTypeEnum::Customer;

            // 如果原本是客户类型，删除对应的客户表脏数据
            if ($wasCustomer) {
                Customer::query()->where('user_id', $adminUser->id)->delete();
            }

            // 更新为超级管理员账号
            $adminUser->update([
                'username'     => env('APP_ADMIN', 'admin'),
                'password'     => Hash::make(env('APP_ADMIN_PASSWORD', 'admin')),
                'status'       => 1,
                'account_type' => AccountTypeEnum::Admin,
            ]);

            // 更新管理员资料
            UserProfile::query()->updateOrCreate(
                ['user_id' => $adminUser->id],
                [
                    'real_name' => env('APP_ADMIN_NAME', '超级管理员'),
                    'gender' => GenderTypeEnum::male->name,
                ]
            );

            // 如果原本是客户类型，需要创建管理员信息（原本是管理员类型的话，前面循环中已创建）
            if ($wasCustomer) {
                Admin::query()->create([
                    'id' => generateId(),
                    'user_id' => $adminUser->id,
                    'company_id' => null,
                    'department_id' => null,
                ]);
            }
        }
    }
}
