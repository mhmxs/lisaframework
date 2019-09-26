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
class DirectoryHandler
{
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
     * Returns with files in direcotry
     * @param string $directory
     * @optional param array $extensions
     * you can give the extensions with or without . (dot)
     * @return array
     */

	static public function getFiles($directory, array $extensions = array())
	{
    	setType($directory, "string");

    	setType($extensions, "array");

    	if (count($extensions)){
    		foreach ($extensions as $key => $extension) {
      			$extensions[$key] = (substr($extension, 0, 1) == "." ) ? $extension : "." . $extension;
      		}
    	}

		$files = array();

		if (($dh = opendir($directory)) !== false) {
		    while (false !== ($file = readdir($dh))) {
		    	if (is_file($directory . "/" . $file)) {
		            if (count($extensions)){
		              if (in_array(strrchr($file, "."), $extensions) === true) {
		              	$files[] = $file;
		              }
		            } else {
		  		    	$files[] = $file;
		            }
			    }
		    }
		}

		return $files;
	}

    /**
     * Returns with direcoties in directory
     * @param string $directory
     * @return array
     */
	static public function getDirs($directory)
	{
        setType($directory, "string");

		$files = array();

		$nonDirs = array(
			".", ".."
		);

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
	 * Create directory recursive.
	 * @access public
	 * @static
	 * @param string $pathname
	 * @param integer $mode
	 * @return boolean
	 */
	static public function mkdirRecursive($pathname, $mode) {
    	is_dir(dirname($pathname)) || self::mkdirRecursive(dirname($pathname), $mode);
    	return is_dir($pathname) || @mkdir($pathname, $mode);
  }

}

?>