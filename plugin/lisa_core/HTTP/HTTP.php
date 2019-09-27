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
 * HTTP comunicate with client, send response, handling headers, set cookies, get client information.
 * @package Core
 * @subpackage HTTP
 * @author kovacsricsi
 */

namespace lisa_core\HTTP;

class HTTP {

    /**
     * Get instance method.
     * @access public
     * @static
     * @return HTTP
     */
    public static function getInstance() {
        return new self();
    }

    /**
     * Constructor.
     * @access private
     * @return void
     */
    private function __construct() {

    }

    /**
     * Echo output.
     * @access public
     * @param \lisa_core_api\IView $view
     * @param \lisa_core_api\IHeader $header
     * @return void
     */
    public function sendOutput(\lisa_core_api\IView $view, \lisa_core_api\IHeader $header) {
        try {
            if (!is_null($header)) {
                foreach ($header->getHeaders() as $line) {
                    $this->sendHeader($line, true);
                }
            }

            $out = $view->getContent();

            $this->sendHeader("Content-Length: " . strlen($out), true);

            $this->write($out, false);
        } catch (\Exception $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * Send header.
     * @access public
     * @param string $header
     * @param boolean $replace
     * @param integer $code
     * @return void
     */
    public function sendHeader($header, $replace = null, $code = null) {
        header($header, $replace, $code);
    }

    /**
     * Returns with all setted headers
     * @access public
     * @return array
     */
    public function getHeaders() {
        return headers_list();
    }

    /**
     * Write string to output.
     * @access public
     * @param string $sring
     * @param boolean $flush
     * @return void
     */
    public function write($string, $flush = true) {
        echo (string) $string;

        if ($flush == true) {
            flush();
        }
    }

    /**
     * Set cookie and send to client.
     * @access public
     * @param string $name
     * @param mixed $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $httpOnly
     * @return boolean
     */
    public function setCookie($name, $value, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null) {
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Set cookie without urlencoding and send to client.
     * @access public
     * @param string $name
     * @param mixed $valie
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $httpOnly
     * @return boolean
     */
    public function setRawCookie($name, $value, $expire = null, $path = null, $domain = null, $secure = null, $httpOnly = null) {
        return setrawcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Returns client ip address
     * @access public
     * @return string
     */
    public function getIpAddress() {
        $ip = false;

        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }

        /**
         * User is behind a proxy and check that we discard RFC1918 IP addresses
         * if they are behind a proxy then only figure out which IP belongs to the
         * user.  Might not need any more hackin if there is a squid reverse proxy
         * infront of apache.
         */
        if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ips = explode(", ", $_SERVER["HTTP_X_FORWARDED_FOR"]);

            if ($ip) {
                array_unshift($ips, $ip);
                $ip = false;
            }

            for ($i = 0; $i < count($ips); $i++) {
                // Skip RFC 1918 IP's 10.0.0.0/8, 172.16.0.0/12 and 192.168.0.0/16
                if (!preg_match("/^(?:10|172\.(?:1[6-9]|2\d|3[01])|192\.168)\./", $ips[$i])) {
                    if (version_compare(phpversion(), "5.0.0", ">=")) {
                        if (ip2long($ips[$i]) != false) {
                            $ip = $ips[$i];
                            break;
                        }
                    } else {
                        if (ip2long($ips[$i]) != -1) {
                            $ip = $ips[$i];
                            break;
                        }
                    }
                }
            }
        }

        return ($ip != false) ? $ip : $_SERVER["REMOTE_ADDR"];
    }

    /**
     * Returns with hostname of client.
     * @access public
     * @return string
     */
    public function getHostname() {
        return @gethostbyaddr($this->getIpAddress());
    }

    /**
     * Return client agent info
     * @access public
     * @return string
     */
    public function getUserAgent() {
        return isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
    }

    /**
     * Redirect browser to the defined url.
     * @access public
     * @param string  $to
     * @param integer $code
     * @return boolean
     */
    public function redirect($to = null, $code = 301) {
        $location = null;

        if ($to === null) {
            $location = $_SERVER["REQUEST_URI"]; // reload
        } elseif (substr($to, 0, 4) == "http") {
            $location = $to; // Absolute URL
        } else {
            $schema = ($_SERVER["SERVER_PORT"] == "443") ? "https" : "http";
            $host = (strlen($_SERVER["HTTP_HOST"])) ? $_SERVER["HTTP_HOST"] : $_SERVER["SERVER_NAME"];
            if (substr($to, 0, 1) == "/") {
                $location = $schema . "://" . $host . $to;
            } elseif (substr($to, 0, 1) == ".") {
                $location = $schema . "://" . $host . "/";
                $pu = parse_url($to);
                $cd = dirname($_SERVER["SCRIPT_FILENAME"]) . "/";
                $np = realpath($cd . $pu["path"]);
                $np = str_replace($_SERVER["DOCUMENT_ROOT"], "", $np);
                $location .= $np;

                if ((isset($pu["query"])) && (strlen($pu["query"]) > 0)) {
                    $location .= "?" . $pu["query"];
                }
            }
        }

        $hs = headers_sent();
        if ($hs == false) {
            if ($code == 301) {
                header("301 Moved Permanently HTTP/1.1"); // Convert to GET
            } elseif ($code == 302) {
                header("302 Found HTTP/1.1"); // Conform re-POST
            } elseif ($code == 303) {
                header("303 See Other HTTP/1.1"); // dont cache, always use GET
            } elseif ($code == 304) {
                header("304 Not Modified HTTP/1.1"); // use cache
            } elseif ($code == 305) {
                header("305 Use Proxy HTTP/1.1");
            } elseif ($code == 306) {
                header("306 Not Used HTTP/1.1");
            } elseif ($code == 307) {
                header("307 Temorary Redirect HTTP/1.1");
            } else {
                $eh = \Context::getService('ErrorHandler');
                if (!is_null($eh)) {
                    $eh->trace("Unhandled redirect() HTTP Code: " . $code, E_USER_ERROR);
                }
            }

            header("Location: " . $location);
            header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
        } elseif (($hs == true) || ($code == 302) || ($code == 303)) {
            echo "<p>Please See: <a href=" . $to . ">" . htmlspecialchars($location) . "</a></p>\n";
        }

        exit();
    }

    /**
     * Redirect browser to the previous url.
     * @param bool $goToRoot
     * @return void
     */
    public function goBack($goToRoot = false) {
        $schema = ($_SERVER["SERVER_PORT"] == "443") ? "https" : "http";
        $host = (strlen($_SERVER["HTTP_HOST"])) ? $_SERVER["HTTP_HOST"] : $_SERVER["SERVER_NAME"];
        $referer = ( isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"] != $schema . ":\\" . $host . $_SERVER["REQUEST_URI"] ) ? $_SERVER["HTTP_REFERER"] : null;

        if (is_null($referer) && $goToRoot === true) {
            $referer = "/";
        }
        $this->redirect($referer);
    }

    /**
     * Tests if the current request is an AJAX request by checking the X-Requested-With.
     * @access public
     * @return boolean
     */
    public function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

}

?>