<?php

    class User {
        private $conn;
        private $table = "users";

        public $username;
        public $password;
        private $hash;
        public $role_id;
        public $role_name;
        public $user_logged_in = "false";
        
        public function __construct($db) {
            $this->conn = $db;
        }

        public function userLogin() {
            $query = "SELECT
                users.user_id AS user_id,
                users.username AS username,
                users.PASSWORD AS hash,
                users.role_id AS role_id,
                roles.role_name AS role_name
            FROM
                `users`
            JOIN
                roles
            ON
                roles.role_id = users.role_id
            WHERE
                users.username ='".$this->username."'";

           $stmt = $this->conn->prepare($query);
           $stmt->execute();
           return $stmt;
        }
    }