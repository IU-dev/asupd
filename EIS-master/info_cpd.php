<?php

require_once 'includes/global.inc.php';
$page = "info_cpd.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$display = 0;

$user = unserialize($_SESSION['user']);

if ($user->admin < 1) {
    header("Location: access_denied.php");
}

if (isset($_POST['submit'])) {
    $data['data'] = "'" . $_POST['newPD'] . "'";
    $data['last_update_by'] = "'" . $user->id . "'";
    date_default_timezone_set("GMT");
    $data['last_update_datetime'] = "'" . date("Y-m-d H:i:s", time()) . "'";
    date_default_timezone_set($tool->getGlobal('tz'));
    $igg = $db->update($data, 'pdata', "id = '" . $_POST['pdid'] . "'");
    header("Location: info.php?uid=" . $_POST['uid']);
}

if (!isset($_GET['id'])) {
    die('Отсутствует UID.');
} else {
    $uid = $_GET['id'];
    $usr = $userTools->get($uid);
}

if (!isset($_GET['pd'])) {
    die('Отсутствует ID элемента ПД.');
} else {
    $pdid = $_GET['pd'];
    $pd = $db->select('pdata', "id = '" . $pdid . "'");
    $changed = $db->select('users', "id = '" . $pd['last_update_by'] . "'");
    $apd = $db->select('pdata_fields', "id = '" . $pd['field_id'] . "'");
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Изменить значение поля персональных данных | <?php echo $pname; ?></title>
</head>
<body>
<br>
<?php if (isset($msg)) echo "<h3>" . $msg . "</h3>"; ?>

<form class="md-form border border-light p-5" action="info_cpd.php" method="post">
    <p class="h4 mb-4 text-center">Изменить значение поля персональных данных</p>
    Субъект ПД:
    <input type="text" id="textInput" name="field_name" class="form-control mb-4" placeholder=""
           value="<?php echo "(" . $usr->id . ") " . $usr->f . " " . $usr->i . " " . $usr->o ?>" disabled>
    Наименование поля:
    <input type="text" id="textInput" name="field_name" class="form-control mb-4" placeholder=""
           value="<?php echo $apd['name'] ?>" disabled>
    Старое значение:
    <input type="text" id="textInput" name="login" class="form-control mb-4" placeholder=""
           value="<?php echo $pd['data'] ?>" disabled>
    Новое значение:
    <input type="text" id="textInput" name="newPD" class="form-control mb-4" placeholder="">
    <input type="hidden" name="uid" value="<?php echo $_GET['id']; ?>">
    <input type="hidden" name="pdid" value="<?php echo $_GET['pd']; ?>">


    <button class="btn btn-info btn-block" type="submit" name="submit">Произвести изменение</button>
    ID записи - PDA-<?php echo $pd['id']; ?>. Последнее изменение было
    произведено <?php echo date("d.m.Y H:i:s", strtotime($pd['last_update_datetime'] . " GMT")) ?>
    пользователем <?php echo "(" . $changed['id'] . ") " . $changed['f'] . " " . $changed['i'] . " " . $changed['o']; ?>
</form>
</body>
</html>