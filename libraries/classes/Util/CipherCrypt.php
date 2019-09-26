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
 * Encode and decode data by mcrypt.
 * @package Util
 * @author nullstring
 */
class CipherCrypt
{
	protected $_cryptDescriptor;
	protected $_chipter = "blowfish";
	protected $_cryptMode = "cfb";
	protected $_cryptKey;

	public function __construct($cryptKey, $chipter = "blowfish", $cryptMode = "cfb")
	{
		$this->_cryptKey        = $cryptKey;
		$this->_chipter         = $chipter;
		$this->_cryptMode       = $cryptMode;
		$this->_cryptDescriptor = mcrypt_module_open($this->_chipter, "", $this->_cryptMode, "");
	}

	/**
	 * Encrypts the given data
	 *
	 * @access public
	 * @param  mixed $chipData
	 * @return array
	 */
	public function encrypt($chipData)
	{
		srand();
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->_cryptDescriptor), MCRYPT_RAND);
		mcrypt_generic_init($this->_cryptDescriptor, $this->_cryptKey, $iv);
		$cryptText = mcrypt_generic($this->_cryptDescriptor, serialize($chipData));
		mcrypt_generic_deinit($this->_cryptDescriptor);

		$cryptAttay = array(
			"iv"   => trim(base64_encode($iv), "="),
			"data" => trim(base64_encode($cryptText), "=")
		);

		return $cryptAttay;
	}

	/**
	 * Decrypts the given data
	 *
	 * @param  string $iv
	 * @param  string $data
	 * @return mixed
	 */
	public function decrypt($iv, $data)
	{
		$iv   = base64_decode($iv);
		$data = base64_decode($data);

		mcrypt_generic_init($this->_cryptDescriptor, $this->_cryptKey, $iv);
		$chipData = mdecrypt_generic($this->_cryptDescriptor, $data);
		mcrypt_generic_deinit($this->_cryptDescriptor);

		return unserialize($chipData);
	}

	public function __destruct()
	{
		mcrypt_module_close($this->_cryptDescriptor);
	}
}
?>