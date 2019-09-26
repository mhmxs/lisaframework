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
  * Session error exception.
  * @package Core
  * @subpackage Session
  * @category Exception
  * @author kovacsricsi
  */

 class InvalidSessionException extends Exception
 {
	/**
	 * Contructor.
	 * @access public
	 * @param string $message
	 * @return void
	 */
 	public function __construct($message = "")
 	{
        trigger_error("Invalid session request from : " . (string)$message);

 		parent::__construct("Invalid session request from : " . (string)$message);
 	}
 }

?>
