<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Generator
 *
 * @author mhmxs
 */
namespace lisa_static_html;

class Generator {
			public static function getInstance() {
			return new self();
		}

    public function generate($context) {
		$contentFile = DIR_ROOT . "/view/StaticHtml/" . $context . ".html";
		if (file_exists($contentFile)) {
			$content =  \file_get_contents($contentFile);
			
			if (\Context::getView()) {
				\Context::getView()->addContent($content);
			} else {
				echo $content;
			}
		}
	}
}
?>
