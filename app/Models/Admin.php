<?php

namespace App\Models;

use Database\Factories\AdminFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Siushin\Util\Traits\ParamTool;

/**
 * 模型：管理员
 */
class Admin extends Authenticatable
{
    /** @use HasFactory<AdminFactory> */
    use HasApiTokens, HasFactory, Notifiable, ParamTool;

    protected $primaryKey = 'id';
    protected $table      = 'sys_admins';

    protected $fillable = [
        'username',
        'mobile',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * 修改管理员密码
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function updatePassword(array $params): array
    {
        self::checkEmptyParam($params, ['admin_id', 'password']);

        $info = self::query()->findOrFail($params['admin_id']);
        !$info && throw_exception('管理员账号不存在');

        $info->password = Hash::make($params['password']);
        $info->save();

        return [];
    }

    /**
     * 根据管理员IDs获取管理员用户名（键值对 - 列表）
     * @param array $admin_ids
     * @return Collection
     * @author siushin<siushin@163.com>
     */
    public static function getUsernameByAdminIDs(array $admin_ids): Collection
    {
        return self::query()->whereIn('id', $admin_ids)->pluck('username', 'id');
    }
}
