<?php

namespace Modules\Base\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 模型：菜单
 */
class SysMenu extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'menu_id';
    protected $table      = 'sys_menu';

    protected $fillable = [
        'menu_id',
        'account_type',
        'menu_name',
        'name',
        'menu_path',
        'component',
        'menu_icon',
        'menu_type',
        'parent_id',
        'redirect',
        'layout',
        'access',
        'wrappers',
        'is_required',
        'sort',
        'status',
    ];

    protected $casts = [
        'layout'      => 'boolean',
        'is_required' => 'integer',
        'status'      => 'integer',
        'sort'        => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
