# LaravelAPI 基于 Laravel 的API框架，提供常用工具类、助手函数等服务

## 开始使用

```shell
composer create-project siushin/laravel-api
```

## 创建数据库

1. 提前在本地，创建数据库: `laravel_api`（数据库用户名`root`，密码：``(空)）
2. 初始化系统，后台登录的账号密码：`admin` / `admin`

## ⚠️⚠️⚠️注意事项

> 注意：执行命令 `composer create-project` 或 `composer install` 都会执行 **清空** 表并 **重新填充** 数据
`php artisan migrate:fresh --seed`。如有重要数据，请自行备份。

## LaravelAPI接口文档

[LaravelAPI - API接口文档](https://s.apifox.cn/9e462aa5-5078-455c-b631-75b9d9e2a303)

## 目录结构

| 目录名    | 描述                                                           |
|--------|--------------------------------------------------------------|
| Cases  |                                                              |
| Enums  | 枚举，以 `Sys` 开头（方便全局搜索）                                        |
| Funcs  | 助手函数，分以 `Lara` 开头的基于Laravel的助手函数，以及以 `Func`开头的常用助手函数（方便全局搜索） |
| Traits | 特征，没有明显命名规范，自行查询源码或文档                                        |

## 已实现模块

✅管理员登录/授权
✅管理员管理
✅日志管理

## 🧑🏻‍💻 关于作者

十年开发经验，具有丰富的前、后端软件开发经验~

👤 作者：<https://github.com/siushin>

💻 个人博客：<http://www.siushin.com>

📮 邮箱：<a href="mailto:siushin@163.com">siushin@163.com</a>

## 💡 反馈交流

在使用过程中有任何想法、合作交流，请加我微信 `lintonggg` （备注 <mark>github</mark> ）：

<img src="https://raw.githubusercontent.com/siushin/doc/refs/heads/main/docs/public/%E5%BE%AE%E4%BF%A1%E4%BA%8C%E7%BB%B4%E7%A0%81.jpg" alt="添加我微信备注「github」" style="width: 180px;" />

## ☕️ 打赏赞助

如果你觉得知识对您有帮助，可以请作者喝一杯咖啡 ☕️

<div class="coffee" style="display: flex;align-items: center;margin-top: 20px;">
<img src="https://raw.githubusercontent.com/siushin/doc/refs/heads/main/docs/public/%E5%BE%AE%E4%BF%A1%E6%94%B6%E6%AC%BE%E7%A0%81.jpg" alt="微信收款码" style="width: 180px;margin-right: 80px;" />
<img src="https://raw.githubusercontent.com/siushin/doc/refs/heads/main/docs/public/%E6%94%AF%E4%BB%98%E5%AE%9D%E6%94%B6%E6%AC%BE%E7%A0%81.jpg" alt="支付宝收款码" style="width: 180px;" />
</div>
