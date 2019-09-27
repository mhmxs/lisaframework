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
 * Die with 503 Service not available error.
 */
function die503($msg = null) {
    @ob_end_clean();
    @header("HTTP/1.1 503 Server Unavailable", true, 503);
    echo "503 error! " . $msg;
    exit(503);
}

if (strnatcmp(phpversion(), "5.3.0") < 0) {
    die503("Requested PHP version : 5.3.0");
}

if (!include_once("../config/config.php")) {
    die503("Config not loaded!");
}

if (!include "../plugin/jin_core/init.php") {
    die503("Jin-plugin not found!");
}

Context::getService('ErrorHandler');
Context::getService('Core')->run();

?>