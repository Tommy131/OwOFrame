<?php

/*********************************************************************
	 _____   _          __  _____   _____   _       _____   _____
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/

	* Copyright (c) 2015-2021 OwOBlog-DGMT.
	* Developer: HanskiJay(Tommy131)
	* Telegram:  https://t.me/HanskiJay
	* E-Mail:    support@owoblog.com
	* GitHub:    https://github.com/Tommy131

**********************************************************************/

// Check PHP version;
if(version_compare(PHP_VERSION, '7.1.0') === -1) {
	die('[PHP_VERSION_TO_LOW] OwOWebFrame need to run at higher PHP version, minimum PHP 7.1.0.');
}

// Define framework start running time;
if(!defined('START_MICROTIME'))  define('START_MICROTIME',  microtime(true));

// Define the GitHub Page;
if(!defined('GITHUB_PAGE'))      define('GITHUB_PAGE',     'https://github.com/Tommy131/OwOFrame/');

// Define OwOFrame start time;
if(!defined('FRAME_VERSION'))    define('FRAME_VERSION',   '1.0.2-dev');

// Check whether the current environment supports mbstring extension;
if(!defined('MB_SUPPORTED'))     define('MB_SUPPORTED',    extension_loaded('mbstring'));

// Define root path;
if(!defined('ROOT_PATH'))        define('ROOT_PATH',       dirname(realpath(dirname(__FILE__)), 1) . DIRECTORY_SEPARATOR);

// Define source code path;
if(!defined('OWO_PATH'))         define('OWO_PATH',        __DIR__ . DIRECTORY_SEPARATOR . 'owoframe' . DIRECTORY_SEPARATOR);

// Define ClassLoader file path;
if(!defined('CLASS_LOADER'))     define('CLASS_LOADER',    ROOT_PATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

// Define public path;
if(!defined('PUBLIC_PATH'))      define('PUBLIC_PATH',     ROOT_PATH . 'public' . DIRECTORY_SEPARATOR);

// Define storage path;
if(!defined('STORAGE_PATH'))     define('STORAGE_PATH',    ROOT_PATH . 'storages' . DIRECTORY_SEPARATOR);

// Define storage path;
if(!defined('STORAGE_A_PATH'))   define('STORAGE_A_PATH',  ROOT_PATH . 'storages' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

// Define module path;
if(!defined('MODULE_PATH'))      define('MODULE_PATH',     STORAGE_PATH . 'modules' . DIRECTORY_SEPARATOR);

// Define framework path;
if(!defined('FRAMEWORK_PATH'))   define('FRAMEWORK_PATH',  STORAGE_PATH . 'system' . DIRECTORY_SEPARATOR);

// Define cache path;
if(!defined('F_CACHE_PATH'))     define('F_CACHE_PATH',    FRAMEWORK_PATH . 'cache' . DIRECTORY_SEPARATOR);

// Define configuration path;
if(!defined('CONFIG_PATH'))      define('CONFIG_PATH',     FRAMEWORK_PATH . 'config' . DIRECTORY_SEPARATOR);

// Define log files path;
if(!defined('LOG_PATH'))         define('LOG_PATH',        FRAMEWORK_PATH . 'logs' . DIRECTORY_SEPARATOR);

// Define application path;
if(!defined('APP_PATH'))         define('APP_PATH',        ROOT_PATH . 'application' . DIRECTORY_SEPARATOR);

// Define cache files path for application;
if(!defined('A_CACHE_PATH'))     define('A_CACHE_PATH',    STORAGE_PATH . 'application' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);

// Define public static resource path;
if(!defined('RESOURCE_PATH'))    define('RESOURCE_PATH',   STORAGE_PATH . 'resources' . DIRECTORY_SEPARATOR);

// Start to check composer status and load autoload file;
if(!file_exists(CLASS_LOADER)) {
	exit('[AutoLoader/ERROR] Please execute command \'composer install\' at root path \'' . ROOT_PATH . '\' at first!');
}
$classLoader = require_once(CLASS_LOADER);

