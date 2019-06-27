<?php

    session_start();

    include_once '../config/Database.php';
    include_once '../model/Ticket.php';
    include_once '../view/ticketview.php';
    
    // header('Access-Control-Allow-Origin: *');
    // header('Access-Control-Allow-Headers: X-Requested-With');
    // header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

    $database = new Database();
    $db = $database->connect();
    $ticket = new Ticket($db);
    $ticket_view = new TicketView;

    $ticket->ticket_id = isset($_POST['ticket_id'])? $_POST['ticket_id']: NULL;

    if($ticket->ticket_id) {
        try {
            $ticket->getFullTicketById();

            // Get lists of types and statuses
            $result = $ticket->getTypes();
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                array_push($ticket->types, $row);
            }
            
            $result = $ticket->getStatuses();
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                array_push($ticket->statuses, $row);
            }           
            $ticket_view->role_id = $_SESSION['role_id'];
            $ticket_view->showFullTicket($ticket);
        }
        catch (Exception $e) {
            echo 'Ошибка: ',  $e->getMessage(), "\n";
        }

        
    }


