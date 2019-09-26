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
 * Set error reporting
 */
error_reporting(E_ALL);
/**
 * Define LISA version
 */
define('LISA_VERSION',  '0.2.0');
/**
 * Define LISA running on Windows or not
 */
define('SERVER_IS_WIN', DIRECTORY_SEPARATOR === '\\');
/**
 * Set server timezone, and default date format
 */
define("DATE_FORMAT", "Y-m-d H:i:s");
@date_default_timezone_set("Europe/Budapest");
/**
 * Root directory for the engine.
 */
define("DIR_ROOT", dirname(dirname(dirname(__FILE__))));
/**
 * Cache directory
 */
define("DIR_CACHE", DIR_ROOT . "/tmp/cache");
/**
 * Log directory.
 */
define("DIR_LOGS", DIR_ROOT . "/tmp/logs");
/**
 * Configuration directory.
 */
define("DIR_CONFIG", DIR_ROOT . "/config");
/**
 * Templates directory
 */
define("DIR_TEMPLATES", DIR_ROOT . "/webroot/templates");
/**
 * Core classes root
 */
define("DIR_CORE", DIR_ROOT . "/system/libraries");
/**
 * User classes root for the autoloader
 */
define("DIR_LIB", DIR_ROOT . "/libraries");
/**
 * External directory
 */
define("DIR_EXT", DIR_ROOT . "/libraries/External");
/**
 * Error trace host settings
 */
define("ERR_TRACE", false);
define("ERR_TRACE_HOST", "127.0.0.1");
define("ERR_TRACE_PORT", 10002);
/**
 * default charset
 */
define("CHARSET", ini_get("default_charset") != false ? ini_get("default_charset") : "utf-8");
/**
 * define class load error function
 */
define("CLASS_NOT_FOUND", "die503");

?>
