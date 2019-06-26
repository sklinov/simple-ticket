<?php

    class Ticket {
        private $conn;
        private $table = "tickets";

        public $ticket_id;
        public $status_id = 1;
        public $status_name;
        public $type_id;
        public $type_name;
        public $updated_at;
        public $topic;
        public $link;
        public $user_id;
        public $role_id;
        public $current_page;
        public $file_id;
        public $file_path;
        public $file_name;
        public $tmp;
        public $text;
        public $message_id;

        public $types = [];

        public function __construct($db) {
            $this->conn = $db;
            $this->user_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:null;
            $this->role_id = isset($_SESSION['role_id'])?$_SESSION['role_id']:null;
        }

        public function getTypes() {
            $query = "SELECT * from types";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
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

        public function addTicket() {
            //$this->addToTickets();
            if($this->addToTickets() &&
               $this->addToMessages() &&
               $this->addToFiles()
               ) 
                {
                    return true;
                }
        }

        private function addToTickets() {
            $query = 'INSERT INTO '.$this->table.'
            SET
                user_id =:user_id,
                status_id =:status_id,
                type_id =:type_id,
                topic =:topic,
                link =:link';
            //Prepare statement
            $stmt = $this->conn->prepare($query);

            //Clean data up
            //$this->name = htmlspecialchars(strip_tags($this->title));

            //Bind data
            $stmt->bindParam(':user_id',$this->user_id);
            $stmt->bindParam(':status_id',$this->status_id);
            $stmt->bindParam(':type_id',$this->type_id);
            $stmt->bindParam(':topic',$this->topic);
            $stmt->bindParam(':link',$this->link);

            // Execute query
            if($stmt->execute()) {
                $this->ticket_id = $this->conn->lastInsertId();
                return true;
            }
            //print error 
            echo "Error: %s".$stmt->error;
            return false; 
        }

        private function addToMessages() {
            $query = 'INSERT INTO messages
            SET
                user_id =:user_id,
                ticket_id =:ticket_id,
                text =:text';
            //Prepare statement
            $stmt = $this->conn->prepare($query);

            //Clean data up
            //$this->name = htmlspecialchars(strip_tags($this->title));

            //Bind data
            $stmt->bindParam(':user_id',$this->user_id);
            $stmt->bindParam(':ticket_id',$this->ticket_id);
            $stmt->bindParam(':text',$this->text);

            // Execute query
            if($stmt->execute()) {
                $this->message_id = $this->conn->lastInsertId();
                return true;
            }
            //print error 
            echo "Error: %s".$stmt->error;
            return false; 
        }

        private function addToFiles() {
            if(isset($this->file_path) && isset($this->file_name))
            {
                $query = 'INSERT INTO files
                SET
                    message_id =:message_id,
                    file_path =:file_path,
                    file_name =:file_name';
                //Prepare statement
                $stmt = $this->conn->prepare($query);

                //Clean data up
                //$this->name = htmlspecialchars(strip_tags($this->title));

                //Bind data
                $stmt->bindParam(':message_id',$this->message_id);
                $stmt->bindParam(':file_path',$this->file_path);
                $stmt->bindParam(':file_name',$this->file_name);

                // Execute query
                if($stmt->execute()) {
                    $this->file_id = $this->conn->lastInsertId();
                    return true;
                }
                //print error 
                echo "Error: %s".$stmt->error;
                return false;
            }
            else {
                return true;
            } 
        }

        private function calculateLimits() {
            return true;
        }
    }

//    SELECT messages.datetime AS updated_at FROM `messages` WHERE ticket_id = 1 ORDER BY messages.datetime DESC LIMIT 1