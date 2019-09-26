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
namespace Core\Runner;

class Filter implements IRunner
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
	public static function runPreFilters()
	{
		$class = get_called_class();
		return new $class("Pre");
	}

	/**
	 * Factory method for Post plugin running.
	 * @access public
	 * @static
	 * @return PluginRunner
	 */
	public static function runPostFilters()
	{
		$class = get_called_class();
		return new $class("Post");
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
			throw new RunnerException("Invalid argument for FilterRunner");
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
            if (!is_dir(DIR_LIB . "/Filter/" . $this->_type)) {
                \Util\DirectoryHandler::mkdirRecursive(DIR_LIB . "/Filter/" . $this->_type, 0777);
            }
            foreach(\Util\DirectoryHandler::getFiles(DIR_LIB . "/Filter/" . $this->_type, "php") as $plugin) {
                    $pluginName = "\Filter\\" . $this->_type . "\\" . str_replace(".php", "", $plugin);
                    new $pluginName;
            }
	}
}

?>