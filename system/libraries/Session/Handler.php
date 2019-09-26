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
 * SessionHandler manage sessions.
 * @package Core
 * @subpackage Session
 * @author kovacsricsi
 */

namespace Core\Session;

class Handler {

    /**
     * Expire date to sessions
     * @access proteced
     * @var string
     */
    protected $_timeAllow;
    /**
     * Path to save sessions
     * @access proteced
     * @var string
     */
    protected $_sessionPath;
    /**
     * Name of session.
     * @access protected
     * @var string
     */
    protected $_name;
    /**
     * Answer the is new session?
     * @access protected
     * @var boolean
     */
    protected $_isNew;

    /**
     * Constructor set session.
     * @access public
     * @param string $name
     * @return void
     */
    public function __construct($name = "LISA_Session") {
        $this->_name = $name;
        $this->_isNew = false;

        $configReader = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Config.ini");
        $this->_sessionPath = DIR_ROOT . $configReader->SESSION->save_path;
        $this->_timeAllow = date("Y-m-d H:i:s", mktime(date("H"), (date("i") - $configReader->SESSION->expire), date("s"), date("m"), date("d"), date("Y")));

        ini_set("session.entropy_file", "/dev/urandom");
        ini_set("session.entropy_length", "512");
        ini_set("session.save_handler", $configReader->SESSION->save_handler);
        ini_set("session.save_path", $this->_sessionPath);
        ini_set("session.gc_probability", $configReader->SESSION->gcprobability);
        ini_set("session.cache_expire", $configReader->SESSION->expire);

        session_set_save_handler(array($this, "open"), array($this, "close"), array($this, "read"), array($this, "write"), array($this, "destroy"), array($this, "gc"));

        session_start($this->_name);
    }

    /**
     * Open function; Opens/starts session.
     * @param string $save_path
     * @param string $session_name
     * @return bool
     */
    public function open($save_path, $session_name) {
        return true;
    }

    /**
     * Close function; closes session
     * closes mysql connection
     * @access public
     * @return bool
     */
    public function close() {
        return true;
    }

    /**
     * Read function; downloads data from repository to current session
     * Queries the mysql database, unencrypts data, and returns it.
     * This function protects against session theft.
     * @param  string $id Session ID.
     * @throws InvalidSessionException
     * @return mixed Session data.
     */
    public function read($id) {
        if (!file_exists($this->_sessionPath . "/" . $id) || (filemtime($this->_sessionPath . "/" . $id) < (time() - $this->_timeAllow))) {
            $this->_isNew = true;
            return false;
        } else {
            return unserialize(file_get_contents($this->_sessionPath . "/" . $id));
        }
    }

    /**
     * Write function; uploads data from current session to repository
     * Inserts/updates mysql records of current session.
     * @access public
     * @param string $id Session ID
     * @param string $data Session data
     * @return bool
     */
    public function write($id, $data) {
        if ($this->_isNew && !$data) {
            return true;
        }
        file_put_contents($this->_sessionPath . "/" . $id, serialize($data));
        return true;
    }

    /**
     * destroy function; deletes session data
     * deletes records of current session. called ONLY when function 'session_destroy()'
     * is called
     * @access public
     * @param string $id Session ID
     * @return boolean
     */
    public function destroy($id) {
        @unlink($this->_sessionPath . "/" . $id);
        return true;
    }

    /**
     * Garbage collector. Deletes old sessions.
     * @access public
     * @param int $expire Expiration time
     * @return boolean
     */
    public function gc($expire) {
        foreach (\Util\DirectoryHandler::getFiles($this->_sessionPath) as $session) {
            if (filemtime($this->_sessionPath . "/" . $session) < (time() - $expire)) {
                unlink($this->_sessionPath . "/" . $session);
            }
        }

        return true;
    }

    /**
     * Destructor - fixes the chicken and egg problem with the session being written after all object are destroyed.
     * @access public
     * @return boolean
     */
    public function writeSession() {
        session_write_close();
        return true;
    }

    /**
     * Destructor
     * @access public
     * @return void
     */
    public function __destruct() {
        $this->writeSession();
    }

    /**
     * Destroy session.
     * @access protected
     * @return boolean
     */
    protected function _destroy() {
        if (session_destroy() == true) {
            $_SESSION = array();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Regenerate session id.
     * @access public
     * @static
     * @param boolean $deleteOld
     * @return boolean
     */
    public static function regenerateId($deleteOld = true) {
        $_SESSION = array();
        return (session_destroy() & session_regenerate_id((boolean) $deleteOld)) ? true : false;
    }

}

?>

