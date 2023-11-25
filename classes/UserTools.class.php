<?php

//UserTools.class.php
require_once 'User.class.php';
require_once 'DB.class.php';
require_once 'Tools.class.php';

class UserTools
{
    public function login($username, $password)
    {
        $tool = new Tools();
        $db = new DB();
        $connection = $db->connect_get();
        $hashedPassword = md5($password);
        $result = mysqli_query($connection, "SELECT * FROM users WHERE id = '$username' AND 
			password = '$hashedPassword'");
        if (mysqli_num_rows($result) == 1) {
            $data = mysqli_fetch_assoc($result);
            if ($data['active'] == '0') return 2;
            else {
                $_SESSION["user"] = serialize(new User($data));
                date_default_timezone_set("GMT");
                $_SESSION["login_time"] = time();
                date_default_timezone_set($tool->getGlobal('tz'));
                $_SESSION["logged_in"] = 1;
                $data2['token2'] = "'" . bin2hex(random_bytes(16)) . "'";
                $db->update($data2, 'users', "id = '" . $username . "'");
                return 1;
            }
        } else {
            return 0;
        }
    }

    public function logout()
    {
        $db = new DB();
        unset($_SESSION['user']);
        unset($_SESSION['login_time']);
        unset($_SESSION['logged_in']);
        session_destroy();
        header("Location: index.php");
    }

    public function checkUsernameExists($username)
    {
        $db = new DB();
        $connection = $db->connect_get();
        $result = mysqli_query($connection, "select id from users where username='" . $username . "'");
        if (mysqli_num_rows($result) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function get($id)
    {
        $db = new DB();
        $connection = $db->connect_get();
        $result = $db->select('users', "id = $id");
        return new User($result);
    }

    public function fio($id, $rid = true){
        $db = new DB();
        $connection = $db->connect_get();
        $result = $db->select('users', "id = $id");
        if ($rid) return '('.$result['id']. ') '. $result['f'].' '.$result['i'].' '.$result['o'];
        else return $result['f'].' '.$result['i'].' '.$result['o'];
    }

    public function get_name($id){
        $db = new DB();
        $connection = $db->connect_get();
        $result = $db->select('users', "id = $id");
        return $result['f'].' '.$result['i'].' '.$result['o'];
    }

    public function add_points($username, $who, $num, $comment)
    {
        $tool = new Tools();
        $db = new DB();
        $connection = $db->connect_get();
        $now_state = $db->select('users', "username = '" . $username . "'");
        $now_points = (float)$now_state['points'];
        $up_points = $now_points + (float)$num;
        $data['points'] = "'" . strval($up_points) . "'";
        $db->update($data, 'users', "username = '" . $username . "'");
        $data1['userid'] = "'" . $now_state['id'] . "'";
        date_default_timezone_set("GMT");
        $data1['datetime'] = "'" . date("Y-m-d H:i:s", time()) . "'";
        date_default_timezone_set($tool->getGlobal('tz'));
        $data1['ot'] = "'Система'";
        $data1['text'] = "'Пользователь " . $who . " зачислил на Ваш баланс " . number_format((float)$num, 2, '.', '') . " рублей (было " . number_format((float)$now_points, 2, '.', '') . ", стало " . number_format((float)$up_points, 2, '.', '') . "). Комментарий: <br><em>" . $comment . "</em>'";
        $db->insert($data1, 'logs');
        return true;
    }

    public function add_points_to_id($username, $who, $num, $comment)
    {
        $tool = new Tools();
        $db = new DB();
        $connection = $db->connect_get();
        $now_state = $db->select('users', "id = '" . $username . "'");
        $now_points = (float)$now_state['points'];
        $up_points = $now_points + (float)$num;
        $data['points'] = "'" . strval($up_points) . "'";
        $db->update($data, 'users', "id = '" . $username . "'");
        $data1['userid'] = "'" . $now_state['id'] . "'";
        date_default_timezone_set("GMT");
        $data1['datetime'] = "'" . date("Y-m-d H:i:s", time()) . "'";
        date_default_timezone_set($tool->getGlobal('tz'));
        $data1['ot'] = "'Система'";
        $data1['text'] = "'Пользователь " . $who . " зачислил на Ваш баланс " . number_format((float)$num, 2, '.', '') . " рублей (было " . number_format((float)$now_points, 2, '.', '') . ", стало " . number_format((float)$up_points, 2, '.', '') . "). Комментарий: <br><em>" . $comment . "</em>'";
        $db->insert($data1, 'logs');
        return true;
    }

    public function rem_points($username, $who, $num, $comment)
    {
        $db = new DB();
        $tool = new Tools();
        $connection = $db->connect_get();
        $now_state = $db->select('users', "username = '" . $username . "'");
        $now_points = (float)$now_state['points'];
        $up_points = $now_points - (float)$num;
        $data['points'] = "'" . strval($up_points) . "'";
        $db->update($data, 'users', "username = '" . $username . "'");
        $data1['userid'] = "'" . $now_state['id'] . "'";
        date_default_timezone_set("GMT");
        $data1['datetime'] = "'" . date("Y-m-d H:i:s", time()) . "'";
        date_default_timezone_set($tool->getGlobal('tz'));
        $data1['ot'] = "'Система'";
        $data1['text'] = "'Пользователь " . $who . " списал с Вашего баланса " . number_format((float)$num, 2, '.', '') . " рублей (было " . number_format((float)$now_points, 2, '.', '') . ", стало " . number_format((float)$up_points, 2, '.', '') . "). Комментарий: <br><em>" . $comment . "</em>'";
        $db->insert($data1, 'logs');
        return true;
    }

    public function notify($userid, $who, $comment)
    {
        $db = new DB();
        $tool = new Tools();
        $connection = $db->connect_get();
        $data1['userid'] = "'" . $userid . "'";
        date_default_timezone_set("GMT");
        $data1['datetime'] = "'" . date("Y-m-d H:i:s", time()) . "'";
        date_default_timezone_set($tool->getGlobal('tz'));
        $data1['ot'] = "'" . $who . "'";
        $data1['text'] = "'" . $comment . "'";
        $db->insert($data1, 'logs');
        return true;
    }
}

?>