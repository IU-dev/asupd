<?php

require_once 'includes/global.inc.php';
$page = "a_give_solo.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$display = 0;

$user = unserialize($_SESSION['user']);

if ($user->admin < 9) {
    header("Location: access_denied.php");
}

if (isset($_POST['submit'])) {
    $adata['user_eis'] = "'" . $_POST['user_eis'] . "'";
    $adata['service_id'] = "'" . $_POST['service_id'] . "'";
    $adata['login'] = "'" . $_POST['login'] . "'";
    $adata['password'] = "'" . $_POST['password'] . "'";
    date_default_timezone_set("GMT");
    $adata['last_update'] = "'" . date("Y-m-d H:i:s", time()) . "'";
    date_default_timezone_set($tool->getGlobal('tz'));
    $adata['last_update_user_eis'] = "'" . $user->id . "'";
    $adata['created_by_eis'] = "'" . $user->id . "'";
    $igg = $db->insert($adata, 'accounts');
    $userTools->notify($adata['user_eis'], "Система", "Вам был выдан аккаунт № ACC-" . $res . " (персональная выдача)");
    $msg = "Успешно выдан аккаунт за номером ACC-" . $igg;
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Выдать аккаунт участнику | <?php echo $pname; ?></title>
</head>
<body>
<center><br>
    <h1><?php echo $_SESSION['grand']['name']; ?></h1>
    <?php if (isset($msg)) echo "<h3>" . $msg . "</h3>"; ?>
    <form class="md-form border border-light p-5" action="a_give_solo.php" method="post">
        <p class="h4 mb-4 text-center">Выдать аккаунт участнику</p>
        <input type="text" id="textInput" name="user_eis" class="form-control mb-4 fixed-15em"
               placeholder="ЕИС участника">
        <input type="text" id="textInput" name="login" class="form-control mb-4" placeholder="Логин">
        <input type="text" id="textInput" name="password" class="form-control mb-4" placeholder="Пароль">
        <select class="browser-default custom-select mb-4" id="select" name="service_id">
            <?php
            $sections = $db->select_fs('services', "id != '0'");
            foreach ($sections as $section) {
                echo '<option value="' . $section['id'] . '">' . $section['name'] . '</option>';
            }
            ?>
        </select>
        <button class="btn btn-info btn-block" type="submit" name="submit">Выдать аккаунт</button>
    </form>
</body>
</html>