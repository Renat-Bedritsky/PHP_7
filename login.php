<?php

$dir = $_GET['dir'] ?? '.\\';   // Если 'local' существует, то принимает 'local', иначе \
$dir = realpath($dir);             // Абсолютный путь к файлу
chdir($dir);                       // Изменяет текущий каталог на указанный

$iniArr = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/admin/config.ini');

if (isset($_POST['login']) && isset($_POST['password'])) {
    if (strlen($_POST['login']) <= 2 || 
        strlen($_POST['password']) <= 5) {
        echo '<p>Check form data</p>';
    }
    else {
        if ($_POST['login'] == $iniArr['login'] && 
            $_POST['password'] == $iniArr['password']) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $login = password_hash($_POST['login'], PASSWORD_DEFAULT);
            setcookie('login', $login, time()+1800);
            setcookie('password', $password, time()+1800);
            header("location: /admin/?dir=$dir");
        }
        else echo '<p>Check form data</p>';
    }
}

?>

<form class="enter" style="margin: 10px 0 0 10px;" method="POST">

<input type="text" name="login" style="margin-bottom: 10px;"> Логин<br>
<input type="password" name="password" style="margin-bottom: 10px;"> Пароль<br>
<button>Войти</button>

</form>