<?php

namespace Modules\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 模型：职位
 */
class Position extends Model
{
    use SoftDeletes;

    protected $table = 'gpa_position';

    protected $hidden = [
        'deleted_at',
    ];
}
