<?php

namespace Modules\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 模型：模块
 */
class Module extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'module_id';
    protected $table      = 'gpa_module';

    protected $fillable = [
        'module_id',
        'module_identifier',
        'module_name',
        'module_alias',
        'module_description',
        'uploader_id',
        'status',
        'priority',
        'version',
        'keywords',
        'providers',
    ];

    protected $casts = [
        'status'    => 'integer',
        'priority'  => 'integer',
        'keywords'  => 'array',
        'providers' => 'array',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

