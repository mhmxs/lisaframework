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
 * @subpackage ACL.ARO
 * @version    SVN: $Id: ARO.php 2885 2008-12-17 12:23:43Z bartu $
 */
namespace lisa_util\ACL\ARO;

class ARO
{
	/**
	 * Store engine of ARO's.
	 *
	 * @access protected
	 * @var    string
	 */
	protected $_storeEngine;

	/**
	 * Adapter for real functions by $_storeEngine.
	 *
	 * @access protected
	 * @var     AAdapter
	 */
	protected $_adapter;

	/**
	 * Constructor sets default values.
	 *
	 * @access public
	 * @param  mixed $unique_id
	 * @param  boolean $group
	 * @return void
	 */
	public function __construct($unique_id = null, $group = false)
	{
		$this->_readConfig();

		$adapter        = "\\Util\\ACL\\ARO\\" . ucwords(strtolower($this->_storeEngine));
		$this->_adapter = new $adapter($unique_id, $group);
	}

	/**
	 * Returns unigue identify.
	 *
	 * @access public
	 * @return string
	 */
	public function getUniqueID()
	{
		return $this->_adapter->getUniqueID();
	}

	/**
	 * Returns _AROData element if exists.
	 *
	 * @access public
	 * @param  string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		$this->_adapter->__get($name);
	}

	/**
	 * sets _AROData emelent.
	 *
	 * @access public
	 * @param  string $name
	 * @param  mixed $value
	 * @return void
	 */
	public function __set($name, $value)
	{
		$this->_adapter->__set($name, $value);
	}

	/**
	 * Add ARO to group.
	 *
	 * @access public
	 * @param  string $group
	 * @param  boolean $temp //templerary add or not
	 * @return void
	 */
	public function addGroup($group, $temp = false)
	{
		$this->_adapter->addGroup($group, $temp);
	}

	/**
	 * Remove ARO from group.
	 *
	 * @access public
	 * @param  string $group
	 * @return void
	 */
	public function removeGroup($group)
	{
		$this->_adapter->removeGroup($group);
	}

	/**
	 * Returns al groups.
	 *
	 * @access public
	 * @return array
	 */
	public function getGroups()
	{
		return $this->_adapter->getGroups();
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
		return $this->_adapter->isMember($group);
	}

	/**
	 * Returns group flag.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isGroup()
	{
		return $this->_adapter->isGroup();
	}

	/**
	 * Returns all member of the group.
	 *
	 * @access public
	 * @throws AROException
	 * @return array
	 */
	public function getMembers()
	{
		return $this->_adapter->getMembers();
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
		return $this->_adapter->isNew();
	}

	/**
	 * Validate before commit.
	 *
	 * @access public
	 * @throws AROException
	 * @return void
	 */
	public function validate()
	{
		$this->_adapter->validate();
	}

	/**
	 * Store ARO to database.
	 *
	 * @access public
	 * @return void
	 */
	public function commit()
	{
		$this->_adapter->commit();
	}

	/**
	 * Delet ARO.
	 *
	 * @access public
	 * @return void
	 */
	public function delete()
	{
		$this->_adapter->delete();
	}

	/**
	 * Read ACL configuration.
	 *
	 * @access protected
	 * @throws ACLException
	 * @return void
	 */
	protected function _readConfig()
	{
		$reader = \Util\Config\Cache::getConfig(DIR_CONFIG . "/Config.ini");

		if (!is_null($reader->ACL->store_engine)) {
			$this->_storeEngine = $reader->ACL->store_engine;
		} else {
			throw new \Util\ACL\ACLException("No configuration for ACL in Config.ini!");
		}
	}
}

?>