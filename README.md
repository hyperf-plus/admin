<div align="center">
    <br/>
<!--     <img src="" alt="HPlus Admin V3 Logo" /> -->
    <h1>HPlus Admin V3</h1>
    <h4>拥抱后端开发的未来</h4>
    <p>基于最新技术栈Hyperf、PHP 8.1、Swoole 5打造的高性能后台管理框架，整合amis前端技术，为快速开发而生。</p>

[GitHub](https://github.com/hyperf-plus/admin) | [文档](您的文档地址) | [在线体验](您的演示地址) | [社区支持](您的社区支持链接)

</div>

<p align="center">
    <a href="https://www.php.net/releases/8.1/zh.php">
        <img src="https://img.shields.io/badge/PHP-8.1-%23777BB4.svg?style=flat-square&logo=php" alt="PHP版本">
    </a>
    <a href="https://www.swoole.com/">
        <img src="https://img.shields.io/badge/Swoole-5-%238B0000.svg?style=flat-square&logo=swoole" alt="Swoole版本">
    </a>
    <a href="https://hyperf.io/">
        <img src="https://img.shields.io/badge/Hyperf-3.1%2B-%23268af1.svg?style=flat-square&logo=hyperf" alt="Hyperf版本">
    </a>
    <a href="LICENSE">
        <img src="https://img.shields.io/badge/license-MIT-%23268af1.svg?style=flat-square" alt="许可证">
    </a>
</p>

## 🚀 介绍

HPlus Admin V3是一个革命性的后台管理框架，专为追求高性能、灵活性和前沿Web开发实践的开发者设计。该框架基于PHP 8.1、Swoole 5和Hyperf构建，提供了前所未有的性能提升和开发效率。HPlus Admin V3的一个重要特性是深度整合了amis前端框架，通过JSON配置即可快速构建出美观、功能丰富的界面，极大简化了前端的开发工作量。此外，HPlus Admin V3吸收了Laravel的开发理念，结合了Laravel在ORM、中间件、服务容器等方面的优势，为Hyperf提供了更加丰富的开发体验和生态支持。

## 💡 特点

- **前沿技术栈:** 构建于PHP 8.1、Swoole 5和Hyperf之上，拥抱异步编程和协程的最新进展，为您的应用提供了前所未有的性能提升。

- **与amis无缝集成:** HPlus Admin V3深度整合amis，让开发者能够通过JSON配置轻松构建出美观、功能丰富的前端界面。amis的这一特性极大地降低了前端开发的复杂性，使得无需深入了解复杂的前端框架或编写大量的JavaScript代码，就能快速开发出响应式、交云互动的Web应用。

- **快速开发:** 提供了丰富的模板和构建工具，支持快速生成CRUD操作界面，大大加快了开发速度，使得从概念到产品的时间更短。

- **高度可扩展:** 支持自定义插件和模块，开发者可以根据业务需求灵活添加功能，或者整合第三方服务，满足多变的业务场景。

- **安全性:** 结合Hyperf的安全机制和Swoole的稳定性，提供了强大的安全保障，帮助您的应用抵御网络攻击，保护用户数据。

通过整合amis，HPlus Admin V3不仅仅是一个后台管理框架，它更是一个强大的全栈解决方案，使得从数据后端到前端界面的开发变得前所未有地简单和高效。无论是复杂的数据处理还是丰富的用户交互，HPlus Admin V3都能帮助您快速实现，加速产品的迭代和上市。

### 🔥 功能丰富的组件库

- **丰富的组件库：** amis提供了150+的组件库，涵盖了从基础的表单元素到复杂的数据展示组件，甚至包括图表、对话框、标签页等高级组件。这些组件的丰富性保证了几乎所有类型的前端页面需求都能得到满足。

- **高度可定制化：** 尽管amis的组件通过JSON配置即可使用，但它们也支持高度的定制化。开发者可以根据自己的需求调整组件的样式、行为和交互方式，甚至可以扩展自定义组件，以实现特定的功能。

- **快速响应式布局：** amis的组件设计遵循响应式布局原则，能够自动适配不同大小的屏幕。无论是在PC端还是移动设备上，都能保证用户界面的美观和用户体验。

- **声明式UI构建：** 通过JSON配置即可声明式地构建用户界面，这种方式使得界面的构建变得简单明了，大大减少了前端代码的编写量。开发者可以通过可视化工具直接生成这些JSON配置，实现所见即所得的界面开发。


## 🚀 快速开始

### 环境要求

确保您的服务器满足以下条件：

- PHP >= 8.1
- Swoole >= 5.0
- Hyperf >= 3.0

### 安装步骤
<span color="red">【开发中暂未发布】</span>
1. 创建Hyperf项目：

```bash
composer create-project hyperf/hyperf-skeleton your-project-name
```

2. 安装HPlus Admin V3包：

```bash
cd your-project-name
composer require hyperf-plus/admin
```

3. 发布配置文件和资源：

```bash
php bin/hyperf.php vendor:publish hyperf-plus/admin
```

4. 初始化数据库：

```bash
php bin/hyperf.php admin:install
```

5. 启动项目：

```bash
php bin/hyperf.php start
```

现在您可以通过访问`http://localhost:9501/admin`来使用HPlus Admin V3了，默认账号和密码均为`admin`。

## 🤝 贡献

欢迎各位开发者对HPlus Admin V3的贡献，无论是通过提交Pull Request来修复bug或添加新特性，还是通过提供意见和反馈来帮助我们改进项目。如果您喜欢这个项目，不妨给我们一个Star，您的支持是我们前进的最大动力！

## 🔒 许可证

HPlus Admin V3遵循MIT许可证发布。详细内容请参见[LICENSE](LICENSE)文件。

感谢您对HPlus Admin V3的关注，期待您的加入和贡献！
