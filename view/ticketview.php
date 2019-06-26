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

    private $number_of_pages;
    private $number_of_tickets_on_page = 4;
    
    

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