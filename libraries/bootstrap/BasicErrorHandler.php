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
 * @package Bootstrap
 * @author nullstring
 */
class BasicErrorHandler
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
	 * Instance of BasicErrorHandler
	 * @access protected
	 * @staticvar BasicErrorHandler
	 */
	protected static $_instance = null;
	
	/**
	 * Get instance of Basic error Handler.
	 * @access public
	 * @static
	 * @return BasicErrorHandler
	 */
	public static function getInstance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
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
	 * @static
	 * @param string $message
	 * @return void
	 */
	public static function trace($message)
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
        if (!file_exists(DIR_LOGS . "/debug.log")) {
		   touch(DIR_LOGS . "/debug.log");
		   chmod(DIR_LOGS . "/debug.log", 0777);
		}
		if ($fp = @fopen(DIR_LOGS . "/debug.log", "a")) {
			$string = date("Y-m-d H:i:sO") . "\t" . @$_SERVER["HTTP_HOST"] . "\t" . @$_SERVER["REQUEST_URI"]
			          . "\t" . $errfile . "\t" . $errline . "\t" . $errstr . "\n";

			fwrite($fp, $string, strlen($string));
			fclose($fp);
		}
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
        if (!file_exists(DIR_LOGS . "/smarty.log")) {
		   touch(DIR_LOGS . "/smarty.log");
		   chmod(DIR_LOGS . "/smarty.log", 0777);
		}
		if ($fp = @fopen(DIR_LOGS . "/smarty.log", "a")) {
			$string = date("Y-m-d H:i:sO") . "\t" . @$_SERVER["HTTP_HOST"] . "\t" . @$_SERVER["REQUEST_URI"]
			          . "\t" . $errfile . "\t" . $errline . "\t" . $errstr . "\n";

			fwrite($fp, $string, strlen($string));
			fclose($fp);
		}
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
        if (!file_exists(DIR_LOGS . "/process.log")) {
		   touch(DIR_LOGS . "/process.log");
		   chmod(DIR_LOGS . "/process.log", 0777);
		}
		if ($fp = @fopen(DIR_LOGS . "/process.log", "a")) {
			$string = date("Y-m-d H:i:sO") . "\t" . @$_SERVER["HTTP_HOST"] . "\t" . @$_SERVER["REQUEST_URI"]
			          . "\t" . $errfile . "\t" . $errline . "\t" . $errstr . "\n";

			fwrite($fp, $string, strlen($string));
			fclose($fp);
		}
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
        if (!file_exists(DIR_LOGS . "/error.log")) {
		   touch(DIR_LOGS . "/error.log");
		   chmod(DIR_LOGS . "/error.log", 0777);
		}
		if ($fp = @fopen(DIR_LOGS . "/error.log", "a")) {
			$string = date("Y-m-d H:i:sO") . "\t" . @$_SERVER["HTTP_HOST"] . "\t" . @$_SERVER["REQUEST_URI"]
			          . "\t" . $errfile . "\t" . $errline . "\t" . $errstr . "\n";
			fwrite($fp, $string, strlen($string));
			fclose($fp);
		}
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
		
        if (!file_exists(DIR_LOGS . $log)) {
		   touch(DIR_LOGS . $log);
		   chmod(DIR_LOGS . $log, 0777);
		}
		if ($fp = @fopen(DIR_LOGS . $log, "a")) {
			$string = date("Y-m-d H:i:sO") . "\t" . @$_SERVER["HTTP_HOST"] . "\t" . @$_SERVER["REQUEST_URI"]
			          . "\t" . $errno . "\t" . $errstr . "\n";
			fwrite($fp, $string, strlen($string));
			fclose($fp);
		}
	}

	/**
	 * Issues a HTTP/1.1 503 error and terminates the running with an error code effective immediately.
	 * @access protected
	 * @return void
	 */
	protected function issue_503()
	{
		@ob_end_clean();
		@header("HTTP/1.1 503 Service Unavailable");

		if ($fp = @fopen(dirname(__FILE__) . "/503.html", "r")) {
			fpassthru($fp);
		}

		exit(-503);
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
				$this->issue_503();
					break;

				case E_ERROR:
					$this->write_error_log($errno, $errstr, $errfile, $errline);
					$this->issue_503();
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
		$this->write_error_log(E_USER_ERROR, "Uncaught exception: " . $exception->getMessage(), $exception->getFile(), $exception->getLine());
		$this->issue_503();
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
}

?>