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
 * Class for Access Request Objects on file architecture.
 *
 * @package    Util
 * @subpackage ACL
 * @author kovacsricsi
  */
class AROFile extends AAROAdapter
{
	/**
	 * configuration file if $_storeEngine is file.
	 *
	 * @access protected
	 * @var    string
	 */
	protected static $_configFile = null;

	/**
	 * ACL datas.
	 *
	 * @access protected
	 * @static
	 * @var    ConfigReader
	 */
	protected static $_acl = null;

	/**
	 * Returns _AROData element if exists.
	 *
	 * @access public
	 * @param  string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if (isset($this->_AROData->$name)) {
			return $this->_AROData->$name;
		} else {
			return null;
		}
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
		$this->_AROData->$name = $value;
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
		if (!$this->isMember($group) && ("@" . $group != $this->_unique_id)) {
			$this->_AROGroups[$group] = 1;

			if ($temp === false) {
				$this->_AROData->$group = 1;
			}
		}
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
		unset($this->_AROGroups[$group]);

		$this->_AROData->$group = 0;
	}

	/**
	 * Returns groups withc the ARO owned.
	 *
	 * @access public
	 * @return array
	 */
	public function getRealGroups()
	{
		return $this->_AROData->toArray();
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
		if (!$this->isGroup()) {
			throw new AROException("Only groups have member!");
		}

		if ($this->_memebers !== null) {
			return $this->_memebers;
		} else {
			$this->_memebers = array();

			if (self::$_acl === null) {
				self::_readACL();
			}

			foreach(self::$_acl->toArray() as $name => $groups) {
				$isGroup = $this->isGroup($name);
				$name    = preg_replace("/^@/", "", $name);

				$aro = new ARO($name, $isGroup);

				if ($aro->isMember($this->_unique_id)) {
					$this->_memebers[] = $aro->getUniqueID();
				}
			}

			return $this->_memebers;
		}
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
		if (!$this->_AROData instanceof INITag) {
			throw new AROException("Invalid type of _AROData");
		}
	}

	/**
	 * Store ARO to database.
	 *
	 * @access public
	 * @return void
	 */
	public function commit()
	{
		$this->validate();

		self::_store($this);
	}

	/**
	 * Delete ARO from database.
	 *
	 * @access protected
	 * @return void
	 */
	protected function _deleteThis()
	{
		self::_remove($this);

		$this->__construct();
	}

	/**
	 * Initialize ARO by groupname.
	 *
	 * @access protected
	 * @return void
	 */
	protected function _initialize()
	{
		$this->_AROData = self::_get($this->_unique_id);

		if ($this->_AROData !== null) {
			$this->_AROGroups = $this->getRealGroups();

			foreach($this->getGroups() as $group) {
				$groupARO = new self($group, true);

				foreach($groupARO->getGroups() as $newGroup) {
					$this->addGroup($newGroup, true);
				}
			}

			$this->_isNew = false;
		} else {
			$this->_AROData = new INITag($this->_AROGroups);

			$this->_isNew = true;
		}
	}

	/**
	 * Search item in ACL.ini.
	 *
	 * @access protected
	 * @static
	 * @param  mixed $uniqued_id
	 * @return mixed
	 */
	protected static function _get($unique_id)
	{
		if (self::$_acl === null) {
			self::_readACL();
		}

		if (!is_null(self::$_acl->$unique_id)) {
			return self::$_acl->$unique_id;
		} else {
			return null;
		}
	}

	/**
	 * Store ARO to ini file.
	 *
	 * @access protected
	 * @static
	 * @param  ARO $aro
	 * @throws AROException
	 * @return void
	 */
	protected static function _store(self $aro)
	{
		if (self::$_configFile === null) {
			self::_setACLFilePath();
		}

		$acl = new FileHandler(self::$_configFile);

		if (!$acl->isWritable()) {
			throw new AROException("Ini file not writeable!");
		}

		$lines = $acl->getLines();

		$del     = false;
		$replace = false;

		foreach($lines as $i => $line) {
			if (preg_match("/\[[a-zA-Z0-9@]*\]/", $line)) {
				$del = false;
			}
			if (preg_match("/\[" . $aro->getUniqueID() . "\]/", $line)) {
				$del     = true;
				$replace = true;
			}

			if ($del === true) {
				unset($lines[$i]);
			}
		}

		$lines[] = ($replace == false ? "\n" : "") . "[" . $aro->getUniqueID() . "]\n";

		foreach($aro->getRealGroups() as $group => $flag) {
			if ($flag == 1) {
				$lines[] = $group . " = 1\n";
			}
		}

		$acl->overWrite(join("", $lines));
	}

	/**
	 * Remove ARO from ini file.
	 *
	 * @access protected
	 * @static
	 * @param  ARO $aro
	 * @throws AROException
	 * @return void
	 */
	protected static function _remove(self $aro)
	{
		if (self::$_configFile === null) {
			self::_setACLFilePath();
		}

		$acl = new FileHandler(self::$_configFile);

		if (!$acl->isWritable()) {
			throw new AROException("Ini file not writeable!");
		}

		$lines = $acl->getLines();

		$del     = false;
		$replace = false;

		foreach($lines as $i => $line) {
			if (preg_match("/\[[a-zA-Z0-9@]*\]/", $line)) {
				$del = false;
			}
			if (preg_match("/\[" . $aro->getUniqueID() . "\]/", $line)) {
				$del     = true;
				$replace = true;
			}

			if ($del === true) {
				unset($lines[$i]);
			}
		}

		$acl->overWrite(join("", $lines));
	}

	/**
	 * Reads ACL data from ini file.
	 *
	 * @access protected
	 * @static
	 * @return ConfigReader
	 */
	protected static function _readACL()
	{
		if (self::$_configFile === null) {
			self::_setACLFilePath();
		}

		try {
			$acl = new ConfigReader(self::$_configFile);
		} catch (Exception $e) {
			$file = new FileHandler(self::$_configFile);
			$file->erase();

			$acl = new ConfigReader(self::$_configFile);
		}

		self::$_acl = $acl;
	}

	/**
	 * Set ACL file path.
	 *
	 * @access protected
	 * @static
	 * @return void
	 */
	protected static function _setACLFilePath()
	{
		self::$_configFile = DIR_CONFIG . "/ACL.ini";
	}
}

?>