<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____  
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___| 
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |     
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _  
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| | 
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/ 
	
	* Copyright (c) 2015-2019 OwOBlog-DGMT All Rights Reserevd.
	* Developer: HanskiJay(Teaclon)
	* Telegram: https://t.me/HanskiJay E-Mail: support@owoblog.com
	*
	* 引导文件

************************************************************************/

declare(strict_types=1);
namespace OwOBootstrap
{
	use backend\OwOFrame;
	use backend\system\exception\ExceptionOutput;
	use backend\system\http\Session;
	use backend\system\plugin\PluginLoader;
	use backend\system\route\ClientRequestFilter;
	use backend\system\utils\{ClassLoader, LogWriter};

	/* PHP Environment Checker */
	if(version_compare(PHP_VERSION, "7.1.0") === -1) writeLogExit("OwOBlogWebFrame need to run at high PHP version, minimum 7.1.");

	$needExts = ["mbstring", "pdo_mysql", "pdo_sqlite"];
	foreach($needExts as $ext) {
		if(!extension_loaded($ext)) {
			writeLogExit("Couldn't find extension '{$ext}'!");
		}
	}
	
	// Define OwOFrame start time;
	if(!defined("START_MICROTIME"))  define("START_MICROTIME",  microtime(true));
	// Define Timezone;
	if(!defined('TIME_ZONE'))        define('TIME_ZONE',        'Europe/Berlin');
	// Define OwOFrame start time;
	if(!defined("APP_VERSION"))      define("APP_VERSION",      "dev20210218@v1.0.0");
	// Project root directory (absolute path);
	if(!defined("ROOT_PATH"))        define("ROOT_PATH",        dirname(realpath(dirname(__FILE__)), 2) . DIRECTORY_SEPARATOR);
	// The Back-End source code is stored in the root directory (absolute path); here you need to check whether http can be accessed;
	if(!defined("__BACKEND__"))      define("__BACKEND__",      ROOT_PATH . "backend" . DIRECTORY_SEPARATOR);
	// Define Common path(absolute path);
	if(!defined("COMMON_PATH"))      define("COMMON_PATH",      __BACKEND__ . "common" . DIRECTORY_SEPARATOR);
	// Define Plugin path(absolute path);
	if(!defined("PLUGIN_PATH"))      define("PLUGIN_PATH",      __BACKEND__ . "plugin" . DIRECTORY_SEPARATOR);
	// Define configuration files path(absolute path);
	if(!defined("CONFIG_PATH"))      define("CONFIG_PATH",      COMMON_PATH . 'configuration' . DIRECTORY_SEPARATOR);
	// Runtime directory for Back-End(relative path);
	if(!defined("RUNTIME_PATH"))     define("RUNTIME_PATH",     "runtime" . DIRECTORY_SEPARATOR);
	// Temp files directory for Back-End(relative path);
	if(!defined("TMP_PATH"))         define("TMP_PATH",         __BACKEND__ . "tmp" . DIRECTORY_SEPARATOR);
	// Log file directory (relative path);
	if(!defined("LOG_PATH"))         define("LOG_PATH",         TMP_PATH . "log" . DIRECTORY_SEPARATOR);
	// Check whether the current environment supports mbstring extension;
	if(!defined("__MB_SUPPORTED__")) define('__MB_SUPPORTED__', function_exists('mb_get_info') && function_exists('mb_regex_encoding'));
	// Define whether connect to database automaticly;
	if(!defined("DEFAULT_DATABASE_CONNECT")) define("DEFAULT_DATABASE_CONNECT", false);
	// Define whether NO DEBUG_MODE then define it;
	if(!defined("DEBUG_MODE"))       define("DEBUG_MODE", false);
	

	/* System Widgets */;
	require_once(__BACKEND__ . "system" . DIRECTORY_SEPARATOR . "ConfigurationParser.php");
	require_once(__BACKEND__ . "system" . DIRECTORY_SEPARATOR . "functions.php");

	// Init Global Configuration;
	if(!defined("GLOBAL_CONFIG")) define("GLOBAL_CONFIG", loadConfig(CONFIG_PATH . 'global.ini'));

	// get_include_path: Get the current environment variables;
	// PATH_SEPARATOR: 路径分隔符, include多个路径使用, WINNT使用 ";" 分离路径; LINUX使用 ":" 分离路径;
	// PATH_SEPARATOR: Path separator, include multiple paths, WINNT uses ";" to separate paths; LINUX uses ":" to separate paths;
	// set_include_path(get_include_path() . PATH_SEPARATOR . __BACKEND__ . PATH_SEPARATOR . ROOT_PATH);
	
	// Use ClassLoader.php;
	if(!defined("__CLASS_LOADER__")) define("__CLASS_LOADER__", __BACKEND__ . 'system' . DIRECTORY_SEPARATOR . "utils" . DIRECTORY_SEPARATOR . "ClassLoader.php");
	(!file_exists(__CLASS_LOADER__)) ? writeLogExit("Cannot find File ClassLoader.php!") : require_once(__CLASS_LOADER__);
	
	classLoader()->addPath(dirname(__BACKEND__));
	classLoader()->register(true);
	OwOFrame::checkEnvironment();
	set_error_handler([ExceptionOutput::class, 'ErrorHandler'], E_ALL);
	set_exception_handler([ExceptionOutput::class, 'ExceptionHandler']);

	if(OwOFrame::isRunningWithCGI()) {
		foreach(["DEBUG_MODE", "LOG_ERROR" , "DEFAULT_APP_NAME", "DENY_APP_LIST", "USE_REDIS_SESSION", "REDIS_SERVER", "REDIS_SERVER_PASSWD"] as $define) {
			if(!defined($define)) {
				writeLogExit("Constant parameter '{$define}' not found!");
			}
		}
	}


	function writeLogExit(string $msg, string $style = '')
	{
		if(OwOFrame::isRunningWithCGI()) {
			if(defined('LOG_ERROR') && LOG_ERROR) {
				$msg = str_replace(["<br/>", "<br>"], PHP_EOL, strip_tags($msg, '<br><br/>'));
				LogWriter::setFileName('owoblog_error.log');
				LogWriter::write($msg, $prefix, $level);
				$logged = '<span id="logged">--- Logged ---</span>';
			} else {
				$logged = '';
			}
			$debug = @array_shift(debug_backtrace());
			echo str_replace(
				['{logged}', '{type}', '{message}', '{file}', '{line}', '{trace}', '{runTime}'],
				[$logged, 'OwOError', $msg, $debug['file'], $debug['line'], '[#null]', runTime()],
			ExceptionOutput::getTemplate());
		}
		exit();
	}

	function logger(string $msg, string $prefix = 'OwOCLI', string $level = 'INFO')
	{
		LogWriter::setFileName(OwOFrame::isRunningWithCLI() ? 'owoblog_cli_run.log' : 'owoblog_run.log');
		LogWriter::write($msg, $prefix, $level);
	}

	function ask(string $output, $default = null)
	{
		logger($output . (!is_null($default) ? "[Default: {$default}]" : ''));
		return trim(fgets(STDIN) ?? $default);
	}

	function runTime() 
	{
		return round(microtime(true) - START_MICROTIME, 7);
	}

	function useJsonFormat()
	{
		return defined('GLOBAL_USE_JSON_FORMAT') && GLOBAL_USE_JSON_FORMAT;
	}

	function classLoader()
	{
		static $classLoader;
		if(!$classLoader instanceof ClassLoader) {
			$classLoader = new ClassLoader();
		}
		return $classLoader;
	}

	function request()
	{
		static $static;
		if(!$static instanceof ClientRequestFilter) {
			$static = new ClientRequestFilter;
		}
		return $static;
	}
	
	function start(bool $httpMode = true)
	{
		if(defined('HAS_STARTED') && HAS_STARTED) return;
		else define('HAS_STARTED', true);

		try {
			date_default_timezone_set(TIME_ZONE);

			// File Upload permission;
			# ini_set('file_uploads', '1');
			# ini_set('upload_max_filesize', '1000m');
			# ini_set('post_max_size', '1000m');

			if(OwOFrame::isRunningWithCLI()) {
				logger('§3--------------------------------------------------------------');
				logger('§3OwOFrame is running with CLI Mode now. Service is starting.');
			}

			require_once(__BACKEND__ . "vendor" . DIRECTORY_SEPARATOR . "autoload.php");
			if(DEFAULT_DATABASE_CONNECT) \backend\system\db\DbConfig::init();
			// Active PluginLoader;
			PluginLoader::setPath(__BACKEND__ . "plugin" . DIRECTORY_SEPARATOR);
			PluginLoader::autoLoad();
			// Active AppManager;
			\backend\system\app\AppManager::setPath(__BACKEND__ . "application" . DIRECTORY_SEPARATOR);

			if($httpMode) {
				if(ob_get_level() == 0) ob_start();
				Session::start();
				request()->checkValid();
				// Start Listening from http uri;
				\backend\system\route\Router::dispath();
			}
		} catch(\Throwable $e) {
			ExceptionOutput::ExceptionHandler($e);
		}
	}

	function stop($code = null)
	{
		exit($code);
	}
}
?>
