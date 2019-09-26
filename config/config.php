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
 * Session entropy
 */
ini_set("session.entropy_file", "/dev/urandom");
ini_set("session.entropy_length", "512");
ini_set("display_errors", 1);
ini_set("session.auto_start", 0);
ini_set("session.use_trans_sid", 0);
ini_set("session.use_cookies", 1);

/**
 * Set server timezone
 */
@date_default_timezone_set("Europe/Budapest");

/**
 * Root directory for the engine.
 */
define("DIR_ROOT", dirname(dirname(__FILE__)));
/**
 * Root for the core functions, such as the bootloader.
 */
define("DIR_CORE", DIR_ROOT . "/libraries/bootstrap");
/**
 * Cache directory
 */
define("DIR_CACHE", DIR_ROOT . "/tmp/cache");
/**
 * Core cache directory
 */
define("DIR_CORE_CACHE", DIR_CACHE . "/core");
/**
 * autoload cache
 */
define("AUTOLOAD_CACHE", DIR_CORE_CACHE . "/autoload.php");
/**
 * autoload index
 */
define("AUTOLOAD_INDEX", DIR_CORE_CACHE . "/classindex.ini");
/**
 * Data cache directory
 */
define("DIR_DATA_CACHE", DIR_CACHE . "/data");
/**
 * Configuration directory.
 */
define("DIR_CONFIG", DIR_ROOT . "/config");
/**
 * Templates directory
 */
define("DIR_TEMPLATES", DIR_ROOT . "/webroot/templates");
/**
 * Classes root for the autoloader
 */
define("DIR_CLASSES", DIR_ROOT . "/libraries/classes");
/**
 * External directory
 */
define("DIR_EXT", DIR_ROOT . "/libraries/external");
/**
 * Log directory.
 */
define("DIR_LOGS", DIR_ROOT . "/tmp/logs");
/**
 * headers directory.
 */
define("DIR_HEADERS", DIR_ROOT . "/webroot/headers");


define("ERR_TRACE", false);
define("ERR_TRACE_HOST", "127.0.0.1");
define("ERR_TRACE_PORT", 10002);

/**
 * database settings
 */
DEFINE("DB_TYPE", "MySQL");

/**
 * default charset
 */
DEFINE("CHARSET", "UTF-8");

?>
