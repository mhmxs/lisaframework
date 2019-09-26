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
 * Core class. Initialises the basic functions of the framework after the bootstrap procedure. Run Runner usually HTTPRunner.
 * @package Core
 * @category Core
 * @author nullstring
 */
class Core
{
	/**
	 * Constructor. MUST run the pre_core plugins.
	 * @return void
	 */
	function __construct()
	{
		$this->run(PluginRunner::runPrePlugins());
	}

	/**
	 * Destructor. MUST run the post_core plugins.
	 * @return void
	 */
	function __destruct()
	{
		$this->run(PluginRunner::runPostPlugins());
	}

	/**
	 * Runs the runner. Nothing to explain here.
	 * @param IRunner $r
	 * @return void
	 */
	function run(IRunner $r)
	{
		$r->run();
	}
}
?>