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
 * File(s) upload.
 * Required DirectoryHandler and StringHandler util from LISA framework
 * @package Util
 * @author Somlyai Dezső
 */
namespace lisa_util;

class FileUpload
{
	/**
	 * Uploaded file(s).
	 * @access protected
	 * @var array
	 */
	protected $_uploadedFiles = array();

	/**
	 * File(s) to upload generated from $_FILES variable.
	 * @access protected 
	 * @var array
	 */
	protected $_files = array();

	/**
	 * Destination path of uploaded file(s).
	 * @acces protected
	 * @var string
	 */
	protected $_path;

	/**
	 * Allowed file extensions.
	 * @access public
	 * @var mixed
	 */
	public $allowedExtensions;

	/**
	 * Generated safe filename.
	 * @access public
	 * @var bool
	 */
	public $safeFilename = true;

	/**
	 * Replace file path
	 * @access public
	 * @var string
	 */
	public $saveToPath = null;

	/**
	 * Safe filename character settings
	 * @access protected
	 * @var string
	 */
	protected $_charSet;

	/**
	 * Constructor.
	 * @access public
	 * @param string $fieldsName
	 * @param string $path
	 * @param mixed $allowedExtensions
	 * @param string $charset
     * @throws Exception
     * @return void
	 */
	public function __construct($fieldsName, $path, $allowedExtensions = null, $charset = null)
	{
        if (!isset($_FILES[$fieldsName])) {
            throw new \Exception("File not exists!");
        }

		$this->_path             = $path;
		$this->allowedExtensions = $allowedExtensions;
		$this->_charSet          = $charset;

		if (is_array($_FILES[$fieldsName]["name"])) {
			foreach($_FILES[$fieldsName]["error"] as $key => $error) {
				if ($error == UPLOAD_ERR_OK) {
					$this->_files[] = array(
						"name"		=> $this->safeFilename == true ? StringHandler::safeFileName($_FILES[$fieldsName]["name"][$key], $this->_charSet) : $_FILES[$fieldsName]["name"][$key],
						"type"		=> $_FILES[$fieldsName]["type"][$key],
						"tmp_name"	=> $_FILES[$fieldsName]["tmp_name"][$key],
						"size"		=> $_FILES[$fieldsName]["size"][$key]
					);
				}
			}
		} elseif ($_FILES[$fieldsName]["error"] == UPLOAD_ERR_OK) {
			$this->_files[] = $_FILES[$fieldsName];
			$this->_files[0]["name"] = $this->safeFilename == true ? StringHandler::safeFileName($this->_files[0]["name"], $this->_charSet) : $this->_files[0]["name"];

		}
	}

	/**
	 * Factory method of Upload
	 * @access public
	 * static
	 * @param string $fieldsName
	 * @param string $path
	 * @param mixed $allowedExtensions
	 * @param string $charset
	 * @return Upload 
	 */
	public static function init($fieldsName, $path, $allowedExtensions = null, $charset = null) {
		$class = get_called_class();
		return new $class($fieldsName, $path, $allowedExtensions, $charset);
	}

	/**
	 * List of successfully uploaded file(s).
	 * @access public
	 * @return array 
	 */
	public function getSucces() {
		return $this->_getFiles(true);
	}

	/**
	 * List of not uploaded file(s).
	 * @access public
	 * @return array
	 */
	public function getErrors() {
		return $this->_getFiles(false);
	}

	/**
	 * Commit upload.
	 * @access public
	 * @return array
	 */
	public function commit() {
		$this->_upload();
		return $this->_uploadedFiles;
	}

	/**
	 * List of all file(s).
	 * @access protected
	 * @param bool $access
	 * @return array
	 */
	protected function _getFiles($access = null) {

		if (is_null($access)) {
			$out = $this->_uploadedFiles;
		} else {
			$out = array();

			foreach($this->_uploadedFiles as $key=>$value) {
				if ($value == $access) {
					$out[] = $key;
				}
			}
		}

		return $out;
	}

	/**
	 * Copy files to other directory.
	 * @access protected
	 * @param string $from
	 * @param string $to
	 * @return boolean
	 */
	protected function _copyTo($from, $to) {
		return copy($from, $to);
	}

	/**
	 * Upload files.
	 * @access protected
	 * @return void
	 */
	protected function _upload() {
		if ($this->_files == true) {
			if (!is_null($this->allowedExtensions)) {
				$this->_checkExtensions();
			}

			$this->_checkFileSize();

			foreach ($this->_files as $key=>$value) {
				$path = $this->_checkPath($this->_path);
				$name = DirectoryHandler::getUniqueFileName($path, $value["name"]);

				$error = false;

				if (move_uploaded_file($value["tmp_name"], $path . $name)) {
					if (!is_null($this->saveToPath)) {
						if (!$this->_copyTo($path . $name, $this->_checkPath($this->saveToPath) . $name)) {
							$error = true;
						}
					}
				} else {
					$error = true;
				}

				if ($error === true) {
					$this->_uploadedFiles[$name] = false;
					unset($this->_files[$key]);
				} else {
					$this->_uploadedFiles[$name] = true;
				}
			}
		}

	}

	/**
	 * Check or create upload directory.
	 * @access protected
	 * @param string $path
	 * @return string
	 */
	protected function _checkPath($path = null) {
		if (!is_null($path)) {
			$path = trim($path, "/") . "/";
			DirectoryHandler::mkdirRecursive($path, 0774);
		}

		return $path;
	}

	/**
	 * Check upload max file size according to php.ini.
	 * @access protected
	 * @return void
	 */
	protected function _checkFileSize() {
		$size = ini_get("upload_max_filesize");

		if (preg_match("/^(.*)M$/i", $size, $matches)) {
			$iSize = $matches[1]*1024*1024;
		} elseif (preg_match("/^(.*)K$/i", $size, $matches)) {
			$iSize = $matches[1]*1024;
		} elseif (preg_match("/^(.*)G$/i", $size, $matches)) {
			$iSize = $matches[1]*1024*1024*1024;
		}

		foreach ($this->_files as $key => $value) {
			if ($value["size"] > $iSize) {
				$this->_uploadedFiles[$value["name"]] = false;
				unset($this->_files[$key]);
			}
		}
	}

	/**
	 * Check Allowed file extensions.
	 * @access protected
	 * @return void
	 */
	protected function _checkExtensions() {
		if (is_array($this->allowedExtensions)) {
			foreach($this->allowedExtensions as $key=>$value) {
				$this->allowedExtensions[$key] = ltrim($value, ".");
			}
		} else {
			$this->allowedExtensions = array(ltrim($this->allowedExtensions, "." ));
		}

		foreach($this->_files as $key=>$value) {
			$ext = ltrim(StringHandler::localeToCommonLower(strrchr($value["name"], "."), $this->_charSet), ".");

			if (!in_array($ext, $this->allowedExtensions) ) {
				$this->_uploadedFiles[$value["name"]] = false;
				unset($this->_files[$key]);
			}	
		}
	}
}
?>
