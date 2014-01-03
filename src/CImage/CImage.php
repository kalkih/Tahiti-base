<?php
/**
 * Image class
 *
 */
class CImage {

    // Member variables
    private $src                 = null;
    private $verbose             = null;
    private $saveAs              = null;
    private $quality             = null;
    private $ignoreCache         = null;
    private $width               = null;
    private $height              = null;
    private $newWidth            = null;
    private $newHeight           = null;
    private $maxWidth            = 800;
    private $maxHeight           = 640;
    private $cropToFit           = null;
    private $sharpen             = null;
    private $pathToImage         = null;
    //private $errorMessage        = null;
    private $cacheFileName       = null;
    private $image               = null;
    private $fileExtension       = null;
    private $filesize            = null;     
    private $aspectRatio         = null;

    // Constructor
    public function __construct() {
        $this->src              = isset($_GET['src'])             ? $_GET['src']      : null; 
        $this->verbose          = isset($_GET['verbose'])         ? true              : null; 
        $this->saveAs           = isset($_GET['save-as'])         ? $_GET['save-as']  : null;
        $this->quality          = isset($_GET['quality'])         ? $_GET['quality']  : 50; 
        $this->ignoreCache      = isset($_GET['no-cache'])        ? true              : false; 
        $this->newWidth         = isset($_GET['width'])           ? $_GET['width']    : null; 
        $this->newHeight        = isset($_GET['height'])          ? $_GET['height']   : null; 
        $this->cropToFit        = isset($_GET['crop-to-fit'])     ? true              : null; 
        $this->sharpen          = isset($_GET['sharpen'])         ? true              : null; 
        $this->pathToImage      = realpath(IMG_PATH . $this->src);

        $this->validateIncoming();
        $this->imageInfo();
        $this->createCache();
        $this->checkCache();
        $this->openImage();
        $this->resizeImage();
        $this->cropImage();
        $this->sharpenImage();
        $this->saveImage();
        $this->outputImage($this->pathToImage);
    }

    // Error Messages
    private function errorMessage($message) {
        // echo "<p>" . $message . "</p>";
        die($message);
    }

    private function verbose($message) {
        echo "<p>" . ($message) . "</p>";
    }
    
    // Validate information
    private function validateIncoming() {
        is_dir(IMG_PATH) or $this->errorMessage('The image dir is not a valid directory. try');
        is_writable(CACHE_PATH) or $this->errorMessage('The cache dir is not a writable directory.');
        isset($this->src) or $this->errorMessage('Must set src-attribute. <a href="?src=/babe/November.jpg&verbose=true">?src=/babe/November.jpg&verbose=true</a>');
        preg_match('#^[a-z0-9A-Z-_\.\/]+$#', $this->src) or $this->errorMessage('Filename contains invalid characters.');
        substr_compare(IMG_PATH, $this->pathToImage, 0, strlen(IMG_PATH)) == 0 or $this->errorMessage('Security constraint: Source image is not directly below the directory IMG_PATH.');
    
        is_null($this->saveAs) or in_array($this->saveAs, array('png', 'jpg', 'jpeg','gif')) or $this->errorMessage('Not a valid extension to save image as');
        is_null($this->quality) or (is_numeric($this->quality) and $this->quality > 0 and $this->quality <= 100) or $this->errorMessage('Quality out of range');
        is_null($this->newWidth) or (is_numeric($this->newWidth) and $this->newWidth > 0 and $this->newWidth <= $this->maxWidth) or $this->errorMessage('Width out of range');
        is_null($this->newHeight ) or (is_numeric($this->newHeight) and $this->newHeight > 0 and $this->newHeight <= $this->maxHeight) or $this->errorMessage('Height out of range');
        is_null($this->cropToFit) or ($this->cropToFit and $this->newWidth and $this->newHeight) or $this->errorMessage('Crop to fit needs both width and height to work');
    }

    // Get Image information
    private function imageInfo() {
        $imgInfo = list($width, $height, $type, $attr) = getimagesize($this->pathToImage);
        !empty($imgInfo) or $this->errorMessage("The file doesn't seem to be an image.");
        $mime = $imgInfo['mime'];
        $this->width = $width;
        $this->height = $height;

        if($this->verbose) {
            $this->filesize = filesize($this->pathToImage);
            $this->verbose("Image file: {$this->pathToImage}");
            $this->verbose("Image information: " . print_r($imgInfo, true));
            $this->verbose("Image width x height (type): {$width} x {$height} ({$type}).");
            $this->verbose("Image file size: {$this->filesize} bytes.");
            $this->verbose("Image mime type: {$mime}.");
        }
    }

    private function createCache() {
        $parts                 = pathinfo($this->src);
        $this->fileExtension   = $parts['extension'];
        $this->saveAs          = is_null($this->saveAs) ? $this->fileExtension : $this->saveAs;
        $this->quality         = is_null($this->quality) ? null : $this->quality;;
        $this->cropToFit       = is_null($this->cropToFit) ? null : "_cf";
        $this->sharpen         = is_null($this->sharpen) ? null : "_s";
        $dirName               = preg_replace('/\//', '-', dirname($this->src));
        $this->cacheFileName   = CACHE_PATH . "-{$dirName}-{$parts['filename']}_{$this->newWidth}_{$this->newHeight}{$this->quality}{$this->cropToFit}{$this->sharpen}.{$this->saveAs}";
        $this->cacheFileName   = preg_replace('/^a-zA-Z0-9\.-_/', '', $this->cacheFileName);

        if($this->verbose) { $this->verbose("Cache file is: {$this->cacheFileName}"); }
    }

    private function checkCache() {
        $imageModifiedTime = filemtime($this->pathToImage);
        $cacheModifiedTime = is_file($this->cacheFileName) ? filemtime($this->cacheFileName) : null;

        // If cached image is valid, output it.
        if(!$this->ignoreCache && is_file($this->cacheFileName) && $imageModifiedTime < $cacheModifiedTime) {
            if($this->verbose) { $this->verbose("Cache file is valid, output it."); }
            $this->outputImage($this->cacheFileName, $this->verbose);
        }

        if($this->verbose) { $this->verbose("Cache is not valid, process image and create a cached version of it."); }
    }

    // Open Image
    private function openImage() {
        if($this->verbose) { $this->verbose("File extension is: {$this->fileExtension}"); } 

        switch($this->fileExtension) {   
            case 'jpg':
            case 'jpeg':
                $this->image = imagecreatefromjpeg($this->pathToImage);
                if($this->verbose) { $this->verbose("Opened the image as a JPEG image."); }
                break;
     
            case 'gif':
                $this->image = imagecreatefromgif($this->pathToImage);
                if($this->verbose) { $this->verbose("Opened the image as a GIF image."); }
                break;

            case 'png':
                $this->image = imagecreatefrompng($this->pathToImage);
                if($this->verbose) { $this->verbose("Opened the image as a PNG image."); }
                break;

            default: $this->errorMessage('No support for this file extension.');
        }
    }

    // Resize Image
    private function resizeImage() {
        $this->aspectRatio = $this->width / $this->height;
        if($this->newWidth && !$this->newHeight) {
            $this->newHeight = round($this->newWidth / $this->aspectRatio);
            if($this->verbose) { $this->verbose("New width is known {$this->newWidth}, height is calculated to {$this->newHeight}."); }
        }
        else if(!$this->newWidth && $this->newHeight) {
            $this->newWidth = round($this->newHeight * $this->aspectRatio);
            if($this->verbose) { $this->verbose("New height is known {$this->newHeight}, width is calculated to {$this->newWidth}."); }
        }
        else if($this->newWidth && $this->newHeight) {
            $ratioWidth  = $this->width  / $this->newWidth;
            $ratioHeight = $this->height / $this->newHeight;
            $ratio = ($ratioWidth > $ratioHeight) ? $ratioWidth : $ratioHeight;
            $this->newWidth  = round($this->width  / $ratio);
            $this->newHeight = round($this->height / $ratio);
            if($this->verbose) { $this->verbose("New width & height is requested, keeping aspect ratio results in {$this->newWidth}x{$this->newHeight}."); }
        }
        else {
            $this->newWidth = $this->width;
            $this->newHeight = $this->height;
            if($this->verbose) { $this->verbose("Keeping original width & heigth."); }
        }

        if(!($this->newWidth == $this->width && $this->newHeight == $this->height)) {
            $imageResized = $this->createImageKeepTransparency($this->newWidth, $this->newHeight);
            imagecopyresampled($imageResized, $this->image, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->width, $this->height);
            $this->image  = $imageResized;
            $this->width  = $this->newWidth;
            $this->height = $this->newHeight;
        }
    } 

    // Crop Image
    private function cropImage() {
        if($this->cropToFit && $this->newWidth && $this->newHeight) {
            $targetRatio = $this->newWidth / $this->newHeight;
            $cropWidth   = $targetRatio > $this->aspectRatio ? $this->width : round($this->height * $targetRatio);
            $cropHeight  = $targetRatio > $this->aspectRatio ? round($this->width  / $targetRatio) : $this->height;
            if($this->verbose) { $this->verbose("Crop to fit into box of {$this->newWidth}x{$this->newHeight}. Cropping dimensions: {$cropWidth}x{$cropHeight}."); }
        }

        if($this->cropToFit) {
            if($this->verbose) { $this->verbose("Resizing, crop to fit."); }
            $cropX = round(($this->width - $cropWidth) / 2);
            $cropY = round(($this->height - $cropHeight) / 2);
            $imageResized = $this->createImageKeepTransparency($this->newWidth, $this->newHeight);
            imagecopyresampled($imageResized, $this->image, 0, 0, $cropX, $cropY, $this->newWidth, $this->newHeight, $cropWidth, $cropHeight);

            $this->image = $imageResized;
            $this->width = $this->newWidth;
            $this->height = $this->newHeight;
        }
    }

    // Sharpen Image
    private function sharpenImage() {
        if($this->sharpen){
            $matrix = array( 
                array(-1,-1,-1,), 
                array(-1,16,-1,), 
                array(-1,-1,-1,) 
            ); 
            $divisor = 8; 
            $offset = 0; 
            if(imageconvolution($this->image, $matrix, $divisor, $offset)){ 
                if($this->verbose){ $this->verbose("The image has been sharpened.");}
                return $this->image; 
            }
        }
        else{
            if($this->verbose){ if($this->verbose){ $this->verbose("No sharp settings to apply, carry on img.php.");}}
        }
    }

    // Keep Transparency
    private function createImageKeepTransparency($width, $height) {
        $img = imagecreatetruecolor($width, $height);
        imagealphablending($img, false);
        imagesavealpha($img, true);  
        return $img;
    }

    // Save Image
    private function saveImage() {
        switch($this->saveAs) {
            case 'jpeg':
            case 'jpg':
                if($this->verbose) { $this->verbose("Saving image as JPEG to cache using quality = {$this->quality}."); }
                imagejpeg($this->image, $this->cacheFileName, $this->quality);
            break;

            case 'gif':
                if($this->verbose) { $this->verbose("Saving the image as a GIF image."); }
                imagegif($this->image, $this->cacheFileName);
                break;

            case 'png':
                if($this->verbose) { $this->verbose("Saving image as PNG to cache."); }

                imagealphablending($this->image, false);
                imagesavealpha($this->image, true);
                imagepng($this->image, $this->cacheFileName);
            break;

            default:
                $this->errorMessage('No support to save as this file extension.');
            break;
        } 

        if($this->verbose) {
            clearstatcache();
            $cacheFilesize = filesize($this->cacheFileName);
            $this->verbose("File size of cached file: {$cacheFilesize} bytes.");
            $this->verbose("Cache file has a file size of " . round($cacheFilesize/$this->filesize*100) . "% of the original size.");
        }
    }

    private function outputImage($file) {
        $info = getimagesize($file);
        !empty($info) or $this->errorMessage("The file doesn't seem to be an image.");
        $mime   = $info['mime'];
        $lastModified = filemtime($file);

        $gmdate = gmdate("D, d M Y H:i:s", $lastModified);

        if($this->verbose) {
            $query = array();
            parse_str($_SERVER['QUERY_STRING'], $query);
            unset($query['verbose']);
            $url = '?' . http_build_query($query);

echo <<<EOD
<html lang='en'>
<meta charset='UTF-8'/>
<title>img.php verbose mode</title>
<h1>Verbose mode</h1>
<p><a href=$url><code>$url</code></a><br>
<img src='{$url}' /></p>
EOD;

            $this->verbose("Memory peak: " . round(memory_get_peak_usage() /1024/1024) . "M");
            $this->verbose("Memory limit: " . ini_get('memory_limit'));
            $this->verbose("Time is {$gmdate} GMT.");
            if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {$this->verbose("HTTP Last modified: ". $_SERVER['HTTP_IF_MODIFIED_SINCE']);}
        }

        if(!$this->verbose) {
            header('Cache-Control: public');
            header('Last-Modified: ' . $gmdate . ' GMT');
        }

        if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified){
            if($this->verbose) { $this->verbose("Would send header 304 Not Modified, but its $this->verbose mode."); exit; }
            header('HTTP/1.0 304 Not Modified');
        }
        else {
            if($this->verbose) { $this->verbose("Would send header to deliver image with modified time: {$gmdate} GMT, but its $this->verbose mode."); exit; }
            header('Content-type: ' . $mime);
            readfile($file);
        }
        exit;
    }

}

?>