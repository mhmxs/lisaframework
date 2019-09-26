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
 * Die with 503 Service not available error.
 */
function die503()
{
	header("HTTP/1.1 503 Server Unavailable", true, 503);

	echo "<h1>503 error!</h1>";

	exit(-503);
	exit;
}

/**
 * Include engine configuration file or die.
 */
if (!include_once(dirname(dirname(dirname(__FILE__))) . "/config/config.php")) {
	die503();
}

/**
 * Include class BasicErrorHandler.
 */
if (!include_once(dirname(__FILE__) . "/BasicErrorHandler.php")) {
	die503();
}

BasicErrorHandler::getInstance();

/**
 * Include class autoloader.
 */
if (!include_once(dirname(__FILE__) . "/autoload.php")) {
	die503();
}

?>