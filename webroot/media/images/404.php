<?php

if (!include_once($_SERVER["DOCUMENT_ROOT"] . '/../libraries/Util/DirectoryHandler.php')) {
	die;
}
if (!include_once($_SERVER["DOCUMENT_ROOT"] . '/../libraries/Util/ImageModifier.php')) {
	die;
}

/**
 * StartResize
 * @author kovacsricsi
 */
class StartResize {

	private $baseDir;
	private $newDir;
	private $basePath;
	private $www;
	private $newFile;
	private $baseFile;
	private $width;
	private $height;
	private $ext;
	private $type;

	public function __construct() {
		$this->baseDir = dirname($_SERVER["REQUEST_URI"]);
		$this->basePath = $_SERVER["DOCUMENT_ROOT"] . $this->baseDir;
		$this->www = rtrim($_SERVER["DOCUMENT_ROOT"], "/");

		$this->resizeImage();
	}

	private function resizeImage() {
		$this->setMatches();

		if (!file_exists($this->www . $this->baseFile . '.' . $this->ext)) {
			$this->ImageNotFound();
		}

		if (!is_dir($this->newDir)) {
			\Util\DirectoryHandler::mkdirRecursive($this->newDir, "775");
		}

		if (!file_exists($this->newDir . basename($this->baseFile) . "." . $this->ext)) {
			$img = new \Util\ImageModifier($this->www . $this->baseFile . "." . $this->ext);
			$img->newdimension($this->width, $this->height, $this->type);

			$img->generate($this->newDir . basename($this->baseFile) . "." . $this->ext);
		}
		$this->output();
	}

	private function output() {
		$ext = strtolower($this->ext);
		if ($ext == "jpg") {
			header('Content-type: image/jpeg');
		} elseif ($ext == "jpeg") {
			header('Content-type: image/jpeg');
		} elseif ($ext == "png") {
			header('Content-type: image/png');
		} elseif ($ext == "gif") {
			header('Content-type: image/gif');
		} else {
			$this->ImageNotFound();
		}

		header('HTTP/1.1 200 OK');

		readfile($this->newDir . basename($this->baseFile) . "." . $this->ext);
		die();
	}

	private function setMatches() {
		$matches = array();
		if (!preg_match("!($this->baseDir.*)_([0-9]+)x([0-9]+)(\.crop|\.fill||\.resize)\.(jpg|gif|png|jpeg)!i", $_SERVER["REQUEST_URI"], $matches)) {
			$this->ImageNotFound();
		} else {
			list($this->newFile, $this->baseFile, $this->width, $this->height, $this->type, $this->ext ) = $matches;

			$this->type = ($this->type == "") ? "resize" : ltrim($this->type, ".");

			$media = (strpos($this->baseDir, "/images/") === 0 || $this->baseDir == "/images" ) ? "/media" : "";

			$this->baseFile = $media . $this->baseFile;
			$this->newFile = $media . $this->newFile;
			$this->newDir = $this->www . "/" . trim(dirname($this->baseFile), "/") . "/" . $this->width . "x" . $this->height . "_" . $this->type . "/";
		}
	}

	private function ImageNotFound() {
		header("HTTP/1.0 404 Not Found");
		die();
	}

}

new StartResize();
?>