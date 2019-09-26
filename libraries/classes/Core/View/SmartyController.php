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

if (!include_once(DIR_EXT . "/Smarty/libs/Smarty.class.php")) {
	trigger_error("Failed to load Smarty.", E_USER_ERROR);
}

/**
 * Smarty controller handling Smarty class
 * @package Core
 * @subpackage Controller
 * @author nullstring
 *
 */
class SmartyController extends Smarty
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
        $this->template_dir    = DIR_TEMPLATES;
        $this->config_dir      = DIR_CACHE . "/smarty/config";
        $this->compile_dir     = DIR_CACHE . "/smarty/compiled";
        $this->cache_dir       = DIR_CACHE . "/smarty/cached";
        $this->caching         = false;
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
			throw new TemplateNotFoundException($templateFile);
		}

		return $this->fetch($this->template_dir . "/" . $templateFile);
	}
}

?>