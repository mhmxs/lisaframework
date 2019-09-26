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

$root  = $_SERVER["argv"][1];
$cFile = $_SERVER["argv"][2];

/**
 * create class index for __autoload
 *
 * @param string $startDir
 * @return array
 */
function listdir($startDir = '.')
{
	global $root;

	$files = array();
	if (is_dir($startDir)) {
		$fh = opendir($startDir);

		while (($file = readdir($fh)) !== false) {
			if ((strcmp($file, '.') == 0) || (strcmp($file, '..') == 0) || (strcmp($file, '.svn') == 0)) {
				continue;
			}

			if (stristr(PHP_OS, "WIN")) {
				$rootPrefix = "";
			} else {
				$rootPrefix = "/";
			}

			$filePath = $rootPrefix . trim($startDir, "/") . "/" . $file;

			if (is_dir($filePath)) {
				$files = array_merge($files, listdir($filePath));
			} else {
				$rootDirectory = dirname(dirname(dirname(__FILE__)));
				$fileDirectory = str_replace($rootDirectory, "", $startDir);
				$filePath = "/" . trim($fileDirectory, "/") . "/" . $file;
				array_push($files, substr($filePath, strlen($root)));
			}
		}

		closedir($fh);
	} else {
		$files = false;
	}

	return $files;
}

$files = listdir($root);

if ($fp = fopen($cFile, "wb")) {
	foreach ($files as $file) {
		fwrite($fp, basename($file, ".php") . "=\"" . $file . "\"\n");
	}

	fclose($fp);
	@chmod($cFile, 0777);
}

load_autoloader_cache();

?>