<?php
    session_start();

    include_once '../config/Database.php';
    include_once '../model/Ticket.php';
    include_once '../view/ticketview.php';
    
    $database = new Database();
    $db = $database->connect();

    $tickets = new Ticket($db);
    
    $result = $tickets->getStats();
    
    $num = $result->rowCount();

    if($num > 0) {
        $stats_array = array();
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            array_push($stats_array, $row);
        }
  
        $ticket_view = new TicketView;
        $ticket_view->showStats($stats_array);
        
    } else {
        echo 'No Stats returned from database'; 
    }