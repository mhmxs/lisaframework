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
 * CachedView extends View with cache funcionality
 * @package Core
 * @subpackage View
 * @category View
 * @author kovacsricsi
 */

class Cached extends View
{
	/**
	 * Is cached view or not.
	 * @access protected
	 * @var boolean
	 */
	protected $_isCached;

	/**
	 * Constructor.
	 * @access public
	 * @param string $templateDirectory
	 * @param string $template
	 * @param string $layout
	 * @param boolean $cacheabe
	 * @return void
	 */
	 public function __construct($templateDirectory = "", $template, $layout = "", $title = null, $cacheTime = 300)
	 {
	 	$this->_template = (string)$template;
	 	$this->_isCached = false;
		$this->_loadCachedView((int)$cacheTime);

		if ($this->_isCached === false) {
			parent::__construct($templateDirectory, $template, $layout, $title);
		}
	 }

	 /**
	  * load view from cache.
	  * @access protected
	  * @param integer $cacheTime
	  * @return void
	  */
	 protected function _loadCachedView($cacheTime)
	 {
		$view = DataCache::cacheGet("view/" . $this->_template, $cacheTime);

		if ($view !== false) {
			$this->_isCached           = true;
			$this->_templateDirectory  = $view->getTemplateDirectory();
			$this->_layout             = $view->getLayout();
			$this->_title              = $view->getTitle();
			$this->_addons			   = $view->getAddons();
		 	$this->_data               = $view->getVars();
			$this->_output             = $view->getOutput();
		}

		unset($view);
	 }

	 /**
	  * Returns with template directory.
	  * @access public
	  * @return string
	  */
	 public function getTemplateDirectory()
	 {
	 	return $this->_templateDirectory;
	 }

	 /**
	  * Returns with title.
	  * @access public
	  * @return Helper
	  */
	 public function getTitle()
	 {
	 	return $this->_title;
	 }

	 /**
	  * Returns with addons.
	  * @access public
	  * @return array
	  */
	 public function getAddons()
	 {
	 	return $this->_addons;
	 }


	 /**
	  * Returns boolean is cached or not question.
	  * @access public
	  * @return boolean
	  */
	 public function isCached()
	 {
	 	return $this->_isCached;
	 }

	 /**
	 * Create output.
	 * @access protected
	 * @return void
	 */
	 protected function _createOutput()
	 {
	 	if ($this->_isCached === false) {
	 		parent::_createOutput();

	 		$this->_isCached = true;
	 		DataCache::cacheSave("view/" . $this->_template, $this);
	 		$this->_isCached = false;
	 	}
	 }
}

?>