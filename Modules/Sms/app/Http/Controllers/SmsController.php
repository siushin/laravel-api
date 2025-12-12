<?php

namespace Modules\Sms\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Sms\Services\SmsService;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 控制器：短信服务
 */
class SmsController extends Controller
{
    /**
     * 短信服务
     */
    private SmsService $smsService;

    /**
     * 构造函数
     * @param SmsService $smsService
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * 发送短信验证码
     * @param Request $request
     * @return JsonResponse
     * @throws Exception|InvalidArgumentException
     * @author siushin<siushin@163.com>
     */
    public function sendSms(Request $request): JsonResponse
    {
        // 校验参数
        $params = $this->smsService->validateParams($request->all());

        // 调用服务类发送验证码
        $result = $this->smsService->sendVerificationCode($params['mobile'], $params['type']);

        return success($result, '验证码发送成功');
    }
}
