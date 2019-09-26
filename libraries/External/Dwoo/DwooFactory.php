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
 * Load Dwoo template engine.
 * @package External
 * @subpackage Dwoo
 * @author kovacsricsi
 */
namespace External\Dwoo;

include 'Dwoo.php';

class DwooFactory
{
	public static function getDwoo()
	{
		$dwoo = new \Dwoo(DIR_CACHE . "/template/compiled", DIR_CACHE . "/template/cached");
		$reader = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Config.ini");
		if ((int)$reader->VIEW->template_cache != 0) {
			$dwoo->setCacheTime((int)$reader->VIEW->template_cache_time);
		}

		return $dwoo;
	}
}