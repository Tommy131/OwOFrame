# OwOFrame

![CodeFactor](https://www.codefactor.io/repository/github/tommy131/owoframe/badge) ![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg) [Learn More](https://opensource.org/licenses/Apache-2.0)

OwO! `OwOFrame` is developed based on the `MVC (Model-Views-Controller)` model. The standard of the framework is drawn from the current popular PHP-MVC framework to a certain extent. This project is only my personal practice project.

If you think this repository is helpful to you, please give this repository a `Star` QWQ

Donation: <https://owoblog.com/donation/>

[中文版简介请点这里](README_CHS.md)

## What can I do?

OwOFrame is a small framework that I developed using my free time. Of course, there are many shortcomings. The functions currently supported by this framework are listed below:

### Basic System Components

- [x] `!!!IDE Friendly!!!`  All comments display nicely in `Visual Studio Code`
- [x] `Application`         This framework is a multi-application coexistence framework, and the current application is identified by `HTTP_URI`
- [x] `Console`             Support some operations in the terminal through `CommandLine`
- [x] `Config`              Configuration file parsing class
- [x] `EventManager`        Event Manager (hook method)
- [x] `Exception`           Error and exception capture and Stack output
- [x] `ModuleLoader`        Module loader (Application independent and flexible management, in global mode)
- [x] `Language`            ~~Custom language pack support~~ (rewriting)
- [x] `Logger`              Support the most basic logging
- [x] `Redis`               ~~Support basic Redis operations~~ (rewriting)
- [x] `Route`               Route analysis/management (personalized route binding settings)
- [x] `Template`            Back-end rendering template (basic functions have been completed, and advanced functions have been developed quickly o(*￣▽￣*)o)
- [x] `TextColorOutput`     Support ANSI control code output color in CMD & Shell
- [x] `WMI`                 WMI operation class written for Windows system
- [x] `Cookie`              A common cookie processing class
- [x] `Session`             A common Session processing class
- [x] `EmptyAppGenerator`   One-click generation of new application templates
- [x] `FileUploader`        File upload class support
- [x] `systemFunctions`     A file function that integrates the bottom layer of the system (see the source code for details)
- [x] `httpFunctions`       A file function that integrates the bottom layer of HTTP (see the source code for details)

### Third Party Resources

- [x] [`PasswordHash`](http://www.openwall.com/phpass/)
- [x] [`Think-ORM`](https://github.com/top-think/think-orm)

## How to use?

### §1 Installation

- First way can use command `git clone https://github.com/Tommy131/OwOFrame.git` to clone the repository from Github to location.  
- Or you can use the second way with command `composer create-project tommy131/owoframe -s dev` to create the repository to location.

### §2 Next Step

After open `CMD` in Windows or `Shell` in Linux, use command `cd owoframe && composer install` to change work path on owoframe.  
If you used composer to install this repository, you may not to run the command from the top.

## Do I need modify my Web configuration (e.g. for Nginx) ?

Yes. The step(s) please see the below:

``` nginx
# Set your web root path to /public (Example);
root /www/owoframe/public;

location / {
    index index.php index.html;
    try_files $uri $uri/ /index.php$is_args$query_string;
}
```

## How to running my Application in CLI-Mode with OwOFrame?

In the OwOFrame, I built the Command Manager. You can customize the registration management of the implementation Commands, and you can also modify the entry file in the CLI-Mode to touch your project requirements. The basically usage will be called in the root path like `owo [command]`.  
> Note: The premise of using the `owo` command directly is that the root directory path of the current project folder has been set in the system's global environment ($PATH) variables.

![Show](.repo-data/Linux_owo_command.png)

## Statement

&copy; 2016-2023 [`OwOBlog-DGMT`](https://www.owoblog.com). Please comply with the open source license of this project for modification, derivative or commercial use of this project.  
My Contacts:

- Website: [`HanskiJay`](https://www.owoblog.com)
- Telegram: [`HanskiJay`](https://t.me/HanskiJay)
- E-Mail: [`HanskiJay`](mailto:support@owoblog.com)

## Stargazers over time

[![Stargazers over time](https://starchart.cc/Tommy131/OwOFrame.svg)](https://starchart.cc/Tommy131/OwOFrame)
