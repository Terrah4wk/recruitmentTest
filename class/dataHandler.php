<?php

/**
 * Data handler
 *
 * @author Julian Vieser
 */
class dataHandler {
    
    /**
     * Decimal to binary
     * @param string $value
     * @param int $bits bits
     * @return string
     */
    public function dec2bin($value, $bits = 8) {

        $bin = decbin($value);
        $bin = substr(str_repeat(0, $bits), 0, str_pad($bits, strlen($bin), $bin, STR_PAD_RIGHT));

        // Split into 4-bit
        $result = '';
        for ($i = 0; $i < $bits / 4; $i++) {
            $result .= ' ' . substr($bin, $i * 4, 4);
        }//end for 

        return ltrim($result);
        
    }//end dec2bin
    
    /**
     * getRank
     *
     * @access public
     * @param numeric $rgb2 rgb1
     * @param numeric $rgb2 rgb2
     * @param numeric $rgb3 rgb3
     * @return int
     */
    public function getRank($rgb1, $rgb2, $rgb3) {

        $a = $this->dec2bin($rgb1);
        $b = $this->dec2bin($rgb2);
        $c = $this->dec2bin($rgb3);

        return bindec($a[0].$a[5] . $b[0].$b[5] . $c[0].$c[5]);

    }//end getRank
    
    
    /**
     * Paging //! very simple ;)
     *
     * @access public
     * @param array $data Data
     * @param array $perPage items per Page
     * @return array
     */
    public function paging($data, $perPage = 20, $page) {

        $items = count($data);
        
        $newData = array();
        
        if($page < 1) $page = 1;
        
        $newData['page'] = $page;
        
        if($items > $perPage):
        
            $lastPage = ceil($items / $perPage);

            if($page > $lastPage) $page = $lastPage;

            $newData['data'] = array_chunk($data, $perPage);

            if ($page < $lastPage) $newData['forward'] = $page+1;
            if ($page > 1) $newData['backward'] = $page-1;
            if ($page < $lastPage-50) $newData['forward50'] = $page+50;
            if ($page >= 51) $newData['backward50'] = $page-50;
            
            $newData['data'] = $newData['data'][$page];
                
            return $newData;

        endif;

        $newData['data'] = $data;

        return $newData;

    }//end paging
    
    /**
     * Get pager
     *
     * @access public
     * @param array $pagingData Data
     * @param string $filename filename
     * @param array $sort sort
     * @return string
     */
    public function getPager($data, $filename = 'index.php', $sort) {

        echo '<p class="paging">';
        
        if(isset($data['backward50'])) {
            echo '<a href="' . $filename . '?page=' . $data['backward50'] . 
                 '&sort1=' . $sort[0]['get'] . '&sort2=' . $sort[1]['get'] . '">&lt;&lt;</a>'."\n";
        } else {
            echo '&lt;&lt; ';
        }//end if 
        
        if(isset($data['backward'])) {
            echo '<a href="' . $filename . '?page=' . $data['backward'] . 
                 '&sort1=' . $sort[0]['get'] . '&sort2=' . $sort[1]['get'] . '">&lt;</a>'."\n";
        } else {
            echo '&lt; ';
        }//end if
        
        echo '<b>' . $data['page'] . '</b>' . "\n";
        
        if(isset($data['forward'])) {
            echo '<a href="' . $filename . '?page=' . $data['forward'] . 
                 '&sort1=' . $sort[0]['get'] . '&sort2=' . $sort[1]['get'] . '">&gt;</a>'."\n";
        } else {
            echo '&gt; ';
        }//end if 
        
        if(isset($data['forward50'])) {
            echo '<a href="' . $filename . '?page=' . $data['forward50'] . 
                  '&sort1=' . $sort[0]['get'] . '&sort2=' . $sort[1]['get'] . '">&gt;&gt;</a>'."\n";
        } else {
            echo '&gt;&gt; ';
        }//end if 
        
        echo '</p>';
        
    }//end getPager
    
    /**
     * Save image as px in database
     * @access public
     * @param string $image
     * @return array
     */
    public function saveImage($src, $ext) {

        $db = new database();
        $data = new dataHandler();
        
        $size = getimagesize($src);
        
        $width = (int) $size[0];
        $height = (int) $size[1];

        if($ext === 'png')
        $image = imagecreatefrompng($src);
        
        if($ext === 'jpg' || $ext === 'jpeg')
        $image = imagecreatefromjpeg($src);
        
        if($ext === 'gif')
        $image = imagecreatefromgif($src);
        
        if($ext === 'bmp')
        $image = imagecreatefromwbmp($src);

        $values = '';
        for ($y = 0; $y < $height; ++$y) {

            for ($x = 0; $x < $width; ++$x) {
                
                $res = imagecolorat($image, $x, $y);
                
                $rgb1 = ($res >> 16) & 0xFF;
                $rgb2 = ($res >> 8) & 0xFF;
                $rgb3 = $res & 0xFF;               
                
                $values .= '(
                            AES_ENCRYPT("'.$rgb1.'", "' . $db->aesKey . '"), 
                            AES_ENCRYPT("'.$rgb2.'", "' . $db->aesKey . '"), 
                            AES_ENCRYPT("'.$rgb3.'", "' . $db->aesKey . '"),
                            '.$x.',
                            '.$y.',
                            ' . $data->getRank($rgb1, $rgb2, $rgb3) . '
                        ),';
                
                if ($x % 100 == 0) $values .= '|';

            }//end for $x
            
        }//end for $y

        $valueArray = explode('|', $values);
        $i_max = count($valueArray);
        for ($i = 0; $i < $i_max; ++$i) {
            
            $valueArray[$i] = substr($valueArray[$i], 0, strlen($valueArray[$i])-1);
            
            $sql = 'INSERT INTO 
                        pixel (pixelRGB1, pixelRGB2, pixelRGB3, pixelX, pixelY, pixelRank)
                    VALUES ' . $valueArray[$i];

            $db->insert($sql);
            
        }

        imagedestroy($image);
        
    }//end saveImage

}//end dataHandler

?>