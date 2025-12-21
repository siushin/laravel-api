<?php

namespace Modules\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 模型：账号资料
 */
class AccountProfile extends Model
{
    protected $table = 'gpa_account_profile';

    protected $fillable = [
        'id',
        'account_id',
        'nickname',
        'gender',
        'avatar',
        'real_name',
        'id_card',
        'verification_method',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * 关联账号
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
