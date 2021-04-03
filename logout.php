<?php

setcookie('login', $_POST['login'], time()-10);         // Удаление COOKIE логин
setcookie('password', $_POST['password'], time()-10);   // Удаление COOKIE пароль
header("location: /admin/index.php/?dir=$dir");                   // Выполняется перевод на текущую директорию

?>
    