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
 * String manipulations.
 * @package Util
 * @author nullstring
 */

namespace Util;

class StringHandler {

	/**
	 * Constructor protecte the classs, because it is stataic.
	 * @access private
	 * @throws Exception
	 * @return void
	 */
	private function __construct() {
		throw new \Exception("Illegal operation, this class is only static class!");
	}

	/**
	 * Cut text in limited words.
	 * @access public
	 * @static
	 * @param string $input
	 * @param integer $limit
	 * @param string $endChar
	 * @return string
	 */
	public static function limitWords($input, $limit, $endChar = null) {
		$limit = (int) $limit;
		$endChar = ($endChar === null) ? '&#8230;' : $endChar;

		if (trim($input) === '') {
			return $input;
		}

		if ($limit <= 0) {
			return $endChar;
		}

		preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $input, $matches);

		return rtrim($matches[0]) . (strlen($matches[0]) === strlen($input) ? '' : $endChar);
	}

	/**
	 * Cut string in limited character.
	 * @access public
	 * @static
	 * @param string $input
	 * @param integer $limit
	 * @param string $endChar
	 * @param boolean $preserveWords
	 * @return string
	 */
	public static function limitChars($input, $limit, $endChar = null, $preserveWords = false) {
		$endChar = ($endChar === null) ? '&#8230;' : $endChar;

		$limit = (int) $limit;

		if (trim($input) === '' || Utf8::strlen($input) <= $limit) {
			return $input;
		}

		if ($limit <= 0) {
			return $endChar;
		}

		if ($preserveWords == false) {
			return rtrim(Utf8::substr($input, 0, $limit)) . $endChar;
		}

		preg_match('/^.{' . ($limit - 1) . '}\S*/us', $input, $matches);

		return rtrim($matches[0]) . (strlen($matches[0]) == strlen($input) ? '' : $endChar);
	}

	/**
	 * Remove double splashes.
	 * @access public
	 * @static
	 * @param string $input
	 * @return string
	 */
	public static function removeSlashes($input) {
		return preg_replace('#(?<!:)//+#', '/', $input);
	}

	/**
	 * Convert non locale characters to [a-z] characters
	 * TODO : needs to be expanded with all kind of letters
	 * @access public
	 * @static
	 * @param  string $string
	 * @return string
	 */
	public static function localeToCommonLower($string, $charset = null) {
		if ($charset === null) {
			$charset = CHARSET;
		}

		$string = mb_strtolower($string, $charset);
		$chars_from = array("ö", "ü", "ó", "ő", "ú", "é", "á", "ű", "í", "û", "õ", "ä", "ß" ,"а","б","в","г","д","е","ж" ,"з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ц","ч" ,"ш" ,"щ"  ,"ъ","ь","ю" ,"я");
		$chars_to   = array("o", "u", "o", "o", "u", "e", "a", "u", "i", "u", "o", "a", "ss","a","b","v","g","d","e","zh","z","i","j","k","l","m","n","o","p","r","s","t","u","f","h","c","ch","sh","sht","y","j","ju","ja");
		$string = str_replace($chars_from, $chars_to, $string);

		return $string;
	}

	/**
	 * Remove special characters from the string.
	 * @access public
	 * @static
	 * @param  string $string
	 * @return string
	 */
	public static function createAlias($string, $charset = null) {
		if ($charset === null) {
			$charset = CHARSET;
		}

		$string = trim($string);
		$string = static::localeToCommonLower($string, $charset);
		$string = preg_replace("/[^a-z0-9-]/i", "-", $string);
		$string = preg_replace('/[\s-]+/', '-', $string); // changes double spaces, and double dashes into single dashes("  ","--" => "-")
		return trim($string, "-");
	}

	/**
	 * Generates a random string of a given type and length.
	 *
	 * @param string $type
	 * @param integer $length
	 * @return string
	 *
	 * @tutorial  alnum     alpha-numeric characters
	 * @tutorial  alpha     alphabetical characters
	 * @tutorial  hexdec    hexadecimal characters, 0-9 plus a-f
	 * @tutorial  numeric   digit characters, 0-9
	 * @tutorial  nozero    digit characters, 1-9
	 * @tutorial  distinct  clearly distinct alpha-numeric characters
	 */
	public static function random($type = 'alphanum', $length = 8) {
		switch ($type) {
			case 'alphanum':
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;

			case 'alpha':
				$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;

			case 'hexdec':
				$pool = '0123456789abcdef';
				break;

			case 'numeric':
				$pool = '0123456789';
				break;

			case 'nozero':
				$pool = '123456789';
				break;

			default:
				return false;
				break;
		}

		$max = strlen($pool) - 1;

		$string = '';
		for ($i = 0; $i < $length; $i++) {

			$string .= $pool[mt_rand(0, $max)];
		}

		return $string;
	}

	/**
	 * Create safe filname without special chars etc.
	 * @access public
	 * @static
	 * @param  string $fileName
	 * @return string
	 */
	public static function safeFileName($fileName, $charset = null) {
		if ($charset === null) {
			$charset = CHARSET;
		}

		$ext = static::fileExtension($fileName, false, false, $charset);
		$name = static::fileNameWithoutExtension($fileName, false, $charset);

		return rtrim(static::CreateAlias($name) . "." . static::CreateAlias($ext), ".");
	}

	/**
	 * Returns with file extension.
	 * @access public
	 * @static
	 * @param string $fileName
	 * @param boolean $converToLowerCase
	 * @param boolean $startingWithDot
	 * @param string $charset
	 * @return string
	 */
	public static function fileExtension($fileName, $converToLowerCase = false, $startingWithDot = false, $charset = null) {
		if ($charset === null) {
			$charset = CHARSET;
		}

		$extWithDot = strrchr(ltrim($fileName, "."), ".");
		$extWithDot = ($converToLowerCase === false) ? $extWithDot : mb_strtolower($extWithDot, $charset);
		$extWithDot = ($extWithDot) ? $extWithDot : ".";

		return ($startingWithDot) ? $extWithDot : ltrim($extWithDot, ".");
	}

	/**
	 * Returns the file name without extension.
	 * @access public
	 * @static
	 * @param string $fileName
	 * @param boolean $converToLowerCase
	 * @param string $charset
	 * @return string
	 */
	public static function fileNameWithoutExtension($fileName, $converToLowerCase = false, $charset = null) {
		if ($charset === null) {
			$charset = CHARSET;
		}

		$extension = static::fileExtension($fileName);
		$file = ($extension === "") ? $fileName : substr($fileName, 0, -strlen($extension));
		$file = rtrim($file, ".");
		$file = ($converToLowerCase === false) ? $file : mb_strtolower($file, $charset);

		return $file;
	}

	/**
	 * Return dir path of fullpath
	 * @param string $path
	 * @return string
	 */
	public static function getDir($path) {
		return str_replace(basename($path), "", $path);
	}

	/**
	 * Convert any localization string's first letter to uppercase.
	 * @access public
	 * @static
	 * @param  string $string
	 * @return string
	 */
	public static function ucFirst($string, $charset = null) {
		if ($charset === null) {
			$charset = CHARSET;
		}

		mb_internal_encoding($charset);
		$string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);

		return $string;
	}

	/**
	 * Creates an email anchor.
	 *
	 * @access public
	 * @static
	 * @param   string  $email
	 * @param   string  $title
	 * @return  string
	 */
	public static function mailto($email, $title = null) {
		if (strpos($email, "?") !== false) {
			list($email, $params) = explode("?", $email, 2);

			$params = "?" . str_replace(" ", "%20", $params);
		} else {
			$params = "";
		}

		$safe = static::createEmail($email);

		if (is_null($title)) {
			$title = $safe;
		}

		return '<a href="&#109;&#097;&#105;&#108;&#116;&#111;&#058;' . $safe . $params . '">' . $title . '</a>';
	}

	/**
	 * Generates an obfuscated version of an email address.
	 * @access public
	 * @static
	 * @param   string  $email
	 * @return  string
	 */
	public static function createEmail($email) {
		$safe = "";

		foreach (str_split($email) as $letter) {
			switch (($letter === "@") ? rand(1, 2) : rand(1, 3)) {
				case 1: $safe .= "&#" . ord($letter) . ";";
					break;
				case 2: $safe .= "&#x" . dechex(ord($letter)) . ";";
					break;
				case 3: $safe .= $letter;
			}
		}

		return $safe;
	}

	/**
	 * Makes a query's where part from the parameters.
	 * @access public
	 * @static
	 * @param array $keys
	 * @param array $tableAndFields associative array: array( "table" => array("searchField1", "searchField2") )
	 * @return string
	 */
	public static function search($keys, $tableAndFields) {
		foreach ($table as $key => $value) {
			$where = "";
			foreach ($value as $index => $field) {
				if ($index > 0) {
					$where .= ") OR (";
				} else {
					$where .= "(";
				}
				foreach ($keys as $kindex => $keyword) {
					if ($kindex > 0) {
						$where .= " AND ";
					}
					$where .= "`" . $key . "`.`" . $field . "` LIKE '%" . addslashes($keyword) . "%'";
				}
			}
			$where .= ")";
		}

		return $where;
	}

	/**
	 * Creates an urlencoded input, except slash ( / ), percent ( % ) wont be encoded, cause the framework don't accep urlencoded (/, %) signs.
	 * @access public
	 * @static
	 * @param string $string
	 * @return string
	 */
	public static function encodeSearchURL($string) {
		$string = urlencode($string);
		$string = str_replace("%2F", "/", $string);
		$string = str_replace("%25", "+", $string);

		return $string;
	}

	/**
	 * Creates an urldencoded input, except slash ( / ), percent ( % ) wont be decodes, cause the framework don't accep urlencoded (/, %) signs.
	 * @access public
	 * @static
	 * @param string $string
	 * @return string
	 */
	public static function decodeSearchURL($string) {
		$string = str_replace("/", "%2F", $string);
		$string = str_replace("+", "%25", $string);
		$string = urldecode($string);

		return $string;
	}

	/**
	 * Fetches an absolute site URL based on a URI segment.
	 * @access public
	 * @static
	 * @param string $uri
	 * @param string $protocol
	 * @return string
	 */
	public static function uri2url($uri = '', $protocol = "http") {
		$path = trim(parse_url($uri, PHP_URL_PATH), '/');

		if ($query = parse_url($uri, PHP_URL_QUERY)) {
			$query = '?' . $query;
		}

		if ($fragment = parse_url($uri, PHP_URL_FRAGMENT)) {
			$fragment = '#' . $fragment;
		}

		return $protocol . "://" . $_SERVER["HTTP_HOST"] . "/" . $path . $query . $fragment;
	}

	/**
	 * Returns with JavaScript encoded varians of string.
	 * @access public
	 * @static
	 * @param string $string
	 * @return string
	 */
	public static function JSEncode($string) {
		$string = "document.write('" . $string . "');";
		$chars = array();
		$len = strlen($string);
		for ($i = 0; $i < $len; $i++) {
			$chars[] = ord($string[$i]);
		}

		return "<script type=\"text/javascript\">eval(String.fromCharCode(" . join(",", $chars) . "));</script>";
	}

	/**
	 * Convert arab numbers to roman.
	 * @access public
	 * @static
	 * @param integer $int
	 * @return string
	 */
	public static function arabToRoman($int) {
		if ($int < 0 || $int > 9999) {
			return -1;
		}

		$collection1 = array(1 => "I", 2 => "II", 3 => "III", 4 => "IV", 5 => "V", 6 => "VI", 7 => "VII", 8 => "VIII", 9 => "IX");
		$collection10 = array(1 => "X", 2 => "XX", 3 => "XXX", 4 => "XL", 5 => "L", 6 => "LX", 7 => "LXX", 8 => "LXXX", 9 => "XC");
		$collection100 = array(1 => "C", 2 => "CC", 3 => "CCC", 4 => "CD", 5 => "D", 6 => "DC", 7 => "DCC", 8 => "DCCC", 9 => "CM");
		$collection1000 = array(1 => "M", 2 => "MM", 3 => "MMM", 4 => "MMMM", 5 => "MMMMM", 6 => "MMMMMM", 7 => "MMMMMMM", 8 => "MMMMMMMM", 9 => "MMMMMMMMM");

		$ones = $int % 10;
		$tens = ($int - $ones) % 100;
		$hundreds = ($int - $tens - $ones) % 1000;
		$thou = ($int - $hundreds - $tens - $ones) % 10000;

		$tens = $tens / 10;
		$hundreds = $hundreds / 100;
		$thou = $thou / 1000;

		$response = "";
		if ($thou) {
			$response .= $collection1000[$thou];
		}
		if ($hundreds) {
			$response .= $collection100[$hundreds];
		}
		if ($tens) {
			$response .= $collection10[$tens];
		}
		if ($ones) {
			$response .= $collection1[$ones];
		}

		return $response;
	}

	/**
	 * Convert roman numbers to arab.
	 * @access public
	 * @static
	 * @param string $string
	 * @return integer
	 */
	public static function romanToArab($string) {
		$response = 0;
		$lastNumber = 0;
		$tmp = 0;
		$len = strlen($string);
		for ($i = $len; $i >= 0; $i--) {
			$tmp = self::_arabInt($string[$i]);
			if ($tmp >= $lastNumber) {
				$response += $tmp;
			} else {
				$response -= $tmp;
				$lastNumber = $tmp;
			}
		}
		return $response;
	}

	/**
	 * Returns with arab representation of roman character.
	 * @access protected
	 * @static
	 * @param string $n
	 * @return integer
	 */
	protected static function _arabInt($n) {
		if (strtoupper($n) == 'I')
			return 1;
		elseif (strtoupper($n) == 'V')
			return 5;
		elseif (strtoupper($n) == 'X')
			return 10;
		elseif (strtoupper($n) == 'L')
			return 50;
		elseif (strtoupper($n) == 'C')
			return 100;
		elseif (strtoupper($n) == 'D')
			return 500;
		elseif (strtoupper($n) == 'M')
			return 1000;
		else
			return 0;
	}

	/**
	 * Returns with utf8 encoded string.
	 * @access public
	 * @static
	 * @param string $string
	 * @return string
	 */
	public static function utf8UrlDecode($string) {
		if (!Utf8::isUtf8($string)) {
			$string = mb_convert_encoding($string, "UTF-8", "ISO-8859-2");
		} else {
			$matches = array();
			preg_match_all("/%([A-F0-9]{2})/i", $string, $matches);
			if (count($matches[1]) > 0) {
				foreach ($matches[1] as $key => $hex) {
					$string = str_replace($matches[0][$key], chr(hexdec($hex)), $string);
				}
			}
		}
		return $string;
	}

	/**
	 * Serialize to store in database
	 * @param mixed $text
	 * @return mixed
	 */
	public static function dbSerialize($var) {
		return base64_encode(serialize($var));
	}

	/**
	 * Unserialize from database
	 * @param mixed $text
	 * @return mixed
	 */
	public static function dbUnserialize($var) {
		return unserialize(base64_decode($var));
	}

	/**
	 * Truncrate string
	 * @param string string
	 * @param int allowed string size
	 * @param string truncrate suffix
	 * @param bool if true length setted by the num parameter will be WITH the suffix, else without
	 * @param string charset
	 */
	public static function truncate($string, $num = 30, $end = "...", $numIsMaxLength = true, $charset = null) {
		if ($charset === null) {
			$charset = CHARSET;
		}

		$string = trim($string);

		if (mb_strlen($string, $charset) > $num) {
			return nl2br(mb_substr($string, 0, ($numIsMaxLength ? $num - strlen($end) : $num), $charset)) . $end;
		} else {
			return $string;
		}
	}

}

?>