<?php
/*
 *       _____   _          __  _____   _____   _       _____   _____
 *     /  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
 *     | | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
 *     | | | | | | /  | / /   | | | | |  _  { | |     | | | | | |   _
 *     | |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
 *     \_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/
 *
 * Copyright (c) 2023 by OwOTeam-DGMT (OwOBlog).
 * @Author       : HanskiJay
 * @Date         : 2023-02-01 20:16:11
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-01 22:17:52
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);



/**
 * Define framework start running time
 */
define('START_MICROTIME', microtime(true));

/**
 * Define memory real usage
 */
define('MEMORY_USAGE', memory_get_usage(true));

/**
 * Define the GitHub Page
 */
define('GITHUB_PAGE', 'https://github.com/Tommy131/OwOFrame/');

/**
 * Define OwOFrame version
 */
define('OWO_VERSION', '1.0.5-dev');

/**
 * Define OwOFrame Codename
 */
define('OWO_CODE', 'Neuschwamm');

/**
 * Check whether the current environment supports mbstring extension
 */
define('MB_SUPPORTED', extension_loaded('mbstring'));

/**
 * Define root path
 */
define('ROOT_PATH', dirname(realpath(dirname(__FILE__)), 2) . DIRECTORY_SEPARATOR);

/**
 * Define source code path
 */
define('OWO_PATH', ROOT_PATH . 'src' . DIRECTORY_SEPARATOR . 'owoframe' . DIRECTORY_SEPARATOR);

/**
 * Define ClassLoader file path
 */
define('CLASS_LOADER', ROOT_PATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

// Start to check composer status and load autoload file
if(!file_exists(CLASS_LOADER)) {
    exit("Initialized failed: PHP-Composer isn't installed. Please run the command 'composer install' on '" . ROOT_PATH . "' first!");
}
$classLoader = require_once(CLASS_LOADER);
?>