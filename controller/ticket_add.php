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

    $ticket->user_id = isset($_POST['user_id'])? $_POST['user_id']: NULL;
    $ticket->type_id = isset($_POST['type_id'])? $_POST['type_id']: NULL;
    $ticket->topic   = isset($_POST['topic'])? $_POST['topic']: NULL;
    $ticket->text = isset($_POST['text'])? $_POST['text']: NULL;
    $ticket->link = isset($_POST['link'])? $_POST['link']: NULL;
    $ticket->timestamp_created = $ticket->getCurDate();

    $uploaddir = dirname( dirname(__FILE__)).'/uploads/';
    
    if(isset($_FILES['file']))
    {
        if($_FILES['file']['size']<=3145728) {
            if($_FILES['file']['type']=='image/jpeg' || 
            $_FILES['file']['type']=='image/png'  || 
            $_FILES['file']['type']=='image/svg'  ||
            $_FILES['file']['type']=='image/gif') 
            {
                $uploadfile = $uploaddir.$ticket->user_id."_".time()."__".basename($_FILES['file']['name']);
                var_dump($uploadfile);
                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
                    $ticket->file_path = '/uploads/'.$ticket->user_id."_".time()."__".basename($_FILES['file']['name']);
                    $ticket->file_name = $_FILES['file']['name'];
                    //echo "Файл ". $_FILES['file']['name'] ." был успешно загружен.\n";
                } 
                else {
                    echo "Ошибка загрузки файла:".$_FILES['file']['error'];
                }
            }
            else {
                echo "Тип файла не подходит для загрузки.\n";
            }
        }
        else {
            echo "Размер файла больше 3Мб и он не будет загружен \n";
        }
    }
    
    if($ticket->addTicket()) {
        echo json_encode(array("status"=>"success", "ticket_id"=>$ticket->ticket_id));
    };

