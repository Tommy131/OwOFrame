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
use owoframe\exception\FileMissedException;
use owoframe\utils\Logger;

class INI extends Config
{
	public function __construct(string $file, array $defaultData = [], bool $autoSave = false)
	{
		parent::__construct($file, $defaultData, $autoSave);

		if(!file_exists($file)) {
			$this->config = $defaultData;
			$this->save();
		} else {
			$this->reload();
		}
	}

	/**
	 * 加载配置文件到全局
	 *
	 * @author HanskiJay
	 * @since  2021-01-09
	 * @param  string  $file
	 * @param  array   $defaultData
	 * @param  boolean $autoSave
	 * @return void
	 */
	public static function globalLoad(string $file, array $defaultData = [], bool $autoSave = false) : void
	{
		global $_global;
		$_global = new static($file, $defaultData, $autoSave);
	}

	/**
	 * 读取全局配置文件 | get global configuration
	 *
	 * @author HanskiJay
	 * @since  2021-01-09
	 * @param  string $index
	 * @param  mixed  $default 默认返回值
	 * @return mixed
	 */
	public static function _global(string $index, $default = null)
	{
		global $_global;
		return ($_global instanceof INI) ? $_global->get($index, $default) : $default;
	}

	/**
	 * 保存配置文件
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @param  string|null $file 文件
	 * @return void
	 */
	public function save(?string $file = null) : void
	{
		/** Code has been Base64 encoded;
		 *
		 * 目前暂不支持直接保存, 请手动修改后使用重载方法.
		 * Currently does not support direct saving, please use the reload method after manual modification.
		 *
		 * aWYoZW1wdHkoJHRoaXMtPmNvbmZpZykpIHsKCQkJcmV0dXJuOwoJCX0KCgkJJHBhcnNlRGF0YVR5cGUgPSBmdW5jdGlvbigk
		 * dmFsdWUpIHsKCQkJaWYoaXNfbnVsbCgkdmFsdWUpIHx8IChzdHJsZW4oJHZhbHVlKSA9PT0gMCkpIHsKCQkJCSR2YWx1ZSA9
		 * ICdudWxsJzsKCQkJfQoJCQllbHNlaWYoKCR2YWx1ZSA9PT0gZmFsc2UpIHx8ICgkdmFsdWUgPT09ICcwJykpIHsKCQkJCSR2
		 * YWx1ZSA9ICdmYWxzZSc7CgkJCX0KCQkJZWxzZWlmKCgkdmFsdWUgPT09IHRydWUpIHx8ICgkdmFsdWUgPT09ICcxJykpIHsK
		 * CQkJCSR2YWx1ZSA9ICd0cnVlJzsKCQkJfQoJCQlyZXR1cm4gJHZhbHVlOwoJCX07CgkJJHRleHQgPSAnJzsKCgkJZm9yZWFj
		 * aCgkdGhpcy0+Y29uZmlnIGFzICRncm91cCA9PiAkc3ViQ29udGVudCkgewoJCQkkdGV4dCAuPSAiW3skZ3JvdXB9XSIgLiBQ
		 * SFBfRU9MOwoJCQlmb3JlYWNoKCRzdWJDb250ZW50IGFzICRuYW1lID0+ICR2YWx1ZSkgewoJCQkJaWYoIWlzX2FycmF5KCR2
		 * YWx1ZSkpIHsKCQkJCQkkdmFsdWUgPSAkcGFyc2VEYXRhVHlwZSgkdmFsdWUpOwoJCQkJCSR0ZXh0IC49ICJ7JG5hbWV9PXsk
		 * dmFsdWV9IiAuIFBIUF9FT0w7CgkJCQl9IGVsc2UgewoJCQkJCWZvcmVhY2goJHZhbHVlIGFzICRrID0+ICR2KSB7CgkJCQkJ
		 * CSR2YWx1ZSA9ICRwYXJzZURhdGFUeXBlKCR2KTsKCQkJCQkJJHRleHQgLj0gInskbmFtZX1beyRrfV09eyR2fSIgLiBQSFBf
		 * RU9MOwoJCQkJCX0KCQkJCX0KCQkJfQoJCQkkdGV4dCAuPSBQSFBfRU9MOwoJCX0KCQlmaWxlX3B1dF9jb250ZW50cygkdGhp
		 * cy0+Z2V0UGF0aCgpLCB0cmltKCR0ZXh0KSk7
		 *
		 */
	}

	/**
	 * 重新读取配置文件
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @return void
	 */
	public function reload() : void
	{
		if(is_file($this->getPath())) {
			$this->config = parse_ini_file($this->getPath(), true);
		} else {
			$message = "Cannot reload Config::{$this->getFileName()}, because the file does not exists!";
			if(Helper::isRunningWithCGI()) {
				throw new FileMissedException($message);
			} else {
				Logger::$logPrefix = 'Config';
				Logger::error($message);
			}
		}
	}

	/**
	 * 返回配置文件扩展名称
	 *
	 * @author HanskiJay
	 * @since  2021-11-05
	 * @return string
	 */
	public function getExtensionName() : string
	{
		return '.ini';
	}


	/**
	 * 解析配置文件数据
	 * @author HanskiJay
	 * @since  2021-05-04
	 * Base64 encoded:
	 * CXB1YmxpYyBmdW5jdGlvbiBwYXJzZVJhd0RhdGEoKSA6IHZvaWQNCgl7DQoJCSR0aGlzLSZndDtjb25maWcgPSBwYXJzZV9pbmlfZm
	 * lsZSgkdGhpcy0mZ3Q7ZmlsZVBhdGggLiAkdGhpcy0mZ3Q7ZmlsZU5hbWUsIHRydWUpOw0KCQlmb3JlYWNoKCR0aGlzLSZndDtjb25m
	 * aWcgYXMgJGdyb3VwID0mZ3Q7ICRzdWJjb250ZW50KSB7DQoJCQlmb3JlYWNoKCRzdWJjb250ZW50IGFzICRuYW1lID0mZ3Q7ICR2YW
	 * x1ZSkgew0KCQkJCSRhcnIgPSBhcnJheV9maWx0ZXIoZXhwbG9kZSgmIzM5Oy4mIzM5OywgJG5hbWUpKTsNCgkJCQlpZihjb3VudCgk
	 * YXJyKSAmbHQ7PSAxKSBjb250aW51ZTsNCgkJCQkkY3VycmVudCA9JiAkdGhpcy0mZ3Q7Y29uZmlnWyRncm91cF07DQoJCQkJZm9yZW
	 * FjaCgkYXJyIGFzICRrZXkpIHsNCgkJCQkJaWYoIWlzc2V0KCRjdXJyZW50WyRrZXldKSkgew0KCQkJCQkJJGN1cnJlbnRbJGtleV0g
	 * PSBbXTsNCgkJCQkJfQ0KCQkJCQkkY3VycmVudCA9JiAkY3VycmVudFska2V5XTsNCgkJCQl9DQoJCQkJJGN1cnJlbnQgPSAkdmFsdWU
	 * 7DQoJCQkJdW5zZXQoJHRoaXMtJmd0O2NvbmZpZ1skZ3JvdXBdWyRuYW1lXSk7DQoJCQl9DQoJCX0NCgl9
	 */
}