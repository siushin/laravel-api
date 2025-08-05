<?php

namespace App\Enums;

/**
 * 枚举：上传文件类型
 */
enum UploadFileType: string
{
    case NONE = 'none';

    // 图片
    case JPG  = 'jpg';
    case JPEG = 'jpeg';
    case PNG  = 'png';
    case GIF  = 'gif';

    // PDF文件
    case PDF = 'pdf';
}
