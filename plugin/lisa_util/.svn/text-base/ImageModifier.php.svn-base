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
 * Image modifier class.
 * @package Util
 * @author Somlyai Dezső http://sitemakers.hu
 */

namespace lisa_util;

class ImageModifier {

	/**
	 * Original filename.
	 * @access protected
	 * @var string
	 */
	protected $_orig;
	/**
	 * Generated image.
	 * @access protected
	 * @var string
	 */
	protected $_img;
	/**
	 * Original width.
	 * @access protected
	 * @var string
	 */
	protected $_width;
	/**
	 * Original height.
	 * @access protected
	 * @var string
	 */
	protected $_height;
	/**
	 * Width of the watermarkPicture.
	 * @access protected
	 * @var string
	 */
	protected $_wmWidth;
	/**
	 * Height of the watermarkPicture.
	 * @access protected
	 * @var string
	 */
	protected $_wmHeight;
	/**
	 * New width.
	 * @access protected
	 * @var string
	 */
	protected $_newWidth;
	/**
	 * New height.
	 * @access protected
	 * @var string
	 */
	protected $_newHeight;
	/**
	 * HTML size.
	 * @example width="640" height="480"
	 * @access protected
	 * @var string
	 */
	protected $_htmlSize;
	/**
	 * Image format.
	 * @access protected
	 * @var string
	 */
	protected $_format;
	/**
	 * Image extension.
	 * @access protected
	 * @var string
	 */
	protected $_extension;
	/**
	 * Image size.
	 * @access protected
	 * @var string
	 */
	protected $_size;
	/**
	 * Image filename.
	 * @access protected
	 * @var string
	 */
	protected $_basename;
	/**
	 * Image directory.
	 * @access protected
	 * @var string
	 */
	protected $_directory;
	/**
	 * Allowed extensions.
	 * @access protected
	 * @var string
	 */
	protected $_validExtensions;
	/**
	 * RGB.
	 * @access protected
	 * @var string
	 */
	protected $_rgb;
	/**
	 * Keep orginal image sizes, if new dimensoins is larger.
	 * @access public
	 * @var bool
	 */
	public $originalSize;

	/**
	 * Constructor.
	 * @access public
	 * @param string $orig
	 * @param array $validExtensions
	 */
	public function __construct($image, $validExtensions = array("jpg", "jpeg", "jpe", "gif", "bmp", "png")) {

		$this->_orig = $image;
		$this->_img = "";
		$this->_width = 0;
		$this->_height = 0;
		$this->_newWidth = 0;
		$this->_newHeight = 0;
		$this->_format = 0;
		$this->_extension = "";
		$this->_size = "";
		$this->_validExtensions = $validExtensions;
		$this->_basename = "";
		$this->_directory = "";
		$this->_rgb = array(255, 255, 255);
		$this->_htmlSize = "";
		$this->originalSize = true;
		$this->_wmWidth = 0;
		$this->_wmHeight = 0;

		if (strlen($this->_orig) > 0) {
			if (is_file($this->_orig)) {
				$this->_getPathInfo();
				if ($this->_checkImage()) {
					$this->_getOrigDimens();
					$this->_createImage();
				}
			}
		}
	}

	/**
	 * Get Image Information.
	 * @access protected
	 * @return void
	 */
	protected function _getOrigDimens() {
		$dimensions = getimagesize($this->_orig);
		$this->_width = $dimensions[0];
		$this->_height = $dimensions[1];
		$this->_format = $dimensions[2];
		$this->_htmlSize = $dimensions[3];
	}

	/**
	 * Get path information.
	 * @access protected
	 * @return void
	 */
	protected function _getPathInfo() {
		$pathinfo = pathinfo($this->_orig);
		$this->_extension = strtolower($pathinfo["extension"]);
		$this->_basename = $pathinfo["basename"];
		$this->_directory = $pathinfo["dirname"];
		$this->_size = filesize($this->_orig);
	}

	/**
	 * Check image extensions.
	 * @access protected
	 * @return bool
	 */
	protected function _checkImage() {
		if (!in_array($this->_extension, $this->_validExtensions)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Create image.
	 * @access protected
	 * @return void
	 */
	protected function _createImage() {
		switch ($this->_format) {
			case 1:
				$this->_img = imagecreatefromgif($this->_orig);
				$this->_extension = "gif";
				break;
			case 2:
				$this->_img = imagecreatefromjpeg($this->_orig);
				$this->_extension = "jpg";
				break;
			case 3:
				$this->_img = imagecreatefrompng($this->_orig);
				$this->_extension = "png";
				break;
			case 6:
				$this->_img = $this->_imagecreatefrombmp($this->_orig);
				$this->_extension = "bmp";
				break;
		}
	}

	/**
	 * Initialize new dimensions.
	 * @access public
	 * @param int $new_width
	 * @param int $new_height
	 * @param string $type
	 * @param array $rgb
	 * @return void
	 */
	public function newdimension($new_width = 0, $new_height = 0, $type = null, $rgb = array(255, 255, 255)) {
		$this->_newWidth = $new_width;
		$this->_newHeight = $new_height;

		if ($this->originalSize) {
			if ($this->_width <= $this->_newWidth && $this->_height <= $this->_newHeight) {
				$this->_newWidth = $this->_width;
				$this->_newHeight = $this->_height;
			} elseif ($this->_width <= $this->_newWidth && $this->_height > $this->_newHeight) {
				$this->_newWidth = 0;
			} elseif ($this->_width > $this->_newWidth && $this->_height <= $this->_newHeight) {
				$this->_newHeight = 0;
			}
		}

		$this->_rgb = $rgb;

		if ($this->_newWidth == 0 && $this->_newHeight == 0) {
			return false;
		} elseif ($this->_newWidth == 0) {
			$this->_newWidth = $this->_width / ( $this->_height / $this->_newHeight );
		} elseif ($this->_newHeight == 0) {
			$this->_newHeight = $this->_height / ( $this->_width / $this->_newWidth );
		}

		if ($type == "fill") {
			$this->_resizeFill();
		} elseif ($type == "crop") {
			$this->_resizeCrop();
		} elseif ($type == "wfill") {
			$this->_whiteFill($new_width, $new_height);
		} else {
			$this->_resize();
		}
	}

	/**
	 * Returns with images width
	 * @return int
	 */
	public function getImageWidth() {
		return imagesx($this->_img);
	}

	/**
	 * Returns with images height
	 * @return int
	 */
	public function getImageHeight() {
		return imagesy($this->_img);
	}

	/**
	 * Resize picture.
	 * @access protected
	 * @return void
	 */
	protected function _resize() {
		$imgtemp = $this->_protectAlphaColors($this->_newWidth, $this->_newHeight);

		imagecopyresampled($imgtemp, $this->_img, 0, 0, 0, 0, $this->_newWidth, $this->_newHeight, $this->_width, $this->_height);
		$this->_img = $imgtemp;
	}

	/**
	 * Resize and fill picture.
	 * @access protected
	 * @return void
	 */
	protected function _resizeFill() {

		$xRatio = $this->_newWidth / $this->_width;
		$yRatio = $this->_newHeight / $this->_height;

		if (($this->_width <= $this->_newWidth) && ($this->_height <= $this->_newHeight)) {
			$nw = $this->_width;
			$nh = $this->_height;
		} elseif (($xRatio * $this->_height) < $this->_newHeight) {
			$nh = ceil($xRatio * $this->_height);
			$nw = $this->_newWidth;
		} else {
			$nw = ceil($yRatio * $this->_width);
			$nh = $this->_newHeight;
		}

		$this->_newWidth = (int) $nw;
		$this->_newHeight = (int) $nh;

		$imgtemp = $this->_protectAlphaColors($this->_newWidth, $this->_newHeight);
		imagecopyresampled($imgtemp, $this->_img, 0, 0, 0, 0, $this->_newWidth, $this->_newHeight, $this->_width, $this->_height);
		$this->_img = $imgtemp;
	}

	protected function _whiteFill($new_width, $new_height) {
		$this->_resizeFill();

		$nx = $new_width > $this->_newWidth ? ($new_width - $this->_newWidth) / 2 : 0;
		$ny = $new_height > $this->_newHeight ? ($new_height - $this->_newHeight) / 2 : 0;

		$imgtemp = imagecreatetruecolor($new_width, $new_height);
		$white = imagecolorallocate($imgtemp, 255, 255, 255);
		imagefill($imgtemp, 0, 0, $white);

		imagecopymerge($imgtemp, $this->_img, $nx, $ny, 0, 0, $this->_newWidth, $this->_newHeight, 100);

		$this->_img = $imgtemp;
	}

	protected function _protectAlphaColors($x, $y) {
		$tmp = imagecreatetruecolor($x, $y);
		if (($this->_format == IMAGETYPE_GIF) || ($this->_format == IMAGETYPE_PNG)) {
			$trnprt_indx = imagecolortransparent($this->_img);
			if ($trnprt_indx >= 0) {
				$trnprt_color = imagecolorsforindex($this->_img, $trnprt_indx);
				$trnprt_indx = imagecolorallocate($tmp, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				imagefill($tmp, 0, 0, $trnprt_indx);
				imagecolortransparent($tmp, $trnprt_indx);
			} elseif ($this->_format == IMAGETYPE_PNG) {
				imagealphablending($tmp, false);
				$color = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
				imagefill($tmp, 0, 0, $color);
				imagesavealpha($tmp, true);
			}
		}
		return $tmp;
	}

	/**
	 * Resize and crop picture.
	 * @access protected
	 * @return void
	 */
	protected function _resizeCrop() {
		$imgtemp = $this->_protectAlphaColors($this->_newWidth, $this->_newHeight);

		$hm = $this->_height / $this->_newHeight;
		$wm = $this->_width / $this->_newWidth;

		$h_height = $this->_newHeight / 2;
		$h_width = $this->_newWidth / 2;

		if ($wm > $hm) {
			$adjusted_width = $this->_width / $hm;
			$half_width = $adjusted_width / 2;
			$int_width = $half_width - $h_width;
			imagecopyresampled($imgtemp, $this->_img, -$int_width, 0, 0, 0, $adjusted_width, $this->_newHeight, $this->_width, $this->_height);
		} elseif (( $wm <= $hm)) {
			$adjusted_height = $this->_height / $wm;
			$half_height = $adjusted_height / 2;
			$int_height = $half_height - $h_height;
			imagecopyresampled($imgtemp, $this->_img, 0, -$int_height, 0, 0, $this->_newWidth, $adjusted_height, $this->_width, $this->_height);
		}

		$this->_img = $imgtemp;
	}

	/**
	 * Rotate image.
	 * @access public
	 * @param int $degree
	 * @param bool $clockwish
	 * @return void
	 */
	public function rotate($degree = 90, $clockwise = false) {
		$this->_img = imagerotate($this->_img, $clockwise ? $degree : 360 - $degree, 0);
	}

	/**
	 * Flip picture vertical and horizontal.
	 * @access public
	 * @param string $type
	 * @return void
	 */
	public function flip($type = "h") {
		$w = imagesx($this->_img);
		$h = imagesy($this->_img);

		$imgtemp = $this->_protectAlphaColors($w, $h);

		if ($type == "v") {
			for ($y = 0; $y < $h; $y++) {
				imagecopy($imgtemp, $this->_img, 0, $y, 0, $h - $y - 1, $w, 1);
			}
		}

		if ($type == "h") {
			for ($x = 0; $x < $w; $x++) {
				imagecopy($imgtemp, $this->_img, $x, 0, $w - $x - 1, 0, 1, $h);
			}
		}

		$this->_img = $imgtemp;
	}

	/**
	 * Add legend to picture.
	 * @access public
	 * @param string $text
	 * @param int $size
	 * @param int $x
	 * @param int $y
	 * @param array $rgb
	 * @param bool $truetype
	 * @param string $font
	 * @return void
	 */
	public function legend($text, $size = 10, $x = 0, $y = 0, $rgb = array(255, 255, 255), $truetype = false, $font = null) {
		$color = imagecolorallocate($this->_img, $rgb[0], $rgb[1], $rgb[2]);

		if ($truetype === true) {
			imagettftext($this->_img, $size, 0, $x, $y, $color, $font, $text);
		} else {
			imagestring($this->_img, $size, $x, $y, $text, $color);
		}
	}

	/**
	 * Create watermark to x-y position.
	 * @access public
	 * @param string $image
	 * @param int $x
	 * @param int $y
	 * @param int $alfa
	 * @return bool
	 */
	public function watermark($water, $x = 0, $y = 0, $alfa = 100) {
		if (!is_resource($water)) {
			$water = $this->_getImgResource($water);
		}

		$imgtemp = $this->_protectAlphaColors($this->_width, $this->_height);

		imagecopyresampled($imgtemp, $this->_img, 0, 0, 0, 0, $this->_width, $this->_height, $this->_width, $this->_height);
		$this->_img = $imgtemp;

		if (is_numeric($alfa) && ( ( $alfa > 0 ) && ( $alfa < 100 ) )) {
			imagecopymerge($this->_img, $water, $x, $y, 0, 0, $this->_wmWidth, $this->_wmHeight, $alfa);
		} else {
			imagecopy($this->_img, $water, $x, $y, 0, 0, $this->_wmWidth, $this->_wmHeight);
		}
		return true;
	}

	protected function _resizeWaterMarkPicture($water, $sizePercent) {
		$ratio = $this->_wmWidth / $this->_wmHeight;
		$newWMWidth = round($this->_width * $sizePercent / 100, 0);
		$newWMHeight = round($newWMWidth / $ratio, 0);

		$imgtemp = imagecreatetruecolor($newWMWidth, $newWMHeight);
		$imgtemp = $this->_alphaFix($imgtemp);

		imagecopyresampled($imgtemp, $water, 0, 0, 0, 0, $newWMWidth, $newWMHeight, $this->_wmWidth, $this->_wmHeight);
		$this->_wmWidth = $newWMWidth;
		$this->_wmHeight = $newWMHeight;
		return $imgtemp;
	}

	protected function _alphaFix($imgtemp) {
		imagealphablending($imgtemp, false);
		$color = imagecolorallocatealpha($imgtemp, 0, 0, 0, 127);
		imagefill($imgtemp, 0, 0, $color);
		imagesavealpha($imgtemp, true);
		return $imgtemp;
	}

	protected function _getImgResource($image) {
		if (strlen($image) > 0) {
			$pathinfo = pathinfo($image);
			switch (strtolower($pathinfo["extension"])) {
				case "jpg":
				case "jpeg":
					$picResource = imagecreatefromjpeg($image);
					break;
				case "png":
					$picResource = imagecreatefrompng($image);
					break;
				case "gif":
					$picResource = imagecreatefromgif($image);
					break;
				case "bmp":
					$picResource = $this->_imagecreatefrombmp($image);
					break;
				default:
					return false;
			}
		} else {
			return false;
		}
		return $picResource;
	}

	/**
	 * Create fix position watermark.
	 * @access public
	 * @param string $image
	 * @param string $position
	 * @param int $alfa
	 * @return void
	 */
	public function watermarkFixPosition($image, $position, $alfa = 100, $percent = 30) {
		$water = $this->_getImgResource($image);

		$this->_wmWidth = imagesx($water);
		$this->_wmHeight = imagesy($water);

		$water = $this->_resizeWaterMarkPicture($water, $percent);

		switch ($position) {
			case "top-left":
				$x = 0;
				$y = 0;
				break;
			case "top-center":
				$x = ( $this->_width - $this->_wmWidth ) / 2;
				$y = 0;
				break;
			case "top-right":
				$x = $this->_width - $this->_wmWidth;
				$y = 0;
				break;
			case "middle-left":
				$x = 0;
				$y = ( $this->_height / 2 ) - ( $this->_wmHeight / 2 );
				break;
			case "middle-center":
				$x = ( $this->_width - $this->_wmWidth ) / 2;
				$y = ( $this->_height / 2 ) - ( $this->_wmHeight / 2 );
				break;
			case "middle-right":
				$x = $this->_width - $this->_wmWidth;
				$y = ( $this->_height / 2) - ( $this->_wmHeight / 2 );
				break;
			case "bottom-left":
				$x = 0;
				$y = $this->_height - $this->_wmHeight;
				break;
			case "bottom-center":
				$x = ( $this->_width - $this->_wmWidth ) / 2;
				$y = $this->_height - $this->_wmHeight;
				break;
			case "bottom-right":
				$x = $this->_width - $this->_wmWidth;
				$y = $this->_height - $this->_wmHeight;
				break;
			default:
				return false;
				break;
		}

		$this->watermark($water, $x, $y, $alfa);
	}

	/**
	 * Generate resized image.
	 * @access public
	 * @param string $filename
	 * @param int $quality
	 * @return bool
	 */
	public function generate($filename = "", $quality = 100) {
		if (strlen($filename) > 0) {
			$pathinfo = pathinfo($filename);
			$dir = $pathinfo["dirname"];
			$extension = strtolower($pathinfo["extension"]);

			if (!is_dir($dir)) {
				return false;
			}
		}

		if (!isset($extension)) {
			$extension = $this->_extension;
		} else {
			if (!in_array($extension, $this->_validExtensions)) {
				return false;
			}
		}

		switch ($extension) {
			case "jpg":
			case "jpeg":
			case "bmp":
				if ($filename) {
					imagejpeg($this->_img, $filename, $quality);
				} else {
					header("Content-type: image/jpeg");
					imagejpeg($this->_img, NULL, $quality);
					imagedestroy($this->_img);
					exit;
				}
				break;
			case "png":
				if ($filename) {

					$this->_img = $this->_alphaFix($this->_img);

					imagepng($this->_img, $filename);
				} else {
					header("Content-type: image/png");
					imagepng($this->_img);
					imagedestroy($this->_img);
					exit;
				}
				break;
			case "gif":
				if ($filename) {
					imagejpeg($this->_img, $filename, 100); //it creates a jpeg from gif too, because the watermark. The extension doesn't change.
				} else {
					header("Content-type: image/gif");
					imagegif($this->_img);
					imagedestroy($this->_img);
					exit;
				}
				break;
			default:
				return false;
				break;
		}
	}

	/**
	 * Generate bmp images.
	 * @access protected
	 * @param string $filename
	 * @return string
	 */
	protected function _imagecreatefrombmp($filename) {
		if (!$f1 = fopen($filename, "rb")) {
			return FALSE;
		}

		$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
		if ($FILE["file_type"] != 19778)
			return FALSE;

		$BMP = unpack("Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel" . "/Vcompression/Vsize_bitmap/Vhoriz_resolution" . "/Vvert_resolution/Vcolors_used/Vcolors_important", fread($f1, 40));
		$BMP["colors"] = pow(2, $BMP["bits_per_pixel"]);
		if ($BMP["size_bitmap"] == 0)
			$BMP["size_bitmap"] = $FILE["file_size"] - $FILE["bitmap_offset"];
		$BMP["bytes_per_pixel"] = $BMP["bits_per_pixel"] / 8;
		$BMP["bytes_per_pixel2"] = ceil($BMP["bytes_per_pixel"]);
		$BMP["decal"] = ($BMP["width"] * $BMP["bytes_per_pixel"] / 4);
		$BMP["decal"] -= floor($BMP["width"] * $BMP["bytes_per_pixel"] / 4);
		$BMP["decal"] = 4 - (4 * $BMP["decal"]);
		if ($BMP["decal"] == 4)
			$BMP["decal"] = 0;

		$PALETTE = array();
		if ($BMP["colors"] < 16777216) {
			$PALETTE = unpack("V" . $BMP["colors"], fread($f1, $BMP["colors"] * 4));
		}

		$IMG = fread($f1, $BMP["size_bitmap"]);
		$VIDE = chr(0);

		$res = imagecreatetruecolor($BMP["width"], $BMP["height"]);
		$P = 0;
		$Y = $BMP["height"] - 1;
		while ($Y >= 0) {
			$X = 0;
			while ($X < $BMP["width"]) {
				if ($BMP["bits_per_pixel"] == 24)
					$COLOR = @unpack("V", substr($IMG, $P, 3) . $VIDE);
				elseif ($BMP["bits_per_pixel"] == 16) {
					$COLOR = @unpack("n", substr($IMG, $P, 2));
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				} elseif ($BMP["bits_per_pixel"] == 8) {
					$COLOR = @unpack("n", $VIDE . substr($IMG, $P, 1));
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				} elseif ($BMP["bits_per_pixel"] == 4) {
					$COLOR = @unpack("n", $VIDE . substr($IMG, floor($P), 1));
					if (($P * 2) % 2 == 0)
						$COLOR[1] = ($COLOR[1] >> 4); else
						$COLOR[1] = ($COLOR[1] & 0x0F);
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				}
				elseif ($BMP["bits_per_pixel"] == 1) {
					$COLOR = @unpack("n", $VIDE . substr($IMG, floor($P), 1));
					if (($P * 8) % 8 == 0)
						$COLOR[1] = $COLOR[1] >> 7;
					elseif (($P * 8) % 8 == 1)
						$COLOR[1] = ($COLOR[1] & 0x40) >> 6;
					elseif (($P * 8) % 8 == 2)
						$COLOR[1] = ($COLOR[1] & 0x20) >> 5;
					elseif (($P * 8) % 8 == 3)
						$COLOR[1] = ($COLOR[1] & 0x10) >> 4;
					elseif (($P * 8) % 8 == 4)
						$COLOR[1] = ($COLOR[1] & 0x8) >> 3;
					elseif (($P * 8) % 8 == 5)
						$COLOR[1] = ($COLOR[1] & 0x4) >> 2;
					elseif (($P * 8) % 8 == 6)
						$COLOR[1] = ($COLOR[1] & 0x2) >> 1;
					elseif (($P * 8) % 8 == 7)
						$COLOR[1] = ($COLOR[1] & 0x1);
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				}
				else
					return FALSE;
				imagesetpixel($res, $X, $Y, $COLOR[1]);
				$X++;
				$P += $BMP["bytes_per_pixel"];
			}
			$Y--;
			$P+=$BMP["decal"];
		}

		fclose($f1);

		return $res;
	}

}

?>
