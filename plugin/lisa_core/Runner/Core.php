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
 * Core class. Initialises the basic functions of the framework after the bootstrap procedure. Run Runner usually HTTPRunner.
 * @package Core
 * @subpackage Runner
 * @author nullstring
 */

namespace lisa_core\Runner;

class Core implements IRunner {

    /**
     * Session handler
     * @access private
     * @var \lisa_core_api\ISessionHandler
     */
    private $_sessionHandler;

    /**
     * Get instance method.
     * @access public
     * @static
     * @return Core
     */
    public function getInstance() {
        return new self();
    }

    /**
     * Constructor. MUST run the pre_core plugins.
     * @access private
     * @return void
     */
    private function __construct() {
        if (!is_dir(DIR_ROOT . "/tmp")) {
            die503("Missing tmp directory, please create cache, logs, sessions in tmp directory, sample is in the package!");
        }
    }

    /**
     * Run Core processing.
     * @access public
     * @return void
     */
    public function run() {
        $Reader = \lisa_util\Config\Cache::getConfig(DIR_ROOT . "/config/Config.ini");
        $service = $Reader->SESSION->service;
        if ($service) {
            $this->_sessionHandler = \Context::getService($service);
        }
        if (is_null($this->_sessionHandler)) {
            session_start();
        }
//TODO filtereket megcsinálni
        //Filter::runPreFilters()->run();

        $httpRunner = new HTTP();
        $httpRunner->run();
    }

    /**
     * Destructor. MUST run the post_core plugins.
     * @public
     * @return void
     */
    public function __destruct() {
        if ($this->_sessionHandler instanceof \lisa_core_api\ISessionHandler) {
            $this->_sessionHandler->writeSession();
        }

        //Filter::runPostFilters()->run();
    }

	private function recursive_copy($from, $to) {
		if(is_dir($from)) {
			$dir = opendir($from); 
			while($file = readdir($dir))
			{
				if(strpos($file, '.') === 0)
					continue;
				$this->recursive_copy($from . '/' . $file, $to . '/' . $file);
			}
			closedir($dir);
		} else {
			copy($from, $to);
		}
	}

	public function init_plugin($context) {
		$pluginDir = $context['pluginDir'];
		$web_root_dir = $pluginDir . '/webroot';
		if(file_exists($web_root_dir)) 
			$this->recursive_copy($web_root_dir,  DIR_WEBROOT);
	}

}

?>