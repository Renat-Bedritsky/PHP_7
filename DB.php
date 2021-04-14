<?php

class DB
{

    private static $instances = [];    

    protected function __construct() { }

    protected function __clone() { }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(): DB
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }


    public function add($props = []){

        if(empty($props)){
            return false;
        }

        foreach($props as $fieldCode => &$fieldValue){
            if(!in_array($fieldCode, $this->fields)){
                unset($fieldValue);
            }
        }

        //array_diff()

        $this->data[] = $props;

    }

    final function del($id){

        unset($this->data[$id]);

    }

    public function update($id, $props = []){

        if(!isset($this->data[$id]) && empty($props)){
            return false;
        }

        foreach($props as $fieldCode => &$fieldValue){
            if(!in_array($fieldCode, $this->fields)){
                unset($fieldValue);
            }
        }

        $this->data[$id] = $props;

    }

    /**
     * сортировка ["ключ поля" => asc|desc, "ключ поля" => asc|desc ]
     */
    public function getList($arFilter = [], $arSelect = [], $arSort = []){

        $arRes[] = array_filter($this->data, function($item)use($arFilter){
                foreach($arFilter as $fieldCode => $fieldValue){
                    if($item[$fieldCode] != $fieldValue){
                        return false;
                    }
                }
                return true;
            });
        
            // select, sort

            return $arRes;
    }

}