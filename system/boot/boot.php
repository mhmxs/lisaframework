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
 * Boot framework.
 * @package Bootstrap
 * @author nullstring
 */

/**
 * Include engine configuration file or die.
 */
if (!include_once("config.php")) {
	die503("Config not loaded!");
}

/**
 * Include class \Core\ErrorHandler\Basic.
 */
if (!include_once(DIR_CORE . "/ErrorHandler/Basic.php")) {
	die503("Errorhandler not loaded!");
}

/**
 * Include class autoloader.
 */
if (!include_once("autoload.php")) {
	die503("Autoload not loaded!");
}

\Core\Core::useSessionHandler();

?>