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
class ACL
{
	/**
	 * Factory method to create Osiris_Libraries_ACL_ACL.
	 *
	 * @access public
	 * @static
	 * @return Osiris_Libraries_ACL_ACL
	 */
	public static function init()
	{
		return new self();
	}

	/**
	 * Allow Access from group.
	 *
	 * @access public
	 * @param  IACLAccess $access
	 * @param  string $group
	 * @return void
	 */
	public function allow(IACLAccess $access, $group)
	{
		$aro = new ARO($access->getID(), $this->_accessIsGroup($access));
		$aro->addGroup($group);
		$aro->commit();
	}
	
	/**
	 * Deny Access from group.
	 *
	 * @access public
	 * @param  IACLAccess $access
	 * @param  string $group
	 * @return void
	 */
	public function deny(IACLAccess $access, $group)
	{
		$aro = new ARO($access->getID(), $this->_accessIsGroup($access));
		$aro->removeGroup($group);
		$aro->commit();
	}

	/**
	 * Check ARO allowed frag.
	 *
	 * @access public
	 * @param  IACLAccess $access
	 * @param  string $group
	 * @return boolean
	 */
	public function isAllowed(IACLAccess $access, $group)
	{
		$aro = new ARO($access->getID(), $this->_accessIsGroup($access));
		return $aro->isMember($group);
	}

	/**
	 * Returns Access all groups.
	 *
	 * @access public
	 * @param  IACLAccess $access
	 * @return array
	 */
	public function getGroups(IACLAccess $access)
	{
		$aro = new ARO($access->getID(), $this->_accessIsGroup($access));
		return $aro->getGroups();
	}

	/**
	 * Returns Group all members.
	 *
	 * @access public
	 * @param  ACLGroup $group
	 * @return array
	 */
	public function getMembers(ACLGroup $group)
	{
		$aro = new ARO($group->getID(), true);
		return $aro->getMembers();
	}
	
	/**
	 * Remove access.
	 * 
	 * @access public
	 * @param  IACLAccess $access
	 * @return void
	 */	
	public function delete(IACLAccess $access)
	{
		$aro = new ARO($access->getID(), $this->_accessIsGroup($access));
		$aro->delete();
	}

	/**
	 * Create group if non exists.
	 *
	 * @access public
	 * @param  ACLGroup $group
	 * @throws ACLException
	 * @return boolen
	 */
	public function createGroup(ACLGroup $group)
	{
		$aro = new ARO($group->getID(), true);

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
	 * @param  ACLAccount $account
	 * @throws ACLException
	 * @return boolen
	 */
	public function createAccount(ACLAccount $account)
	{
		$aro = new ARO($account->getID());

		if ($aro->isNew() === true) {
			$aro->commit();
		} else {
			throw new ACLException("Account access already exists!");
		}
	}

	/**
	 * Check acces is group or not.
	 *
	 * @access protected
	 * @param  IACLAccess $access
	 * @return boolean
	 */
	protected function _accessIsGroup(IACLAccess $access)
	{
		if ($access instanceof ACLGroup) {
			return true;
		} else {
			return false;
		}
	}
}

?>