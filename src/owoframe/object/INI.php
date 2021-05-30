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

declare(strict_types=1);
namespace owoframe\object;

use owoframe\helper\Helper;

class INI extends Config
{
	public function __construct(string $file, array $defaultData = [], bool $autoSave = false)
	{
		parent::__construct($file, $defaultData, $autoSave);
		$this->fileName = $this->fileName . '.ini';

		if(!file_exists($file)) {
			$this->config = $defaultData;
			$this->save();
		} else {
			$this->reload();
		}
	}

	public static function globalLoad(string $file, array $defaultData = [], bool $autoSave = false) : void
	{
		global $_global;
		$_global = new static($file, $defaultData, $autoSave);
	}

	/**
	 * @method      _global
	 * @description 读取全局配置文件 | get global configuration;
	 * @author      HanskiJay
	 * @doenIn      2021-01-09
	 * @param       string[str]
	 * @param       mixed[default|默认返回值]
	 * @return      mixed
	 */
	public static function _global(string $str, $default = null)
	{
		global $_global;
		return ($_global instanceof INI) ? $_global->get($str, $default) : $default;
	}

	/**
	 * @method      save
	 * @description 保存配置文件
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @param       string|null      $file 文件
	 * @return      void
	 */
	public function save(?string $file = null) : void
	{
		if(empty($this->config)) {
			return;
		}
		
		$parseDataType = function($value) {
			if(is_null($value) || (strlen($value) === 0)) {
				$value = 'null';
			}
			elseif(($value === false) || ($value === '0')) {
				$value = 'false';
			}
			elseif(($value === true) || ($value === '1')) {
				$value = 'true';
			}
			return $value;
		};
		$text = '';

		foreach($this->config as $group => $subcontent) {
			$text .= "[{$group}]" . PHP_EOL;
			foreach($subcontent as $name => $value) {
				if(!is_array($value)) {
					$value = $parseDataType($value);
					$text .= "{$name}={$value}" . PHP_EOL;
				} else {
					foreach($value as $k => $v) {
						$value = $parseDataType($v);
						$text .= "{$name}[{$k}]={$v}" . PHP_EOL;
					}
				}
			}
			$text .= PHP_EOL;
		}
		file_put_contents($this->filePath . $this->fileName, trim($text));
	}

	/**
	 * @method      reload
	 * @description 重新读取配置文件
	 * @author      HanskiJay
	 * @doenIn      2021-01-30
	 * @return      void
	 */
	public function reload() : void
	{
		if(is_file($this->filePath . $this->fileName)) {
			$this->config = parse_ini_file($this->filePath . $this->fileName, true);
		} else {
			Helper::logger("Cannot reload Config::{$this->fileName} because the file does not exists!", 'Config', 'ERROR');
		}
	}


	/**
	 * @method      parseRawData (Draft)
	 * @description 解析配置文件数据
	 * @author      HanskiJay
	 * @doenIn      2021-05-04
	 * Base64 encoded:
	 * CXB1YmxpYyBmdW5jdGlvbiBwYXJzZVJhd0RhdGEoKSA6IHZvaWQNCgl7DQoJCSR0aGlzLSZndDtjb25maWcgPSBwYXJzZV9pbmlfZmlsZSgkdGhpcy0mZ3Q7ZmlsZVBhdGggLiAkdGhpcy0mZ3Q7ZmlsZU5hbWUsIHRydWUpOw0KCQlmb3JlYWNoKCR0aGlzLSZndDtjb25maWcgYXMgJGdyb3VwID0mZ3Q7ICRzdWJjb250ZW50KSB7DQoJCQlmb3JlYWNoKCRzdWJjb250ZW50IGFzICRuYW1lID0mZ3Q7ICR2YWx1ZSkgew0KCQkJCSRhcnIgPSBhcnJheV9maWx0ZXIoZXhwbG9kZSgmIzM5Oy4mIzM5OywgJG5hbWUpKTsNCgkJCQlpZihjb3VudCgkYXJyKSAmbHQ7PSAxKSBjb250aW51ZTsNCgkJCQkkY3VycmVudCA9JiAkdGhpcy0mZ3Q7Y29uZmlnWyRncm91cF07DQoJCQkJZm9yZWFjaCgkYXJyIGFzICRrZXkpIHsNCgkJCQkJaWYoIWlzc2V0KCRjdXJyZW50WyRrZXldKSkgew0KCQkJCQkJJGN1cnJlbnRbJGtleV0gPSBbXTsNCgkJCQkJfQ0KCQkJCQkkY3VycmVudCA9JiAkY3VycmVudFska2V5XTsNCgkJCQl9DQoJCQkJJGN1cnJlbnQgPSAkdmFsdWU7DQoJCQkJdW5zZXQoJHRoaXMtJmd0O2NvbmZpZ1skZ3JvdXBdWyRuYW1lXSk7DQoJCQl9DQoJCX0NCgl9
	 */
}