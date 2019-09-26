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
 * Check for a domain.
 *
 * @package Util
 * @author kovacsricsi
 */
namespace Util;

class Whois {

	/**
	 * Status code.
	 * @access protected
	 * @var integer
	*/
	protected $_status;

	/**
	 * Connection timeout.
	 * @access protected
	 * @var integer
	*/
	protected $_connectionTimeout;

	/**
	 * Socket time out.
	 * @access protected
	 * @var integer
	*/
	protected $_socketTimeout;

	/**
	 * Use Tlds.
	 * @access protected
	 * @var array
	*/
	protected $_useTlds;

	/**
	 * Server settings.
	 * @access protected
	 * @var array
	*/
	protected $_serverSettings;

	/**
	 * Constructor sets default variables.
	 * @access public
	 * @return void
	*/
	public function __construct()
	{
		$this->_status            = 0;
		$this->_connectionTimeout = 5;
		$this->_socketTimeout     = 30;
		$this->_tlds              = array();
		$this->_useTlds           = array();
		$this->_serverSettings    = array();

		$this->_readConfig();
	}

	/**
	 * Return with domain's server.
	 * @access public
	 * @param string $domain
	 * @return string
	 */
	public function getSld($domain)
	{
		$tld = '';
		$this->_splitDomain($domain, '', $tld);
		$server = isset($this->_useTlds[$tld]) ? $this->_tlds[$tld] : '';

		return $server;
	}

	/**
	 * Return with domain's tld.
	 * @access public
	 * @param string $domain
	 * @return string
	 */
	public function getTld($domain)
	{
		$tld = '';
		$this->_splitDomain($domain, '', $tld);
		$server = isset($this->_useTlds[$tld]) ? $this->_tlds[$tld] : '';

		return $tld;
	}

	/**
	 * Validate domain.
	 * @access public
	 * @param string $domain
	 * @return boolean
	 */
	public function isValidDomain($domain)
	{
		$tmp  = '';
		$tmp2 = '';
		return $this->_splitDomain(strtolower($domain), $tmp, $tmp2);
	}

	/**
	 * Lookup for domain.
	 * @access public
	 * @param string $domain
	 * @return boolean
	*/
	public function lookup($domain)
	{
		$domain = strtolower($domain);
		$this->_tld     = '';
		$tmp = '';

		if ($this->_splitDomain($domain, $tmp, $this->_tld)) {
			 $response = $this->_doLookup($domain, $this->_tlds[$this->_tld]);
			 if (!preg_match("/" . $this->_serverSettings[$this->_tlds[$this->_tld]]["no_match"] . "/", $response)) {
			 	return true;
			 }
		}

		return false;
	}

	/**
	 * Set configuration.
	 * @access protected
	 * @return void
	*/
	protected function _readConfig()
	{
		$configReader = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Whois.ini");

		$this->_tlds              = array();
		$this->_useTlds           = array();
		$this->_serverSettings    = array();

		foreach ($configReader->toArray() as $params) {
			$this->_serverSettings[$params["server"]] = array(
				'no_match' => $params["no_match"]
			);
		}

		foreach ($configReader->toArray() as $code => $params) {
			if (isset($this->_serverSettings[$params["server"]])) {
				$this->_useTlds[$code] = true;
				$this->_tlds[$code]    = $params["server"];
			}
		}
	}

	/**
	 * Do lookup on specified server.
	 * @access protected
	 * @param string $domain
	 * @param string $server
	 * @return mixed
	 */
	protected function _doLookup($domain, $server)
	{
		$domain = strtolower($domain);
		$server = strtolower($server);

		if ($domain == '' || $server == '' ) {
			return false;
		}

		$data = "";
		$fp   = @fsockopen($server, 43,$errno, $errstr, $this->_connectionTimeout);

		if ($fp) {
			@fputs($fp, $domain."\r\n");
			@socket_set_timeout($fp, $this->_socketTimeout);
			while( !@feof($fp)) {
				$data .= @fread($fp, 4096);
			}
			@fclose($fp);

			return $data;
		} else {
			return false;
		}
	}

	/**
	 * Split domain to parts.
	 * @access protected
	 * @param string $domain
	 * @param string $sld
	 * @param string $tld
	 * @return boolean
	 */
	protected function _splitDomain($domain, &$sld, &$tld)
	{
		$domain = trim(strtolower($domain));
		$sld    = '';
		$tld    = '';

		if (($pos= strpos($domain, '.')) != -1) {
			$sld = substr($domain, 0, $pos);
			$tld = substr($domain, $pos + 1);

			if (isset($this->_useTlds[$tld]) && $sld != '' ) {
				return true;
			}
		} else {
			$tld = $domain;

			return false;
		}
	}
}
?>