<?php
class TicketView {
    
    public  $role_id;
    public  $current_page;
    
    private $tickets = [];
    public  $types = [
        ["type_id"=> 1, "type_name"=>"Тип 1"],
        ["type_id"=> 2, "type_name"=>"Тип 2"],
        ["type_id"=> 3, "type_name"=>"Тип 3"]
    ];

    public $statuses = [];

    private $number_of_pages;
    private $number_of_tickets_on_page = 4;
    
    public function showFullTicket($ticket) {
        //echo $ticket->ticket_id;
        $this->showHeader($ticket);
        $this->showMessages($ticket);
        $this->showReply($ticket);
    }

    private function showHeader($ticket) {
        echo '
            <nav class="navbar">
                <h2 class="navbar-brand">Просмотр тикета #'.$ticket->ticket_id.'</h2>';
        if($this->role_id == 2) {
        echo '<form>
                <div class="form-row">
                    <div class="form-group mr-3">
                        <label for="status-field">Изменить статус</label>
                        <select class="form-control" id="message-status-field">';
                            foreach($ticket->statuses as $status) {
                                if($ticket->status_id == $status['status_id']) {
                                    $selected = "selected";
                                }
                                else {
                                    $selected = "";
                                }
                                echo '<option value="'.$status['status_id'].'" '.$selected.'>'.$status['status_name'].'</option>';
                            }
                echo '</select>
                    </div>
                    <div class="form-group">
                        <label for="type-field">Изменить тип</label>
                        <select class="form-control" id="message-type-field">';
                            foreach($ticket->types as $type) {
                                if($ticket->type_id == $type['type_id']) {
                                    $selected = "selected";
                                }
                                else {
                                    $selected = "";
                                }
                                echo '<option value="'.$type['type_id'].'" '.$selected.'>'.$type['type_name'].'</option>';
                            }
                    echo '</select>
                    </div>
                </div>
              </form>  
        ';
        }
        echo '</nav>
        <div class="bg-light p-3 border-top border-bottom border-dark">
            Статус: '.$ticket->status_name.', создан '.$ticket->timestamp_created.', тема: '.$ticket->topic.'
        </div>
        ';  
    }

    private function showMessages($ticket) {
        echo '<table class="table"><tbody>';
        foreach($ticket->messages as $message) {
            if($message['role_id'] == 1) {
                $avatar = "./img/user.png";
                $heading = "Ваше сообщение";
            }
            else if ($message['role_id'] == 2) {
                $avatar = "./img/support.png";
                $heading = "Ответ поддержки";
            }

            echo '
                <tr>
                    <td>
                        <img src="'.$avatar.'" alt="avatar">
                    </td>
                    <td>
                        <h5>'.$heading.'</h5>
                        <p>'.$message['text'].'</p>';
                        if(isset($message['files']))
                        {
                            foreach($message['files'] as $file) {
                                echo $file['file_name'].' '.'<a href="'.$_SERVER['DOCUMENT_ROOT'].'/ticket'.$file['file_path'].'">Скачать</a>';
                            }
                        }
                  echo '
                    </td>
                    <td>'.$message['timestamp'].'</td>
                </tr>
            ';
        }
        echo '</tbody></table>';
    } 

    private function showReply($ticket) {
        echo '
        <div>
        <form>
        
        <input type="hidden" id="message-ticket-id-field" value="'.$ticket->ticket_id.'">
        <input type="hidden" id="message-user-id-field" value="'.$_SESSION['user_id'].'">

        <div class="form-group">
            <textarea class="form-control" id="message-text-field" rows="3"></textarea>
        </div>
        <div class="form-group row mx-3">
            <button class="btn btn-primary mr-3" id="message-submit-btn">Ответить</button>
            <input type="file" id="message-file-field">
        </div>
        </form>
        </div>
        ';
    }

    public function showTickets($tickets) {        
        $this->tickets = $tickets;
        $this->showControls();      
        $this->ticketsToShow();
        $this->showPagination();
    }
    
    public function newTicket() {
        echo '
            <nav class="navbar">
                <h2 class="navbar-brand">Новый тикет</h2>
            </nav>
            <hr>
        <form enctype="multipart/form-data" method="POST">
            <input type="hidden" id="user-id-field" value="'.$_SESSION['user_id'].'"></input>
            <div class="form-group">
                <label for="type-field">Тип проблемы *</label>
                <select class="form-control" id="type-field" required>';
                    foreach($this->types as $type) {
                        echo '<option value="'.$type['type_id'].'">'.$type['type_name'].'</option>';
                    }
           echo '</select>
            </div>
            <div class="form-group">
                <label for="topic-field">Краткое описание (тема) *</label>
                <input type="text" id="topic-field" class="form-control" required></input>
            </div>
            <div class="form-group">
                <label for="text-field">Подробное описание *</label>
                <textarea id="text-field" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="link-field">Ссылка на сайт, страницу, и т.д.</label>
                <input type="text" id="link-field" class="form-control"></input>
            </div>
            <div class="form-group">
            <input type="file" name="file" id="file-field"></input>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" id="new-ticket-submit-btn">Отправить</button>
                <button type="reset" class="btn btn-secondary" id="new-ticket-cancel-btn">Отмена</button>
            </div>
        </form>    
        
        ';
        
    }

    private function ticketsToShow() {
        echo '<table class="table">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Статус</th>
                        <th>Тип проблемы</th>
                        <th>Тема</th>
                        <th>Дата обновления</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>';
        echo '<div class="row">';
        foreach($this->tickets as $ticket) {

            echo '<tr>
                    <td>'.$ticket["ticket_id"].'</td>
                    <td>'.$ticket["status_name"].'</td>
                    <td>'.$ticket["type_name"].'</td>
                    <td>'.$ticket["topic"].'</td>
                    <td> - Дата/время - </td>
                    <td><button class="btn btn-outline-secondary" id="ticket-view-btn" data-ticket-id="'.$ticket["ticket_id"].'">Просмотр</button></td>
                  </tr>';
        }
        echo '
                    </tbody>
                </table>';
    }

    private function showControls() {
        echo '
            <nav class="navbar">
                <h2 class="navbar-brand">Мои тикеты</h2>';
        if($this->role_id == 1) {
        echo '
                <button class="btn btn-outline-success my-2 my-sm-0" id="ticket-new-btn" type="button">+ Новый тикет</button>
        ';
        }
        echo '</nav>
        '; 
    }

    private function showPagination() {
        $number_of_tickets  = count($this->tickets);
        $this->number_of_pages = ceil($number_of_tickets/$this->number_of_tickets_on_page);
        echo '
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item"><button class="page-link" id="page-prev">Previous</button></li>';
                for($page_number=1;$page_number<=$this->number_of_pages;$page_number++)
                {
                    echo '<li class="page-item"><button class="page-link" id="page-link" data-page="'.$page_number.'">'.$page_number.'</button></li>';
                }
           echo '<li class="page-item"><button class="page-link" id="page-next">Next</button></li>
            </ul>
        </nav>
        
        ';
    }

    private function sorttickets() {
        if($this->sort_order_asc == "true")
        {
            usort($this->tickets_array, function ($task1, $task2) {
                return $task1[$this->sort_by] <=> $task2[$this->sort_by];
            });
        } else {
            usort($this->tickets_array, function ($task1, $task2) {
                return $task2[$this->sort_by] <=> $task1[$this->sort_by];
            }); 
        }

    }

}