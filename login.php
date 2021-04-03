<?php 

$dir = $_GET['dir'] ?? '.\\';      // Передаёт директорию
$dir = realpath($dir);             // Абсолютный путь к файлу
chdir($dir);                       // Изменяет текущий каталог на указанный

$valueLog = 'false';
global $valueLog;

if (isset($_POST['login']) && isset($_POST['password'])) {
    if (strlen($_POST['login']) <= 2 || 
        strlen($_POST['password']) <= 5) {
        echo '<p>Check form data</p>';
    }
    // else {
    //     $file = file_get_contents('./users.json', true);
    //     $data = json_decode($file);
    //     foreach ($data as $path) {
    //         $path = (array)$path;
    //         echo '<pre>';
    //         print_r($path);
    //         echo '</pre>';
    //         echo $path['login'];
    //         foreach ($path as $key => $value) {
    //             if ($_POST['login'] == $value || $valueLog == 'true') {
    //                 $valueLog = 'true';
    //                 if (md5($_POST['password']) == $value) {
    //                     // $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    //                     $login = md5($_POST['login']);
    //                     setcookie('login', $login, time()+1800);
    //                     // setcookie('password', $password, time()+1800);
    //                     // header("location: /admin/?dir=$dir");
    //                 }
    //             }
    //         }
    //         if ($valueLog == 'true') break;
    //     }
    // }
    else {
        $file = file_get_contents('./users.json', true);
        $data = json_decode($file);
        $data = (array)$data;
        foreach ($data as $path) {
            $path = (array)$path;
            $data[0] = (array)$data[0];
            // if ($data[0] != $path) echo 'Yes';
            // else echo 'No';
            foreach ($path as $key => $value) {
                if ($_POST['login'] == $value || $valueLog == 'true') {
                    $valueLog = 'true';
                    if (md5($_POST['password']) == $value) {
                        // $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $login = md5($_POST['login']);
                        setcookie('login', $login, time()+1800);
                        // setcookie('password', $password, time()+1800);
                        header("location: /admin/?dir=$dir");
                    }
                }
            }
            if ($valueLog == 'true') break;
        }
    }
}

if (isset($_POST['exit'])) header("location: /admin/?dir=$dir");

?>

<form class="enter" style="margin: 10px 0 0 10px;" method="POST">

    <input type="text" name="login" style="margin-bottom: 10px;"> Логин<br>
    <input type="password" name="password" style="margin-bottom: 10px;"> Пароль<br>
    <button>Войти</button>
    <button name="exit">Назад</button>

</form>