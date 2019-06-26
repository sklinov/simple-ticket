$(() => {
    // Login controls

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
            console.log(results.status);
        },
        error: function() {
            alert('Login error');
        }
        });
    });
    // Ticket view page controls
    var current_page=1;

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

    $('#container').on("click","#ticket-new-btn", (e) => {
        e.preventDefault();
        $.ajax({
            type: 'get',
            url: './controller/ticket_new.php',
            success: results => {
                $('#container').html(results);
            },
            error: () => {
                alert('Load error');
            }
        });
    });

    $('#container').on("click","#ticket-view-btn", (e) => {
        e.preventDefault();
        var ticket_id = $(e.target).data("ticket-id")
        console.log(ticket_id);
        $.ajax({
            type: 'post',
            url: './controller/ticket_view.php',
            data: {
                'ticket_id': ticket_id,
            },
            success: results => {
                $('#container').html(results);
            },
            error: () => {
                alert('Load error');
            }
        });
    });

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
        
        //var file_data = $('#file-field').prop('files')[0];
        // $.each($("#file-field").prop('files')[0], (i, file) => {
        //  formData.append('file-'+i, file);
        // });
        console.log(formData);
        $.ajax({
        method: 'post',
        url: './controller/ticket_add.php',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        success: function(results) {
            results = JSON.parse(results);
            console.log(results.status);
            console.log(results.ticket_id);
            if(results.status === "success")
            {
                $('#container').html(success_message_start+results.ticket_id+success_message_end+return_button);
            }
            console.log(results);
        },
        error: function() {
            alert('Login error');
        }
        });
    });

    var success_message_start = '<div class="text-center"><h3 class="text-center">Ваша заявка принята</h3><h5>Ей назначен номер ';
    var success_message_end = ' . Скоро мы вам ответим</h5>';
    var return_button = '<button class="btn btn-primary mt-4" id="back-to-list-btn">Перейти в список тикетов</button></div>';
    
    $('#container').on("click","#back-to-list-btn", (e) => {
        e.preventDefault();
        mainReload();
    });

    $('#container').on("click","#new-ticket-cancel-btn", (e) => {
        e.preventDefault();
        mainReload();
    });

    //Pagination controls

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

});