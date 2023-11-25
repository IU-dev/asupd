<?php
//DB.class.php

/**
 * Сlass DB
 * Используется для взаимодействия с базой данных
 * На текущей версии вызывается один раз для работы с основной БД.
 */
class DB
{

    /**
     * @var string
     * Имя базы данных MySQL/MariaDB
     */
    protected $db_name = 'admin_eis24db';
    /**
     * @var string
     * Имя пользователя для подключения к БД
     */
    protected $db_user = 'admin_eis24';
    /**
     * @var string
     * Пароль пользователя для подключения к БД
     */
    protected $db_pass = 'P@ssw0rdgim24';
    /**
     * @var string
     * IP-адрес MySQL/MariaDB сервера
     */
    protected $db_host = '192.168.13.39';

    // Открывает соединение к БД. Убедитесь, что
    // эта функция вызывается на каждой странице
    /**
     * @return true
     * Функця подключения к БД
     */
    public function connect()
    {
        $connection = mysqli_connect($this->db_host, $this->db_user, $this->db_pass);
        mysqli_select_db($connection, $this->db_name);
        return true;
    }

    public function connect_get()
    {
        $connection = mysqli_connect($this->db_host, $this->db_user, $this->db_pass);
        mysqli_select_db($connection, $this->db_name);
        return $connection;
    }

    // Берет ряд mysql и возвращает ассоциативный массив, в котором
    // названия колонок являются ключами массива. Если singleRow - true,
    // тогда выводится только один ряд
    public function processRowSet($rowSet, $singleRow = false)
    {
        $resultArray = array();
        while ($row = mysqli_fetch_assoc($rowSet)) {
            array_push($resultArray, $row);
        }
        if ($singleRow === true)
            return $resultArray[0];
        return $resultArray;
    }

    //Выбирает ряды из БД
    //Выводит полный ряд или ряды из $table используя $where
    public function select($table, $where)
    {
        $connection = $this->connect_get();
        $sql = "SELECT * FROM $table WHERE $where";
        $result = mysqli_query($connection, $sql);
        if (mysqli_num_rows($result) == 1)
            return $this->processRowSet($result, true);
        return $this->processRowSet($result);
    }

    public function get_global_set($global_name){
        $connection = $this->connect_get();
        $sql = "SELECT * FROM 'globals' WHERE named='".$global_name."'";
        $result = mysqli_query($connection, $sql);
        $res = $this->processRowSet($result, true);
        return $res['value'];
    }

    public function counter($table, $where)
    {
        $connection = $this->connect_get();
        $sql = "SELECT COUNT(*) as count FROM " . $table . " WHERE " . $where;
        $result = mysqli_query($connection, $sql);
        $prs = $this->processRowSet($result, true);
        return $prs['count'];
    }

    public function select_desc($table, $where)
    {
        $connection = $this->connect_get();
        $sql = "SELECT * FROM $table WHERE $where ORDER BY id DESC";
        $result = mysqli_query($connection, $sql);
        if (mysqli_num_rows($result) == 1)
            return $this->processRowSet($result, true);
        return $this->processRowSet($result);
    }

    public function select_fs($table, $where)
    {
        $connection = $this->connect_get();
        $sql = "SELECT * FROM $table WHERE $where";
        $result = mysqli_query($connection, $sql);
        return $this->processRowSet($result);
    }

    public function select_points_fs($table, $where)
    {
        $connection = $this->connect_get();
        $sql = "SELECT * FROM $table WHERE $where ORDER BY total DESC";
        $result = mysqli_query($connection, $sql);
        return $this->processRowSet($result);
    }

    public function select_desc_fs($table, $where)
    {
        $connection = $this->connect_get();
        $sql = "SELECT * FROM $table WHERE $where ORDER BY id DESC";
        $result = mysqli_query($connection, $sql);
        return $this->processRowSet($result);
    }

    public function select_desc_fs_news($table, $where)
    {
        $connection = $this->connect_get();
        $sql = "SELECT * FROM $table WHERE $where ORDER BY 'id' DESC, 'important' DESC";
        $result = mysqli_query($connection, $sql);
        return $this->processRowSet($result);
    }

    public function select_desc_fs_points($table, $where)
    {
        $connection = $this->connect_get();
        $sql = "SELECT * FROM $table WHERE $where ORDER BY points DESC";
        $result = mysqli_query($connection, $sql);
        return $this->processRowSet($result);
    }

    public function sum_points($participant)
    {
        $connection = $this->connect_get();
        $sql = "SELECT SUM(point) AS sum FROM `points` WHERE participant = '" . $participant . "'";
        $result = mysqli_query($connection, $sql);
        $final = $this->processRowSet($result, true);
        return $final['sum'];
    }

    public function delete($table, $where)
    {
        $connection = $this->connect_get();
        $sql = "DELETE FROM $table WHERE $where";
        $result = mysqli_query($connection, $sql);
        return true;
    }

    //Вносит изменения в БД
    public function update($data, $table, $where)
    {
        $connection = $this->connect_get();
        foreach ($data as $column => $value) {
            $sql = "UPDATE $table SET $column = $value WHERE $where";
            mysqli_query($connection, $sql) or die(mysqli_error($connection));
        }
        return true;
    }

    //Вставляет новый ряд в таблицу
    public function insert($data, $table)
    {
        $connection = $this->connect_get();
        $columns = "";
        $values = "";
        foreach ($data as $column => $value) {
            $columns .= ($columns == "") ? "" : ", ";
            $columns .= $column;
            $values .= ($values == "") ? "" : ", ";
            $values .= $value;
        }

        $sql = "insert into " . $table . " (" . $columns . ") values (" . $values . ")";
        mysqli_query($connection, $sql) or die(mysqli_error($connection));

        //Выводит ID пользователя в БД.
        return mysqli_insert_id($connection);
    }
}

?>