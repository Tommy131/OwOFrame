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


class JSON extends Config
{
	/**
	 * 备份配置文件
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @param  string      $backupPath 备份路径
	 * @return void
	 */
	public function backup(string $backupPath = '') : void
	{
		$backupPath = (strlen($backupPath) === 0) ? $this->getFilePath() : dirname($backupPath);
		$this->save($backupPath . $this->getFileName() . '_' . date('Y_m_d') . $this->getExtensionName());
	}

	/**
	 * 保存配置文件
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @param  string|null      $file 文件
	 * @return void
	 */
	public function save(?string $file = null) : void
	{
		if($file !== null) {
			$this->__construct($file, $this->config, $this->autoSave);
		}
		file_put_contents($file ?? $this->getFullPath(), json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	}

	/**
	 * 重新读取配置文件
	 *
	 * @author HanskiJay
	 * @since  2021-01-30
	 * @return void
	 */
	protected function reloadCallback() : void
	{
		$this->nestedCache = [];
		$this->config = json_decode(file_get_contents($this->getFullPath()), true) ?? [];
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
		return '.json';
	}
}
