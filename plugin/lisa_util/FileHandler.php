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
namespace lisa_util;

class FileHandler implements \Iterator
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
		$class = get_called_class();
		return new $class($fileName);
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
	 * @return int
	 */
	public function overWrite($data, $lock = true)
	{
		$this->_open("w");

		if ($lock === true) {
			$this->_lock();
		}

		$bytes = fwrite($this->_file, $data);

		$this->_seek = null;

		if ($lock === true) {
			$this->_unlock();
		}

		$this->_close();

		return $bytes;
	}

	/**
	 * Write string to the end of the file.
	 *
	 * @access public
	 * @param  string  $data
	 * @param  boolean $lock
	 * @return int
	 */
	public function append($data, $lock = true)
	{
		$this->_open("a");

		if ($lock === true) {
			$this->_lock();
		}

		$bytes = fwrite($this->_file, $data);

		$this->_seek = null;

		if ($lock === true) {
			$this->_unlock();
		}

		$this->_close();

		return $bytes;
	}

	/**
	 * Rename file.
	 *
	 * @access public
	 * @return boolean
	 */
	public function rename($newName)
	{
		if (rename($this->_fileName, $newName)) {
			$this->_fileName = $newName;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Touch file.
	 * @access public
	 * @return boolean
	 */
	public function touch()
	{
		return touch($this->_fileName);
	}

	/**
	 * Change chmod of file.
	 * @access public
	 * @return boolean
	 */
	public function chmod($mod)
	{
		return chmod($this->_fileName, $mod);
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
	 * Returns with basename of file.
	 * @access public
	 * @return string
	 */
	public function getBaseName()
	{
		return basename($this->_fileName);
	}

	/**
	 * Returns with dirname of file.
	 * @access public
	 * @return string
	 */
	public function getDirName()
	{
		return dirname($this->_fileName);
	}

	/**
	 * Returns with realpath of file.
	 * @access public
	 * @return string
	 */
	public function getRealPath()
	{
		return realpath($this->_fileName);
	}

	/**
	 * Returns with name of file without extension.
	 *
	 * @access public
	 * @return string
	 */
	public function getName()
	{
		return \Util\StringHandler::fileNameWithoutExtension($this->getBaseName());
	}

	/**
	 * Returns with file extension.
	 *
	 * @access public
	 * @return string
	 */
	public function getExtension()
	{
		return \Util\StringHandler::fileExtension($this->_fileName);
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
	 * Returns with file content type.
	 * @access public
	 * @return string
	 */
	public function getContentType()
	{
		switch ($this->getExtension()) {
			case "pdf":
				$ctype = "application/pdf";
			break;

			case "exe":
				$ctype = "application/octet-stream";
			break;

			case "zip":
				$ctype = "application/zip";
			break;

			case "doc":
				$ctype = "application/msword";
			break;

			case "xls":
				$ctype = "application/vnd.ms-excel";
			break;

			case "ppt":
				$ctype = "application/vnd.ms-powerpoint";
			break;

			case "gif":
				$ctype = "image/gif";
			break;

			case "png":
				$ctype = "image/png";
			break;

			case "jpe": case "jpeg": case "jpg":
				$ctype="image/jpg";
			break;

			default:
				$ctype = "application/force-download";
			break;
		}

		return $ctype;
	}

	/**
	 * Split a file into pieces matching a specific size.
	 * @access public
	 * @param string $outputDir
	 * @param integer $size
	 * @throws Exception
	 * @return integer
	 */
	public static function split($outputDir = false, $size = 10)
	{
		$outputDir = ($outputDir == false) ? pathinfo(str_replace('\\', '/', realpath($this->_fileName)), PATHINFO_DIRNAME) : str_replace('\\', '/', realpath($outputDir));
		$outputDir = rtrim($outputDir, '/').'/';

		$inFile = fopen($this->_fileName, 'rb');

		$size = 1024 * 1024 * (int) $size;

		$read  = 0;
		$piece = 1;
		$chunk = 1024 * 8;

		while (!feof($inFile)) {
			$outFileName = $this->_fileName.'.'.str_pad($piece, 3, '0', STR_PAD_LEFT);
			if (($outFile = @fopen($outFileName, 'wb+')) == false) {
				throw new Exception('Could not write piece '.$outFileName);
			}

			while ($read < $size && $data = fread($inFile, $chunk))
			{
				if (!fwrite($outFile, $data)) {
					throw new Exception('Could not write to open piece '.$outFileName);
				}

				$read += $chunk;
			}

			fclose($outFile);

			$read = 0;
			$piece++;
		}

		fclose($inFile);

		return ($piece - 1);
	}

	/**
	 * Join a splited file. You must initialize output filename with extensoin.
	 * @access public
	 * @throws Exception
	 * @return integer
	 */
	public static function join()
	{
		$piece = 1;
		$chunk = 1024 * 8;

		if (($outFile = @fopen($this->_fileName, 'wb+')) == false) {
			throw new Exception('Could not open output file '.$output);
		}

		while($inFile = @fopen(($this->_getName . '.' . str_pad($piece, 3, '0', STR_PAD_LEFT)), 'rb')) {
			while (!feof($inFile)) {
				fwrite($outFile, fread($inFile, $chunk));
			}

			fclose($inFile);

			$piece++;
		}

		fclose($outFile);

		return ($piece - 1);
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
			throw new \Exception("File error : " . $this->_fileName);
		}
	}
}
?>