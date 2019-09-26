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
 * Generate pager for long lists.
 * @package Util
 * @author nullstring
 */
class PageLimit {

	protected $pageNum = 0;
	protected $link    = "";
	protected $maxCount;
	protected $limit;

	protected $start;
	protected $end;
	protected $maxPageNum;
	protected $nextLink = false;
	protected $prevLink = false;

    /**
     * Constructor
     * @access public
     * @param int $pageNum actual page number
     * @param int $maxCount max page number
     * @param int $limit limit
     * @param string $link the link
     * @return void
     */
	function __construct($pageNum, $maxCount, $limit, $link)
	{
		$this->maxCount   = $maxCount;
		$this->limit      = $limit;
		$this->link       = $link;
		$this->maxPageNum = ceil($maxCount / $limit);

		if ($pageNum > -1) {
			$this->pageNum = $pageNum;
		}

		$this->start = $this->pageNum * $this->limit;
		if ($this->start > $this->maxCount) {
			$this->start   = 0;
			$this->pageNum = 0;
		}
		if ($this->start > 0) {
			$this->prevLink = true;
		}

		$this->end = $this->start + $this->limit;
		if ($this->end > $maxCount) {
			$this->limit = $maxCount - $this->start;
			$this->end   = $this->start + $this->limit;
		}
		if ($this->end < $maxCount) {
			$this->nextLink = true;
		}

	}

    /**
     * Returns the actual page number.
     * @access public
     * @return int
     */
	public function getPageNum()
	{
		return $this->pageNum;
	}

    /**
     * Returns the maximum page number.
     * @access public
     * @return int
     */
	public function getMaxPageNum()
	{
		return $this->maxPageNum;
	}

	public function getMaxCount()
	{
		return $this->maxCount;
	}

    /**
     * Returns with the limit.
     * @access public
     * @return string
     */
	public function getLimit()
	{
		return $this->limit;
	}

	public function getStart()
	{
		return $this->start;
	}

	public function getEnd() {
		return $this->end;
	}

    /**
     * Returns the next page's link.
     * @access public
     * @return string
     */
	public function getNextLink()
	{
		if ($this->nextLink) {
			return str_replace("%page%", $this->pageNum + 1, $this->link);
		}

		return false;
	}

    /**
     * Returns the prevous page's link.
     * @access public
     * @return string
     */
	public function getPrevLink()
	{
		if ($this->prevLink) {
			return str_replace("%page%", $this->pageNum - 1, $this->link);
		}

		return false;
	}

    /**
     * Returns the actual page's link.
     * @access public
     * @return string
     */
	public function getPageLink($pageNum) {
		if (($pageNum >= 0) && ($pageNum <= $this->maxPageNum)) {
			return str_replace("%page%", $pageNum, $this->link);
		}

		return false;
	}

    /**
     * Returns the last page's link.
     * @access public
     * @return string
     */
	public function getLastLink() {
		if ($this->nextLink) {
			return str_replace("%page%", $this->maxPageNum - 1, $this->link);
		}

		return false;
	}

    /**
     * Returns the first page's link.
     * @access public
     * @return string
     */
	public function getFirstLink() {
		if ($this->prevLink) {
			return str_replace("%page%", 0, $this->link);
		}

		return false;
	}

	/**
	 * pick $pageNumber page from all pages, and place actual in the middle of the list.
	 * @access public
	 * @param int $pageNumber
	 * @return array
	 */
	public function pickPages($pageNumber = 10)
	{
			$pages = array();

			$pageNumber = $this->maxPageNum < (int)$pageNumber ? $this->maxPageNum : (int)$pageNumber;

			if (($pageNumber % 2) != 0) {
					$pageNumber++;
			}

			if (($pageNumber / 2) >= $this->pageNum) {
					$afterPages  = $pageNumber - $this->pageNum;
					$beforePages = $pageNumber - $afterPages;
			} else {
					$afterPages  = ($pageNumber / 2);
					$beforePages = $afterPages;

					if ($afterPages > $beforePages) {
						$beforePages++;
						$afterPages--;
					}

					$pages[] = "...";
			}

			$afterPages = ($this->maxPageNum - $this->pageNum) < $afterPages ? ($this->maxPageNum - $this->pageNum) : $afterPages;

			if ($beforePages + $afterPages != $pageNumber) {
			    $beforePages += $pageNumber - ($beforePages + $afterPages);
			}

			$from = ($this->pageNum - $beforePages) <= 0 ? 0 : ($this->pageNum - $beforePages);
			$to   = ($this->pageNum + $afterPages) -1 ;

			for($i = $from; $i <= $to; $i++) {
			    $pages[$i] = $i + 1;
			}

			if (($this->maxPageNum != $i)) {
		        $pages[] = "...";
			}

			return $pages;
	}

}

?>