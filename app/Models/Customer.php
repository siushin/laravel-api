<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 模型：客户
 */
class Customer extends Model
{
    protected $table = 'bs_customer';

    protected $fillable = [
        'id',
        'user_id',
    ];

    /**
     * 关联用户
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

