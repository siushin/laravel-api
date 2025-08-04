<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Siushin\Util\Traits\ParamTool;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, ParamTool;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * 修改用户密码
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function updatePassword(array $params): array
    {
        self::checkEmptyParam($params, ['user_id', 'password']);

        $info = self::query()->findOrFail($params['user_id']);
        !$info && throw_exception('账号不存在');

        $info->password = Hash::make($params['password']);
        $info->save();

        return [];
    }

    /**
     * 根据用户IDs获取用户名（键值对 - 列表）
     * @param array $user_ids
     * @return Collection
     * @author siushin<siushin@163.com>
     */
    public static function getUsernameByIDs(array $user_ids): Collection
    {
        return self::query()->whereIn('id', $user_ids)->pluck('username', 'id');
    }
}
