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
 * IRunner
 * interface for Runners
 * @package Core
 * @subpackage Runner
 * @category Interface
 * @author nullstring
 */
namespace Core\Runner;

interface IRunner
{
	/**
	 * Run Runner.
	 * @access public
	 * @return void
	 */
	public function run();
}

?>