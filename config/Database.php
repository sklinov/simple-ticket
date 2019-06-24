<?php
   
    class Database {
        private $host;
        private $db_name;
        private $username;
        private $password;
        private $conn;
        

        //Database connect 
        public function connect() {
            include_once 'config.php';
            $this->host = $host;
            $this->db_name = $db_name;
            $this->username = $username;
            $this->password = $password;
            $this->conn = null;

            try {
                $this->conn = new PDO('mysql:host='.$this->host.';
                                       dbname='.$this->db_name,
                                       $this->username, 
                                       $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                echo 'Connection error:'. $e->getMessage();
            }
            return $this->conn;
        }
        
    }
