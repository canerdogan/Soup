<?php
/**
 *
 * Soup Helpers
 *
 * @package     Soup
 * @subpackage  Helper
 * @link        https://github.com/canerdogan/Soup
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @since       1.0
 * @version     1.0
 * @author      Semih TURNA <psychaos@gmail.com>
 */

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