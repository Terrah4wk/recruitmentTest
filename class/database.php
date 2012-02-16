<?php

/**
 * Database
 *
 * @author Julian Vieser
 */
class database {

    /**
     * Database name
     *
     * @var string
     * @access private
     */
    private $dbName = 'task1';

    /**
     * Username
     *
     * @var string
     * @access private
     */
    private $user = 'user';

    /**
     * Password
     *
     * @var string
     * @access private
     */
    private $password = 'user';
    
    /**
     * AES key for crypting
     *
     * @var string
     * @access public
     */
    public $aesKey = '1aSdui8j8UhKn80jS';
    
    

    /**
     * Database connection
     *
     * @access private
     * @return object
     */
    private function connect() {

        try {

            $db = new PDO('mysql:host=localhost;dbname=' . $this->dbName, $this->user, $this->password);
            $db->exec('SET CHARACTER SET utf8');
            
            return $db;
            
        } catch (PDOException $e) {

            print "Error!: " . $e->getMessage() . "<br/>";
            die();
            
        }//end try
        
    }//end connect

    /**
     * Select
     *
     * @access public
     * @param string $sql SQL
     * @return array
     */
    public function select($sql) {
        
        try {
            
            $db = $this->connect();
            $q = $db->prepare($sql);
            $q->execute();
            
            return $q->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {

            print "Error!: " . $e->getMessage() . "<br/>";
            die();
            
        }
        
    }//end select
    
    /**
     * Insert
     *
     * @access public
     * @param string $sql SQL
     * @param array $fields Fields
     * @return void
     */
    public function insert($sql = '') {
        
        try {
            
            $db = $this->connect();
            $q = $db->prepare($sql);
            $q->execute();
            
        } catch (PDOException $e) {

            print "Error!: " . $e->getMessage() . "<br/>";
            die();
            
        }
        
    }//end insert
    

}//end database