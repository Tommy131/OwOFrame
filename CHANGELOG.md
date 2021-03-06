
## OwOframe's CHANGELOG (Total `83` Commits)


↓ `[dev@v1.0.1, origin/dev@v1.0.1]` ↓

------

`dev20210218@v1.0.0` <= `20210306_dev@v1.0.1`:
! 重大更新如下:
- 重新构建了项目结构
- 采用 `Composer包依赖` 模式及 `PSR-4` 进行重构
- 尝试将全部模块/类松耦合化
- 第一次尝试将系统及外部的Application分离
- 编写了新的 `Container`, `Bootstratper` 及 `Manager加载器` 作为系统底层
- 修复了一些小BUG
- 引入了 `Composer` 中的 `ClassLoader`
- 修复了在CLI模式下无法抓取异常的BUG
- 将部分代码从 `(dev@v1.0.0)` 迁移到新分支 `(dev@v1.0.1)`
- 更新了版权信息(不影响开源许可使用)
- Updated CHANGELOG.md

------


↓ `[dev@v1.0.0, origin/dev@v1.0.0]` ↓

------

`dev20210218@v1.0.0` <= `dev202100302@v1.0.0`:
- 在 `PluginModule` 中添加了插件加载状态方法

`dev20210218@v1.0.0` <= `dev20210219@v1.0.0`:
- 移除了自带的模型控制器抽象类
- 更新了 `composer.json` 中的错误
- Updated CHANGELOG.md

`dev20210218@v1.0.0`:
- !修复了严重漏洞问题: HTTP访问 `owo` 文件直接被下载问题
- 更新了 `README.md` 中的部分注释
- 加入了系统环境初始化检测方法
- Updated CHANGELOG.md
- 更新了版本号至 `dev20210218@v1.0.0`

`dev20210210@v1.0.0` <= `dev20210216@v1.0.0`:
- 修改了一部分有关Session的功能
- Updated CHANGELOG.md

`dev20210210@v1.0.0` <= `dev20210214@v1.0.0`:
- 开发了 `Redis` 基础工具类
- 修复了Session使用redis连接存在的bug
- 新增了一些工具方法
- 更新了异常处理输出的逻辑
- Updated CHANGELOG.md

`dev20210210@v1.0.0` <= `dev20210213@v1.0.0`:
- 更改了 `Cookie`, `Session` 的文件路径
- 对应修改了一些内容

`dev20210210@v1.0.0` <= `dev20210210@v1.0.0`:
- 增加了文件 `CHANGELOG.md`
- 修改了路由处理Response的方法

`dev20210210@v1.0.0` <= `dev20210209@v1.0.0`:
- 增加了一个Response处理类
- 将其他代码对Response类支持
- 修复了一些东西
- 更新了版本号至 `dev20210210@v1.0.0`

`dev20210201@v1.0.0` <= `dev20210207@v1.0.0`:
- 添加了基本的XSS过滤方法
- 增加了一个独立的API处理模块
- 修复了一些东西
- 更新了使用介绍

`20210203@v1.0.0` -> `dev20210201@v1.0.0`:
- 删除了I8n类
- 修改版本号至 `dev20210201@v1.0.0`

`20210201@v1.0.0` <= `20210131@v1.0.0`:
- 更新了所有文件中的联系方式
- 增加了开源使用许可证

`20210131@v1.0.0`:
- 删除了旧代码
- 更新了使用介绍

`20210127@v1.0.0`:
- 修复了一些bug
- 更改了一些方法命名
- 添加了ANSI控制器模块

`20210126@v1.0.0`:
- 日常修复一些bug

`20210123@v1.0.0`:
- 新增了插件外置处理模块
- 修改了一些文件所在的位置
- 修改了一些地方的代码
- 更新了版权信息
- Changed branch name `origin/master` to `origin/dev@v1.0.0`

------


↓ `[master, origin/master]` ↓

------

`20210122@v1.0.0`:
- 提交代码至GitHub仓库
- Tagged `20210122@V1.0.0`
