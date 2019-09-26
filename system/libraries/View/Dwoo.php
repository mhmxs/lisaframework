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
 * Implemets View on Dwoo template engine.
 * @package Core
 * @subpackage View
 * @category View
 * @author kovacsricsi
 */
namespace Core\View;

class Dwoo extends AView
{
	/**
	 * Create output.
	 * @access protected
	 * @return void
	 */
	protected function _createOutput()
	{
		$dwoo = \External\Dwoo\DwooFactory::getDwoo();

		try {
			if ($this->_layout !== "") {
				$layout = $dwoo->get($this->_templateDirectory . "/layouts/" . $this->_layout);

				$addons = "";

				if ($this->_title instanceof Helper\Helper) {

					if (strpos($layout, "<title>") === false) {
						$addons .= $this->_title->getHelper();
					} else {
						$layout = preg_replace("/<title>.*<\/title>/", $this->_title->getHelper(), $layout);
					}
				}

				if (count($this->_addons) > 0 || $addons !== "") {
					$addons .= $this->_getHeadAddons();
					$layout = str_replace("</head>", $addons . "</head>", $layout);
				}

				$content = $dwoo->get($this->_templateDirectory . "/" . $this->_template, $this->getVars());

				$layout = str_replace("</body>", $content . "</body>", $layout);
				$layout = str_replace("</body>", $this->_getBottomAddons() . "</body>", $layout);

                $layout = $this->_paralellizeResources($layout);

				$this->_output = $layout;

			} else {
				$this->_output = $dwoo->get($this->_templateDirectory . "/" . $this->_template, $this->getVars());
			}
		} catch (\Exception $e) {
			$this->_template = "404.html";
			$this->_output   = $dwoo->get("404.html");
		}
	}
}

?>