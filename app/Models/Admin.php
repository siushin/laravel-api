<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 模型：管理员
 */
class Admin extends Model
{
    protected $table = 'bs_admin';

    protected $fillable = [
        'id',
        'user_id',
        'company_id',
        'department_id',
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

