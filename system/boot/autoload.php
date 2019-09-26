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
 * Autoloader function. See PHP documentation.
 * @param string $classname
 * @return void
 */
$includedClasses = array();

function __autoload($classname)
{
	global $includedClasses;

	$classname = str_replace("\\", "/", ltrim($classname, "\\"));
	$dwooname  = strtr($classname, '_', DIRECTORY_SEPARATOR);

	if (!array_key_exists($classname, $includedClasses)) {
		if (strpos($classname, "Core") === 0) {
			$classPath = DIR_CORE . "/" . substr($classname, 4) . ".php";
		} elseif (defined("DWOO_DIRECTORY") && file_exists(DWOO_DIRECTORY . $dwooname .'.php')) {
			$classPath = DWOO_DIRECTORY . $dwooname .'.php';
		} else {
			$classPath = DIR_LIB . "/" . $classname . ".php";
		}

		if (!include_once($classPath)) {
			if (defined("CLASS_NOT_FOUND")) {
				$method = CLASS_NOT_FOUND;
				$method("Class " . $classname . " could not be found." . E_USER_WARNING);
			}
		} else {
			$includedClasses[$classname] = true;
		}
	}
}

?>