<?php

namespace Modules\Base\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Exception;
use Modules\Base\Enums\AccountTypeEnum;
use Modules\Base\Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Siushin\Util\Traits\ParamTool;

/**
 * 模型：账号
 */
class Account extends Authenticatable
{
    /** @use HasFactory<AccountFactory> */
    use HasApiTokens, HasFactory, Notifiable, ParamTool, SoftDeletes;

    protected $table = 'bs_account';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password',
        'account_type',
        'status',
        'last_login_ip',
        'last_login_time',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'deleted_at',
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
            'last_login_time'   => 'datetime',
            'password'          => 'hashed',
            'account_type'      => AccountTypeEnum::class,
        ];
    }

    /**
     * 修改账号密码
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
     * 根据账号IDs获取用户名（键值对 - 列表）
     * @param array $user_ids
     * @return Collection
     * @author siushin<siushin@163.com>
     */
    public static function getUsernameByIDs(array $user_ids): Collection
    {
        return self::query()->whereIn('id', $user_ids)->pluck('username', 'id');
    }

    /**
     * 管理员信息
     * @return HasOne
     */
    public function adminInfo(): HasOne
    {
        return $this->hasOne(Admin::class, 'user_id');
    }

    /**
     * 客户信息
     * @return HasOne
     */
    public function customerInfo(): HasOne
    {
        return $this->hasOne(User::class, 'user_id');
    }

    /**
     * 账号资料信息
     * @return HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(AccountProfile::class, 'user_id');
    }

    /**
     * 社交网络信息
     * @return HasMany
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(AccountSocial::class, 'user_id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return AccountFactory::new();
    }
}
