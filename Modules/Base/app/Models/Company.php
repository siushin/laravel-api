<?php

namespace Modules\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 模型：公司
 */
class Company extends Model
{
    use SoftDeletes;

    protected $table = 'gpa_company';

    protected $hidden = [
        'deleted_at',
    ];
}
