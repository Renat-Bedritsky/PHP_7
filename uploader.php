<?php

$dir = $_POST['local'] ?? '.\\';   // Если 'local' существует, то принимает 'local', иначе \
$dir = realpath($dir);             // Абсолютный путь к файлу
chdir($dir);                       // Изменяет текущий каталог на указанный



if (isset($_FILES['files']['tmp_name'])) header("location: /admin/?dir=$dir");   // Если файла нет, то возвращает обратно



$destPath = $_SERVER['DOCUMENT_ROOT'].'/admin/uploads';   // uploads - папка загрузки
if (!file_exists($destPath)) {                            // Если нет папки uploads
    mkdir($_SERVER['DOCUMENT_ROOT'].'/admin/uploads');    // Создаётся папка uploads
}

$allFiles = scandir($destPath);

foreach($_FILES['files']['tmp_name'] as $index => $path) {   // Цикл для загрузки изображений
    if (file_exists($path)) {

    $fileInfo = pathinfo($_FILES['files']['name'][$index]);

    function task_8($a, $b, $c, $d, $e) {
        $newName = '';
        $arr = ['а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo','ж'=>'j','з'=>'z','и'=>'i','й'=>'i','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'ii','ь'=>'`','э'=>'e','ю'=>'iu','я'=>'ya', '-' => '-', '_' => '_', '0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9'];
        $arStr = preg_split('//u', $a);
        foreach($arStr as $a) {
            if (!isset($newName)) $newName = $arr[$a];
            else if (isset($newName)) $newName .= $arr[$a];
        }

        $findFiles = preg_grep("/^" . $newName . "(.+)?\." . $b . "$/", $c);
    
        $filename = $newName . (count( $findFiles) > 0 ? '_'  . (count( $findFiles) + 1) : '') . '.' . $b;
        move_uploaded_file($d, $e . '/' . $filename);
        }
    }

    task_8($fileInfo['filename'], $fileInfo['extension'], $allFiles, $path, $destPath);
}

header("location: /admin/?dir=$dir");

?>