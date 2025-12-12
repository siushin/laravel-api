<?php

namespace Modules\Sms\Enums;

/**
 * 枚举：短信类型
 */
enum SmsTypeEnum: string
{
    case Register      = 'register';        // 注册验证码
    case Login         = 'login';           // 登录验证码
    case ResetPassword = 'reset_password';  // 重置密码验证码
    case BindMobile    = 'bind_mobile';     // 绑定手机号验证码
    case ChangeMobile  = 'change_mobile';   // 更换手机号验证码
}
