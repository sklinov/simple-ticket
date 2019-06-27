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
        public $timestamp;
        public $timestamp_created;
        public $user_id;
        public $role_id;
        public $current_page;
        public $file_id;
        public $file_path;
        public $file_name;
        public $tmp;
        public $text;
        public $message_id;
        public $messages = [];
        
        public $types = [];
        public $statuses = [];

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

        public function getStatuses() {
            $query = "SELECT * from statuses";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        public function getFullTicketById() {
            $result_t = $this->getTicketById();
            if($result_t->rowCount() > 0) {
                $row = $result_t->fetch(PDO::FETCH_ASSOC);
                extract($row);
                
                $this->user_id = $user_id;
                $this->status_id = $status_id;
                $this->status_name = $status_name;
                $this->type_id = $type_id;
                $this->type_name = $type_name;
                $this->topic = $topic;
                $this->link = $link;
                $this->timestamp_created = $timestamp_created;

                $result_m = $this->getTicketMessagesByTicketId();

                if(isset($result_m) && $result_m->rowCount()>0)
                {
                    while($row = $result_m->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $message = array(
                            'message_id'=> $message_id,
                            'user_id' => $user_id,
                            'username' => $username,
                            'text' => $text,
                            'role_id' => $role_id,
                            'timeshift' => $timeshift,
                            'timestamp' => $timestamp,
                        );
                        $result_f = $this->getFilesByMessageId($message_id);
                        if(isset($result_f) && $result_f->rowCount() > 0)
                        {
                            $message['files'] = [];
                            while($row_f = $result_f->fetch(PDO::FETCH_ASSOC)) {
                                extract($row_f);
                                $file = array(
                                    'file_id' => $file_id,
                                    'file_path' => $file_path,
                                    'file_name' => $file_name,
                                );
                                //echo("----FILES----");
                                //var_dump($file);
                                array_push($message['files'],$file);
                            }
                        }
                        //echo("----MESSAGE---");
                        //var_dump($message);
                        array_push($this->messages, $message);
                    }
                }
                return true;
            }
            //echo("----FULL TICKET---");
            //var_dump ($this);
            else {
               throw new Exception('Запрос не удался, попробуйте позже');
            }
        }

        private function getTicketById() {
            $query = "SELECT 
                        tickets.ticket_id AS ticket_id,
                        tickets.user_id AS user_id,
                        tickets.status_id AS status_id,
                        statuses.status_name AS status_name,
                        tickets.type_id AS type_id,
                        types.type_name AS type_name,
                        tickets.topic AS topic,
                        tickets.link AS link,
                        tickets.timestamp_created AS timestamp_created
                        FROM tickets
                        JOIN statuses ON statuses.status_id = tickets.status_id
                        JOIN types ON types.type_id = tickets.type_id
                        WHERE tickets.ticket_id = '".$this->ticket_id."'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
 
        }

        private function getTicketMessagesByTicketId() {
            $query = "SELECT
                        messages.message_id AS message_id,
                        messages.ticket_id AS ticket_id,
                        messages.user_id AS user_id,
                        messages.text AS text,
                        users.username AS username,
                        users.role_id AS role_id,
                        users.timeshift AS timeshift,
                        messages.timestamp AS timestamp
                      FROM messages 
                      JOIN users ON users.user_id = messages.user_id
                      WHERE messages.ticket_id = '".$this->ticket_id."'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        private function getFilesByMessageId($current_message_id) {
            $query = "SELECT 
                        files.file_id AS file_id,
                        files.file_path AS file_path,
                        files.file_name AS file_name,
                        files.message_id AS message_id
                      FROM files
                      WHERE files.message_id = '".$current_message_id."'";
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

        public function addMessage() {
            //$this->addToTickets();
            if($this->addToMessages() &&
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
                link =:link,
                timestamp_created =:timestamp_created';
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
            $stmt->bindParam(':timestamp_created',$this->timestamp_created);

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

        public function updateStatusAndType() {
            $query = "UPDATE tickets
                SET
                    status_id =:status_id,
                    type_id =:type_id
                WHERE ticket_id = :ticket_id";
                //Prepare statement
            
            $stmt = $this->conn->prepare($query);

                //Bind data
            $stmt->bindParam(':status_id',$this->status_id);
            $stmt->bindParam(':type_id',$this->type_id);
            $stmt->bindParam(':ticket_id',$this->ticket_id);

            if($stmt->execute()) {
                return true;
            }
            else {
                return false;
            }
        }

        private function calculateLimits() {
            return true;
        }
    }

//    SELECT messages.timestamp AS updated_at FROM `messages` WHERE ticket_id = 1 ORDER BY messages.timestamp DESC LIMIT 1