<?php

namespace Modules\Base\Http\Controllers;

use Modules\Base\Attributes\OperationAction;
use Modules\Base\Enums\OperationActionEnum;
use Modules\Base\Models\File;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 控制器：文件
 * @module 文件管理
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
    #[OperationAction(OperationActionEnum::upload)]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->file('file');
        !isset($file) && throw_exception('请上传文件');
        return success(File::uploadFile($file));
    }

    /**
     * 删除文件
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::delete)]
    public function delete(Request $request): JsonResponse
    {
        $params = trimParam($request->all());
        $account_id = currentUserId();
        empty($account_id) && throw_exception('无效token，请重新登录');
        $params['account_id'] = $account_id;
        return success(File::deleteFile($params), '删除文件成功');
    }

    /**
     * 清空文件（只能清空属于自己的）
     * @return JsonResponse
     * @throws Exception
     * @author siushin<siushin@163.com>
     */
    #[OperationAction(OperationActionEnum::delete)]
    public function cleanup(): JsonResponse
    {
        $account_id = currentUserId();
        empty($account_id) && throw_exception('无效token，请重新登录');
        File::cleanupFileByAccountId($account_id, true);
        return success([], '清空文件成功');
    }
}
