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
 * Resize or crop image.
 * @package Util
 * @author nullstring
 */
class ImageResize {

	/**
	 * Source image.
	 * @access protected
	 * @var string
	 */
	protected $sourceImage;

	/**
	 * Crop position
	 * @access protected
	 * @var array
	 */
	protected $cropPosition = array("x" => 0, "y" => 0);

	/**
	 * New image.
	 * @access protected
	 * @var string
	 */
	protected $newImage = object;

	/**
	 * Image type.
	 * @access protected
	 * @var string
	 */
	protected $imageType;

	/**
	 * Original dimension.
	 * @access protected
	 * @var array
	 */
	protected $origDimension = array();

	/**
	 * New dimension.
	 * @access protected
	 * @var array
	 */
	protected $newDimension = array();

	/**
	 * Constructor set default variables.
	 * @access public
	 * @param string $sourceImage
	 * @param integer $maxWidth
	 * @param integer $maxHeight
	 * @param array $crop
	 * @return unknown_type
	 */
	public function __construct($sourceImage, $maxWidth = 1024, $maxHeight = 768, $crop = false) {

		$this->sourceImage = $sourceImage;

		$picInfos = getimagesize($this->sourceImage);
		$this->oldDimension["width"]  = $picInfos[0];
		$this->oldDimension["height"] = $picInfos[1];

		$typeArray = explode("/", $picInfos["mime"]);
		$this->imageType = $typeArray[1];

		if ($this->oldDimension["width"] > $maxWidth & $this->oldDimension["height"] <= $maxHeight) {
			if ( $crop != false ){
				$ratio = $maxHeight / $this->oldDimension["height"];
			} else {
				$ratio = $maxWidth / $this->oldDimension["width"];
			}
		} elseif ($this->oldDimension["height"] > $maxHeight & $this->oldDimension["width"] <= $maxWidth) {
			if ( $crop != false ) {
				$ratio = $maxWidth / $this->oldDimension["width"];
			} else {
				$ratio = $maxHeight / $this->oldDimension["height"];
			}
		} elseif ($this->oldDimension["width"] > $maxWidth & $this->oldDimension["height"] > $maxHeight) {
			$ratio1 = $maxWidth / $this->oldDimension["width"];
			$ratio2 = $maxHeight / $this->oldDimension["height"];
			if ( $crop != false ){
				$ratio  = ($ratio2 < $ratio1) ? $ratio1 : $ratio2;
			} else {
				$ratio  = ($ratio1 < $ratio2) ? $ratio1 : $ratio2;
			}
		} else {
			$ratio = 1;
		}

		if ( $crop != false ){

			$this->newDimension["width"]   = $maxWidth;
			$this->newDimension["height"]  = $maxHeight;

			$maxHeightPRation = (int) ($maxHeight/$ratio);
			$maxWidthPRation = (int) ($maxWidth/$ratio);

			if ( $maxHeightPRation != $this->oldDimension["height"] ){
				$this->cropPosition["x"] = 0;
			} else {
				$this->cropPosition["x"] = (($this->oldDimension["width"] - $maxWidthPRation) / 2);
				$this->oldDimension["width"]  = ($this->oldDimension["width"] - ($this->cropPosition["x"] * 2));
			}

			if ( $maxWidthPRation  != $this->oldDimension["width"] ) {
				$this->cropPosition["y"] = 0;
			} else {
				$this->cropPosition["y"] = (($this->oldDimension["height"] - $maxHeightPRation) / 2);
				$this->oldDimension["height"]  = ($this->oldDimension["height"] - ($this->cropPosition["y"] * 2));
			}


		} else {
			$this->newDimension["width"]   = floor($this->oldDimension["width"] * $ratio);
			$this->newDimension["height"]  = floor($this->oldDimension["height"] * $ratio);
		}
	}

	/**
	 * generate resized image.
	 * @access public
	 * @return void
	 */
	public function generateImage()
	{
		if ($this->imageType == "jpeg") {
			$origPic = imagecreatefromjpeg($this->sourceImage);
		} elseif ($this->imageType == "png") {
			$origPic = imagecreatefrompng($this->sourceImage);
		} elseif ($this->imageType == "gif") {
			$origPic = imagecreatefromgif($this->sourceImage);
		}

		$this->newImage = imagecreatetruecolor($this->newDimension["width"], $this->newDimension["height"]);

		if (($this->imageType == "gif") || ($this->imageType == "png")) {
			switch ($this->imageType) {
				case "gif":
					$alphablending = true;
					break;

					case "png":
						$alphablending = false;
						break;

						imagealphablending($origPic, true);
						imagesavealpha($origPic, true);
					}

					$background = imagecolorallocate($this->newImage, 0, 0, 0);
					ImageColorTransparent($this->newImage, $background);
					imagealphablending($this->newImage, $alphablending);
					imagesavealpha($this->newImage, true);
				}

				imagecopyresized($this->newImage, $origPic,  0, 0, $this->cropPosition["x"], $this->cropPosition["y"], $this->newDimension["width"], $this->newDimension["height"], $this->oldDimension["width"], $this->oldDimension["height"]);
				//imagecopyresampled($this->newImage, $origPic,  0, 0, $this->cropPosition["x"], $this->cropPosition["y"], $this->newDimension["width"], $this->newDimension["height"], $this->oldDimension["width"], $this->oldDimension["height"]);
			}

	/**
	 * Returns with new dimension.
	 * @access public
	 * @return array
	 */
			public function getNewDimension()
			{
				return $this->newDimension;
			}

	/**
	 * Save image to added path.
	 * @access public
	 * @param string $name
	 * @param string $path
	 * @return void
	 */
			public function saveTo($name, $path = "") {
				if ($this->imageType == "jpeg") {
					imagejpeg($this->newImage, $path . "/" . $name, 100);
				} elseif ($this->imageType == "png") {
					imagepng($this->newImage, $path . "/" . $name, 100);
				} elseif ($this->imageType == "gif") {
					imagegif($this->newImage, $path . "/" . $name, 100);
				}
			}

		}

		?>