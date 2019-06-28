<?php

    session_start();

    include_once '../config/Database.php';
    include_once '../model/Ticket.php';
    include_once '../view/ticketview.php';
    
    $database = new Database();
    $db = $database->connect();
    $ticket = new Ticket($db);

    $ticket->ticket_id = isset($_POST['ticket_id'])? $_POST['ticket_id']: NULL;
    $ticket->user_id = isset($_POST['user_id'])? $_POST['user_id']: NULL;
    $ticket->text = isset($_POST['text'])? $_POST['text']: NULL;
    $ticket->status_id = isset($_POST['status_id'])? $_POST['status_id']: NULL;
    $ticket->type_id = isset($_POST['type_id'])? $_POST['type_id']: NULL;
    $ticket->timestamp = time();

    $uploaddir = dirname( dirname(__FILE__)).'/uploads/';
    
    //$current_dir = dirname(__FILE__);

            
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
                    $ticket->file_path = $uploadfile;
                    $ticket->file_name = $_FILES['file']['name'];
                    echo "Файл ". $_FILES['file']['name'] ." был успешно загружен.\n";
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
    
    // Статус на "В работе", если пользователь пишет после закрытия тикета    
    if($ticket->status_id == '4' && $ticket->role_id == '1') {
        $ticket->status_id = 2;
        $ticket->updateStatusAndType();
    } 

    if($ticket->addMessage()) {
        echo json_encode(array("status"=>"success", "ticket_id"=>$ticket->ticket_id, "status_id"=>$ticket->status_id, "role_id"=>$ticket->role_id));
    };

