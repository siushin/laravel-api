<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 模型：用户资料
 */
class UserProfile extends Model
{
    protected $table = 'bs_user_profile';

    protected $fillable = [
        'id',
        'user_id',
        'real_name',
        'gender',
        'avatar',
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

