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
 * ACL Account.
 *
 * @package    Util
 * @subpackage ACL
 * @author kovacsricsi
  */
namespace lisa_util\ACL;

class Account implements IAccess
{
	/**
	 * Unique identify.
	 *
	 * @access protected
	 * @var    mixed
	 */
	protected $_id;

	public function __construct($id)
	{
		$this->_id = $id;
	}

	/**
	 * Returns unique identify.
	 *
	 * @access public
	 * @return mixed
	 */
	public function getID()
	{
		return $this->_id;
	}
}
?>