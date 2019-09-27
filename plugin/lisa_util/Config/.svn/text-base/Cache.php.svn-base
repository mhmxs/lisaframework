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
 * Cache and represent INI config files.
 *
 * @package    Util
 * @subpackage Config
 * @version    SVN: $Id$
 */
namespace lisa_util\Config;

class Cache
{
	/**
	 * Cached configs.
	 * @access protected
	 * @staticvar array
	 */
	protected static $_configs = array();

	/**
	 * Returns with config object.
	 * @access public
	 * @static
	 * @param string $config
	 * @return Reader
	 */
	public static function getConfig($config)
	{
		settype($config, "string");

		if (!array_key_exists($config, static::$_configs)) {
			static::$_configs[$config] = new Reader($config);
		}

		return static::$_configs[$config];

	}
}
?>