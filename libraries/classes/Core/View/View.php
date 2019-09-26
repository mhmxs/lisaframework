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
 * View implements basic funcionality of a view.
 * @package Core
 * @subpackage View
 * @category View
 * @author kovacsricsi
 */

class View extends AView
{
	/**
	 * Create output.
	 * @access protected
	 * @return void
	 */
	 protected function _createOutput()
	 {
		$smarty = new SmartyController();

		$smarty->add($this->getVars());

		try {
			if ($this->_layout !== "") {
				$layout  = $smarty->createOutput("/layouts/" . $this->_layout);

				if ($this->_title instanceof Helper) {
					$layout = preg_replace("/<title>.*<\/title>/", $this->_title->getHelper(), $layout);
				}

				if (count($this->_headAddons) > 0) {
					$addons = "";

					foreach($this->_headAddons as $addon) {
						$addons .= $addon->getHelper() . "\r\n";
					}

					$layout = str_replace("</head>", $addons . "</head>", $layout);
				}

				$content = $smarty->createOutput($this->_template);

				$this->_output = str_replace("</body>", $content . "</body>", $layout);
			} else {
				$this->_output = $smarty->createOutput($this->_template);
			}
		} catch (Exception $e) {
            $this->_template = "404.html";
			$this->_output   = $smarty->createOutput("404.html");
		}
	 }
}

?>