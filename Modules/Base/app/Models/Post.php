<?php

namespace Modules\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 模型：岗位
 */
class Post extends Model
{
    use SoftDeletes;

    protected $table = 'gpa_post';

    protected $hidden = [
        'deleted_at',
    ];
}
