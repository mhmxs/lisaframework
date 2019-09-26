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
 * Load and represent Smarty in namespaces.
 * @package External
 * @subpackage Smarty
 * @author kovacsricsi
 */
namespace External\Smarty;

if (!include_once(DIR_EXT . "/Smarty/Smarty.class.php")) {
	trigger_error("Failed to load Smarty.", E_USER_ERROR);
}

class Smarty extends \Smarty
{
	/**
	 * Smarty.
	 * @access protected
	 * @var Smarty
	 */
    protected $smarty;

    /**
     * Constructor.
     * @access public
     * @return void
     */
    public function __construct()
    {
    	$reader = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Config.ini");

        $this->template_dir    = DIR_TEMPLATES;
        $this->config_dir      = DIR_CACHE . "/template/config";
        $this->compile_dir     = DIR_CACHE . "/template/compiled";
        $this->cache_dir       = DIR_CACHE . "/template/cached";
        $this->caching         = (int)$reader->VIEW->template_cache == 0 ? false : true;
        if ((int)$reader->VIEW->template_cache != 0) {
        	$this->cache_lifetime = (int)$reader->VIEW->template_cache_time;
        }
        $this->use_sub_dirs    = false;
        $this->error_reporting = E_ERROR;
    }

    /**
     * Assign variables to template.
     * @access public
     * @param array $variables
     * @return void
     */
	public function add(array $variables)
	{
		foreach ($variables as $variable => $value) {
			$this->assign($variable, $value);
		}
	}

	/**
	 * Create output.
	 * @access public
	 * @param string $templateFile
     * @throws TemplateNotFoundException
	 * @return string
	 */
	public function createOutput($templateFile)
	{
		settype($templateFile, "string");

		if (!file_exists($this->template_dir . "/" . $templateFile)) {
			throw new \Core\View\TemplateNotFoundException($templateFile);
		}

		return $this->fetch($this->template_dir . "/" . $templateFile);
	}

    /**
     * Factory method for Smarty.
     * @param array $variablesToAdd
     * @return \External\Smarty\Smarty
     */
    public static function init(array $variablesToAdd){
        $class = get_called_class();
        $smarty = new $class();
        $smarty->add( $variablesToAdd );
        return $smarty;
    }
}

?>