<?php
    namespace CopaControleVeiculos;

    class Connection {
        
        const SERVER = '';
        const DATABASE = '';
        const USERNAME = '';
        const PASSWD = '';

        private $mysqli;

        public function __construct() {
               $this->mysqli = new \mysqli(self::SERVER, self::USERNAME, self::PASSWD, self::DATABASE);
        }
    
        public function close() {
            $this->mysqli->close();
        }

        public function getConnection() {
            return $this->mysqli;
        }

        public function getConnectionErrorNo() {
            return $this->mysqli->connect_errno;
        }
        
        public function getConnectionErrorStr() {
            return $this->mysqli->connect_error;
        }

        public function getErrorNo() {
            return $this->mysqli->errno;
        }

        public function getError() {
            return $this->mysqli->error;
        }

        public function executeQuery($query) {
            return $this->mysqli->query($query);
        }
        
        public function realEscapeString($str) {
            return $this->mysqli->real_escape_string($str);
        }
    }   

?>
