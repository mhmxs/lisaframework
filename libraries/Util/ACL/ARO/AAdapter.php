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
 * Abstract class for Access Request Objects.
 *
 * @package    Util
 * @subpackage ACL.ARO
 * @author kovacsricsi
  */
namespace Util\ACL\ARO;

abstract class AAdapter
{
	/**
	 * Access Request Object datas.
	 *
	 * @access protected
	 * @var    array
	 */
	protected $_AROData;

	/**
	 * Object groups.
	 *
	 * @access protected
	 * @var    array
	 */
	protected $_AROGroups;

	/**
	 * Memebers of the group.
	 *
	 * @access protected
	 * @var    array
	 */
	protected $_memebers;

	/**
	 * Unique identify for this ARO.
	 *
	 * @access protected
	 * @var    string
	 */
	protected $_unique_id = "";

	/**
	 * The ARO is new or not.
	 *
	 * @access protected
	 * @var    boolean
	 */
	protected $_isNew;

	/**
	 * Constructor set default settings.
	 *
	 * @access public
	 * @param  mixed $unique_id
	 * @param  boolean $group
	 * @return void
	 */
	public function __construct($unique_id, $group = false)
	{
		$this->_unique_id = ($group == false ? "@" : "") . $unique_id;
		$this->_AROData   = null;
		$this->_AROGroups = array();
		$this->_isNew     = null;

		$this->_initialize();
	}

	/**
	 * Asnwer for is new? question.
	 *
	 * @access public
	 * @throws AROException
	 * @return boolean
	 */
	public function isNew()
	{
		if ($this->_isNew === null) {
			throw new AROException("Invalid ARO new flag");
		}
		return $this->_isNew;
	}

	/**
	 * Returns unigue identify.
	 *
	 * @access public
	 * @return string
	 */
	public function getUniqueID()
	{
		return $this->_unique_id;
	}

	/**
	 * Returns al groups.
	 *
	 * @access public
	 * @return array
	 */
	public function getGroups()
	{
		$groups = array();

		foreach($this->_AROGroups as $group => $flag) {
			if ($flag != 0) {
				$groups[] = $group;
			}
		}

		return $groups;
	}

	/**
	 * Returns member status of a group.
	 *
	 * @access public
	 * @param  string $group
	 * @return boolean
	 */
	public function isMember($group)
	{
		if (array_key_exists($group, $this->_AROGroups) && ($this->_AROGroups[$group] != 0)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns group flag.
	 *
	 * @access public
	 * @param  string $name
	 * @return boolean
	 */
	public function isGroup($name = null)
	{
		$name = ($name !== null) ? $name : $this->_unique_id;
		return strpos($name, "@") === 0 ? false : true;
	}

	/**
	 * Delete ARO from database.
	 *
	 * @access public
	 * @return void
	 */
	public function delete()
	{
		if ($this->isGroup()) {
			foreach($this->getMembers() as $member) {
				$class = get_called_class();
				$aro = new $class($member, $this->isGroup($member));
				$aro->removeGroup(preg_replace("/^@/", "", $this->_unique_id));
			}
		}

		$this->_deleteThis();
	}

	/**
	 * Returns _AROData element if exists.
	 *
	 * @access public
	 * @param  string $name
	 * @return mixed
	 */
	abstract public function __get($name);

	/**
	 * sets _AROData emelent.
	 *
	 * @access public
	 * @param  string $name
	 * @param  mixed $value
	 * @return void
	 */
	abstract public function __set($name, $value);

	/**
	 * Add ARO to group.
	 *
	 * @access public
	 * @param  string $group
	 * @param  boolean $temp //templerary add or not
	 * @return void
	 */
	abstract public function addGroup($group, $temp = false);

	/**
	 * Remove ARO from group.
	 *
	 * @access public
	 * @param  string $group
	 * @return void
	 */
	abstract public function removeGroup($group);

	/**
	 * Returns groups withc the ARO owned.
	 *
	 * @access public
	 * @return array
	 */
	abstract public function getRealGroups();

	/**
	 * Returns all member of the group.
	 *
	 * @access public
	 * @throws AROException
	 * @return array
	 */
	abstract public function getMembers();

	/**
	 * Validate before commit.
	 *
	 * @access public
	 * @throws AROException
	 * @return void
	 */
	abstract public function validate();

	/**
	 * Store ARO to database.
	 *
	 * @access public
	 * @return void
	 */
	abstract public function commit();

	/**
	 * Delete ARO from database.
	 *
	 * @access protected
	 * @return void
	 */
	abstract protected function _deleteThis();


	/**
	 * Initialize ARO by groupname.
	 *
	 * @access protected
	 * @throws ACLException
	 * @return void
	 */
	abstract protected function _initialize();
}

?>