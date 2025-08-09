<?php

namespace App\Enums;

/**
 * 枚举：字典类别
 */
enum DictionaryCategory: string
{
    case UserType            = '用户类型';
    case AllowUploadFileType = '允许上传文件类型';
    case Region              = '地区';
}
