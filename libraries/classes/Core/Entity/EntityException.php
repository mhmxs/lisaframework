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
  * Entity exception.
  * @package Core
  * @subpackage Entity
  * @category Exception
  * @author kovacsricsi
  */
 class EntityException extends Exception
 {
	/**
	 * Contructor.
	 * @access public
	 * @param string $message
	 * @return void
	 */
 	public function __construct($message = "")
 	{
 		parent::__construct("Entity exception : " . (string)$message);
 	}
 }

?>
