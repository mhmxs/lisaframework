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
 * FileHandler class implements all functions with files.
  * @package Util
  * @author kovacsricsi
  */
class FileHandler implements Iterator
{
	/**
	 * Pointer position in file.
	 *
	 * @access protected
	 * @var    int
	 */
	protected $_seek;

	/**
	 * File resource.
	 *
	 * @access protected
	 * @var    resource
	 */
	protected $_file;

	/**
	 * Name of file.
	 *
	 * @access protected
	 * @var    string
	 */
	protected $_fileName;

	/**
	 * Prevous line postion
	 *
	 * @access protected
	 * @var    array
	 */
	protected $_prevLines;

	/**
	 * Factory method for FileHandler
	 * @access public
	 * @static
	 * @return FileHandler
	 */
	public static function init($fileName)
	{
		return new self($fileName);
	}

	/**
	 * Constructor.
	 *
	 * @access public
	 * @param  string $fileName
	 * @return void
	 */
	public function __construct($fileName)
	{
		$this->_seek       = null;
		$this->_file       = null;
		$this->_fileName   = (string)$fileName;
		$this->_prevLines  = array();
	}

	/**
	 * Open file resource.
	 *
	 * @access protected
	 * @return void
	 */
	protected function _open($attrib)
	{
		$this->_file = fopen($this->_fileName, $attrib);

		$this->valid();

		if (($attrib == "r") && ($this->_seek === null)) {
			$this->_seek = ftell($this->_file);
		} elseif ($attrib == "r") {
			fseek($this->_file, $this->_seek);
		}
	}

	/**
	 * Close file resource.
	 *
	 * @access protected
	 * @return void
	 */
	protected function _close()
	{
		fclose($this->_file);
	}

	/**
	 * Lock file for writer.
	 *
	 * @access protected
	 * @return void
	 */
	protected function _lock()
	{
		flock($this->_file, LOCK_EX);
	}

	/**
	 * Unock file for writer.
	 *
	 * @access protected
	 * @return void
	 */
	protected function _unlock()
	{
		flock($this->_file, LOCK_UN);
	}

	/**
	 * Delete file from had disk.
	 *
	 * @access public
	 * @return boolean
	 */
	public function delete()
	{
		return unlink($this->_fileName);
	}

	/**
	 * Erase file content.
	 *
	 * @access public
	 * @return boolean
	 */
	public function erase()
	{
		$this->overWrite("");
	}

	/**
	 * Overwrite the file with new string.
	 *
	 * @access public
	 * @param  string  $data
	 * @param  boolean $lock
	 * @return void
	 */
	public function overWrite($data, $lock = true)
	{
		$this->_open("w");

		if ($lock === true) {
			$this->_lock();
		}

		fwrite($this->_file, $data);

		$this->_seek = null;

		if ($lock === true) {
			$this->_unlock();
		}

		$this->_close();
	}

	/**
	 * Write string to the end of the file.
	 *
	 * @access public
	 * @param  string  $data
	 * @param  boolean $lock
	 * @return void
	 */
	public function append($data, $lock = true)
	{
		$this->_open("a");

		if ($lock === true) {
			$this->_lock();
		}

		fwrite($this->_file, $data);

		$this->_seek = null;

		if ($lock === true) {
			$this->_unlock();
		}

		$this->_close();
	}

	/**
	 * Check file exists or not.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isExists()
	{
		return file_exists($this->_fileName);
	}

	/**
	 * Returns file writeable status.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isWritable()
	{
		if ($this->isExists($this->_fileName)) {
			return is_writable($this->_fileName);
		} else {
			return null;
		}
	}

	/**
	 * Returns file readable status.
	 *
	 * @access public
	 * @return boolean
	 */
	public function isReadable()
	{
		if ($this->isExists($this->_fileName)) {
			return is_readable($this->_fileName);
		} else {
			return null;
		}
	}

	/**
	 * Returns with file modification time.
	 *
	 * @access public
	 * @return int
	 */
	public function getMTime()
	{
		if ($this->isExists($this->_fileName)) {
			return filemtime($this->_fileName);
		} else {
			return null;
		}
	}

	/**
	 * Returns with last access time of file.
	 *
	 * @access public
	 * @return int
	 */
	public function getATime()
	{
		if ($this->isExists($this->_fileName)) {
			return fileatime($this->_fileName);
		} else {
			return null;
		}
	}

	/**
	 * Returns with inode change time of file.
	 *
	 * @access public
	 * @return int
	 */
	public function getCTime()
	{
		if ($this->isExists($this->_fileName)) {
			return filectime($this->_fileName);
		} else {
			return null;
		}
	}

	/**
	 * Returns with file size in bytes.
	 *
	 * @access public
	 * @return int
	 */
	public function getSize()
	{
		if ($this->isExists($this->_fileName)) {
			return filesize($this->_fileName);
		} else {
			return null;
		}
	}

	/**
	 * Returns with file user's id.
	 *
	 * @access public
	 * @return int
	 */
	public function getUID()
	{
		if ($this->isExists($this->_fileName)) {
			return fileowner($this->_fileName);
		} else {
			return null;
		}
	}

	/**
	 * Returns with file group's id.
	 *
	 * @access public
	 * @return int
	 */
	public function getGID()
	{
		if ($this->isExists($this->_fileName)) {
			return filegroup($this->_fileName);
		} else {
			return null;
		}
	}

	/**
	 * Returns the Mine type of the file.
	 *
	 * @access public
	 * @return string
	 */
	public function getMimeType()
	{
		if ($this->isExists($this->_fileName)) {
			return filetype($this->_fileName);
		} else {
			return null;
		}
	}

	/**
	 * Reads entire file into an array
	 *
	 * @access public
	 * @return array
	 */
	public function getLines()
	{
		if ($this->isExists($this->_fileName)) {
			return file($this->_fileName);
		} else {
			return null;
		}
	}

	/**
	 * Reads entire file into a string
	 *
	 * @access public
	 * @return string
	 */
	public function getContents()
	{
		if ($this->isExists($this->_fileName)) {
			return file_get_contents($this->_fileName);
		} else {
			return null;
		}
	}

	/**
	 * Returns the current line if not end of file.
	 *
	 * @access public
	 * @return string
	 */
	public function getLine()
	{
        $this->_open("r");

		$this->valid();

		if (feof($this->_file)) {
            $this->_close();

			return null;
		} else {
			$line = fgets($this->_file, 1024);

			fseek($this->_file, $this->_seek);

            $this->_close();

			return $line;
		}
	}

	/**
	 * Returns the current character.
	 *
	 * @access public
	 * @return string
	 */
	public function current()
	{
        $this->_open("r");

		$this->valid();

		$c = fgetc($this->_file);

		fseek($this->_file, $this->_seek);

        $this->_close();

		return $c;
	}

	/**
	 * Returns the actual position.
	 *
	 * @access public
	 * @return int
	 */
	public function key()
	{
		return $this->_seek;
	}

	/**
	 * Returns next line from file.
	 *
	 * @access public
	 * @return sting
	 */
	public function next()
	{
        $this->_open("r");

		$this->valid();

		if (feof($this->_file)) {
            $this->_close();

			return null;
		} else {
			$this->_prevLines[] = $this->_seek;

			$line = fgets($this->_file, 1024);

			$this->_seek = ftell($this->_file);

            $this->_close();

			return $line;
		}
	}

	/**
	 * Returns prevous character from file
	 *
	 * @access public
	 * @return string
	 */
	public function prev()
	{
        $this->_open("r");

		if (($this->_seek > 0) && (count($this->_prevLines) > 0)) {
			$this->_seek = array_pop($this->_prevLines);

			fseek($this->_file, $this->_seek);

            $this->_close();

			return $this->getLine();
		} else {
            $this->_close();

			return null;
		}
	}

	/**
	 * Position file pointer to the begin of the file.
	 *
	 * @access public
	 * @return void
	 */
	public function rewind()
	{
        $this->_open("r");

		$this->valid();

		$this->_seek = 0;

		$r = rewind($this->_file);

        $this->_close();

        return $r;
	}

	/**
	 * Position file pointer to the end of file.
	 *
	 * @access public
	 * @return int
	 */
	public function fwind()
	{
        $this->_open("r");

		$this->valid();

		fseek($this->_file, 0, SEEK_END);

		$this->_seek = ftell($this->_file);

        $this->_close();

		return $this->_seek;
	}

	/**
	 * Validate file resource.
	 *
	 * @access public
	 * @throws Exception
	 * @return void
	 */
	public function valid()
	{
		if (!is_resource($this->_file)) {
			throw new Exception("File error : " . $this->_fileName);
		}
	}
}
?>