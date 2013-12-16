<?php

class Soup_Helper {
	
	private static $_loadedModelFiles = array();
	
	public static function loadModel($className, $path = null)
	{
		self::$_loadedModelFiles[$className] = $path;
	}
	
	public static function getLoadedModelFiles()
	{
		return self::$_loadedModelFiles;
	}
	
	public static function makeDirectories($path, $mode = 0777)
	{
		if ( ! $path) {
			return false;
		}
	
		if (is_dir($path) || is_file($path)) {
			return true;
		}
	
		return mkdir(trim($path), $mode, true);
	}
}