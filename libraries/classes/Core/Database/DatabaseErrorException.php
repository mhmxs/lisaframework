<?
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
  * Database error exception.
  * @package Core
  * @subpackage Database
  * @category Exception
  * @author kovacsricsi
  */

 class DatabaseErrorException extends Exception
 {
	/**
	 * Contructor.
	 * @access public
	 * @param string $message
	 * @return void
	 */
 	public function __construct($message = "")
 	{
 		parent::__construct("Cannot connect to database : " . (string)$message);
 	}
 }

?>
