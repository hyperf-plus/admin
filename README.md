## HPlus Admin，全新admin插件 快速开发框架， 兼容laravel-admin用法
#### 欢迎加入HPlus交流群，群聊号码：512465490
点击链接加入群聊【hyperf-admin交流群】：<a href="https://qm.qq.com/cgi-bin/qm/qr?k=pCkT8bLR-scfzGhiLYAu2AuEu5pzOfdD&authKey=0L9w5QrmZJQpDdaH9R5WpPK5mUPyh1RiM3nqcRggpMpM8heAgBBXWdzuk9zkyRko&noverify=0">群聊号码：512465490</a>
<p align="center">
    <a href="https://github.com/lphkxd/hyperf-admin/releases"><img src="https://poser.pugx.org/mzh/hyperf-admin-plugin/v/stable" alt="Stable Version"></a>
    <a href="https://travis-ci.org/mzh/hyperf-admin-plugin"><img src="https://travis-ci.org/mzh/hyperf-admin-plugin.svg?branch=master" alt="Build Status"></a>
    <a href="https://packagist.org/packages/mzh/hyperf-admin-plugin"><img src="https://poser.pugx.org/mzh/hyperf-admin-plugin/downloads" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/mzh/hyperf-admin-plugin"><img src="https://poser.pugx.org/mzh/hyperf-admin-plugin/d/monthly" alt="Monthly Downloads"></a>
    <a href="https://www.php.net"><img src="https://img.shields.io/badge/php-%3E=7.3-brightgreen.svg?maxAge=2592000" alt="Php Version"></a>
    <a href="https://github.com/swoole/swoole-src"><img src="https://img.shields.io/badge/swoole-%3E=4.5-brightgreen.svg?maxAge=2592000" alt="Swoole Version"></a>
    <a href="https://github.com/lphkxd/hyperf-admin-plugin/blob/master/LICENSE"><img src="https://img.shields.io/github/license/lphkxd/hyperf-admin-plugin.svg?maxAge=2592000" alt="HyperfAdmin License"></a>
</p>


### 安装
```bash
1、安装Admin插件
    composer require hyperf-plus/admin

2、生成admin auth file配置文件
    php bin/hyperf.php vendor:publish hyperf-plus/admin

3、UI资源初始化
    php bin/hyperf.php ui:init

4、配置好数据库（必须），然后执行下面安装命令
    php bin/hyperf.php admin:install

5、启动服务
	php bin/hyperf.php start
```
### 访问 http://127.0.0.1:9501/auth
- 账户 admin
- 密码 admin

#### 以插件形式开箱即用
#### 可以做到无需VUE前端可实现快速开发各种表单
#### 喜欢的帮忙点个star