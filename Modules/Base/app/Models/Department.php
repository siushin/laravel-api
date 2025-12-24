<?php

namespace Modules\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 模型：部门
 */
class Department extends Model
{
    use SoftDeletes;

    protected $table = 'gpa_department';

    protected $hidden = [
        'deleted_at',
    ];
}
