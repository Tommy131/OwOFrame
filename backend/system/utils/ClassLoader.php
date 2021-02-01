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

************************************************************************/

namespace backend\system\utils;

class ClassLoader
{
	
	/** @var string[] */
	private $lookup;
	/** @var string[] */
	private $classes;
	
	public function __construct()
	{
		$this->lookup = [];
		$this->classes = [];
	}
	
	/**
	 * Adds a path to the lookup list
	 *
	 * @param string $path
	 * @param bool   $prepend
	 */
	public function addPath($path, $prepend = false)
	{
		foreach($this->lookup as $p) {
			if($p === $path) {
				return;
			}
		}
		
		if($prepend) {
			// TODO NULL;
		} else {
			$this->lookup[] = $path;
		}
	}
	
	/**
	 * Removes a path from the lookup list
	 *
	 * @param $path
	 */
	public function removePath($path)
	{
		foreach($this->lookup as $i => $p) {
			if($p === $path) {
				unset($this->lookup[$i]);
			}
		}
	}
	
	/**
	 * Returns an array of the classes loaded
	 *
	 * @return string[]
	 */
	public function getClasses()
	{
		$classes = [];
		foreach($this->classes as $class) {
			$classes[] = $class;
		}
		return $classes;
	}
	
	public function register($prepend = false)
	{
		spl_autoload_register([$this, "loadClass"], true, $prepend);
	}
	
	/**
	 * Called when there is a class to load
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function loadClass($name)
	{
		$path = $this->findClass($name);
		if($path !== null) {
			include($path);
			if(!class_exists($name, false) and !interface_exists($name, false) and !trait_exists($name, false)) {
				return false;
			}
			
			if(method_exists($name, "onClassLoaded") and (new ReflectionClass($name))->getMethod("onClassLoaded")->isStatic()) {
				$name::onClassLoaded();
			}
			
			$this->classes[] = $name;
			return true;
		}
		return false;
	}
	
	/**
	 * Returns the path for the class, if any
	 *
	 * @param string $name
	 *
	 * @return string|null
	 */
	public function findClass($name)
	{
		$components = explode("\\", $name);
		$baseName = implode(DIRECTORY_SEPARATOR, $components);
		
		foreach($this->lookup as $path) {
			if(file_exists($path . DIRECTORY_SEPARATOR . $baseName . ".php")) {
				return $path . DIRECTORY_SEPARATOR . $baseName . ".php";
			}
		}
		return null;
	}
}

?>