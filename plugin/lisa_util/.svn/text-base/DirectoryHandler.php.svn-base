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
 * Handling directory and file list.
 * @package Util
 * @author nullstring
 */

namespace lisa_util;

class DirectoryHandler {

	/**
	 * Constructor protecte the classs, because it is stataic.
	 * @access private
	 * @throws Exception
	 * @return void
	 */
	private function __construct() {
		throw new \Exception("Illegal operation, this class is only static class!");
	}

	/**
	 * Returns with files in direcotry
	 * @param string $directory
	 * @optional param array $extensions
	 * @optional param string $pattern
	 * you can give the extensions with or without . (dot)
	 * @return array
	 */
	static public function getFiles($directory, $extensions = array()) {
		setType($directory, "string");
		setType($extensions, "array");

		if (!empty($extensions)) {
			foreach ($extensions as $key => $extension) {
				$extensions[$key] = (substr($extension, 0, 1) == "." ) ? $extension : "." . $extension;
			}
		}

		$files = array();
		if (($dh = opendir($directory)) !== false) {

			while (false !== ($file = readdir($dh))) {
				if (is_file($directory . "/" . $file)) {
					if (!empty($extensions)) {
						if (in_array(strrchr($file, "."), $extensions) === true) {
							$files[] = $file;
						}
					} else {
						$files[] = $file;
					}
				}
			}
		}
		sort($files);
		return $files;
	}

	/**
	 * Returns with direcoties in directory
	 * @param string $directory
	 * @param array $exceptions array of dirs not required
	 * @return array
	 */
	static public function getDirs($directory, array $exceptions = null) {
		setType($directory, "string");

		$files = array();

		$nonDirs = array(
			".", ".."
		);

		if (!is_null($exceptions)) {
			$nonDirs = array_merge($nonDirs, $exceptions);
		}

		if (($dh = opendir($directory)) !== false) {
			while (false !== ($file = readdir($dh))) {
				if (is_dir($directory . "/" . $file) && !in_array($file, $nonDirs)) {
					$files[] = $file;
				}
			}
		}

		return $files;
	}

	/**
	 * Returns with directory tree.
	 * @access public
	 * @static
	 * @param string $dir
	 * return array
	 */
	public static function getDirectoryTree($dir) {
		$tree = array();

		foreach (self::getDirs($dir) as $d) {
			$tree[$d] = self::getDirectoryTree($dir . "/" . $d);
		}
		foreach (self::getFiles($dir) as $f) {
			$tree[] = $f;
		}

		return $tree;
	}

	/**
	 * Create directory recursive.
	 * @access public
	 * @static
	 * @param string $pathname
	 * @param integer $mode
	 * @return boolean
	 */
	static public function mkdirRecursive($pathname, $mode = 666) {
		is_dir(dirname($pathname)) || self::mkdirRecursive(dirname($pathname), $mode);

		if (@mkdir($pathname, $mode)) {
			chmod($pathname, $mode);
		}

		return is_dir($pathname);
	}

	/**
	 * Remove directoy recursive.
	 * @access public
	 * @static
	 * @param string $dir
	 * @return boolean
	 */
	public static function rmdirRecursive($dir) {
		if (is_dir($dir)) {
			$dirHandle = opendir($dir);
		}

		if ($dirHandle) {
			while ($file = readdir($dirHandle)) {
				if (($file != ".") && ($file != "..")) {
					if (!is_dir($dir . "/" . $file)) {
						@unlink($dir . "/" . $file);
					} else {
						static::rmdirRecursive($dir . "/" . $file);
					}
				}
			}

			closedir($dirHandle);
			@rmdir($dir);

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Generate unique file name.
	 * @access public
	 * @static
	 * @param string $dir
	 * @param string $file
	 * @return string
	 */
	static public function getUniqueFileName($dir, $file) {
		if (substr($dir, -1) != "/") {
			$dir .= "/";
		}

		$file = ltrim($file, "/");

		if (file_exists($dir . $file)) {
			$fileNameParts = explode('.', $file);
			$name = $fileNameParts[0];
			$i = 0;
			do {
				$i++;
				$fileNameParts[0] = $name . '_' . $i;
				$file = implode('.', $fileNameParts);
			} while (file_exists($dir . $file));
		}

		return $file;
	}

	/**
	 * Generate unique file name.
	 * @access public
	 * @static
	 * @param string $dir
	 * @param string $file
	 * @return string
	 */
	static public function getUniquePath($path) {
		$paths = explode("/", $path);
		$file = array_pop($paths);
		$dir = implode('/', $paths);
		return $dir . "/" . static::getUniqueFileName($dir, $file);
	}

	/**
	 * Search file in directory tree
	 * @access public
	 * static
	 * @param string $filename
	 * @param string $dir
	 * @return array
	 */
	public static function search($filename, $dir = null) {
		$result = array();
		$dir = is_null($dir) ? $_SERVER["DOCUMENT_ROOT"] : rtrim($_SERVER["DOCUMENT_ROOT"], "/") . "/" . trim($dir, "/");
		foreach (self::getDirectories($dir) as $dir) {
			if (file_exists($dir . "/" . $filename)) {
				$result[] = $dir . "/" . $filename;
			}
		}
		return $result;
	}

	/**
	 * Search file in directory tree with regexp
	 * @access public
	 * static
	 * @param string $pattern
	 * @param string $dir
	 * @return array
	 */
	public static function regExpSearch($pattern, $dir = null) {
		$result = array();
		$dir = is_null($dir) ? $_SERVER["DOCUMENT_ROOT"] : \rtrim($_SERVER["DOCUMENT_ROOT"], "/") . "/" . trim($dir, "/");
		foreach (self::getDirectories($dir) as $dir) {
			foreach (self::getFiles($dir) as $file) {
				if (preg_match("/" . preg_quote($pattern) . "/i", $file)) {
					$result[] = $dir . "/" . $file;
				}
			}
		}
		return $result;
	}

	/**
	 * List of all subdirectory
	 * @access public
	 * static
	 * @param string $dir
	 * @return array
	 */
	public static function getDirectories($dir) {
		$directories = array($dir);
		foreach (self::getDirs($dir) as $d) {
			$directories = array_merge($directories, self::getDirectories($dir . "/" . $d));
		}
		return $directories;
	}

}

?>