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
 * PluginRunner run plugins.
 * @package Core
 * @subpackage Runner
 * @category Runner
 * @author kovacsricsi
 */

class PluginRunner implements IRunner
{
	/**
	 * When must run the plugin pre or post
	 * @access protected
	 * @var string
	 */
	protected $_type;

	/**
	 * Factory method for Pre plugin running.
	 * @access public
	 * @static
	 * @return PluginRunner
	 */
	public static function runPrePlugins()
	{
		return new self("Pre");
	}

	/**
	 * Factory method for Post plugin running.
	 * @access public
	 * @static
	 * @return PluginRunner
	 */
	public static function runPostPlugins()
	{
		return new self("Post");
	}

	/**
	 * Constructor set plugin runtime.
	 * @access protected
	 * @param string $type
	 * @throws Exception
	 * @return void
	 */
	protected function __construct($type)
	{
		if (($type != "Pre") && ($type != "Post")) {
			throw new Exception("Invalid argument for PluginRunner");
		}

		$this->_type = $type;
	}

	/**
	 * Run all plugins from DIR_CLASSES/Plugin directory.
	 * @access public
	 * @return void
	 */
	public function run()
	{
		foreach(DirectoryHandler::getFiles(DIR_CLASSES . "/Plugin/" . $this->_type) as $plugin) {
			$pluginName = str_replace(".php", "", $plugin);
			new $pluginName;
		}
	}
}

?>