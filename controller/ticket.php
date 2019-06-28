<?php
    session_start();

    include_once '../config/Database.php';
    include_once '../model/Ticket.php';
    include_once '../view/ticketview.php';
    
    $database = new Database();
    $db = $database->connect();

    $tickets = new Ticket($db);
    
    $tickets->current_page = isset($_GET['current_page'])? $_GET['current_page']:1;
    
    $result = $tickets->getTicketsByUserIdAndRole();
    
    $num = $result->rowCount();

    if($num > 0) {
        $tickets_array = array();
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            array_push($tickets_array, $row);
        }
  
        $ticket_view = new TicketView;
        $ticket_view->role_id = $tickets->role_id;
        $ticket_view->tickets_on_page = $tickets->tickets_on_page;
        $ticket_view->current_page = isset($_GET['current_page'])? $_GET['current_page']:1;
        $ticket_view->showTickets($tickets_array);
        
    } else {
        echo 'No Tickets returned from database'; 
    }