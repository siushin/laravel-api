<?php

namespace Modules\Base\Enums;

/**
 * 枚举：字典类别
 */
enum DictionaryCategoryEnum: string
{
    case RequestSource       = '请求来源';
    case OrganizationType    = '组织架构类型';
    case AllowUploadFileType = '允许上传文件类型';
}
