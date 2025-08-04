# 基于 `Laravel` 的API服务

## 介绍

版本号：v1.0.0

## 快速开始

创建项目：`composer create-project siushin/laravel-api`

> ⚠️⚠️⚠️注意：执行命令 `composer create-project` 或 `composer install` 都会执行 **清空** 表并 **重新填充** 数据
`php artisan migrate:fresh --seed`。如有重要数据，请自行备份。

### 创建数据库

1. 提前在本地，创建数据库: `laravel_api`（数据库用户名`root`，密码：``(空)）
2. 初始化系统，后台登录的账号密码：`admin` / `admin`

## 软件架构

软件架构说明

## 安装说明

1. 创建符号链接：php artisan storage:link
2. 配置环境变量文件.env（配置 数据库 等信息）
3. php.ini取消 `symlink` 函数禁用
4. 创建数据表并填充：
    - 初次执行：`php artisan migrate --seed`
    - 清空所有并重新执行：`php artisan migrate:fresh --seed`

## 运行命令历史

```shell
#!/bin/sh

# 启用 API 路由
php artisan install:api &&

# 创建 系统枚举类
php artisan make:enum SysLogAction &&
php artisan make:enum SysUserType &&
```

## 使用说明

### 使用前须安装以下扩展

- 开启 `fileinfo` 扩展

## 更新 `Composer` 的自动加载文件

```shell
composer dump-autoload
// 后续开启系统扩展都等同,不做赘述
```

- 开启 `mbstring` 扩展

## 常见问题

### 413 Request Entity Too Large

处理方案：

1. 调整Nginx配置
    - 配置文件中增加或修改 `client_max_body_size` 指令。例如，将大小设置为100MB（http { client_max_body_size 100m; }）
2. 调整PHP配置
    - 调整PHP的 `upload_max_filesize` 和 `post_max_size` 配置项（upload_max_filesize = 100M post_max_size = 100M）

## 参考资料

- [overtru 相关扩展包](https://packagist.org/packages/overtrue/)
