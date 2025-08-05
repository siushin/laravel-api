<?php

namespace App\Models;

use App\Models\Intfs\IFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * 模型：图片文件
 */
class SysFileImage extends Model implements IFile
{
    use HasFactory;

    protected $primaryKey = 'image_id';
    protected $table      = 'sys_file_images';

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * 获取图片文件
     * @return MorphOne
     * @author siushin<siushin@163.com>
     */
    public function file(): MorphOne
    {
        return $this->morphOne(SysFile::class, 'fileable');
    }

    /**
     * 后置钩子：上传文件扩展
     * @param string $fileable_id
     * @param string $full_file_path
     * @return void
     * @author siushin<siushin@163.com>
     */
    public static function uploadFileExtraAfterHook(string $fileable_id, string $full_file_path): void
    {
        list($width, $height) = getimagesize($full_file_path);
        $data = [
            'fileable_id' => $fileable_id,
            'image_width' => $width,
            'image_height' => $height,
        ];
        // 图片信息 入库
        self::query()->create($data);
    }
}
