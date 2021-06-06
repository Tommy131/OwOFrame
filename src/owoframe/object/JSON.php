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

class JSON extends Config
{

	public function __construct(string $file, array $defaultData = [], bool $autoSave = false)
	{
		parent::__construct($file, $defaultData, $autoSave);
		$this->fileName = $this->fileName . '.json';

		if(!file_exists($file)) {
			$this->config = $defaultData;
			$this->save();
		} else {
			$this->reload();
		}
	}

	/**
	 * @method      backup
	 * @description 备份配置文件
	 * @author      HanskiJay
	 * @doneIn      2021-01-30
	 * @param       string      $backupPath 备份路径
	 * @return      void
	 */
	public function backup(string $backupPath = '') : void
	{
		$backupPath = (strlen($backupPath) === 0) ? $this->filePath : dirname($backupPath);
		$this->save($backupPath . @array_shift(explode('.', $this->fileName)) . '_' . date('Y_m_d') . '.json');
	}

	/**
	 * @method      save
	 * @description 保存配置文件
	 * @author      HanskiJay
	 * @doneIn      2021-01-30
	 * @param       string|null      $file 文件
	 * @return      void
	 */
	public function save(?string $file = null) : void
	{
		if($file !== null) {
			$this->__construct($file, $this->config, $this->autoSave);
		}
		file_put_contents($file ?? $this->filePath . $this->fileName, json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	}

	/**
	 * @method      reload
	 * @description 重新读取配置文件
	 * @author      HanskiJay
	 * @doneIn      2021-01-30
	 * @return      void
	 */
	public function reload() : void
	{
		if(is_file($this->filePath . $this->fileName)) {
			$this->nestedCache = [];
			$this->config = json_decode(file_get_contents($this->filePath . $this->fileName), true) ?? [];
		} else {
			Helper::logger("Cannot reload Config::{$this->fileName} because the file does not exists!");
		}
	}
}
