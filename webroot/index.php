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

error_reporting(E_ALL);

/**
 * Include bootstrap system or fail.
 *
 */
if (include_once(dirname(dirname(__FILE__)) . "/libraries/bootstrap/bootstrap.php")) {
	$core = new Core();
	$core->run(HTTPRunner::useSessionHandler());
} else {
	header("HTTP/1.1 503 Service Unavailable", true, 503);
	echo("<h1>503 Service Unavailable</h1>");
	exit(-503);
}

?>