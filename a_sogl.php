<?php

require_once 'includes/global.inc.php';
$page = "a_sogl.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}
$user = unserialize($_SESSION['user']);

if ($user->admin < 9) {
    header("Location: access_denied.php");
}

$show = 0;

if (isset($_GET['sign_sogl'])) {
    if ($user->admin >= 9) {
        $sogl = $db->select('pdata_docs', "id = '" . $_GET['sign_sogl'] . "'");
        if ($sogl['state'] == "0") {
            $data['state'] = "'1'";
            $data['who_signed'] = "'" . $user->id . "'";
            date_default_timezone_set("GMT");
            $data['date_accept'] = "'" . date("Y-m-d H:i:s", time()) . "'";
            date_default_timezone_set($tool->getGlobal('tz'));
            $p = $db->update($data, 'pdata_docs', "id = '" . $_GET['sign_sogl'] . "'");
            $msg = '<script type="text/javascript">toastr.success("Вы успешно подписали соглашение", "Успешно!");</script>';
        } else $msg = '<script type="text/javascript">toastr.error("Данное соглашение уже подписано, либо отозвано", "Ошибка!");</script>';
    } else $msg = '<script type="text/javascript">toastr.error("У вас нет прав для подписания соглашения", "Ошибка!");</script>';
}

if (isset($_GET['refuse_sogl'])) {
    if ($user->admin >= 9) {
        $sogl = $db->select('pdata_docs', "id = '" . $_GET['refuse_sogl'] . "'");
        if ($sogl['state'] != "2") {
            $data['state'] = "'2'";
            $data['who_null'] = "'" . $user->id . "'";
            date_default_timezone_set("GMT");
            $data['date_null'] = "'" . date("Y-m-d H:i:s", time()) . "'";
            date_default_timezone_set($tool->getGlobal('tz'));
            $p = $db->update($data, 'pdata_docs', "id = '" . $_GET['refuse_sogl'] . "'");
            $msg = '<script type="text/javascript">toastr.success("Вы успешно отозвали соглашение", "Успешно!");</script>';
        } else $msg = '<script type="text/javascript">toastr.error("Данное соглашение уже отозвано", "Ошибка!");</script>';
    } else $msg = '<script type="text/javascript">toastr.error("У вас нет прав для отзыва соглашения", "Ошибка!");</script>';
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Соглашения | <?php echo $pname; ?></title>
</head>
<body><br>
<div class="card card-cascade narrower">
    <div
            class="view view-cascade gradient-card-header blue-gradient narrower py-2 mx-4 mb-3 d-flex justify-content-between align-items-center">
        <div>
        </div>
        <a href="" class="white-text mx-3">Реестр соглашений</a>

        <div>
        </div>
    </div>
    <div class="px-4">
        <div class="table-wrapper">
            <?php
            echo '<table id="participants" class="table table-sm table-hover">' .
                '<thead>' .
                '<tr>' .
                '<th>ID</th>' .
                '<th>Пользователь</th>' .
                '<th>Статус</th>' .
                '<th>Действие</th>' .
                '</tr>' .
                '</thead>';
            $parts = $db->select_fs('pdata_docs', "state != '-1'");
            foreach ($parts as $part) {
                echo '<tr>';
                echo '<td>' . $part['id'] . '</td>';
                $usr = $db->select('users', "id = '" . $part['user_id'] . "'");
                echo '<td>' . $usr['f'] . ' ' . $usr['i'] . ' ' . $usr['o'] . ' (' . $usr['id'] . ')' . '</td>';
                if ($part['state'] == "0") echo '<td>Не подписано</td>';
                else if ($part['state'] == "1") echo '<td><em>Подписано ' . date("d.m.Y H:i:s", strtotime($part['date_accept'] . " GMT")) . ' (' . $userTools->get_name($part['who_signed']) . ')</em></td>';
                else if ($part['state'] == "2") echo '<td><em>Подписано ' . date("d.m.Y H:i:s", strtotime($part['date_accept'] . " GMT")) . ' (' . $userTools->get_name($part['who_signed']) . ')</em><br><em>Отозвано ' . date("d.m.Y H:i:s", strtotime($part['date_null'] . " GMT")) . ' (' . $userTools->get_name($part['who_null']) . ')</em></td>';
                if ($part['state'] == "0") echo '<td><a href="print.php?sysdoc=1&id=' . $part['id'] . '" class="badge badge-pill badge-primary" target="_blank">Распечатать</a> <a href="a_sogl.php?uid=' . $usr->id . '&sign_sogl=' . $part['id'] . '" class="badge badge-pill badge-primary">Подписать</a></td>';
                else if ($part['state'] == "1") echo '<td><a href="a_sogl.php?uid=' . $usr->id . '&refuse_sogl=' . $part['id'] . '" class="badge badge-pill badge-primary">Отозвать</a></td>';
                else if ($part['state'] == "2") echo '<td>Нет действий</td>';
                echo '</tr>';
            }
            echo '</table>';
            ?>
        </div>
    </div>
    <?php require_once 'includes/footer.inc.php'; ?>
    <script>
        $(document).ready(function () {
            $('#participants').DataTable();
            $('.dataTables_length').addClass('bs-select');
        });

    </script>
</body>
</html>