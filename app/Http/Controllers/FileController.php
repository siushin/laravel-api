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

    /**
     * 删除文件
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function delete(Request $request): JsonResponse
    {
        $params = $request->all();
        $user_id = currentUserId();
        empty($user_id) && throw_exception('无效token，请重新登录');
        $params['user_id'] = $user_id;
        return success(SysFile::deleteFile($params), '删除文件成功');
    }

    /**
     * 清空文件（只能清空属于自己的）
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    public function cleanup(): JsonResponse
    {
        $user_id = currentUserId();
        empty($user_id) && throw_exception('无效token，请重新登录');
        SysFile::cleanupFileByUserId($user_id, true);
        return success([], '清空文件成功');
    }
}
