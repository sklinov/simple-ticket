<?php

    class Ticket {
        private $conn;
        private $table = "tickets";

        public $ticket_id;
        public $status_id;
        public $status_name;
        public $type_id;
        public $type_name;
        public $updated_at;
        public $topic;
        public $link;
        public $user_id;
        public $role_id;
        public $current_page;

        public function __construct($db) {
            $this->conn = $db;
            $this->user_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:null;
            $this->role_id = isset($_SESSION['role_id'])?$_SESSION['role_id']:null;
        }


        public function getTicketsByUserIdAndRole() {
           $this->calculateLimits();
           if($this->role_id == 1)
           {
            $query = 
            "SELECT
                    tickets.ticket_id AS ticket_id,
                    tickets.user_id AS user_id,
                    tickets.topic AS topic,
                    tickets.status_id AS status_id,
                    statuses.status_name AS status_name,
                    tickets.type_id AS type_id,
                    types.type_name AS type_name
                FROM ".$this->table."
                JOIN statuses ON statuses.status_id = tickets.status_id
                JOIN types ON types.type_id = tickets.type_id
                WHERE tickets.user_id = '".$this->user_id."'";
           }
           if($this->role_id == 2)
           {
            $query = 
            "SELECT
                    tickets.ticket_id AS ticket_id,
                    tickets.user_id AS user_id,
                    tickets.topic AS topic,
                    tickets.status_id AS status_id,
                    statuses.status_name AS status_name,
                    tickets.type_id AS type_id,
                    types.type_name AS type_name
                FROM ".$this->table."
                JOIN statuses ON statuses.status_id = tickets.status_id
                JOIN types ON types.type_id = tickets.type_id";  
           }

           $stmt = $this->conn->prepare($query);
           $stmt->execute();
           return $stmt;
        }

        private function calculateLimits() {
            return true;
        }
    }

//    SELECT messages.datetime AS updated_at FROM `messages` WHERE ticket_id = 1 ORDER BY messages.datetime DESC LIMIT 1