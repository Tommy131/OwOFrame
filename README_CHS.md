## OwOFrame
开源许可证: ![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg) [Learn More](https://opensource.org/licenses/Apache-2.0)

OwO! 欢迎来到本项目. `OwOFrame` 基于 `MVC (Model-Views-Controller)` 模式开发, 框架的制定标准借鉴了 `ThinkPHP` ~~和 `Laravel`~~(框架太大了), 因此有部分方法的命名规则看起来与其有相似之处. 此项目仅为我个人的练习项目.

如果您觉得本项目对您有帮助, 请给本项目一颗小小的 `Star` 呗QWQ

------

## 我能做什么?
> OwOFrame 是我利用个人的空闲时间以及数不尽多少天熬夜开发出来的小框架, 当然也有很多不足之处. 本框架目前支持的功能请参见下列:

#### 基础系统组件
- [x] `!!!IDE友好!!!`       所有注释都能在 `Visul Studio Code` 中获得良好的显示效果, 全为手动注释 :)
- [x] ~~`ApiProcessor`      一个独立的API处理模块, 用于分离与AppManager的区别~~ (在 [`dev@v1.0.1-ALPHA27`](https://github.com/Tommy131/OwOFrame/commit/317ec78fa53b5a684a899cb664e486d1fc8ae971) 中已被移除)
- [x] `AppManager`          本框架为多应用共存框架, 通过HTTP_URI识别当前的应用
- [x] `CommandManager`      支持在CLI端通过CommandLine方法实现一些操作
- [x] `ConfigurationParser` 配置文件解析器
- [x] `EventManager`        事件管理器(钩子方法)
- [x] `Exception`           错误异常抓取及Stack输出(我知道高级的框架都拥有且比我的更好QAQ)
- [x] `PluginLoader`        插件加载器(独立于Application之外的可灵活更改的一种支持方法)
- [x] `Language`            自定义语言包支持
- [x] `Logger`              支持最基础的日志记录
- [x] `Redis`               支持基本Redis操作
- [x] `RouterManager`       路由管理器
- [x] `BetterRouter`        更好的路由控制器(更加个性化的路由绑定设置)
- [x] `Template`            后端渲染模板(基本功能已经完成, 龟速开发进阶功能o(*￣▽￣*)o)
- [x] `TextFormat`          支持ANSI控制码在CMD&Shell输出色彩
- [x] `WMI`                 针对Windows系统编写的WMI操作类

#### 基础功能组件
- [x] `CookieClass`         一个普通的Cookie处理类
- [x] `EmptyAppGenerator`   一键生成新的Application模板
- [x] `FileUploadClass`     文件上传类支持
- [x] `Helper`              一个集成化的方法类(具体请看源代码)
- [x] `SessionClass`        一个普通的Session处理类

#### 第三方资源
- [x] [`PasswordHash`](http://www.openwall.com/phpass/) 在此项目中包含/集成了该类库
- [x] [`Think-ORM`](https://github.com/top-think/think-orm) 使用此项目达到了OwOFrame的ModelBase目的实现



## 如何使用?
### §1. 安装方法
1.1. 可以使用 `git clone https://github.com/Tommy131/OwOFrame.git` 方法直接将项目克隆到本地.<br/>
1.2. 或使用 `composer create-project tommy131/owoframe -s dev` 将本项目在本地创建.

### §2. 命令行运行方法
打开命令行 `CMD` 或Linux的 `Shell` 之后, 进入到项目根目录并且输入指令 `cd owoframe && composer install` 安装所需的依赖包.<br/>
如果你选择的第二种安装方法, 则不需要执行上述命令.

### §3. 然后介绍一下目录格式吧.

- 引导文件为根目录下的 `/public/index.php`, 通过此文件初始化框架.
- TODO

#### 是否需要修改Web环境?
需要. Nginx的修改方法参考下方:

``` nginx
# Set your web root path to /public (Example);
root /www/owoframe/public;

location / {
    index index.php index.html;
    try_files $uri $uri/ /index.php$is_args$query_string;
}
```

#### 如何在CLI模式下运行OwOFrame?
打开命令行 `CMD` 或任意终端之后, 进入OwOFrame的工程路径, 在控制台输入 `owo [command]` 即可.<br/>
> 注意: 直接使用 `owo` 指令的前提是, 当前项目文件夹的根目录路径已经在系统的全局环境变量中设置.

![截图展示](.repo-data/Linux_owo_command.png)

------

## 项目声明
&copy; 2016-2023 [`OwOBlog-DGMT`](https://www.owoblog.com). Please comply with the open source license of this project for modification, derivative or commercial use of this project.

> My Contacts:
- Website: [`HanskiJay`](https://www.owoblog.com)
- Telegram: [`HanskiJay`](https://t.me/HanskiJay)
- E-Mail: [`HanskiJay`](mailto:support@owoblog.com)


## 快给这个项目一个Star吧!
[![Stargazers over time](https://starchart.cc/Tommy131/OwOFrame.svg)](https://starchart.cc/Tommy131/OwOFrame)
