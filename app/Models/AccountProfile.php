<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 模型：账号资料
 */
class AccountProfile extends Model
{
    protected $table = 'bs_account_profile';

    protected $fillable = [
        'id',
        'user_id',
        'real_name',
        'gender',
        'avatar',
    ];

    /**
     * 关联账号
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'user_id');
    }
}
