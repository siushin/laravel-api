<?php

namespace Modules\Base\Models;

use Exception;
use Modules\Base\Enums\AccountTypeEnum;
use Modules\Base\Enums\OperationActionEnum;
use Modules\Base\Enums\ResourceTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Siushin\Util\Traits\ParamTool;

/**
 * 模型：管理员
 */
class Admin extends Model
{
    use ParamTool;

    protected $table = 'bs_admin';

    protected $fillable = [
        'id',
        'account_id',
        'company_id',
        'department_id',
        'is_super',
    ];

    /**
     * 关联账号
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * 获取管理员列表（分页）
     * @param array $params
     * @return array
     * @author siushin<siushin@163.com>
     */
    public static function getPageData(array $params): array
    {
        $page = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? 10;

        $query = Account::query()
            ->where('account_type', AccountTypeEnum::Admin->value)
            ->with('adminInfo')
            ->when(!empty($params['username']), function ($q) use ($params) {
                $q->where('username', 'like', "%{$params['username']}%");
            })
            ->when(isset($params['account_type']), function ($q) use ($params) {
                $q->where('account_type', $params['account_type']);
            })
            ->when(isset($params['status']), function ($q) use ($params) {
                $q->where('status', $params['status']);
            })
            ->when(!empty($params['keyword']), function ($q) use ($params) {
                $q->where(function ($query) use ($params) {
                    $query->where('username', 'like', "%{$params['keyword']}%")
                        ->orWhere('last_login_ip', 'like', "%{$params['keyword']}%");
                });
            })
            ->when(!empty($params['last_login_time']), function ($q) use ($params) {
                if (is_array($params['last_login_time']) && count($params['last_login_time']) === 2) {
                    $q->whereBetween('last_login_time', $params['last_login_time']);
                }
            })
            ->when(!empty($params['created_at']), function ($q) use ($params) {
                if (is_array($params['created_at']) && count($params['created_at']) === 2) {
                    $q->whereBetween('created_at', $params['created_at']);
                }
            });

        // 如果有is_super筛选
        if (isset($params['is_super'])) {
            $query->whereHas('adminInfo', function ($q) use ($params) {
                $q->where('is_super', $params['is_super']);
            });
        }

        $total = $query->count();
        $list = $query->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(function ($account) {
                $adminInfo = $account->adminInfo;
                return [
                    'id' => $account->id,
                    'account_id' => $account->id,
                    'username' => $account->username,
                    'account_type' => $account->account_type->value,
                    'status' => $account->status,
                    'is_super' => $adminInfo?->is_super ?? 0,
                    'company_id' => $adminInfo?->company_id,
                    'department_id' => $adminInfo?->department_id,
                    'last_login_ip' => $account->last_login_ip,
                    'last_login_time' => $account->last_login_time?->format('Y-m-d H:i:s'),
                    'created_at' => $account->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $account->updated_at?->format('Y-m-d H:i:s'),
                ];
            });

        return [
            'data' => $list,
            'page' => [
                'total' => $total,
                'page' => $page,
                'pageSize' => $pageSize,
            ],
        ];
    }

    /**
     * 新增管理员
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function addAdmin(array $params): array
    {
        self::checkEmptyParam($params, ['username', 'password', 'account_type']);

        DB::beginTransaction();
        try {
            // 检查用户名是否已存在
            if (Account::query()->where('username', $params['username'])->exists()) {
                throw_exception('用户名已存在');
            }

            // 创建账号
            $account = new Account();
            $account->username = $params['username'];
            $account->password = Hash::make($params['password']);
            $account->account_type = $params['account_type'];
            $account->status = $params['status'] ?? 1;
            $account->save();

            // 创建管理员信息
            $admin = new self();
            $admin->account_id = $account->id;
            $admin->is_super = $params['is_super'] ?? 0;
            $admin->company_id = $params['company_id'] ?? null;
            $admin->department_id = $params['department_id'] ?? null;
            $admin->save();

            DB::commit();

            // 记录审计日志
            logAudit(
                request(),
                currentUserId(),
                '管理员管理',
                OperationActionEnum::add->value,
                ResourceTypeEnum::user->value,
                $account->id,
                null,
                $account->only(['id', 'username', 'account_type', 'status']),
                "新增管理员: {$account->username}"
            );

            return ['id' => $account->id];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 更新管理员
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function updateAdmin(array $params): array
    {
        self::checkEmptyParam($params, ['id']);

        DB::beginTransaction();
        try {
            $account = Account::query()->findOrFail($params['id']);
            if ($account->account_type !== AccountTypeEnum::Admin->value) {
                throw_exception('该账号不是管理员账号');
            }

            // 保存旧数据
            $old_data = $account->only(['id', 'username', 'account_type', 'status']);

            // 更新账号信息
            if (isset($params['status'])) {
                $account->status = $params['status'];
            }
            if (isset($params['password']) && !empty($params['password'])) {
                $account->password = Hash::make($params['password']);
            }
            $account->save();

            // 更新管理员信息
            $admin = self::query()->where('account_id', $account->id)->first();
            if ($admin) {
                if (isset($params['is_super'])) {
                    $admin->is_super = $params['is_super'];
                }
                if (isset($params['company_id'])) {
                    $admin->company_id = $params['company_id'];
                }
                if (isset($params['department_id'])) {
                    $admin->department_id = $params['department_id'];
                }
                $admin->save();
            }

            DB::commit();

            // 记录审计日志
            $new_data = $account->fresh()->only(['id', 'username', 'account_type', 'status']);
            logAudit(
                request(),
                currentUserId(),
                '管理员管理',
                OperationActionEnum::update->value,
                ResourceTypeEnum::user->value,
                $account->id,
                $old_data,
                $new_data,
                "更新管理员: {$account->username}"
            );

            return [];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 删除管理员
     * @param array $params
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function deleteAdmin(array $params): array
    {
        self::checkEmptyParam($params, ['id']);

        $account = Account::query()->findOrFail($params['id']);
        if ($account->account_type !== AccountTypeEnum::Admin->value) {
            throw_exception('该账号不是管理员账号');
        }

        // 保存旧数据
        $old_data = $account->only(['id', 'username', 'account_type', 'status']);

        // 删除账号（会级联删除管理员信息）
        $account->delete();

        // 记录审计日志
        logAudit(
            request(),
            currentUserId(),
            '管理员管理',
            OperationActionEnum::delete->value,
            ResourceTypeEnum::user->value,
            $account->id,
            $old_data,
            null,
            "删除管理员: {$account->username}"
        );

        return [];
    }
}


