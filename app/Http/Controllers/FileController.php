<?php

namespace App\Http\Controllers;

use App\Models\SysFile;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 控制器：文件
 */
class FileController extends Controller
{
    /**
     * 文件上传
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function upload(Request $request): JsonResponse
    {
        $file = $request->file('file');
        !isset($file) && throw_exception('请上传文件');
        return success(SysFile::uploadFile($file));
    }
}
