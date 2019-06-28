$(() => {
    
    // Login controls
    validateForm();

    // Кнопка логина
    $('#container').on("click","#login-btn", (e) => {
        e.preventDefault();
        var formData = [];
        formData.username = $('#login-username').val();
        formData.password = $('#login-password').val(); 
        
        $.ajax({
        type: 'post',
        url: './controller/login.php',
        data: {
                'username':formData.username,
                'password':formData.password,
              },
        success: function(results) {
            if(results.status == "success")
            {
                $('#container').load("./controller/ticket.php");
            }
            else if (results.status == "fail") {
                alert('Неверное имя пользователя или пароль');
            }
        },
        error: function() {
            alert('Login error');
        }
        });
        e.stopPropagation();
    });

    // Управление страницей просмотра списка тикетов
    
    var current_page=1;
    var ticket_id;

    // Запрос обновления информации на странице
    mainReload = () => {
        $.ajax({
            type: 'get',
            url: './controller/ticket.php',
            data: {
                'current_page': current_page,
            },
            success: results => {
                $('#container').html(results);
            },
            error: () => {
                alert('Load error');
            }
        });
    };

    // Кнопка - "+Новый тикет"
    $('#container').on("click","#ticket-new-btn", (e) => {
        e.preventDefault();
        $.ajax({
            type: 'get',
            url: './controller/ticket_new.php',
            success: results => {
                $('#container').html(results);
                validateForm();
            },
            error: () => {
                alert('Load error');
            }
        });
    });

    // Кнопка "Просмотр"
    $('#container').on("click","#ticket-view-btn", (e) => {
        e.preventDefault();
        ticket_id = $(e.target).data("ticket-id")
        detailsReload();
    });

    // Отправка формы нового сообщения
    $('#container').on("click","#message-submit-btn", (e) => {
        e.preventDefault();

        var formData = new FormData();
        
        formData.append('ticket_id',$('#message-ticket-id-field').val());      
        formData.append('status_id', $('#message-status-id-field').val());
        formData.append('type_id', $('#message-type-id-field').val());
        formData.append('user_id',$('#message-user-id-field').val());
        formData.append('text', $('#message-text-field').val());
        formData.append('file', $('#message-file-field').prop('files')[0]);
        
        $.ajax({
        method: 'post',
        url: './controller/message_add.php',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        success: function(results) {
            detailsReload();
        },
        error: function() {
            alert('Login error');
        }
        });
    });

    // Изменение статуса и типа тикета
    $('#container').on("change","#message-status-field, #message-type-field", (e) => {
        e.preventDefault();
        
        $('option:selected', $(e.target)).removeAttr('selected');

        var formData = new FormData();
        
        formData.append('ticket_id',$('#message-ticket-id-field').val());      
        formData.append('user_id',$('#message-user-id-field').val());
        formData.append('status_id', $('#message-status-field').val());
        formData.append('type_id', $('#message-type-field').val());
        
        $.ajax({
        method: 'post',
        url: './controller/status_type_update.php',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        success: function(results) {
            detailsReload();
        },
        error: function() {
            alert('Login error');
        }
        });
    });

    // Запрос обновления подробной информации о тикете
    detailsReload = () => {
        $.ajax({
            type: 'post',
            url: './controller/ticket_view.php',
            data: {
                'ticket_id': ticket_id,
            },
            success: results => {
                $('#container').html(results);
                validateForm();
            },
            error: () => {
                alert('Load error');
            }
        });
    }

    //New ticket controls

    $('#container').on("click","#new-ticket-submit-btn", (e) => {
        e.preventDefault();
        var formData = new FormData();
              
        formData.append('user_id',$('#user-id-field').val());
        formData.append('type_id', $('#type-field').val());
        formData.append('topic', $('#topic-field').val()); 
        formData.append('text', $('#text-field').val());
        formData.append('link', $('#link-field').val());
        formData.append('file', $('#file-field').prop('files')[0]);
        
        $.ajax({
        method: 'post',
        url: './controller/ticket_add.php',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        success: function(results) {
            results = JSON.parse(results);
            if(results.status === "success")
            {
                $('#container').html(success_message_start+results.ticket_id+success_message_end+return_button);
            }
        },
        error: function() {
            alert('Login error');
        }
        });
    });

    var success_message_start = '<div class="text-center"><h3 class="text-center">Ваша заявка принята</h3><h5>Ей назначен номер ';
    var success_message_end = ' . Скоро мы вам ответим</h5>';
    var return_button = '<button class="btn btn-primary mt-4" id="back-to-list-btn">Перейти в список тикетов</button></div>';
    
    // Кнопка "Статистика"
    $('#container').on("click","#show-stats-btn", (e) => {
        e.preventDefault();
        
        $.ajax({
        method: 'get',
        url: './controller/stats.php',
        success: function(results) {
            $('#container').html(results);
        },
        error: function() {
            alert('Ошибка получения статистики');
        }
        });
    });

    // Кнопка "Назад к списку тикетов и отмена"
    $('#container').on("click","#back-to-list-btn, #new-ticket-cancel-btn", (e) => {
        e.preventDefault();
        mainReload();
    });

    
    // Управление пагитацией

    $('#container').on("click", "#page-prev", (e) => {
        if(current_page>1)
        {
            current_page--;
            mainReload();  
        }
     })
     $('#container').on("click", "#page-next", (e) => {
        var last_page = $('.page-link').length - 2;
        if(current_page<last_page)
        {
            current_page++;
            mainReload();
        }  
     })

    $('#container').on("click", "#page-link", (e) => {
            current_page = $(e.target).data("page");
            mainReload();  
    })

    // Валидация форм

    function validateForm() {
          let form = $('.needs-validation');
        $(form).click(function(e){
    
            if(this.checkValidity() == false) {
                $(this).addClass('was-validated');
                e.preventDefault();
                e.stopPropagation();
            }
    
        });
        
        $(':input').blur(function(){
            let fieldType = this.type;
    
            switch(fieldType){
                case 'text': 
                case 'password':
                case 'textarea':
                    validateText($(this));
                    break;
                case 'email':
                    validateEmail($(this));
                    break;
                case 'checkbox':
                    validateCheckBox($(this));
                    break;
                case 'select-one':
                    validateSelectOne($(this));
                    break;
                case 'select-multiple':
                    validateSelectMultiple($(this));
                    break;
                default:
                    break;
            }
        });
    
        $(':input').click(function(){
            $(this).removeClass('is-valid is-invalid');
        });
    
        $(':input').keydown(function(){
            $(this).removeClass('is-valid is-invalid');
        });
        $(':reset').click(function(){
            $(':input, :checked').removeClass('is-valid is-invalid');
            $(form).removeClass('was-validated');
        });
    
    };
        function validateText(thisObj) {
            let fieldValue = thisObj.val();
            if(fieldValue.length > 1) {
                $(thisObj).addClass('is-valid');
            } else {
                $(thisObj).addClass('is-invalid');
            }
        }

        function validateEmail(thisObj) {
            let fieldValue = thisObj.val();
            let pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
    
            if(pattern.test(fieldValue)) {
                $(thisObj).addClass('is-valid');
            } else {
                $(thisObj).addClass('is-invalid');
            }
        }
    
        function validateCheckBox(thisObj) {
             
            if($(':checkbox:checked').length > 0) {
                $(thisObj).addClass('is-valid');
            } else {
                $(thisObj).addClass('is-invalid');
            }
        }

        function validateSelectOne(thisObj) {
    
            let fieldValue = thisObj.val();
            
            if(fieldValue != null) {
                $(thisObj).addClass('is-valid');
            } else {
                $(thisObj).addClass('is-invalid');
            }
        }
    
        function validateSelectMultiple(thisObj) {
    
            let fieldValue = thisObj.val();
            
            if(fieldValue.length > 0) {
                $(thisObj).addClass('is-valid');
            } else {
                $(thisObj).addClass('is-invalid');
            }
        }

});