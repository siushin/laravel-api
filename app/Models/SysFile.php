<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Siushin\LaravelTool\Enums\SysLogAction;
use Siushin\LaravelTool\Enums\SysUploadFileType;
use Siushin\LaravelTool\Traits\ModelTool;

/**
 * 模型：文件
 */
class SysFile extends Model
{
    use HasFactory, ModelTool;

    protected $primaryKey = 'file_id';
    protected $table      = 'sys_files';

    // 文件类型 => 存储 disk 符
    const string FILE_IMAGE = 'image';
    const string FILE_PDF   = 'pdf';

    protected $fillable = [
        'file_name', 'origin_file_name', 'file_path', 'file_size', 'mime_type', 'file_ext_name',
        'user_id', 'checksum', 'fileable_type', 'fileable_id',
    ];

    private static array $allowImages = [
        SysUploadFileType::JPG, SysUploadFileType::JPEG, SysUploadFileType::PNG, SysUploadFileType::GIF,
    ];

    private static array $allowPDFs = [
        SysUploadFileType::PDF,
    ];

    protected $hidden = ['original_file_name', 'fileable_type', 'fileable_id', 'created_at', 'updated_at'];

    /**
     * 获取一对一（多态）的关联模型
     * @param string $file_type
     * @return string
     * @author siushin<siushin@163.com>
     */
    public static function getFileableType(string $file_type): string
    {
        return match ($file_type) {
            self::FILE_IMAGE => SysFileImage::class,
            default => '',
        };
    }

    /**
     * 上传文件
     * @param UploadedFile $file
     * @param string       $disk
     * @return array
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public static function uploadFile(UploadedFile $file, string $disk = 'public'): array
    {
        // 获取原始文件名
        $originalName = $file->getClientOriginalName();
        // 获取原始文件的扩展名
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        if ($extension) $extension = strtolower($extension);
        // 文件类型 / 按文件类型分配存储目录
        $file_type = $save_path = self::getAllowFileType($extension);
        empty($file_type) && throw_exception('该文件不支持上传');

        $file_path = Storage::disk($disk)->putFile("/$save_path", $file);
        !$file_path && throw_exception('存储文件失败');

        // 文件名
        $full_file_path = storage_path("app/" . ltrim($disk, '/')) . "/$file_path";
        // 额外表信息处理
        $extra_obj = $fileable_type = self::getFileableType($file_type);
        $fileable_id = Str::ulid()->toBase32();

        // 存储文件信息到数据库
        $data = [
            'file_name' => basename($full_file_path),
            'origin_file_name' => $file->getClientOriginalName(),
            'file_path' => $file_path,
            'file_size' => $file->getSize(),
            'mime_type' => mime_content_type($file->getPathname()),
            'file_ext_name' => $extension,
            'user_id' => currentUserId(),
            'checksum' => hash_file('sha256', $file->getPathname()),
            'fileable_type' => $fileable_type,
            'fileable_id' => $fileable_id,
        ];
        $result = self::query()->create($data);

        logging(SysLogAction::upload_file->name, "上传文件({$result['file_name']})", $result->toArray());

        // 根据文件类型分发执行附属模型
        if (method_exists($extra_obj, 'uploadFileExtraAfterHook')) {
            $extra_obj::uploadFileExtraAfterHook($fileable_id, $full_file_path);
        }
        return compact('file_path');
    }

    /**
     * 获取文件类型
     * @param string $extension
     * @return string
     * @author siushin<siushin@163.com>
     */
    public static function getAllowFileType(string $extension): string
    {
        $file_type = '';
        if (in_array($extension, array_column(self::$allowImages, 'value'))) {
            $file_type = self::FILE_IMAGE;
        } elseif (in_array($extension, array_column(self::$allowPDFs, 'value'))) {
            $file_type = self::FILE_PDF;
        }
        return $file_type;
    }
}
