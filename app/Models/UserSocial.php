<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Siushin\LaravelTool\Enums\SocialTypeEnum;

/**
 * 模型：用户社交网络
 */
class UserSocial extends Model
{
    protected $table = 'bs_user_social';

    protected $fillable = [
        'id',
        'user_id',
        'social_type',
        'social_account',
        'social_name',
        'avatar',
        'is_verified',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'social_type' => SocialTypeEnum::class,
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * 关联用户
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

