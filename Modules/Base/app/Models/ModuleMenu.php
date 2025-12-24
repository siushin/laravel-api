<?php

namespace Modules\Base\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 模型：模块菜单关联
 */
class ModuleMenu extends Model
{
    protected $table = 'gpa_module_menu';

    protected $fillable = [
        'id',
        'module_id',
        'menu_id',
    ];

    public $timestamps = true;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}

