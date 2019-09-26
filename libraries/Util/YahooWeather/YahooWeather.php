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
 * Yahoo! Weather API component for LISA framework
 * @package Util
 * @subpackage YahooWeather
 * @author Somlyai Dezső
 */
namespace Util\YahooWeather;

class YahooWeather
{

	/**
	 * Yahoo weather XML content.
	 * @access protected
	 * @var string
	 */
	protected $_content;

    /**
	 * Yahoo weather api URL.
	 * @access protected
	 * @var string
	 */
	protected $_weatherApiURL = 'http://weather.yahooapis.com/forecastrss?p=';

    /**
	 * Yahoo weather images base URL.
	 * @access protected
	 * @var string
	 */
	protected $_weatherImageURL = 'http://l.yimg.com/a/i/us/nws/weather/gr/';

    /**
	 * Yahoo weather translate file.
	 * @access protected
	 * @var string
	 */
	protected $_translateFile = 'yahoo.ini';

	/**
	 * Data of parsed XML.
	 * @access protected
	 * @var array
	 */
	protected $_data = array();

	/**
	 * Data of translate
	 * @access protected
	 * @var array
	 */
	protected $_translate = array();

 	/**
	 * __get
	 * @access public
	 * @param string $key
	 * @return string
	 */
   public function __get($key) {
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
		} else {
			return null;
		}
    }

	/**
	 * Constructor.
	 * @access public
	 * @param string $locationCode
	 * @param string $degreeUnit
	 */
	public function  __construct($locationCode, $degreeUnit = 'c', $lang = 'en') {

		if ($this->_content = file_get_contents($this->_weatherApiURL . $locationCode . '&u=' . $degreeUnit)) {
			if ($lang != "en") {
				$translate = true;
				$this->_translate = $this->_setTranslate($lang);
			}
            $this->_data['location']						= $this->_getTagAttributes('yweather:location');
			$this->_data['condition']						= $this->_getTagAttributes('yweather:condition');
            $this->_data['units']							= $this->_getTagAttributes('yweather:units');
            $this->_data['wind']							= $this->_getTagAttributes('yweather:wind');
            $this->_data['atmosphere']						= $this->_getTagAttributes('yweather:atmosphere');
            $this->_data['astronomy']						= $this->_getTagAttributes('yweather:astronomy');
            $this->_data['geo:lat']							= $this->_getTagValue('geo:lat');
            $this->_data['geo:long']						= $this->_getTagValue('geo:long');
            $this->_data['link']							= explode('*', $this->_getTagValue('link'));
            $this->_data['pubDate']							= $this->_getTagValue('pubDate');
            $this->_data['description']						= $this->_getTagValue('description');
            $this->_data['image_url']						= $this->_weatherImageURL . $this->_data['condition']['code'] . 'n.png';
            $forecasts										= $this->_getTagAttributes('yweather:forecast');
            $this->_data['tonight_forecast']				= $forecasts[0];
            $this->_data['tonight_forecast']['image_url']	= $this->_weatherImageURL . $this->_data['tonight_forecast']['code'] . 'n.png';
            $this->_data['tomorrow_forecast']				= $forecasts[1];
            $this->_data['tomorrow_forecast']['image_url']	= $this->_weatherImageURL . $this->_data['tomorrow_forecast']['code'] . 'n.png';
			if (isset($translate) && $translate === true) {
				$this->_data['condition']['text'] = $this->_translate[$this->_data['condition']['code']];
				$this->_data['tonight_forecast']['text'] = $this->_translate[$this->_data['tonight_forecast']['code']];
				$this->_data['tomorrow_forecast']['text'] = $this->_translate[$this->_data['tomorrow_forecast']['code']];
			}
		} else {
            throw new Exception('Unable to connect to yahoo api.');
        }
    }

    /**
	 * Geting XML value.
	 * @access protected
	 * @param string $sTag
	 * @return string
	 */
	protected function _getTagValue($sTag) {
        $aMatches = array();

        if (preg_match("/<" . $sTag . ">([^<]*)<\/" . $sTag . ">/i", $this->_content, $aMatches)) {
            $aResult = array();
            $aResult['value'] = $aMatches[1];
            return trim($aMatches[1]);
        }
        return null;
    }

    /**
	 * Geting XML Attribute.
	 * @access protected
	 * @param string $sTag
	 * @return string
	 */
    protected function _getTagAttributes($sTag) {
        $aMatches = array();

        if (preg_match_all("/<" . $sTag . "([^\/]*)\/>/i", $this->_content, $aMatches)) {
            $aResult = array();

            for ($i = 0; $i < count($aMatches[1]); $i++) {
                $aSubMatches = array();

                if (preg_match_all("/([^=]+)=\"([^\"]*)\"/i", $aMatches[1][$i], $aSubMatches)) {
                    for ($j = 0; $j < count($aSubMatches[1]); $j++) {
                        $aResult[$i][trim($aSubMatches[1][$j])] = trim($aSubMatches[2][$j]);
                    }
                }
            }
            $iNumResults = count($aResult);
            if ($iNumResults > 1) {
                return $aResult;
            } elseif ($iNumResults == 1) {
                return $aResult[0];
            }
        }
        return null;
    }

	/**
	 * Set translate file.
	 * @access public
	 * @param string $lang
	 * @return bool
	 */
	protected function _setTranslate($lang) {
		if (file_exists(dirname(__FILE__) . "/" . $this->_translateFile)) {
			$content = parse_ini_file(dirname(__FILE__) . "/" . $this->_translateFile, true);
			if (isset($content[$lang])) {
				$this->_translate = $content[$lang];
				return true;
			} else {
				throw new Exception("Language not found.");
			}
		} else {
			throw new Exception("Translate file not found.");
		}
		return false;
	}
}

?>
