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
 * Class autoloader.
 * @package Bootstrap
 * @author nullstring
 */

/**
 * Load the autoloader cache or generate it
 * @return void
 */
function load_autoloader_cache()
{
	global $alindex;
	$alcachef = AUTOLOAD_CACHE;
	$alindexf = AUTOLOAD_INDEX;

	if (file_exists($alcachef) && file_exists($alindexf) && (filemtime($alindexf) < filemtime($alcachef))) {
		$cache = true;

		if (!include($alcachef)) {
			$cache = false;
		}
	} else {
		if (!file_exists($alcachef)) {
			$alcachefFileHandle = fopen($alcachef, "w");

			if ($alcachefFileHandle === false) {
				throw new Exception("Failed to create autoload cache. Maybe you need to grant some permissions?");
			}

			fclose($alcachefFileHandle);
		}

		if (!file_exists($alindexf)) {
			$alindexFileHandle = fopen($alindexf, "w");

			if ($alindexFileHandle === false) {
				throw new Exception("Failed to create autoload index. Maybe you need to grant some permissions?");
			}

			fclose($alindexFileHandle);
		}

		$cache = false;
	}

	if (!$cache) {
		if (file_exists($alcachef)) {
			@unlink($alcachef);
		}

		if (file_exists($alindexf) && is_readable($alindexf)) {
			$alindex = parse_ini_file($alindexf);
			$time    = time();

			if ($fp = fopen($alcachef . "." . $time, "wb")) {
				fwrite($fp, "<?php\n/**\n * Auto-generated file. Do not modify.\n */");
				fwrite($fp, "\n\$alindex = array(\n");

				$l = array();
				foreach($alindex as $key => $value) {
					$l[] = "    '" . $key . "'=>'" . $value . "'";
				}

				fwrite($fp, implode(",\n", $l));
				fwrite($fp, "\n);\n?>");
				fclose($fp);

				@rename($alcachef . "." . $time, $alcachef);
				@chmod($alcachef, 0666);

				if (file_exists($alcachef . "." . $time)) {
					unlink($alcachef . "." . $time);
				}
			} else {
				trigger_error("Could not write autoload cache file!", E_USER_WARNING);
			}

			unset($time);
		} else {
			throw new Exception("Failed to load autoload index. Maybe you need to grant some permissions?");
		}
	}
}

/**
 * Autoloader function. See PHP documentation.
 * @param string $classname
 * @return void
 */
function __autoload($classname)
{
	global $alindex;
	global $autoloadCases;

	if (isset($alindex[$classname])) {
		/**
		 * Include class file or trigger error and die. No exceptions here because they won't be caught.
		 */
		if (!include(DIR_ROOT . "/" . $alindex[$classname])) {
			if ($autoloadCases === 0) {
				$_SERVER["argv"][1] = DIR_CLASSES;
				$_SERVER["argv"][2] = AUTOLOAD_INDEX;
				include_once(DIR_ROOT . "/libraries/scripts/IndexClasses.php");

				$autoloadCases++;

				__autoload($classname);
			} else {
				trigger_error("Could not load " . $classname, E_USER_WARNING);
				exit();
			}
		}
	} else {
		if ($autoloadCases === 0) {
			$_SERVER["argv"][1] = DIR_CLASSES;
			$_SERVER["argv"][2] = AUTOLOAD_INDEX;
			include_once(DIR_ROOT . "/libraries/scripts/IndexClasses.php");

			$autoloadCases++;

			__autoload($classname);
		} else {
			trigger_error("Class " . $classname . " could not be found. Maybe you need to run the indexer?" . E_USER_WARNING);
			exit(-503);
		}
	}
}

/**
 * Initialize the autoloader
 */
$autoloadCases = 0;
$alindex = array();
load_autoloader_cache();

?>