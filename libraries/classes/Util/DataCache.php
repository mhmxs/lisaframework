<?php
/**
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; version 3 of the License.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
*/

/**
 * DataCache class
  * Handling cache files create, get, del, clear all cache
  * @package Util
  * @author kovacsricsi
  */
class DataCache
{
	/**
	 * cacheDir
     * @access public
	 * @static
     * @staticvar string
	 */
	public static $cacheDir = null;

	/**
	 * Constructor protecte the classs, because it is stataic.
	 * @access private
	 * @throws Exception
	 * @return void
	 */
	private function __construct()
	{
		throw new Exception("Illegal operation, this class is only static class!");
	}

    /**
     * Set cache directory
     * @access public
     * @static
     * @return void
     */
	public static function setCacheDir()
	{
		self::$cacheDir = DIR_DATA_CACHE . "/";
	}

    /**
     * Save cache to cache directory.
     *
     * @param string $id
     * @param mixed $data
     * @return void
     */
	public static function cacheSave($id, $data)
	{
		if (!self::$cacheDir) {
			self::setCacheDir();
		}

		$id = str_replace('_', '/', strtolower($id));
		$dir_a = explode('/',$id);
		array_pop($dir_a); // the last is the filename.
		$dir = self::$cacheDir;

		foreach($dir_a as $c) {
			$dir .= $c.'/';
			if( !is_dir($dir) ) mkdir($dir);
		}

		if(is_file(self::$cacheDir.$id)) touch(self::$cacheDir.$id);
		$tmpFile = self::$cacheDir.$id.'.'.uniqid(rand());

		$f = fopen($tmpFile, 'wb');
		fwrite($f, serialize($data));
		fclose($f);

		if(is_file(self::$cacheDir.$id)) unlink(self::$cacheDir.$id);

		rename($tmpFile, self::$cacheDir.$id);

	}

    /**
     * Load data from cache if the cachetime is valid.
     *
     * @param string $id
     * @param integer $time
     * @return mixed
     */
	public static function cacheGet($id, $time = 300)
	{
		if (!self::$cacheDir) {
			self::setCacheDir();
		}

		$cacheFile = self::$cacheDir.str_replace('_', '/', strtolower($id));

		if(!is_file($cacheFile) or filemtime($cacheFile) < time() - $time) {
			return false;
		} else {
			return unserialize( file_get_contents($cacheFile) );
		}
	}

    /**
     * Delete cache from cache directoy.
     *
     * @access public
     * @static
     * @param string $id
     * @return void
     */
	public static function cacheDel($id)
	{
		if (!self::$cacheDir) {
			self::setCacheDir();
		}

		$cacheFile = self::$cacheDir.str_replace('_', '/', strtolower($id));

		if (is_dir($cacheFile)) {
			self::_removeDirectory($cacheFile);
		}
		elseif (is_file($cacheFile)) {
			unlink($cacheFile);
		}
	}

    /**
     * Delete all cache file.
     *
     * @access public
     * @static
     * @return void
     */
	public static function cacheClear()
	{
		self::_removeDirectory(self::$cacheDir, false);
	}

    /**
     * Delete directory recrusive.
     *
     * @access protected
     * @static
     * @param string $dir
     * @param boolean $thisDel
     * @return void
     */
	protected static function _removeDirectory($dir, $thisDel = true)
	{
		if (!self::$cacheDir) {
			self::setCacheDir();
		}

		if ($handle = opendir($dir)) {
			while (false !== ($item = readdir($handle))) {
				if ($item != '.' and $item != '..') {
					if (is_dir($dir.'/'.$item)) {
						self::_removeDirectory($dir.'/'.$item);
					} else {
						unlink($dir.'/'.$item);
					}
				}
			}
			closedir($handle);
			// need to del or not
			if($thisDel) rmdir($dir);
		}
	}
}
?>