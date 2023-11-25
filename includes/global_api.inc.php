<?php
require_once 'classes/User.class.php';
require_once 'classes/UserTools.class.php';
require_once 'classes/DB.class.php';
require_once 'classes/Tools.class.php';
require 'vendor/autoload.php';

$pname = 'МБОУ "ИТ-лицей №24"';
//connect to the database
$db = new DB();
$db->connect();

//initialize UserTools object
$userTools = new UserTools();
//start the session

$tool = new Tools();
date_default_timezone_set($tool->getGlobal('tz'));

session_start();
session_regenerate_id();

//refresh session variables if logged in
if (isset($_SESSION['logged_in'])) {
    $user = unserialize($_SESSION['user']);
    $_SESSION['user'] = serialize($userTools->get($user->id));
}
?>