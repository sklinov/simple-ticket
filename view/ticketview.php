<?php
class TicketView {
    
    public  $role_id;
    public  $current_page;
    
    private $tickets = [];
    private $stats = [];
    public  $types = [];

    public $statuses = [];
    private $number_of_pages;
    
    // Просмотр тикета
    public function showFullTicket($ticket) {
        $this->showHeader($ticket);
        $this->showMessages($ticket);
        $this->showReply($ticket);
    }

    private function showHeader($ticket) {
        echo '
            <nav class="navbar sticky-top">
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
            Статус: '.$ticket->status_name.', создан '.$this->dateFull($ticket->timestamp_created).', тема: '.$ticket->topic.'
        </div>
        ';  
    }

    private function showMessages($ticket) {
        echo '<div class="table-wraper"><table class="table" id="messages"><tbody>';
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
                        <img src="'.$avatar.'" class="avatar" alt="avatar">
                    </td>
                    <td>
                        <h5>'.$heading.'</h5>
                        <p>'.$message['text'].'</p>';
                        if(isset($message['files']))
                        {
                            foreach($message['files'] as $file) {
                                echo $file['file_name'].' '.'<a href=".'.$file['file_path'].'">Скачать</a>';
                            }
                        }
                  echo '
                    </td>
                    <td>'.$this->dateFull($message['timestamp'],", ").'</td>
                </tr>
            ';
        }
        echo '</tbody></table></div>';
    } 

    private function showReply($ticket) {
        echo '
        <div class="container fixed-bottom">
        <div class="row">
            <div class="col-8">
                <form class="needs-validation">
                    <input type="hidden" id="message-status-id-field" value="'.$ticket->status_id.'">
                    <input type="hidden" id="message-type-id-field" value="'.$ticket->type_id.'">
                    <input type="hidden" id="message-ticket-id-field" value="'.$ticket->ticket_id.'">
                    <input type="hidden" id="message-user-id-field" value="'.$_SESSION['user_id'].'">

                    <div class="form-group">
                        <textarea class="form-control" id="message-text-field" rows="3" required></textarea>
                    </div>
                    <div class="form-group row mx-3">
                        <button class="btn btn-primary mr-3" id="message-submit-btn">Ответить</button>
                        <input type="file" id="message-file-field">   
                    </div>
                </form>
            </div>
            <div class="col-4">
                <button class="btn btn-secondary mt-4 ml-auto" id="back-to-list-btn">Перейти в список тикетов</button>
            </div>
        </div>
        </div>
        ';
    }

    // Просмотр списка тикетов
    public function showTickets($tickets) {        
        $this->tickets = $tickets;
        $this->showControls();      
        $this->ticketsToShow();
        $this->showPagination($tickets[0]['total'], $this->tickets_on_page);
    }
    
    private function showControls() {
        echo '
            <nav class="navbar">
                <h2 class="navbar-brand">Мои тикеты</h2>';
        if($this->role_id == 1) {
        echo '
                <button class="btn btn-outline-secondary my-2 my-sm-0" id="ticket-new-btn" type="button">+ Новый тикет</button>
        ';
        }
        if($this->role_id == 2) {
            echo '
                    <button class="btn btn-outline-secondary my-2 my-sm-0" id="show-stats-btn" type="button">Статистика</button>
            ';
            }
        echo '</nav>
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
            $exclamation = '';
            if($this->role_id == 2 && $ticket['number_of_messages'] >= 10) {
                $exclamation = '<img class="mx-1" src="./img/exclamation.png" alt="Число сообщений больше 10!">';
            }
            echo '<tr>
                    <td>'.$ticket["ticket_id"].'</td>
                    <td>'.$ticket["status_name"].'</td>
                    <td>'.$ticket["type_name"].'</td>
                    <td>'.$ticket["topic"].'</td>
                    <td>'.$this->dateFull($ticket["updated_at"]).$exclamation.'</td>
                    <td><button class="btn btn-outline-secondary" id="ticket-view-btn" data-ticket-id="'.$ticket["ticket_id"].'">Просмотр</button></td>
                  </tr>';
        }
        echo '
                    </tbody>
                </table>';
    }

    private function showPagination($total, $number_of_tickets_on_page) {
        $number_of_tickets  = $total;
        $this->number_of_pages = ceil($number_of_tickets/$number_of_tickets_on_page);
        echo '
        <nav>
            <ul class="pagination">
                <li class="page-item"><button class="page-link" id="page-prev"><- Сюда</button></li>';
                for($page_number=1;$page_number<=$this->number_of_pages;$page_number++)
                {
                    echo '<li class="page-item"><button class="page-link" id="page-link" data-page="'.$page_number.'">'.$page_number.'</button></li>';
                }
           echo '<li class="page-item"><button class="page-link" id="page-next">Туда -></button></li>
            </ul>
        </nav>
        
        ';
    }

    public function newTicket() {
        echo '
            <nav class="navbar">
                <h2 class="navbar-brand">Новый тикет</h2>
            </nav>
            <hr>
        <form id="message-add-form" enctype="multipart/form-data" method="POST" class="needs-validation">
            <input type="hidden" id="user-id-field" value="'.$_SESSION['user_id'].'"></input>
            <div class="form-group">
                <label for="type-field">Тип проблемы <span class="text-danger">*</span></label>
                <select class="form-control" id="type-field" required>';
                    foreach($this->types as $type) {
                        echo '<option value="'.$type['type_id'].'">'.$type['type_name'].'</option>';
                    }
           echo '</select>
            <div class="invalid-feedback">
            </div>
            <div class="form-group">
                <label for="topic-field">Краткое описание (тема) <span class="text-danger">*</span></label>
                <input type="text" id="topic-field" class="form-control" required></input>
                <div class="invalid-feedback">
            </div>
            <div class="form-group">
                <label for="text-field">Подробное описание <span class="text-danger">*</span></label>
                <textarea id="text-field" class="form-control" required></textarea>
                <div class="invalid-feedback">
            </div>
            <div class="form-group">
                <label for="link-field">Ссылка на сайт, страницу, и т.д.</label>
                <input type="text" id="link-field" class="form-control"></input>
            </div>
            <div class="form-group">
            <input type="file" name="file" id="file-field" multiple></input>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" id="new-ticket-submit-btn">Отправить</button>
                <button type="reset" class="btn btn-secondary" id="new-ticket-cancel-btn">Отмена</button>
            </div>
        </form>    
        
        ';
        
    }

    public function showStats($stats) {        
        $this->stats = $stats;
        echo '  <div class="container">
                <h3>Статистика по дням</h3>
                <table class="table">
                <thead>
                    <td>Дата</td>
                    <td>Количество созданных тикетов</td>
                </thead>
                <tbody>';
        foreach($this->stats as $day) {
            $date = explode('&', $this->dateFull($day['date'],'&'));
            $highlight = '';
            if($day['count'] > 10)
            {
                $highlight = 'class="bg-warning"';
            }
            echo '<tr '.$highlight.'>
                    <td>'.$date[0].'</td>
                    <td>'.$day['count'].'</td>'; 
        }
        echo '</tbody></table>
        <button class="btn btn-primary mt-4" id="back-to-list-btn">Перейти в список тикетов</button></div>
        </div>';
    }

    public function dateFull($dateStr, $splitter=' в ', $tz=null) {
            if (empty($dateStr) or '0000-00-00 00:00:00'==$dateStr)
            {return '...';}

            $zone   = (null===$tz)? intval($_SESSION['timeshift']) : $tz;
            $ts     = strtotime($dateStr) + $zone;
            $nowTs  = time() - (int)date('Z') + $zone;

            $res = date('j',$ts).' '.$this->month3_lower(date('m',$ts));
            if ($y=date('Y',$ts) and $y!=date('Y',$nowTs)){$res.=' '.$y;}
            return $res.$splitter.date('H:i',$ts);
    }
    
    private function month3_lower($monthNum)
    {
        $m='-';
        switch (intval($monthNum))
        {
        case 1:  $m='янв';break;
        case 2:  $m='фев';break;
        case 3:  $m='мар';break;
        case 4:  $m='апр';break;
        case 5:  $m='мая';break;
        case 6:  $m='июн';break;
        case 7:  $m='июл';break;
        case 8:  $m='авг';break;
        case 9:  $m='сен';break;
        case 10: $m='окт';break;
        case 11: $m='ноя';break;
        case 12: $m='дек';break;
        }
        return $m;
    }

}