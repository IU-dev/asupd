<?php

require_once 'includes/global.inc.php';
$page = "info_an.php";

$display = 0;
$rt = false;

if (isset($_POST['submit'])) {
    $flds = $_POST['fld'];
    foreach ($flds as $key => $fld) {
        $check = $db->select('pdata', "eis_id = '" . $_POST['uid'] . "' AND field_id = '" . $key . "'");
        if (isset($check['data'])) {
            $data['data'] = "'" . $tool->safeString($fld) . "'";
            $data['last_update_by'] = "'0'";
            date_default_timezone_set("GMT");
            $data['last_update_datetime'] = "'" . date("Y-m-d H:i:s", time()) . "'";
            date_default_timezone_set($tool->getGlobal('tz'));
            $ib = $db->update($data, 'pdata', "id = '" . $check['id'] . "'");
        } else {
            $data['eis_id'] = "'" . $_POST['uid'] . "'";
            $data['field_id'] = "'" . $key . "'";
            $data['data'] = "'" . $tool->safeString($fld) . "'";
            $data['last_update_by'] = "'0'";
            date_default_timezone_set("GMT");
            $data['last_update_datetime'] = "'" . date("Y-m-d H:i:s", time()) . "'";
            date_default_timezone_set($tool->getGlobal('tz'));
            $ib = $db->insert($data, 'pdata');
        }
        $token2 = bin2hex(random_bytes(16));
        $data2['token2'] = "'".$token2."'";
        $b = $db->update($data2, 'users', "id = '" . $_POST['uid'] . "'");
    }
    $msg = "Внесение данных произведено успешно.";
    $rt = true;
}

if ($rt == false) {
    if (!isset($_GET['id'])) {
        die('Отсутствует UID.');
    } else {
        $uid = $_GET['id'];
        $usr = $userTools->get($uid);
    }

    if (!isset($_GET['gid'])) {
        die('Отсутствует ID группы внесения.');
    } else {
        $gr = $db->select('pdata_groups', "id = '" . $_GET['gid'] . "'");
        $fields = json_decode($gr['value']);
    }

    if ($_GET['firstpass'] != $usr->token2) {
        http_response_code(403);
        die('<h1>Error 403 - Доступ запрещен (Forbidden)</h1><br>Вы попытались получить доступ к внесению персональных данных без актуального для данного пользователя кода безопасности №2 (токена №2).<br>Скорее всего, ваш QR-код устарел. Запросите новый код у технического специалиста или классного руководителя.');
    }
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Внесение данных | <?php echo $pname; ?></title>
</head>
<body>
<br>
<?php if (isset($msg)) echo "<h3>" . $msg . "</h3>"; ?>
<?php if ($rt == false) : ?>
    <form class="md-form border border-light p-5" action="info_an.php" method="post">
        <p class="h4 mb-4 text-center">Ввод персональных данных в систему</p>
        <small>Если Вы не знаете данные поля, которое требуется заполнить, поставьте символ "-".</small><br>
        Субъект ПД:
        <input type="text" id="textInput" name="field_name" class="form-control mb-4" placeholder=""
               value="<?php echo "(" . $usr->id . ") " . $usr->f . " " . $usr->i . " " . $usr->o ?>" disabled>
        <?php
        foreach ($fields as $field) {
            $fd = $db->select('pdata_fields', "id = '" . $field . "'");
            echo $fd['name'];
            $check = $db->select('pdata', "eis_id = '" . $uid . "' AND field_id = '" . $fd['id'] . "'");
            if (isset($check['data'])) echo '<input type="text" id="textInput" value="' . $check['data'] . '" name="fld[' . $fd['id'] . ']" class="form-control mb-4" placeholder="">';
            else echo '<input type="text" id="textInput" name="fld[' . $fd['id'] . ']" class="form-control mb-4" placeholder="">';
        }
        ?>
        <input type="hidden" name="uid" value="<?php echo $_GET['id']; ?>">


        <button class="btn btn-info btn-block" type="submit" name="submit">Внести данные</button>
    </form>
<?php endif ?>
</body>
</html>