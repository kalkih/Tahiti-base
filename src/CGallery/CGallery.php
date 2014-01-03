<?php
/**
 * Image class
 *
 */
class CGallery {

    // Member variables
    private $html           = null;
    private $validImages    = array();

    // Constructor
    public function __construct($validImages) {
        $path = isset($_GET['path']) ? $_GET['path'] : null;
        $pathToGallery = realpath(GALLERY_PATH . '/' . $path);

        $this->validImages = $validImages;
        $this->createGallery($pathToGallery);
    }

    // Get Gallery
    public function getGallery() {
        return $this->html;
    }

    private function errorMessage($message) {
        $this->html .= htmlentities($message);
    }

    // Validate Incoming
    private function validateIncoming() {
        is_dir(GALLERY_PATH) or $this->errorMessage('The gallery dir is not a valid directory.');
        substr_compare(GALLERY_PATH, $pathToGallery, 0, strlen(GALLERY_PATH)) == 0 or $this->errorMessage('Security constraint: Source gallery is not directly below the directory GALLERY_PATH.');
    }

    // Make Gallery
    private function createGallery($pathToGallery) {
        if (is_dir($pathToGallery)) {
            $this->html = $this->readAllItemsInDir($pathToGallery);
        }
        else if (is_file($pathToGallery)) {
            $this->html = $this->readItem($pathToGallery);
        }
    }

    // Read All Items
    private function readAllItemsInDir($path) {
        $files = glob($path . '/*'); 
        $html = $this->createBreadcrumb($path);
        $html .= "<ul class='gallery'>\n";
        $len = strlen(GALLERY_PATH);


        foreach($files as $file) {
            $parts = pathinfo($file);

            // Is this an image or a directory
            if(is_file($file) && in_array($parts['extension'], $this->validImages)) {
                $caption  = basename($file); 
                $link     = GALLERY_BASEURL . substr($file, $len + 1) . "&amp;height=170&amp;width=150";
                $item     = "<img src='img.php?src={$link}' alt=''/>";
                $href     = substr($file, $len + 1); 
            }
            elseif(is_dir($file)) {
                $item    = "<img src='img/folder.png' width='150' alt=''/>";
                $caption = basename($file) . '/';
                $href = substr($file, $len + 1); 
            }
            else {
                continue;
            } 

            // Avoid to long captions breaking layout
            $fullCaption = $caption;
            if(strlen($caption) > 18) {
                $caption = substr($caption, 0, 10) . '…' . substr($caption, -5);
            }

            $html .= "<li><a href='?path={$href}&amp;sp=gallery' title='{$fullCaption}'><figure class='figure overview'>{$item}<figcaption>{$caption}</figcaption></figure></a></li>\n";
        }
        $html .= "</ul>\n";

        return $html;
    }

    // Read Item
    private function readItem($path) {
        $breadCrumb = $this->createBreadcrumb($path);

        $parts = pathinfo($path);
        if(!(is_file($path) && in_array($parts['extension'],  $this->validImages))) {
            return "<p>This is not a valid image for this gallery.";
        } 

        // Get info on image
        $imgInfo = list($width, $height, $type, $attr) = getimagesize($path);
        $mime = $imgInfo['mime'];
        $gmdate = gmdate("D, d M Y H:i:s", filemtime($path));
        $filesize = round(filesize($path) / 1024);

        // Get constraints to display original image
        $displayWidth  = $width > 800 ? "&amp;width=800" : null;
        $displayHeight = $height > 600 ? "&amp;height=600" : null;

        // Display details on image
        $len = strlen(GALLERY_PATH);
        $href = GALLERY_BASEURL . substr($path, $len + 1);
        $item =

<<<EOD
{$breadCrumb}
<p><img src='img.php?src={$href}{$displayHeight}{$displayWidth}' alt=''/></p>
<p>Original image dimensions are {$width}x{$height} pixels. <a href='img.php?src={$href}'>View original image</a>.</p>
<p>File size is: {$filesize}KBytes.</p>
<p>Image has mimetype: {$mime}.</p>
<p>Image was last modified: {$gmdate} GMT.</p>
EOD;

        return $item;
    }

    // Create breadcrumb
    private function createBreadcrumb($path) {
        $parts = explode('/', trim(substr($path, strlen(GALLERY_PATH) + 1), '/'));
        $breadcrumb = "<ul class='breadcrumb'>\n<li><a href='?p=gallery'>Hem</a> »</li>\n";

        if(!empty($parts[0])) {
            $combine = null;
            foreach($parts as $part) {
                $combine .= ($combine ? '/' : null) . $part;
                $breadcrumb .= "<li><a href='?path={$combine}&amp;sp=gallery'>$part</a> » </li>\n";
            }
        }

        $breadcrumb .= "</ul>\n";
        return $breadcrumb;
    }

}

?>