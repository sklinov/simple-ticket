<?php
class LoginView {
    public $username;
    public $password;
    public $role_id;
    public $role_name;

    public function showLogin() {
        echo '
        <div class="text-center">
            <form class="form-signin">
                <img class="mb-4" src="./img/service.png" alt="" width="72" height="72">
                <h1 class="h3 mb-3 font-weight-normal">Вход</h1>
                <label for="login-username" class="sr-only">Имя пользователя</label>
                <input type="username" id="login-username" class="form-control mt-3" placeholder="Имя пользователя" required autofocus>
                <label for="login-password" class="sr-only">Password</label>
                <input type="password" id="login-password" class="form-control mt-3" placeholder="Password" required>
                
                <button class="btn btn-lg btn-primary btn-block mt-3" id="login-btn" type="submit">Войти</button>
                <p class="mt-5 mb-3 text-muted">&copy;2019</p>
            </form>
        </div>
        
        ';
    }
}