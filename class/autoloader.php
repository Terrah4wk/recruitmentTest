<?php

/**
 * Class
 *
 * Autoloader
 *
 * @author Julian Vieser
 */
class autoloader {

    /**
     * Constructor
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        spl_autoload_register(array($this, 'loader'));
    }

    /**
     * Loader
     *
     * @access private
     * @return void
     */
    private function loader($className) {
        include $className . '.php';
    }

}