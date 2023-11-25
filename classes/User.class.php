<?php
//User.class.php

require_once 'DB.class.php';
require_once 'Tools.class.php';

/**
 * Класс пользователя системы
 */
class User
{
    public $id;
    public $username;
    public $hashedPassword;
    public $joinDate;
    public $f;
    public $i;
    public $o;
    public $group_id;
    public $admin;
    public $birthday;
    public $phone;
    public $email;
    public $delo;
    public $token;
    public $token2;
    public $state;

    function __construct($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : "";
        $this->username = (isset($data['username'])) ? $data['username'] : "";
        $this->hashedPassword = (isset($data['password'])) ? $data['password'] : "";
        $this->email = (isset($data['email'])) ? $data['email'] : "";
        $this->joinDate = (isset($data['join_date'])) ? $data['join_date'] : "";
        $this->f = (isset($data['f'])) ? $data['f'] : "";
        $this->i = (isset($data['i'])) ? $data['i'] : "";
        $this->o = (isset($data['o'])) ? $data['o'] : "";
        $this->group_id = (isset($data['group_id'])) ? $data['group_id'] : "";
        $this->admin = (isset($data['admin'])) ? $data['admin'] : "";
        $this->phone = (isset($data['phone'])) ? $data['phone'] : "";
        $this->birthday = (isset($data['birthday'])) ? $data['birthday'] : "";
        $this->delo = (isset($data['delo'])) ? $data['delo'] : "";
        $this->token = (isset($data['token'])) ? $data['token'] : "";
        $this->token2 = (isset($data['token2'])) ? $data['token2'] : "";
        $this->state = (isset($data['state'])) ? $data['state'] : "";
    }

    /**
     * Вернуть ФИО пользователя
     * @param $rid По умолчанию true - вернуть с ID в скобках перед ФИО. Иначе - вернуть ФИО без ID.
     * @return string Отформатированное ФИО пользователя
     */
    public function fio($rid = true){
        if($rid) return '('. $this->id . ') ' . $this->f . ' ' . $this->i . ' ' . $this->o;
        else return $this->f . ' ' . $this->i . ' ' . $this->o;
    }

    public function save($isNewUser = false)
    {
        $tool = new Tools();
        $db = new DB();
        if (!$isNewUser) {
            $data = array(
                "username" => "'$this->username'",
                "password" => "'$this->hashedPassword'",
                "email" => "'$this->email'",
                "f" => "'$this->f'",
                "i" => "'$this->i'",
                "o" => "'$this->o'",
                "group_id" => "'$this->group_id'",
                "admin" => "'$this->admin'",
                "token" => "'$this->token'",
                "token2" => "'$this->token2'",
                "state" => "'$this->state'"
            );

            $db->update($data, 'users', 'id = ' . $this->id);
        } else {
            date_default_timezone_set("GMT");
            $data = array(
                "username" => "'$this->username'",
                "password" => "'$this->hashedPassword'",
                "email" => "'$this->email'",
                "join_date" => "'" . date("Y-m-d H:i:s", time()) . "'",
                "f" => "'$this->f'",
                "i" => "'$this->i'",
                "o" => "'$this->o'",
                "group_id" => "'$this->group_id'",
                "admin" => "'0'",
                "token" => "'".bin2hex(random_bytes(16))."'",
                "token2" => "'".bin2hex(random_bytes(16))."'",
                "state" => "'1'"
            );
            $this->id = $db->insert($data, 'users');
            $this->joinDate = time();
            date_default_timezone_set($tool->getGlobal('tz'));
        }
        return true;
    }
}

?>