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
 * Access Controll Library.
 *
 * @package    Util
 * @subpackage ACL
 * @author kovacsricsi
 */
namespace Util\ACL;

class ACL
{
	/**
	 * Factory method to create Utils\ACL.
	 *
	 * @access public
	 * @static
	 * @return ACL
	 */
	public static function init()
	{
		$class = get_called_class();
		return new $class();
	}

	/**
	 * Allow Access from group.
	 *
	 * @access public
	 * @param  IAccess $access
	 * @param  string $group
	 * @return void
	 */
	public function allow(IAccess $access, Group $group)
	{
		$aro = new \Util\ACL\ARO\ARO($access->getID(), $this->_accessIsGroup($access));
		$aro->addGroup($group->getID());
		$aro->commit();
	}

	/**
	 * Deny Access from group.
	 *
	 * @access public
	 * @param  IAccess $access
	 * @param  string $group
	 * @return void
	 */
	public function deny(IAccess $access, Group $group)
	{
		$aro = new \Util\ACL\ARO\ARO($access->getID(), $this->_accessIsGroup($access));
		$aro->removeGroup($group->getID());
		$aro->commit();
	}

	/**
	 * Check ARO allowed frag.
	 *
	 * @access public
	 * @param  IAccess $access
	 * @param  string $group
	 * @return boolean
	 */
	public function isAllowed(IAccess $access, Group $group)
	{
		$aro = new \Util\ACL\ARO\ARO($access->getID(), $this->_accessIsGroup($access));
		return $aro->isMember($group->getID());
	}

	/**
	 * Returns Access all groups.
	 *
	 * @access public
	 * @param  IAccess $access
	 * @return array
	 */
	public function getGroups(IAccess $access)
	{
		$aro = new \Util\ACL\ARO\ARO($access->getID(), $this->_accessIsGroup($access));
		return $aro->getGroups();
	}

	/**
	 * Returns Group all members.
	 *
	 * @access public
	 * @param  Group $group
	 * @return array
	 */
	public function getMembers(Group $group)
	{
		$aro = new \Util\ACL\ARO\ARO($group->getID(), true);
		return $aro->getMembers();
	}

	/**
	 * Remove access.
	 *
	 * @access public
	 * @param  IAccess $access
	 * @return void
	 */
	public function delete(IAccess $access)
	{
		$aro = new \Util\ACL\ARO\ARO($access->getID(), $this->_accessIsGroup($access));
		$aro->delete();
	}

	/**
	 * Create group if non exists.
	 *
	 * @access public
	 * @param  Group $group
	 * @throws ACLException
	 * @return boolen
	 */
	public function createGroup(Group $group)
	{
		$aro = new \Util\ACL\ARO\ARO($group->getID(), true);

		if ($aro->isNew() === true) {
			$aro->commit();
		} else {
			throw new ACLException("Group access already exists!");
		}
	}

	/**
	 * Create account if non exists.
	 *
	 * @access public
	 * @param  Account $account
	 * @throws ACLException
	 * @return boolen
	 */
	public function createAccount(Account $account)
	{
		$aro = new \Util\ACL\ARO\ARO($account->getID());

		if ($aro->isNew() === true) {
			$aro->commit();
		} else {
			throw new ACLException("Account access already exists!");
		}
	}

	/**
	 * Check for existed access.
	 *
	 * @access public
	 * @param  IAccess $access
	 * @throws ACLException
	 * @return boolen
	 */
	public function isExists(IAccess $access)
	{
		$aro = new \Util\ACL\ARO\ARO($access->getID(), $this->_accessIsGroup($access));

		return $aro->isNew() === true ? false : true;
	}

	/**
	 * Check acces is group or not.
	 *
	 * @access protected
	 * @param  IAccess $access
	 * @return boolean
	 */
	protected function _accessIsGroup(IAccess $access)
	{
		return ($access instanceof Group) ? true : false;
	}
}

?>