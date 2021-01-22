<?php
namespace backend\system\utils\I18n;
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license	GNU General Public License 2.0
 * @version	$Id: I18n.php 106 2008-04-11 02:23:54Z magike.net $
 */


/**
 * 国际化字符翻译API
 *
 * @package I18nAPI
 */
class I18nAPI
{
	private static $instance = \null;
	public static $charset = 'UTF-8';
	
	public function __construct()
	{
		self::$instance = $this;
	}
	
	/**
	 * I18n function
	 *
	 * @param string $string 需要翻译的文字
	 * @return string
	 */
	public static function _t(string $string)
	{
		if(func_num_args() <= 1)
		{
			return I18n::translate($string);
		}
		else
		{
			$args = func_get_args();
			array_shift($args);
			return vsprintf(I18n::translate($string), $args);
		}
	}

	/**
	 * I18n function, translate and echo
	 *
	 * @param string $string 需要翻译并输出的文字
	 * @return void
	 */
	public static function _e()
	{
		$args = func_get_args();
		echo call_user_func_array([self::$instance, '_t'], $args);
	}

	/**
	 * 针对复数形式的翻译函数
	 *
	 * @param string $single 单数形式的翻译
	 * @param string $plural 复数形式的翻译
	 * @param integer $number 数字
	 * @return string
	 */
	public static function _n(string $single, string $plural, int $number)
	{
		return str_replace('%d', $number, I18n::ngettext($single, $plural, $number));
	}
}
?>