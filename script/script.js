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
    //var ticket_id;

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
        console.log("11");
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