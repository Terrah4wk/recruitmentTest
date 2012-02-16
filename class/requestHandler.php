<?php

/**
 * Class
 *
 * Request handler.
 *
 * @author Julian Vieser
 */
class requestHandler {
    
    /**
     * Get post
     *
     * @access public
     * @return array
     */
    public function getPost() {
        
        $post = array();
        
        if(isset($_POST) && count($_POST) > 0) {
            $post = $_POST;
        }//end if 
        
        return $post;
        
    }//end getPost
    
    /**
     * Get File
     * 
     * @access public
     * @return array
     */
    public function getFile() {
        
        $files = array();
        
        if(isset($_FILES)) {
            $files = $_FILES;
        }//end if
        
        return $files;
        
    }//end getFile
    
    /**
     * Get Params
     * 
     * @access public
     * @return array
     */
    public function getParams() {
        
        $params = array();
        
        if(isset($_GET) && count($_GET) > 0) {
            $params = $_GET;
        }//end if
        
        return $params;
        
    }//end getParams
    
    /**
     * Get Page
     * 
     * @access public
     * @return int
     */
    public function getPage() {
        
        $page = 1;
        
        if(isset($_GET['page'])) {
            $page = $_GET['page'];
        }//end if
        
        return $page;
        
    }//end getPage
    
    /**
     * Get sorting
     * 
     * @access public
     * @return array
     */
    public function getSorting() {
        
        $sort = array();
        
        $sort[0]['get'] = 'asc';
        $sort[0]['link'] = 'desc';
        
        if(isset($_GET['sort1']))
        switch ($_GET['sort1']) {
            case 'asc':
                $sort[0]['get'] = 'asc';
                $sort[0]['link'] = 'desc';
                break;

            case 'desc':
                $sort[0]['get'] = 'desc';
                $sort[0]['link'] = 'asc';
                break;
            
            default:
                $sort[0]['get'] = 'asc';
                $sort[0]['link'] = 'desc';         
                break;
        }//end switch
        
        
        $sort[1]['get'] = 'asc';
        $sort[1]['link'] = 'desc';
        
        if(isset($_GET['sort2']))
        switch ($_GET['sort2']) {
            case 'asc':
                $sort[1]['get'] = 'asc';
                $sort[1]['link'] = 'desc';
                break;

            case 'desc':
                $sort[1]['get'] = 'desc';
                $sort[1]['link'] = 'asc';
                break;
        }//end switch
        
        return $sort;
        
    }//end getSorting
    
}