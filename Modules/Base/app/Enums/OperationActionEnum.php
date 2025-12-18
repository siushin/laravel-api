<?php

namespace Modules\Base\Enums;

/**
 * 枚举：操作类型
 */
enum OperationActionEnum: string
{
    case index       = 'index';         // 列表查询
    case create      = 'create';        // 新增
    case add         = 'add';           // 添加
    case update      = 'update';        // 更新
    case edit        = 'edit';          // 编辑
    case delete      = 'delete';        // 删除
    case batchDelete = 'batchDelete';   // 批量删除
    case export      = 'export';        // 导出
    case import      = 'import';        // 导入
    case upload      = 'upload';        // 上传
    case download    = 'download';      // 下载
    case move        = 'move';          // 移动
    case copy        = 'copy';          // 复制
    case view        = 'view';          // 查看
    case search      = 'search';        // 搜索
}
