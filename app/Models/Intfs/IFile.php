<?php

namespace App\Models\Intfs;

/**
 * 接口：文件
 */
interface IFile
{
    /**
     * 后置钩子：上传文件扩展
     * @param string $fileable_id
     * @param string $full_file_path
     * @return void
     * @author siushin<siushin@163.com>
     */
    public static function uploadFileExtraAfterHook(string $fileable_id, string $full_file_path): void;
}
