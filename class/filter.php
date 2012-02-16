<?php

/**
 * Class
 *
 * Filter.
 *
 * @author Julian Vieser
 */
class filter {
    
    /** 
     * Is image
     *
     * @access public
     * @param string $path path to image
     * @return mixed
     */
    public function isImage($filename) {
        
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if($ext === 'png' || $ext === 'jpg' || $ext === 'jpeg' || $ext === 'gif' || $ext === 'bmp') {
            return $ext;
        }//end if
        
        return false;
        
    }//end rgbCheck

    /**
     * RGB check
     *
     * @access public
     * @param numeric $rgb RGB Code
     * @return boolean
     */
    public function rgbCheck($value) {
        
        if($value >= 0 && $value <= 255) {
            return true;
        }//end if
        
        return false;
        
    }//end rgbCheck
    
    /**
     * Is size
     *
     * @access public
     * @param int $min
     * @param int $max
     * @return boolean
     */
    public function isSize($filename, $min = 2000, $max = 5120, $height = 200, $width = 200) {
        
        
        $fileSize = filesize($filename);
        $imageSize = getimagesize($filename);
        
        if($fileSize >= $min && $fileSize <= $max && $imageSize[0] >= $width && $imageSize[1] >= $height) {
            return true;
        }//end if
        
        return false;
        
    }//end isSize
    
}//end filter