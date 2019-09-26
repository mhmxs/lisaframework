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
 * Generate and parse RSS feed
 * @package Util
 * @author kovacsricsi
 */
namespace Util;

class Rss
{
	/**
	 * Constructor protecte the classs, because it is stataic.
	 * @access private
	 * @throws Exception
	 * @return void
	 */
	private function __construct()
	{
		throw new \Exception("Illegal operation, this class is only static class!");
	}

	/**
	 * Parses a remote feed into an array.
	 * @access public
	 * @static
	 * @param string $feed
	 * @param integer $limit
	 * @return array
	 */
	public static function parse($feed, $limit = 0)
	{
		$limit = (int)$limit;

		$tmp = error_reporting(0);

		$load = (is_file($feed) || Validate::isUrl($feed)) ? 'simplexml_load_file' : 'simplexml_load_string';
		$feed = $load($feed, 'SimpleXMLElement', LIBXML_NOCDATA);

		error_reporting($tmp);

		if ($feed === false) {
			return array();
		}

		$feed = isset($feed->channel) ? $feed->xpath('//item') : $feed->entry;

		$i = 0;
		$items = array();
		foreach($feed as $item) {
			if ($limit > 0 && $i++ === $limit) {
				break;
			}

			$items[] = (array)$item;
		}

		return $items;
	}

	/**
	 * Creates a feed from the given parameters.
	 * @access public
	 * @static
	 * @param array $info
	 * @param array $items
	 * @param string $link
	 * @param string $format
	 * @param string $charset
	 * @return string
	 */
	public static function generate($info, $items, $link = '', $format = 'rss2', $charset = null)
	{
		if ($charset === null) {
			$charset = CHARSET;
		}

		$info += array('title' => 'Generated Feed', 'link' => (string)$link, 'generator' => 'LISA Framework');

		$feed = '<?xml version="1.0" encoding="'.$charset.'"?><rss version="' . ($format == "rss2" ? '2.0' : '1.0') . '"><channel></channel></rss>';
		$feed = simplexml_load_string($feed);

		foreach ($info as $name => $value) {
			if (($name === 'pubDate' || $name === 'lastBuildDate') && (is_int($value) || ctype_digit($value))) {
				$value = date("D, d M y H:i:s O", $value);
			} elseif (($name === 'link' || $name === 'docs') && strpos($value, '://') === false) {
				$value = \Util\StringHandler::uri2url($value, 'http');
			}

			$feed->channel->addChild($name, $value);
		}

		foreach ($items as $item) {
			$row = $feed->channel->addChild('item');

			if (is_array($item)) {
				foreach ($item as $name => $value) {
					if ($name === 'pubDate' && (is_int($value) || ctype_digit($value))) {
						$value = date("D, d M y H:i:s O", $value);
					} elseif (($name === 'link' || $name === 'guid') && strpos($value, '://') === false) {
						$value = \Util\StringHandler::uri2url($value, 'http');
					}

					$row->addChild($name, $value);
				}
			} else {
				$row->addChild("anonymous", $item);
			}
		}

		return $feed->asXML();
	}
}
?>