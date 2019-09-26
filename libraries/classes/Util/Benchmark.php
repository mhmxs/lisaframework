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
 * @category Util
 */
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
	public static function start($name)
	{
		if (!isset(self::$_marks[$name])) {
			self::$_marks[$name] = array(
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
	public static function stop($name)
	{
		if (isset(self::$_marks[$name]) AND self::$_marks[$name]['stop'] === false) {
			self::$_marks[$name]['stop'] = microtime(true);
			self::$_marks[$name]['memory_stop'] = function_exists('memory_get_usage') ? memory_get_usage() : 0;
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
	public static function get($name, $decimals = 4)
	{
		if ($name === true) {
			$times = array();
			$names = array_keys(self::$_marks);

			foreach ($names as $name) {
				$times[$name] = self::get($name, $decimals);
			}

			return $times;
		}

		if ( ! isset(self::$_marks[$name])) {
			return false;
		}

		if (self::$_marks[$name]['stop'] === false) {
			self::stop($name);
		}

		return array (
			'time'   => number_format(self::$_marks[$name]['stop'] - self::$_marks[$name]['start'], $decimals),
			'memory' => (self::$_marks[$name]['memory_stop'] - self::$_marks[$name]['memory_start'])
		);
	}

}