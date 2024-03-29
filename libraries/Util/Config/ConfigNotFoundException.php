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
  * Config error exception.
  * @package Util
  * @subpackage Config
  * @category Exception
  * @author kovacsricsi
  */
namespace Util\Config;

 class ConfigNotFoundException extends \Exception
 {
	/**
	 * Contructor.
	 * @access public
	 * @param string $message
	 * @return void
	 */
 	public function __construct($message = "")
 	{
 		parent::__construct("Config not found : " . (string)$message);
 	}
 }
?>
