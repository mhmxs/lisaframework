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
 * Benchamrak php functions time and memory usage.
 * @author anonym
 * @package Util
 */
namespace Util;

class Benchmark {

	/**
	 * Store result.
	 * @access protected
	 * @staticvar array
	 */
	protected static $_marks;

	/**
	 * Set a benchmark start point.
	 * @access public
	 * @static
	 * @param   string  benchmark name
	 * @return  void
	 */
	public static function start($name = "default")
	{
		if (!isset(static::$_marks[$name])) {
			static::$_marks[$name] = array(
				'start'        => microtime(true),
				'stop'         => false,
				'memory_start' => function_exists('memory_get_usage') ? memory_get_usage() : 0,
				'memory_stop'  => false
			);
		}
	}

	/**
	 * Set a benchmark stop point.
	 * @access public
	 * @static
	 * @param   string  benchmark name
	 * @return  void
	 */
	public static function stop($name = "default")
	{
		if (isset(static::$_marks[$name]) AND static::$_marks[$name]['stop'] === false) {
			static::$_marks[$name]['stop'] = microtime(true);
			static::$_marks[$name]['memory_stop'] = function_exists('memory_get_usage') ? memory_get_usage() : 0;
		}
	}

	/**
	 * Get the elapsed time between a start and stop.
	 * @access public
	 * @static
	 * @param   string   benchmark name, true for all
	 * @param   integer  number of decimal places to count to
	 * @return  array
	 */
	public static function get($name = "default", $decimals = 4)
	{
		if ($name === true) {
			$times = array();
			$names = array_keys(static::$_marks);

			foreach ($names as $name) {
				$times[$name] = static::get($name, $decimals);
			}

			return $times;
		}

		if (!isset(static::$_marks[$name])) {
			return false;
		}

		if (static::$_marks[$name]['stop'] === false) {
			static::stop($name);
		}

		return array (
			'time'   => number_format(static::$_marks[$name]['stop'] - static::$_marks[$name]['start'], $decimals),
			'memory' => (static::$_marks[$name]['memory_stop'] - static::$_marks[$name]['memory_start'])
		);
	}

}