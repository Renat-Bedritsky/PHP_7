<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/admin/style.css">
    <link rel="stylesheet" href="/admin/media.css">
</head>
<body>





<?php

// Функция для удаления папки (директория, объект)

function delDir($dir, $way) {
    $files = array_diff(scandir($way), ['.','..']);                                                    // Возвращает массив, полученный от $dir, кроме '.' и '..'
    foreach ($files as $file) {
        (is_dir($way.'/'.$file)) ? delDir($way.'/'.$file, $way.'\\'.$file) : unlink($way.'/'.$file);   // Удаление вложенных файлов и папок
    }
    rmdir($way);                                                                                       // Удаление директории
    header("location: /admin/?dir=$dir");                                                              // Выполняется перевод на текущую директорию
}



// Получение директории

$dir = $_GET['dir'] ?? '.\\';   // Если 'dir' существует, то принимает 'dir', иначе \
$dir = realpath($dir);          // Абсолютный путь к файлу
chdir($dir);                    // Изменяет текущий каталог на указанный
$curDir = getcwd();             // Получает имя текущего каталога
$arHere = scandir($curDir);     // Получает список файлов и каталогов, расположенных по указанному пути



// Запрет на index.php

if (preg_match('/\/explorer\.php$/', $_SERVER['PHP_SELF']) == 1) {   // Если в адресной строке есть explorer.php
    header('location: /admin/index.php');                            // Выполняется перевод на index.php
}



// Форма для переименования

if (isset($_GET['rename'])) {                                                            // Если GET 'rename' существует
    $info = pathinfo($dir.'\\'.$_GET['rename']);
    echo '<style>.formNewName{display:block;}</style>';                                  // Выводит форму
?>

<form method="POST" class="formNewName">
    <input type="text" name="rename" value="<?= $info['filename'] ?>">
    <button>Ok</button>
</form>

<?php
    if (isset($_POST['rename'])) {   // Если POST 'rename' существует

        // foreach ($arHere as $inside) {
        //     if (($_POST['rename'].'.'.$info['extension']) == $inside) header("location: /admin/?dir=$dir"); 
        // }

        $checkNewName = '/^[a-z0-9 _-]{1,}$/i';
        if (preg_match($checkNewName, $_POST['rename'])) {                                // Если проходит проверку   TODO
            if (isset($info['extension'])) {
            rename($_GET['rename'], $dir.'\\'.$_POST['rename'].'.'.$info['extension']);   // Переименование файла (старое имя, новое имя)
            }
            else rename($_GET['rename'], $dir.'\\'.$_POST['rename']);                     // Переименование папки (старое имя, новое имя)
        }
        header("location: /admin/?dir=$dir");                                             // Выполняется перевод на текущую директорию
    }
}



// Форма для удаления папки/файла

if (isset($_GET['delete'])) {
    echo '<style>.formDelete{display:block;}</style>';   // Выводит форму
?>

<div class="formDelete" style="display: flex;">
    Удалить? 
    <form method="POST">
        <input type="hidden" name="deleteYes">
        <button>Да</button>
    </form> 
    <form method="POST">
        <input type="hidden" name="deleteNo">
        <button>Нет</button>
    </form>
</div>

<?php
    $way = $dir.'\\'.$_GET['delete'];                     // Присваивание пути к папке
    if (isset($_POST['deleteYes'])) {
        if ($_GET['type'] == 'dir') delDir($dir, $way);   // Вызов функции (директория, объект)
        else {
            unlink($_GET['delete']);                      // Удаление файла
            header("location: /admin/?dir=$dir");         // Выполняется перевод на текущую директорию
        }
    }
    else if (isset($_POST['deleteNo'])) {
        header("location: /admin/?dir=$dir");             // Выполняется перевод на текущую директорию
    }
}



// Скрипт для переименования

if(isset($_POST['type']) && isset($_POST['newWay'])) {
    $newWay = $_POST['newWay'];
    if (preg_match('/^[a-z0-9 _-]{1,}$/i', $newWay)) {                                  // Если проходит проверку   TODO
        $newWay = $dir.'\\'.$newWay;                                                    // Присваивание пути
        $type = $_POST['type'];
        if ($type == 'dir') {
            $i = 2;
            $empty = $newWay;
            while (file_exists($newWay)) {                                               // Если существует
                $newWay = $empty;                                                        // Убрать цифру
                $newWay = $newWay.'_'.$i;                                                // Добавить цифру
                $i++;
            }
            mkdir($newWay);
            header("location: /admin/?dir=$dir");
        }
        else if ($type == 'file' && isset($_POST['format'])) {
            if ($_POST['format'] == 'txt') $newWay .= '.txt';
            else if ($_POST['format'] == 'html') $newWay .= '.html';
            else if ($_POST['format'] == 'css') $newWay .= '.css';
            else if ($_POST['format'] == 'js') $newWay .= '.js';
            else if ($_POST['format'] == 'php') $newWay .= '.php';
            $i = 2;
            $empty = $newWay;
            while (file_exists($newWay)) {                                                // Если существует
                $newWay = $empty;                                                         // Убрать цифру
                $index = strripos($newWay, '.');                                          // Имя файла до '.'
                if ($index !== false) {
                    $newWay = substr($newWay, 0, $index)."_$i".substr($newWay, $index);   // Имя файла, порядковый номер, расширение
                }
                else $newWay .= "_$i";
                $i++;
            }
            $fb = fopen($newWay, "w");                                                     // Открывает файл. W - только для записи
            fclose($fb);
            header("location: /admin/?dir=$dir");
        }
    }
}



// Функция для определения размера
            
function getFilesSize($path){
    $fileSize = 0;
    $dir = scandir($path);

    foreach($dir as $file)
    {
        if (($file!='.') && ($file!='..'))
            if(is_dir($path . '/' . $file))
                $fileSize += getFilesSize($path.'/'.$file);
            else
                $fileSize += filesize($path . '/' . $file);
    }
    return $fileSize;
}





// Открывает html файлы

if (isset($_GET['open'])) {                                                                 // Если существует $_GET['open]
    echo '<style>body{backgroun-color: #fff !important;} .window{display:none;}</style>';   // Скрывает сраницу
    $open = $_GET['open'];
    $content = file_get_contents($dir.'/'.$open);                                           // file_get_contents читает содержимое файла
    echo $content;
}



// Форма для редактирования

if (isset($_GET['edit'])) {
    $edit = $_GET['edit'];
    $content = file_get_contents($dir.'/'.$edit);                                    // file_get_contents читает содержимое файла

    echo '<style>.window{display:none;}.formEdit{display:block;}</style>';           // Выводит форму
?>

<form method='POST' class='formEdit'>
    <textarea name='content'><?= htmlspecialchars_decode($content); ?></textarea>
    <span class='editButton'>
        <button name='edit' type='submit' value='editYes'>Сохранить</button> 
        <button name='edit' type='submit' value='editNo'>Отмена</button>
    </span>
</form>
<?php echo $_SERVER['DOCUMENT_ROOT']; ?>
<?php
    if (isset($_POST['edit'])) {
        if ($_POST['edit'] == 'editYes') {                                            // Если пользователь нажал кнопку "Сохранить"
            $codeUTF8 = mb_convert_encoding($_POST['content'], "UTF-8");    // Перекодировка в UTF-8, "EUC-JP"
            file_put_contents($dir.'/'.$edit, $codeUTF8);                             // file_put_contents записывает содержимое textarea в файл (путь к файлу, путь к textarea)
            header("location: /admin/?dir=$dir");                                     // Выполняется перевод на текущую директорию
        }
        else if ($_POST['edit'] == 'editNo') {header("location: ".$_SERVER['DOCUMENT_ROOT']."/admin/?dir=$dir");   // Выполняется перевод на текущую директорию
        }
    }
}?>





<div class="window">



<div class="wrapper">

<!-- Переводит на скрипт с авторизацией -->

<?php if (isset($_GET['enter'])) header('location: /admin/login.php'); ?>

<a href="/admin/?dir=<?= $dir ?>&enter">Войти</a>



<!-- Скрипт для выхода из аккаунта -->

<?php 
if (!empty($_COOKIE['login'])) {
    echo '<style>.loginExit{display:block;}</style>';          // Выводит форму 
?>
    
<form class="loginExit" method="POST">
    <button name="loginExit">Выйти</button>
</form>

<?php if (isset($_POST['loginExit'])) header('location: ./logout.php'); } 



// Отображает какой пользователь авторизован
if (isset($_COOKIE['login'])) {
    $iniArr = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/admin/config.ini'); 
    echo $iniArr['login'];
} ?>



</div>



<!-- Формирование списка -->

<?php foreach ($arHere as $path) {
    if ($path == '.') continue;
    $checkDir = '/xampp\\\htdocs\\\admin$/';
    if ($path == '..') { 
        if (preg_match($checkDir, $dir)) {
            echo "<p style='margin: 10px 0 0 340px;'>$dir</p>"; continue;
        } ?>   <!-- запрещает подниматься выше admin -->



        <p class="back"><a href="/admin/index.php/?dir=<?= $dir.'\\'.$path; ?>">Назад</a><?php echo "<span style='margin-left:300px;'>$dir</span>"; ?><p>



    <?php 
    } 
    else { ?>



        <table class="table">   <!-- Начало таблицы -->



        <?php

        if (preg_match($checkDir, $dir)) {
            // Скрывает папки и файлы, когда пользователь не авторизирован
            if (isset($_COOKIE['login'])) {
                $iniArr = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/admin/config.ini'); 
                if ($_COOKIE['login'] != $iniArr['login']) {
                    if ($path == 'style.css' || $path == 'explorer.php' || $path == 'media.css' || $path == 'uploader.php' || $path == 'config.ini' || $path == 'login.php' || $path == 'logout.php') {
                        continue;
                    }
                }
            }
            else {
                if ($path == 'index.php' || $path == 'style.css' || $path == 'explorer.php' || $path == 'media.css' || $path == 'uploader.php' || $path == 'config.ini' || $path == 'login.php' || $path == 'logout.php') {
                    continue;
                }
            }
        }



        if (is_dir($path)) {
            ?>



            <tr>
                <td>
                    <a href="/admin/?dir=<?= $dir.'\\'.$path; ?>"><?= $path; ?></a>   <!-- Формируется список папок -->
                </td>



            <?php

            if (getFilesSize($path) <= 1024) {
                echo '<td><span>'.getFilesSize($path).' байт</span></td>';                       // Функция для определения размера папки в байтах
            }
            else if (getFilesSize($path) <= (1024*1024)) {
                echo '<td><span>'. round(getFilesSize($path)/1024) .' Кбайт</span></td>';        // Функция для определения размера папки в Кбайтах
            }
            else {
                echo '<td><span>'. round(getFilesSize($path)/1024/1024) .' Мбайт</span></td>';   // Функция для определения размера папки в Мбайтах
            } ?>



                <td></td>   <!-- Это нужно -->
                <td></td>

                <td>
                    <span class="delete"><a href="/admin/?dir=<?= $dir ?>&delete=<?= $path; ?>&type=dir">Удалить<a>
                </td>
                <td>
                    <span class="rename"><a href="/admin/?dir=<?= $dir; ?>&rename=<?= $path; ?>">Переименовать</a></span>
                </td>
            </tr>



        <?php 
        } 
        else { ?>



            <tr>
                <td>
                    <span><?= $path; ?></span>   <!-- Формируется список файлов -->
                </td>



        <?php 

        if (filesize($path) <= 1024) {
            echo '<td>'.filesize($path).' байт</td>';                     // Размера файла в байтах
        }
        else if (filesize($path) <= (1024*1024)) {
            echo '<td>'. round(filesize($path)/1024) .' Кбайт</td>';      // Размера файла в килобайтах
        }
        else {
            echo '<td>'. round(filesize($path)/1024/1024) .' Мбайт</td>'; // Размера файла в мегабайтах
        } 


        if ($path =='index.php') {
            echo   '<td></td>
                    <td></td>
                    <td></td>
                    <td>Доступ запрещён</td>';
                    continue;
        }?>


        
        <?php 
        $pathHTML = pathinfo($path); 
        if ($pathHTML['extension'] == 'html') { ?>



                <td>
                    <span class="open"><a href="/admin/?dir=<?= $dir ?>&open=<?= $path; ?>" target="blank">Открыть</a></span>   <!-- Открывает html файлы -->
                </td>



        <?php } else echo '<td></td>' ?>



                <td>
                    <span class="edit"><a href="/admin/?dir=<?= $dir; ?>&edit=<?= $path; ?>">Редактировать</a></span>
                </td>
            
                <td>
                    <span class="delete"><a href="/admin/?dir=<?= $dir ?>&delete=<?= $path; ?>&type=file">Удалить</a></span>
                </td>

                <td>
                    <span class="rename"><a href="/admin/?dir=<?= $dir; ?>&rename=<?= $path; ?>">Переименовать</a></span>
                </td>
            </tr>



        <?php 
        }
    }
}

if (!isset($arHere[2])) echo 'Пусто'; ?>



</table>   <!-- Конец таблицы -->



<p class="root"><a href="/admin/">Корень</a></p>

<hr>

<form method="POST" class="create">   <!-- Форма для создания нового объекта -->
Новый файл <input type="text" name="newWay" placeholder="Имя">
Файл <input type="radio" name="type" value="file" id="file">
<label for="file">
    <select name="format">
        <option value="txt">txt</option>
        <option value="html">html</option>
        <option value="css">css</option>
        <option value="js">js</option>
        <option value="php">php</option>
    </select>
</label>
Папка<input type="radio" name="type" value="dir">
<button>Создать</button>
</form>

<hr>

<form action="/admin/uploader.php" class="loadFile" method="POST" enctype="multipart/form-data">   <!-- Форма для загрузки изображений -->
    Загрузить файл на сервер
    <input type="hidden" name="local" value="<?= $dir ?>">
    <p><input type="file" multiple name="files[]"></p>
    <button>Отправить</button>
</form>

</div>

</body>
</html>