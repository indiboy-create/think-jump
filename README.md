## ThinkPHP-PanGu - 盘古ThinkPHP开源索引!
面向 ThinkPHP 的开源工程索引：组件、框架、服务与发布工具，按能力组织，持续整理。
仓库地址：**<https://github.com/indiboy-create/ThinkPHP-PanGu/>**
在线站点：**<https://indiboy-create.github.io/ThinkPHP-PanGu/>**

# youvision/think-jump

适用于 ThinkPHP 8.0 的跳转扩展。通过一个 `Jump` trait 为控制器提供 `success`、`error`、`result`、`redirect` 四个快捷响应方法，自动区分 HTML 页面跳转与 Ajax/JSON 接口返回。

## 目录

- [特性](#特性)
- [环境要求](#环境要求)
- [安装](#安装)
- [配置](#配置)
- [快速开始](#快速开始)
- [方法说明](#方法说明)
- [响应格式](#响应格式)
- [使用建议](#使用建议)
- [自定义跳转模板](#自定义跳转模板)
- [示例](#示例)
- [注意事项](#注意事项)
- [许可证](#许可证)

## 特性

- 一个 trait 接入，无需继承特定基类
- `success`、`error` 自动适配 HTML 跳转页与 JSON 响应
- `result` 统一 API 数据结构，支持指定响应格式
- `redirect` 支持站内、站外地址与隐式传参
- 跳转模板可配置、可替换
- 仅依赖 ThinkPHP 8 与 think-view，无额外依赖

## 环境要求

| 依赖 | 版本 |
| --- | --- |
| PHP | >= 8.0 |
| ThinkPHP | ^8.0 |
| topthink/think-view | ^2.0 |

> 仅支持 ThinkPHP 8.0，不支持 ThinkPHP 6.0。

## 安装

```bash
composer require youvision/think-jump
```

## 配置

安装完成后会在项目的 `config` 目录生成 `jump.php`：

```php
<?php
// config/jump.php
return [
    // 操作成功跳转页面对应的模板文件
    'dispatch_success_tmpl' => app()->getRootPath() . '/vendor/youvision/think-jump/src/tpl/dispatch_jump.tpl',
    // 操作失败跳转页面对应的模板文件
    'dispatch_error_tmpl'   => app()->getRootPath() . '/vendor/youvision/think-jump/src/tpl/dispatch_jump.tpl',
];
```

如未自动生成，可手动创建该文件，内容同上。

## 快速开始

在控制器中引入 `Jump` trait：

```php
<?php

namespace app\controller;

use youvision\think\Jump;

class Index
{
    use Jump;

    public function index()
    {
        return $this->success('操作成功', 'index/index');
    }
}
```

也可以在项目自带的 `BaseController` 中统一引入，之后所有继承该基类的控制器都可直接使用：

```php
<?php

namespace app;

abstract class BaseController
{
    use \youvision\think\Jump;

    // 其余初始化逻辑保持不变
}
```

`Jump` 内部通过 `$this->request ?? request()` 和 `$this->app ?? app()` 获取请求与应用实例，控制器中无需额外声明 `$request`、`$app` 属性。

## 方法说明

| 方法 | 说明 |
| --- | --- |
| `success()` | 操作成功跳转 |
| `error()` | 操作失败跳转 |
| `result()` | 返回封装后的 API 数据 |
| `redirect()` | URL 重定向 |

以上方法均抛出 `HttpResponseException`，由 ThinkPHP 统一接管响应，控制器中直接 `return` 即可。

### success()

操作成功跳转。普通 HTML 请求渲染成功模板，Ajax/JSON 请求返回 JSON。

```php
protected function success(
    mixed $msg = '',
    ?string $url = null,
    mixed $data = '',
    int $wait = 3,
    array $header = []
)
```

| 参数 | 类型 | 默认值 | 说明 |
| --- | --- | --- | --- |
| `$msg` | mixed | `''` | 提示信息 |
| `$url` | ?string | `null` | 跳转地址；为空时取 `HTTP_REFERER` |
| `$data` | mixed | `''` | 返回的数据 |
| `$wait` | int | `3` | 跳转等待时间（秒） |
| `$header` | array | `[]` | 发送的 Header 信息 |

```php
// 跳转到 index/index
return $this->success('登录成功', 'index/index');

// 携带数据并等待 5 秒
return $this->success('登录成功', 'index/index', ['id' => 1], 5);
```

`$url` 的解析规则：

- 包含 `://` 或 `/` 开头时按原样使用；
- 其他相对地址通过 `route->buildUrl()` 生成；
- 为空时使用 `HTTP_REFERER`。

### error()

操作失败跳转。规则与 `success()` 一致，未指定 `$url` 时：

- 普通 HTML 请求使用 `javascript:history.back(-1);` 返回上一页；
- Ajax 请求的 `url` 为空字符串。

```php
protected function error(
    mixed $msg = '',
    ?string $url = null,
    mixed $data = '',
    int $wait = 3,
    array $header = []
)
```

```php
return $this->error('登录失败');

// 指定跳转地址
return $this->error('登录失败', 'login/index');
```

### result()

返回封装后的 API 数据。

```php
protected function result(
    mixed $data,
    int $code = 0,
    mixed $msg = '',
    string $type = '',
    array $header = []
)
```

| 参数 | 类型 | 默认值 | 说明 |
| --- | --- | --- | --- |
| `$data` | mixed | 必填 | 要返回的数据 |
| `$code` | int | `0` | 业务状态码 |
| `$msg` | mixed | `''` | 提示信息 |
| `$type` | string | `''` | 响应格式，为空时按请求类型判断 |
| `$header` | array | `[]` | 发送的 Header 信息 |

```php
return $this->result(['username' => 'youvision']);

// 显式指定 JSON 格式
return $this->result(['username' => 'youvision'], 0, 'ok', 'json');
```

返回结构：

```json
{
    "code": 0,
    "msg": "",
    "time": 1700000000,
    "data": {
        "username": "youvision"
    }
}
```

### redirect()

URL 重定向，默认 HTTP 状态码为 302。

```php
protected function redirect(string $url, int $code = 302, array $with = [])
```

| 参数 | 类型 | 默认值 | 说明 |
| --- | --- | --- | --- |
| `$url` | string | 必填 | 跳转的 URL 表达式 |
| `$code` | int | `302` | HTTP 状态码 |
| `$with` | array | `[]` | 隐式传参 |

```php
// 站内地址
return $this->redirect('/admin/index/index');

// 使用 url 函数生成地址
return $this->redirect(url('index/index', ['name' => 'think']));

// 外部地址
return $this->redirect('http://www.thinkphp.cn');

// 指定状态码与隐式传参
return $this->redirect('/admin/index/index', 301, ['from' => 'home']);
```

## 响应格式

`success()` 和 `error()` 通过 `getResponseType()` 判断响应类型：

| 请求类型 | 响应 |
| --- | --- |
| 普通 HTML | 渲染 `dispatch_success_tmpl` / `dispatch_error_tmpl` 指定的模板 |
| Ajax 或 JSON | 返回 JSON |

Ajax/JSON 下 `success()` 与 `error()` 返回的结构：

```json
{
    "code": 1,
    "msg": "操作成功",
    "data": {},
    "url": "index/index",
    "wait": 3
}
```

其中 `success()` 的 `code` 为 `1`，`error()` 的 `code` 为 `0`。

## 使用建议

控制器的四个方法均可直接 `return`。若使用统一 API 响应，建议在接口控制器中固定使用 `result()`；若用于后台表单提交，使用 `success()` / `error()` 即可自动适配页面跳转与 Ajax。

## 自定义跳转模板

将 `vendor/youvision/think-jump/src/tpl/dispatch_jump.tpl` 复制到项目中（例如 `app/view/dispatch_jump.tpl`），然后修改配置：

```php
return [
    'dispatch_success_tmpl' => app()->getRootPath() . '/app/view/dispatch_jump.tpl',
    'dispatch_error_tmpl'   => app()->getRootPath() . '/app/view/dispatch_jump.tpl',
];
```

模板中可直接使用以下变量：

| 变量 | 说明 |
| --- | --- |
| `$code` | `1` 表示成功，`0` 表示失败 |
| `$msg` | 提示信息 |
| `$data` | 返回的数据 |
| `$url` | 跳转地址 |
| `$wait` | 等待时间（秒） |

## 示例

`examples` 目录提供了可直接复制到 ThinkPHP 8 项目中使用的示例：

| 文件 | 说明 |
| --- | --- |
| `examples/BaseController.php` | 在项目基类中引入 `Jump` trait |
| `examples/controller/Demo.php` | `success`、`error`、`result`、`redirect` 的用法 |
| `examples/controller/Api.php` | Ajax/JSON 接口场景下的用法 |
| `examples/config/jump.php` | 使用自定义跳转模板的配置 |
| `examples/view/dispatch_jump.tpl` | 自定义跳转模板 |

将 `BaseController.php` 中的 `use Jump;` 加到项目自带的 `BaseController`，再复制控制器或参考其写法即可。默认路由下可访问 `/demo/successDemo`、`/demo/errorDemo`、`/demo/resultDemo`、`/demo/redirectDemo`，以及 `/api/successApi`、`/api/errorApi`、`/api/resultApi`。

## 注意事项

- 控制器方法不要命名为 `success`、`error`、`result`、`redirect`，否则会覆盖 trait 中的同名方法，导致调用自身。示例中使用 `successDemo`、`successApi` 等名称避免冲突。
- `success()`、`error()`、`result()`、`redirect()` 都以异常方式返回响应，调用处必须 `return`，并且不要在调用后再执行输出逻辑。
- `result()` 的 `$type` 为空时按 `getResponseType()` 判断，返回 `json` 或 `html`；接口场景建议显式传入 `json`。
- 跳转模板中的 `$url`、`$msg` 来自业务数据，自定义模板时请按需做转义。

## 许可证

木兰宽松许可证，第2版（MulanPSL-2.0）