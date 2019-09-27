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
 * @subpackage API
 * @category Abstract
 * @author kovacsricsi
 */
namespace lisa_core_api;

abstract class AView implements IView
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
	 * Tags for the view. (CSS, JS in HEAD, JS before /BODY, inline js data in HEAD, meta)
	 * @access protected
	 * @var array
	 */
	protected $_addons;

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
	 * @param string $title
	 * @return void
	 */
	public function __construct($templateDirectory = "", $template = "", $layout = "", $title = null)
	{
		$this->_templateDirectory  = (string)$templateDirectory;
		$this->_template           = (string)$template;
		$this->_layout             = (string)$layout;
		$this->_title              = ($title !== null) ? new \lisa_core_api\HttpHelper\Title((string)$title) : $title;
		$this->_addons			   = array();
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
        $this->$name = $data;
	}

	 /**
	  * Get template data.
	  * @access public
	  * @param string $name
	  * @return mixed
	  */
	public function getVar($name)
	{
        $this->$name;
	}

	 /**
	  * Store template data.
	  * @access public
	  * @param string $name
	  * @param mixed $data
	  * @return void
	  */
	public function __set($name, $data)
	{
		$this->_data[$name] = $data;
	}

	 /**
	  * Get template data.
	  * @access public
	  * @param string $name
	  * @return mixed
	  */
	public function __get($name)
	{
		if (isset($this->_data[$name])) {
			return $this->_data[$name];
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
		$this->_title = new \lisa_core_api\HttpHelper\Title($title);
	}

    /**
	 * Returns title string.
	 * @access public
	 * @return string
	 */
	public function getTitle()
	{
		return (is_null($this->_title)) ? null: $this->_title->getTitle();
	}

	/**
	  * Script vars to head addons.
	  * @access public
	  * @param array $data
	  * @return void
	  */
	public function addScriptVars(array $data = array(), $attribs = null)
	{
		$this->_addons["scriptVars"][] = new \lisa_core_api\HttpHelper\ScriptVars($data, $attribs);
	}

	/**
	 * Add meta tag to head addons.
	 * @access public
	 * @param array $attribs
	 * @return void
	 */
	public function addMeta($attribs)
	{
		$this->_addons["meta"][] = new \lisa_core_api\HttpHelper\Meta($attribs);
	}

	/**
	 * Add link tag to head addons.
	 * @access public
	 * @param array $attribs
	 * @return void
	 */
	public function addLink($attribs)
	{
		$this->_addons["link"][] = new \lisa_core_api\HttpHelper\Link($attribs);
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
		$this->_addons["script"][] = new \lisa_core_api\HttpHelper\Script((string)$src, $attribs);
	}

	/**
	 * Add script before </body> tag.
	 * @access public
	 * @param string $src
	 * @param array $attribs
	 * @return void
	 */
	public function addBottomScript($src, $attribs = null)
	{
		$this->_addons["bottomScript"][] = new \lisa_core_api\HttpHelper\Script((string)$src, $attribs);
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
		$this->_addons["style"][] = new \lisa_core_api\HttpHelper\Style((string)$href, $attribs);
	}

	/**
	 * Add rss feed tag to head addons.
	 * @access public
	 * @param string $href
	 * @param string $title
	 * @return void
	 */
	public function addRssLink($href, $title = null)
	{
		$this->_addons["rssLink"] = new \lisa_core_api\HttpHelper\RssFeed((string)$href, (string)$title);
	}

	/**
	 * Returns output.
	 * @access public
	 * @return string
	 */
	public function getContent()
	{
		$this->_createOutput();

		return $this->_output;
	}

	/**
	 * Return head addons
	 * @access protected
	 * @return string
	 */
	protected function _getHeadAddons()
	{
		$addons = "";
		if ( isset($this->_addons["scriptVars"]) ){
			foreach($this->_addons["scriptVars"] as $addon) {
				$addons .= chr(9) . $addon->getHelper() . "\r\n";
			}
		}
		if ( isset($this->_addons["meta"]) ){
			foreach($this->_addons["meta"] as $addon) {
				$addons .= chr(9) . $addon->getHelper() . "\r\n";
			}
		}
		if ( isset($this->_addons["link"]) ){
			foreach($this->_addons["link"] as $addon) {
				$addons .= chr(9) . $addon->getHelper() . "\r\n";
			}
		}
		if ( isset($this->_addons["style"]) ){
			foreach($this->_addons["style"] as $addon) {
				$addons .= chr(9) . $addon->getHelper() . "\r\n";
			}
		}
		if ( isset($this->_addons["rssLinks"]) ){
			foreach($this->_addons["rssLinks"] as $addon) {
				$addons .= chr(9) . $addon->getHelper() . "\r\n";
			}
		}
		if ( isset($this->_addons["script"]) ){
			foreach($this->_addons["script"] as $addon) {
				$addons .= chr(9) . $addon->getHelper() . "\r\n";
			}
		}
		return $addons;
	}

    /**
	 * Modifies layout, setting virtual subdomains in inner resources.
	 * @access protected
     * @param string
	 * @return string
	 */
	protected function _paralellizeResources($layout)
	{
        $configReader = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Config.ini");
        if ($configReader->PARALLELSUBDOMAINS->enabled == true){
            $innerLinkMatches = array();
            $types = array();
            if ($configReader->PARALLELSUBDOMAINS->img != "") {
                $types[] = "img";
            }
            if ($configReader->PARALLELSUBDOMAINS->script != "") {
                $types[] = "script";
            }
            if ($configReader->PARALLELSUBDOMAINS->link != "") {
                $types[] = "link";
            }
            preg_match_all("/<(".join("|", $types).").*?(href|src)[\s]*=[\s]*(\"(\/\S*)\"|'(\/\S*)').*?>/i", $layout, $innerLinkMatches);
            foreach ($innerLinkMatches[0] as $key => $htmlElement) {
                $link = ( $innerLinkMatches[4][$key] == "" ) ? $innerLinkMatches[5][$key]: $innerLinkMatches[4][$key];
                $hosts = explode(",",$configReader->PARALLELSUBDOMAINS->$innerLinkMatches[1][$key]);
                $host = $hosts[md5($link) % count($hosts)];
                $htmlElementNew = str_replace($link, "http://". $host .".".$_SERVER["HTTP_HOST"].$link, $htmlElement);
                $layout = str_replace($htmlElement, $htmlElementNew, $layout);
            }
        }
        return $layout;
	}

	/**
	 * Return addons before </BODY> (bottomScripts)
	 * @access protected
	 * @return string
	 */
	protected function _getBottomAddons()
	{
		$addons = "";
		if ( isset($this->_addons["bottomScript"]) ){
			foreach($this->_addons["bottomScript"] as $addon) {
				$addons .= chr(9) . $addon->getHelper() . "\r\n";
			}
		}
		return $addons;
	}

	/**
	 * Create output.
	 * @access protected
	 * @return void
	 */
	abstract protected function _createOutput();
}

?>