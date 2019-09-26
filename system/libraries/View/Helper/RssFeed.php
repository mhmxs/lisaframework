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
 * Helper for <link> tags.
 * @package Core
 * @subpackage View
 * @category Helper
 * @author dezsi
 */
namespace Core\View\Helper;

class RssFeed extends Helper
{
    /**
     * Constructor.
     *
     * Adds "media" attribute if not specified, and always uses
     * "type" attribute of "text/css".
     * @access public
     * @param string $href The source href for the rss feed.
     * @param string $title The title for the rss feed.
     * @return void
     */
    public function __construct($href, $title = null)
    {
        settype($attribs, 'array');

		$attribs["href"] = $href;
        $attribs["type"] = 'application/rss+xml';
		$attribs["rel"]  = 'alternate';
		if (!is_null($title)) {
			$attribs["title"]  = $title;
		}

		$this->_attributes = $attribs;
    }

	/**
	 * Returns with helper string.
	 * @access public
	 * @return string
	 */
	public function getHelper()
	{
        // build and return the tag rel="stylesheet" href="/rss_feed/news" type="application/rss+xml"
        return '<link' . $this->_createAttributes() . '/>';
	}
}
