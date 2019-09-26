<?
include_once $_SERVER["DOCUMENT_ROOT"] . '/../libraries/classes/Util/ImageResize.php';
/**
 * StartResize
 * @author kovacsricsi
 */
class StartResize
{

        private $baseDir;
        private $basePath;
        private $www;

        private $newFile;
        private $baseFile;
        private $width;
        private $height;
        private $ext;

        public function __construct()
        {
                $this->baseDir = dirname($_SERVER{'REQUEST_URI'});
                $this->basePath = $_SERVER["DOCUMENT_ROOT"].$this->baseDir;
                $this->www = ereg_replace('\/$','',$_SERVER["DOCUMENT_ROOT"]);

                $this->resizeImage();
        }

        private function resizeImage()
        {
                $this->setMatches();

                $this->baseFile = preg_replace("!^/images/!", "/webroot/media/images/", $this->baseFile);
                $this->newFile  = preg_replace("!^/images/!", "/webroot/media/images/", $this->newFile);

                if (file_exists($this->www.$this->baseFile.'.jpg')) {
                        $this->ext = "jpg";
                } elseif (file_exists($this->www.$this->baseFile.'.png')) {
                        $this->ext = "png";
                } elseif (file_exists($this->www.$this->baseFile.'.gif')) {
                        $this->ext = "gif";
                } else {
                        $this->ImageNotFound();
                }

                $image = new ImageResize($this->www . $this->baseFile . "." . $this->ext, $this->width, $this->height);

                $newDimension = $image->getNewDimension();

                if (!file_exists($this->www . $this->newFile . "." . $this->ext)) {
                        $image->generateImage();
                        $image->saveTo($this->www . $this->newFile . "." . $this->ext);
                }

                $this->output();
        }

        private function output()
        {
                if ($this->ext == "jpg") {
                        header('Content-type: image/jpeg');
                } elseif ($this->ext == "png") {
                        header('Content-type: image/png');
                } elseif ($this->ext == "gif") {
                        header('Content-type: image/gif');
                } else {
                        $this->ImageNotFound();
                }

                header('HTTP/1.1 200 OK');

                readfile($this->www . $this->newFile . "." . $this->ext);
                die();
        }

        private function setMatches()
        {
                $matches = array();

                if (!preg_match("(($this->baseDir.*)_([0-9]*)x([0-9]*))", $_SERVER{'REQUEST_URI'}, $matches)) {
                        $this->ImageNotFound();
                } else {
                        list($this->newFile, $this->baseFile, $this->width, $this->height, $this->ext) = $matches;
                }

                if(!$this->width and !$this->height) {
                        $this->ImageNotFound();
                }
        }

        private function ImageNotFound()
        {
                header("HTTP/1.0 404 Not Found");
                die();
        }

}

new StartResize();