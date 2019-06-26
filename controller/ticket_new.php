<?php

    session_start();

    include_once '../config/Database.php';
    include_once '../model/Ticket.php';
    include_once '../view/ticketview.php';
    
    $database = new Database();
    $db = $database->connect();

    $tickets = new Ticket($db);

    $result = $tickets->getTypes();
    
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        array_push($tickets->types, $row);
    }

    $ticket_view = new TicketView;
    $ticket_view->types = $tickets->types; 
    $ticket_view->newTicket();