<?php
require_once 'classes/User.class.php';
require_once 'classes/UserTools.class.php';
require_once 'classes/Prikaz.class.php';
require_once 'classes/DB.class.php';
require_once 'classes/Tools.class.php';
require_once 'includes/footer.inc.php';
require 'vendor/autoload.php';
$pname = 'МБОУ "ИТ-лицей №24"';
//connect to the database
$db = new DB();
$db->connect();

//initialize UserTools object
$userTools = new UserTools();
//start the session

$tool = new Tools();

session_start();
session_regenerate_id();

date_default_timezone_set($tool->getGlobal('tz'));

//refresh session variables if logged in
if (isset($_SESSION['logged_in'])) {
    $user = unserialize($_SESSION['user']);
    $_SESSION['user'] = serialize($userTools->get($user->id));
}
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css">
<!-- Bootstrap core CSS -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<!-- Material Design Bootstrap -->
<link href="css/mdb.min.css" rel="stylesheet">
<!-- Your custom styles (optional) -->
<link href="css/style.css" rel="stylesheet">
<link href="css/toastr.css" rel="stylesheet">
<link href="main.css" rel="stylesheet">
<link href="css/addons/datatables.min.css" rel="stylesheet">