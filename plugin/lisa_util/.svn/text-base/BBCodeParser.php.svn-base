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
 * Class for parsing BBCode
 *
 * This class can be use for parsing common BBCode tags.
 *
 * @package Util
 * @author Dezs
 */
namespace lisa_util;

class BBCodeParser {

  /**
	 * Bbcode patterns .
	 * @access protected
	 * @staticvar array 
	 */
	protected static $_patterns = array(
        '/\<script(.+)\>(.+)\<\/script\>/Uis',
        '/\[b\](.+)\[\/b\]/Uis',
        '/\[i\](.+)\[\/i\]/Uis',
        '/\[u\](.+)\[\/u\]/Uis',
        '/\[s\](.+)\[\/s\]/Uis',
        '/\[url\](.+)\[\/url\]/Uis',
        '/\[url=(.+)\](.+)\[\/url\]/Ui',
        '/\[size=(.+)\](.+)\[\/size\]/Ui',
        '/\[img\](.+)\[\/img\]/Ui',
        '/\[code\](.+)\[\/code\]/Uis',
        '/\[color=(\#[0-9a-f]{6}|[a-z]+)\](.+)\[\/color\]/Ui',
        '/\[color=(\#[0-9a-f]{6}|[a-z]+)\](.+)\[\/color\]/Uis'
    );

  /**
	 * HTML tags that correspond to bbcode patterns.
	 * @access protected
	 * @staticvar array
	 */
	protected static $_replacements = array(
        '&lt;script\1&gt;\2&lt;/script&gt;',
        '<strong>\1</strong>',
        '<i>\1</i>',
        '<u>\1</u>',
        '<s>\1</s>',
        '<a href = "\1" target = "_blank">\1</a>',
        '<a href = "\1" target = "_blank">\2</a>',
		'<font size = "\1%">\2</font>',
        '<img src = "\1" alt = "Image" />',
        '<pre>\1</pre>',
		'<span style = "color: \1;">\2</span>',
 		'<div style = "color: \1;">\2</div>'
	);
    
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
	 * Converts bbcode to (x)HTML tags.
	 * @access public
	 * @static
	 * @param string $string
	 * @return string
	 */
    public static function bbc2html($string) {
        $string = nl2br($string);

        $string = preg_replace(static::$_patterns, static::$_replacements, $string);

        return $string;
    }
}

?>
