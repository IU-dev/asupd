<?php

require_once 'includes/global.inc.php';
$page = "a_create.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}
$user = unserialize($_SESSION['user']);

if ($user->admin < 9) {
    header("Location: access_denied.php");
}

$show = 0;

if (isset($_GET['act'])) {
    if($_GET['act'] == "1"){
        $show = 1;
    }
    else if($_GET['act'] == "2"){
        $show = 2;
    }
}

if(isset($_POST['submit-otkaz'])){
    $data['restored_by_eis'] = "'".$user->id."'";
    $data['state'] = "'3'";
    $data['comment_admin'] = "'".$_POST['comment_admin']."'";
    $a = $db->update($data, 'tickets_create', "id = '".$_POST['id']."'");
    echo '<div class="alert alert-success">Отказ на заявку с ID '.$_POST['id'].' успешно выдан.</div>';
    $tick = $db->select('tickets_create', "id = '".$_POST['id']."'");
    $usvr = $db->select('users', "id = '".$tick['from_eis']."'");
    $userTools->notify($usvr['id'], "Система", "Обновлен статус заявки на создание аккаунта № ".$_POST['id'].": Отказано.");
}

if(isset($_POST['submit-vidat'])){
    $data['restored_by_eis'] = "'".$user->id."'";
    $data['state'] = "'2'";
    $data['comment_admin'] = "'".$_POST['comment_admin']."'";
    $a = $db->update($data, 'tickets_create', "id = '".$_POST['id']."'");
    echo '<div class="alert alert-success">Аккаунт на заявку с ID '.$_POST['id'].' успешно выдан.</div>';
    $tick = $db->select('tickets_create', "id = '".$_POST['id']."'");
    $usvr = $db->select('users', "id = '".$tick['from_eis']."'");
    $userTools->notify($usvr['id'], "Система", "Обновлен статус заявки на создание аккаунта № ".$_POST['id'].": Исполнена.");
    $adata['user_eis'] = "'".$usvr['id']."'";
    $adata['service_id'] = "'".$_POST['service_id']."'";
    $adata['login'] = "'".$_POST['login']."'";
    $adata['password'] = "'".$_POST['password']."'";
    date_default_timezone_set("GMT");
    $adata['last_update'] = "'" . date("Y-m-d H:i:s", time()) . "'";
    date_default_timezone_set($tool->getGlobal('tz'));
    $adata['last_update_user_eis'] = "'".$user->id."'";
    $adata['created_by_eis'] = "'".$user->id."'";
    $igg = $db->insert($adata, 'accounts');
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Заявки на создание УЗ | <?php echo $pname; ?></title>
</head>
<body>
<center><br>
    <?php if ($show == 0) : ?>
    <h3>Панель принятия заявок</h3>
    <br><?php if (isset($msg)) echo $msg; ?><br>
</center>
<div class="card card-cascade narrower">
    <div
            class="view view-cascade gradient-card-header blue-gradient narrower py-2 mx-4 mb-3 d-flex justify-content-between align-items-center">
        <div>
        </div>
        <a href="" class="white-text mx-3">Заявки на создание учетных записей</a>

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
                '<th>Автор</th>' .
                '<th>Сервис</th>' .
                '<th>Комментарий</th>' .
                '<th>Действие</th>' .
                '</tr>' .
                '</thead>';
            $parts = $db->select_fs('tickets_create', "state = '0'");
            foreach ($parts as $part) {
                echo '<tr>';
                echo '<td>' . $part['id'] . '</td>';
                $usver = $db->select('users', "id = '".$part['from_eis']."'");
                echo '<td>' . $usver['f'] . ' ' . $usver['i'] . ' ' . $usver['o'] . ' (ЕИС-'. $usver['id'] . ')</td>';
                $servis = $db->select('services', "id = '".$part['service_id']."'");
                echo '<td>' . $servis['name'] . '</td>';
                echo '<td>' . $part['comment_user'] . '</td>';
                echo '<td><a class="badge badge-success" href="a_create.php?act=1&ticket=' . $part['id'] . '"><i class="fas fa-check"></i> Выдать</a>&nbsp;<a class="badge badge-danger" href="a_create.php?act=2&ticket=' . $part['id'] . '"><i class="far fa-window-close"></i> Отказать</a></td>';
            }
            echo '</table>';
            ?>
        </div>
    </div>
    <?php endif ?>
    <?php if ($show == 1) : ?>
        <form class="md-form border border-light p-5" action="a_create.php" method="post">
            <p class="h4 mb-4 text-center">Выдача аккаунта</p>
            <input type="hidden" name="id" value="<?php echo $_GET['ticket'] ?>">
            <?php
            $tck = $db->select('tickets_create', "id = '".$_GET['ticket']."'");
            $srv = $db->select('services', "id = '".$tck['service_id']."'")
            ?>
            <input type="hidden" name="service_id" value="<?php echo $srv['id'] ?>">
            ID заявки: <?php echo $_GET['ticket'] ?>
            <input type="text" id="textInput" name="login" class="form-control mb-4" placeholder="Логин">
            <input type="text" id="textInput" name="password" class="form-control mb-4" placeholder="Пароль">
            <input type="text" id="textInput" name="comment_admin" class="form-control mb-4" placeholder="Введите комментарий">
            <button class="btn btn-info btn-block" type="submit" name="submit-vidat">Выдать</button>
        </form>
    <?php endif ?>
    <?php if ($show == 2) : ?>
        <form class="md-form border border-light p-5" action="a_create.php" method="post">
            <p class="h4 mb-4 text-center">Регистрация отказа в выдаче</p>
            <input type="hidden" name="id" value="<?php echo $_GET['ticket'] ?>">
            ID заявки: <?php echo $_GET['ticket'] ?>
            <input type="text" id="textInput" name="comment_admin" class="form-control mb-4" placeholder="Введите причину отказа">
            <button class="btn btn-info btn-block" type="submit" name="submit-otkaz">Отказать</button>
        </form>
    <?php endif ?>
    <?php require_once 'includes/footer.inc.php'; ?>
    <script>
        $(document).ready(function () {
            $('#participants').DataTable();
            $('.dataTables_length').addClass('bs-select');
        });

    </script>
</body>
</html>