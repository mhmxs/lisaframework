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
 * AView abstract view classes
 * @package Core
 * @subpackage View
 * @category Abstract
 * @author kovacsricsi
 */

abstract class AView
{
	/**
	 * Template directory.
	 * @access protected
	 * @var string
	 */
	protected $_templateDirectory;

	/**
	 * Template for view.
	 * @access protected
	 * @var string
	 */
	protected $_template;

	/**
	 * Layout for the view.
	 * @access protected
	 * @var string
	 */
	protected $_layout;

	/**
	 * Output string.
	 * @access protected
	 * @var string
	 */
	protected $_output;

	/**
	 * Template data.
	 * @access protected
	 * @var array
	 */
	protected $_data;

	/**
	 * Head tags for the view.
	 * @access protected
	 * @var array
	 */
	protected $_headAddons;

	/**
	 * Title tag for view.
	 * @access protected
	 * @var Title
	 */
	protected $_title;

	/**
	 * Constructor.
	 * @access public
	 * @param string $templateDirectory
	 * @param string $template
	 * @param string $layout
	 * @param boolean $cacheabe
	 * @return void
	 */
	 public function __construct($templateDirectory = "", $template = "", $layout = "", $title = null)
	 {
		$this->_templateDirectory  = (string)$templateDirectory;
	 	$this->_template           = (string)$template;
		$this->_layout             = (string)$layout;
		$this->_title              = ($title !== null) ? new Title((string)$title) : $title;
		$this->_headAddons         = array();
	 	$this->_data               = array();
		$this->_output             = "";
	 }

	 /**
	  * Set template directory.
	  * @access public
	  * @param string $templateDirectory
	  * @return void
	  */
	public function setTemplateDirectory($templateDirectory)
	{
		$this->_templateDirectory = (string)$templateDirectory;
	}

	 /**
	  * Set template for view.
	  * @access public
	  * @param string $template
	  * @return void
	  */
	public function setTemplate($template)
	{
		$this->_template = (string)$template;
	}

	 /**
	  * Get template of view.
	  * @access public
	  * @return string
	  */
	public function getTemplate()
	{
		return $this->_template;
	}

	 /**
	  * Set layout for view.
	  * @access public
	  * @param string $layout
	  * @return void
	  */
	public function setLayout($layout)
	{
		$this->_layout = (string)$layout;
	}

	 /**
	  * Get layout of view.
	  * @access public
	  * @return string
	  */
	public function getLayout()
	{
		return $this->_layout;
	}

	 /**
	  * Store template data.
	  * @access public
	  * @param string $name
	  * @param mixed $data
	  * @return void
	  */
	public function setVar($name, $data)
	{
		$this->_data[$name] =  $data;
	}

	 /**
	  * Get template data.
	  * @access public
	  * @param string $name
	  * @return mixed
	  */
	public function getVar($name)
	{
		if (isset($this->_data[$name])) {
			$this->_data[$name];
		} else {
			return null;
		}
	}

	 /**
	  * Get all template data.
	  * @access public
	  * @return array
	  */
	public function getVars()
	{
		return $this->_data;
	}

	/**
	 * Set title for view.
	 * @access public
	 * @param string
	 * @return void
	 */
	public function setTitle($title)
	{
		$this->_title = new Title($title);
	}

	/**
	 * Add meta tag to head addons.
	 * @access public
	 * @param array $attribs
	 * @return void
	 */
	public function addMeta($attribs)
	{
		$this->_headAddons[] = new Meta($attribs);
	}

	/**
	 * Add script tag to head addons.
	 * @access public
	 * @param string $src
	 * @param array $attribs
	 * @return void
	 */
	public function addScript($src, $attribs = null)
	{
		$this->_headAddons[] = new Script((string)$src, $attribs);
	}

	/**
	 * Add style tag to head addons.
	 * @access public
	 * @param string $href
	 * @param array $attribs
	 * @return void
	 */
	public function addStyle($href, $attribs = null)
	{
		$this->_headAddons[] = new Style((string)$href, $attribs);
	}

	/**
	 * Returns output.
	 * @access public
	 * @return string
	 */
	public function getOutput()
	{
		$this->_checkView();

		$this->_createOutput();

		return $this->_output;
	}

	/**
	 * Check view for create output.
	 * @access protected
	 * @return void
	 */
	protected function _checkView()
	{
		if (!FileHandler::init($this->_templateDirectory . "/" . $this->_template)->isExists()) {
			throw new TemplateNotFoundException($this->_template);
		}

		if(($this->_layout !== "") && !FileHandler::init($this->_templateDirectory . "/layouts/" . $this->_layout)->isExists()) {
			throw new TemplateNotFoundException("/layouts/". $this->_layout);
		}
	}

	/**
	 * Create output.
	 * @access protected
	 * @return void
	 */
	 abstract protected function _createOutput();
}

?>