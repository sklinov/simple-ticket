<?php
    session_start();

    include_once '../config/Database.php';
    include_once '../model/User.php';
    include_once '../view/login.php';
 
    
    $database = new Database();
    $db = $database->connect();

    $user = new User($db);
    
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: X-Requested-With');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Content-type: application/json');

    $user->username = isset($_POST['username'])? $_POST['username']: NULL;
    $user->password = isset($_POST['password'])? $_POST['password']: NULL;
    

    if($user->username && $user->password)
    {   
        $result = $user->userLogin();
        $num = $result->rowCount();

        if($num > 0) {
            $user_add = array();
            $user_arr['data'] = array();
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $user_item = array (
                    'user_id' => $user_id,
                    'username' => $username,
                    'hash' => $hash,
                    'role_id' => $role_id,
                    'role_name' => $role_name
                );
                //Check password
                if(password_verify($user->password, $user_item['hash'])) {
                    $_SESSION['role_id'] = $user_item['role_id'];
                    $_SESSION['user_id'] = $user_item['user_id'];
                    array_push($user_arr['data'],$user_item);
                }
            }
            if(isset($user_arr['data'])) {
                $user_arr['status'] = 'success';
                echo json_encode($user_arr);
            }
            else {
                echo json_encode(
                    array('message' => 'Wrong password',
                          'status' => 'fail')
                );
            }
        } else {
            // No users found
            echo json_encode(
                array('message' => 'No users found with username entered',
                      'status' => 'fail')
            );
        }   
        //$_SESSION['user_role'] = $user->user_logged_in;
    }
    
   


