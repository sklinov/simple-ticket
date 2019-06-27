<?php

    session_start();

    include_once '../config/Database.php';
    include_once '../model/Ticket.php';
    //include_once '../view/ticketview.php';
    
    // header('Access-Control-Allow-Origin: *');
    // header('Access-Control-Allow-Headers: X-Requested-With');
    // header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

    $database = new Database();
    $db = $database->connect();
    $ticket = new Ticket($db);

    //var_dump($_POST);

    $ticket->ticket_id = isset($_POST['ticket_id'])? $_POST['ticket_id']: NULL;
    $ticket->user_id = isset($_POST['user_id'])? $_POST['user_id']: NULL;
    $ticket->status_id = isset($_POST['status_id'])? $_POST['status_id']: NULL;
    $ticket->type_id = isset($_POST['type_id'])? $_POST['type_id']: NULL;

    if($ticket->updateStatusAndType()) {
        echo json_encode(array("status"=>"success", "ticket_id"=>$ticket->ticket_id));
    };