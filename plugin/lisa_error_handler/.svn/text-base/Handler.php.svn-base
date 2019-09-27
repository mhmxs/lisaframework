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
 * Basic error logger
 * This classes duty is to catch errors during the startup phase of the system,
 * log them properly and display an error message to the user (500) if needed.
 * It is important, that this class uses no advanced classes, because it gets
 * loaded before the autoloader. Note, that this class is not really suited for
 * large scale logging, because there are no options to configure for this class
 * besides the log directory.
 * @package ErrorHandler
 * @author nullstring
 */
namespace lisa_error_handler;

class Handler
{
	/**
	 * Old error handler storage. This stores the old error handler as
	 * long as this class is active.
	 * @access protected
	 * @var    string
	 */
	protected $old_error_handler;

	/**
	 * Old exception handler storage. This stores the old exception handler as
	 * long as this class is active.
	 * @access protected
	 * @var    string
	 */
	protected $old_exception_handler;

	/**
	 * Instance of Basic
	 * @access protected
	 * @staticvar Basic
	 */
	protected static $_instance = null;

	/**
	 * Get instance of Basic error Handler.
	 * @access public
	 * @static
	 * @return Basic
	 */
	public static function getInstance()
	{
		if (is_null(static::$_instance)) {
			$class = get_called_class();
			static::$_instance = new $class();
		}

		return static::$_instance;
	}

	/**
	 * Constructor. This initialises the error and exception handler.
	 * @access protected
	 * @return void
	 */
	protected function __construct()
	{
		$this->old_error_handler     = set_error_handler(array($this, "error_handler"));
		$this->old_exception_handler = set_exception_handler(array($this, "exception_handler"));
	}

	/**
	 * Trace message.
	 * @access public
	 * @param string $message
	 * @return void
	 */
	public function trace($message)
	{
		if (defined(ERR_TRACE) && ERR_TRACE === true) {
			$service_port = ERR_TRACE_PORT;
			$address      = ERR_TRACE_HOST;
			$socket       = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			if ($socket === false) {
				trigger_error("socket_create() failed: reason: " . socket_strerror(socket_last_error()), E_USER_WARNING);
			} else {
				$result = socket_connect($socket, $address, $service_port);

				if ($result === false) {
					$socket = false;
					trigger_error("socket_connect() failed: reason: " . socket_strerror(socket_last_error()), E_USER_WARNING);
				}
			}

			if ($socket && (strstr($_SERVER["REQUEST_URI"], ".jpg") === false) && (strstr($_SERVER["REQUEST_URI"], ".gif") === false) &&
			    (strstr($_SERVER["REQUEST_URI"], ".png") === false)) {

				$header  = date("Y-m-d H:i:s") . "   " . session_id() . "   ";
				$indents = strlen($header);
				$ind     = " ";

				for ($i = 0; $i < $indents; $i++) {
					$ind .= " ";
				}

				$message = $header . $_SERVER["REQUEST_URI"] . "    " . $_SERVER["REMOTE_ADDR"] . str_replace("\n", "\n" . $ind, "\n" . $message) . "\n";
				socket_write($socket, $message, strlen($message));
				socket_write($socket, "quit\n", strlen("quit\n"));
			}

			if ($socket) {
				socket_close($socket);
			}
		}
	}

	/**
	 * Writes an error into the debug log.
	 * @access public
	 * @param integer $errno Error code
	 * @param string  $errstr Error message
	 * @param string  $errfile File this error occured in
	 * @param string  $errline Line of code this error occured in
	 * @return void
	 */
	public function write_debug_log($errno, $errstr, $errfile, $errline)
	{
        $this->_checkAndCreateFile("/debug.log");

		$string = date("Y-m-d H:i:sO") . "\t" . @$_SERVER["HTTP_HOST"] . "\t" . @$_SERVER["REQUEST_URI"] . "\t" . $errfile . "\t" . $errline . "\t" . $errstr . "\n";

		$this->_writeToFile("/debug.log", $string);
	}

	/**
	 * Writes an error into the smarty log.
	 * @access public
	 * @param integer $errno Error code
	 * @param string  $errstr Error message
	 * @param string  $errfile File this error occured in
	 * @param string  $errline Line of code this error occured in
	 * @return void
	 */
	public function write_smarty_log($errno, $errstr, $errfile, $errline)
	{
        $this->_checkAndCreateFile("/smarty.log");

		$string = date("Y-m-d H:i:sO") . "\t" . @$_SERVER["HTTP_HOST"] . "\t" . @$_SERVER["REQUEST_URI"] . "\t" . $errfile . "\t" . $errline . "\t" . $errstr . "\n";

		$this->_writeToFile("/smarty.log", $string);
	}

	/**
	 * Writes an error into the process log.
	 * @access public
	 * @param integer $errno Error code
	 * @param string  $errstr Error message
	 * @param string  $errfile File this error occured in
	 * @param string  $errline Line of code this error occured in
	 * @return void
	 */
	public function write_process_log($errno, $errstr, $errfile, $errline)
	{
    	$this->_checkAndCreateFile("/process.log");

		$string = date("Y-m-d H:i:sO") . "\t" . @$_SERVER["HTTP_HOST"] . "\t" . @$_SERVER["REQUEST_URI"] . "\t" . $errfile . "\t" . $errline . "\t" . $errstr . "\n";

		$this->_writeToFile("/process.log", $string);
	}

	/**
	 * Writes an error into the error log.
	 * @access public
	 * @param integer $errno Error code
	 * @param string $errstr Error message
	 * @param string $errfile File this error occured in
	 * @param string $errline Line of code this error occured in
	 * @return void
	 */
	public function write_error_log($errno, $errstr, $errfile, $errline)
	{
        $this->_checkAndCreateFile("/error.log");

        $string = date("Y-m-d H:i:sO") . "\t" . @$_SERVER["HTTP_HOST"] . "\t" . @$_SERVER["REQUEST_URI"] . "\t" . $errfile . "\t" . $errline . "\t" . $errstr . "\n";

        $this->_writeToFile("/error.log", $string);
	}

	/**
	 * Writes a database error into the database error log.
	 * @access public
	 * @param string $databaseType
	 * @param integer $errno Error code
	 * @param string $errstr Error message
	 * @return void
	 */
	public function write_database_error_log($databaseType, $errno, $errstr)
	{
		$log = strtolower("/" . $databaseType . "_error.log");

		$this->_checkAndCreateFile($log);

		$string = date("Y-m-d H:i:sO") . "\t" . @$_SERVER["HTTP_HOST"] . "\t" . @$_SERVER["REQUEST_URI"]
			          . "\t" . $errno . "\t" . $errstr . "\n";
		$this->_writeToFile($log, $string);
	}

	/**
	 * Error handler to catch errors generated by trigger_error. See the PHP documentation for details.
	 * @access public
	 * @param integer $errno Error code
	 * @param string $errstr Error message
	 * @param string $errfile File this error occured in
	 * @param string $errline Line of code this error occured in
	 * @return void
	 */
	public function error_handler($errno, $errstr, $errfile, $errline)
	{
		if (stristr($errfile, "smarty") !== false) {
			$this->write_smarty_log($errno, $errstr, $errfile, $errline);
		} else {
			switch ($errno) {
				case E_USER_NOTICE:
					$this->write_debug_log($errno, $errstr, $errfile, $errline);
				break;

				case E_NOTICE:
					$this->write_debug_log($errno, $errstr, $errfile, $errline);
				break;

				case E_STRICT:
					$this->write_debug_log($errno, $errstr, $errfile, $errline);
				break;

				case E_USER_WARNING:
					$this->write_process_log($errno, $errstr, $errfile, $errline);
				break;

				case E_WARNING:
					$this->write_debug_log($errno, $errstr, $errfile, $errline);
				break;

				case E_USER_ERROR:
					$this->write_error_log($errno, $errstr, $errfile, $errline);
					die503("E_USER_ERROR");
				break;

				case E_ERROR:
					$this->write_error_log($errno, $errstr, $errfile, $errline);
					die503("E_ERROR");
				break;
			}
		}
	}

	/**
	 * Exception handler to catch uncaught exceptions. This is a fatal error and cannot be reverted.
	 * This function provides logging functionality.
	 * @access public
	 * @param Exception $exception
	 * @return void
	 */
	public function exception_handler(Exception $exception)
	{
		if ($exception instanceof \lisa_core\PagenotFoundException) {
			$Reader = \lisa_util\Config\Cache::getConfig(DIR_ROOT . "/config/ErrorHandler/Config.ini");
			
			if (($service = $Reader->PageNotFoundException->service) != false
					&& ($call = $Reader->PageNotFoundException->call) != false) {
				\Context::getService($service)->$call();
			} else {
				$view = \Context::getService("SimpleView");
				$view->setContent("404");
				\Context::getService("Http")->sendOutput($view, new \lisa_core_api\HttpHeader\NotFound404());
			}
		} elseif ($exception instanceof \InvalidParameterException) {
			$this->write_error_log(E_USER_ERROR, "Uncaught exception: " . $exception->getMessage(), $exception->getFile(), $exception->getLine());
			die503("Fatal error - uncaught exception!");
		}
	}

	/**
	 * Destructor. Restores old error and exception handlers.
	 * @access public
	 * @return void
	 */
	public function __destruct()
	{
		if (is_callable($this->old_error_handler)) {
			set_error_handler($this->old_error_handler);
		}

		if (is_callable($this->old_error_handler)) {
			set_exception_handler($this->old_exception_handler);
		}
	}

	/**
	 * Check and create file if not exists.
	 * @access protected
	 * @param string $file
	 * @return void
	 */
	protected function _checkAndCreateFile($file)
	{
	     if (!file_exists(DIR_LOGS . $file)) {
		   touch(DIR_LOGS . $file);
		   chmod(DIR_LOGS . $file, 0777);
		}
	}

	protected function _writeToFile($file, $message)
	{
		if ($fp = @fopen(DIR_LOGS . $file, "a")) {
			fwrite($fp, $message, strlen($message));
			fclose($fp);
		}
	}
}

?>