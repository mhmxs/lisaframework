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
 * Load and represent PHPMailer in namespaces.
 * @package External
 * @subpackage PHPMailer
 * @author Somlyai Dezső
 */
namespace External\PHPMailer;

if (!include_once(DIR_EXT . "/PHPMailer/class.phpmailer.php")) {
	trigger_error("Failed to load PHPMailer.", E_USER_ERROR);
}

class PHPMailer extends \PHPMailer {

	/**
	 * Sender mail addres and name.
	 * @access protected
	 * @var mixed
	 */
	protected $_mail = array();

	/**
	 * Constructor
	 * @access private
	 * @param mixed $sender
	 * @param string $smtpConnection
	 * @param bool $exceptions
	 * @return void
	 */
	public function __construct( $sender, $smtpConnection, $exceptions = false)	{

		$this->exceptions = ($exceptions == true);

		if (\Util\FileHandler::init(DIR_CONFIG . "/smtp.ini")->isExists()) {
			$connection = parse_ini_file(DIR_CONFIG . "/smtp.ini", true);
			if (isset($connection[$smtpConnection])) {
				$this->_mail = $sender;

				$this->_connect($connection[$smtpConnection]);
			} else {
				 throw new \Exception("Smtp connection settings not found.");
			}
		} else {
			 throw new \Exception("smtp.ini not found.");
		}

	}

	protected function _connect($connection) {


		if (isset($connection["smtp_auth"]) && strtolower($connection["smtp_auth"]) == "on") {

			$this->SMTPAuth = true;
			$this->WordWrap = 50;
			$this->IsHTML(true);
			$this->IsSMTP();
			$this->CharSet = "utf-8";

			if (isset($connection["smtp_host"])) {
				$this->Host = $connection["smtp_host"];
			}

			if (isset($connection["smtp_secure"])) {
				$this->SMTPSecure = $connection["smtp_secure"];
			}

			if (isset($connection["smtp_port"])) {
				$this->Port = $connection["smtp_port"];
			}

			if (isset($connection["smtp_keep_alive"]) && $connection["smtp_keep_alive"] == "on") {
				$this->SMTPKeepAlive = true;
			}

			if (isset($connection["smtp_user"])) {
				$this->Username = $connection["smtp_user"];
			}

			if (isset($connection["smtp_pass"])) {
				$this->Password = $connection["smtp_pass"];
			}

		} else {
			$this->SMTPAuth = false;
		}
	}

	/**
	 * Factory method from PHPMailerController
	 * @access public
	 * static
	 * @param mixed $sender
	 * @param string $smtpConnection
	 * @param bool $exceptions
	 * @return PHPMailerController
	 */
	public static function init( $sender, $smtpConnection = "default", $exceptions = false ) {
		return new self( $sender, $smtpConnection, $exceptions );
	}

	/**
	 * Sending email
	 * @access public
	 * @param string $body
	 * @param string $subject
	 * @param array $to
	 * @return bool
	 */
	public function sendMail($body, $subject, $to)	{
		if ($this->SMTPAuth === true) {


			$this->From = $this->_mail[0];

			$this->FromName = isset($this->_mail[1]) ? $this->_mail[1] : "";

			$this->Subject = $subject;
			$this->Body    = $body;

			foreach ($to as $address => $name) {
				$this->AddAddress( $address , $name);
			}
			if (!$this->Send()) {
				throw new \Exception($this->ErrorInfo);
			}

		} else {
			$body = mb_convert_encoding(str_replace("\n.", "\n..", $body), "ISO-8859-1", "UTF-8"); //Windows bug

			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: ' . mb_convert_encoding(isset($this->_mail[1]) ? $this->_mail[1] : "", "ISO-8859-1", "UTF-8") . ' <' . $this->_mail[0] . '>' . "\r\n";
			$headers .= 'X-Mailer: PHP/' . phpversion();

			if (!@mail(join(",", array_keys($to)), mb_convert_encoding($subject, "ISO-8859-1", "UTF-8"), $body, $headers)) {
				throw new \Exception("Message(s) could not be sent.");
			}
		}
	}

}