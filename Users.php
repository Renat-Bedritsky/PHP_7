<?php

class Users extends DB{

    protected $file = "C:/xampp/htdocs/dataBase/users.json",
        $data = [];

    public $fields = [
        "login", 
        "password"
        // "name",
        // "email",
        // "groupId"
    ];

    public function __construct()
    {
        if(file_exists($this->file)){
            $this->data = json_decode(file_get_contents($this->file), true);
        }
    }

    // public function __destruct()
    // {
    //     $json = json_encode($this->data);
    //     file_put_contents($this->file, $json);
    // }

     /** переопределить метод add
     * должен возвращать id созданного пользователя
     * для создания пользователя обязательные поля(login, password)
     * password хешируем
     * если полей нет возвращаем false
     * если не указано name, то name=login
     * :parent
     */

    public function autorization($login, $password) {
        if (!$login || !$password) {
            return false;
        }
        $user = $this->getList(['login' => $login, 'password' => md5($password)]);
        if (empty($user)) {
            return false;
        }
        else return true;
    }

    function checkByCookie($password) {
        
        // $file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/admin/users.json', true);
        // $data = json_decode($file);
        // $pathLog = 0;
        // global $pathLog;
        // $data = (array)$data;
        // $data[0] = (array)$data[0];
        // foreach ($data as $pathF) {
        //     $pathF = (array)$pathF;
        //     if ($login == md5($pathF['login'])) {
        //         if ($login != md5($data[0]['login'])) {
        //             $pathLog = 1;
        //         }
        //         else if ($login == md5($data[0]['login'])) {
        //             $pathLog = 2;
        //         }
        //     }
        // }
        // return $pathLog;

        $checkCookie = $this->getList('password' => $password]);
        if ($checkCookie) return true;

    }
}
