<?php

require_once 'includes/global.inc.php';
$page = "a_delete.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$user = unserialize($_SESSION['user']);

if ($user->admin < 9) {
    header("Location: access_denied.php");
}

if (isset($_GET['delete'])) {
    $itog = $db->delete('accounts', "id = '" . $_GET['delete'] . "'");
    $userTools->notify($adata['user_eis'], "Система", "Вам был удален аккаунт № ACC-" . $_GET['delete']);
    $msg = "<div class='alert alert-success'>Операция удаления аккаунта с ID ACC-" . $_GET['delete'] . " произведена.</div>";
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Удаление аккаунтов | <?php echo $pname; ?></title>
</head>
<body>
<center><br>
    <h1><?php echo $_SESSION['grand']['name']; ?></h1>
    <h3>Панель удаления аккаунтов</h3>
    <strong>Будьте внимательны! Удаление аккаунта невозможно отменить!</strong>
    <br><?php if (isset($msg)) echo $msg; ?><br>
</center>
<div class="card card-cascade narrower">
    <div
            class="view view-cascade gradient-card-header blue-gradient narrower py-2 mx-4 mb-3 d-flex justify-content-between align-items-center">
        <div>
        </div>
        <a href="" class="white-text mx-3">Все аккаунты</a>

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
                '<th>Владелец</th>' .
                '<th>Сервис</th>' .
                '<th>Логин</th>' .
                '<th>Действие</th>' .
                '</tr>' .
                '</thead>';
            $parts = $db->select_fs('accounts', "id != '0'");
            foreach ($parts as $part) {
                echo '<tr>';
                echo '<td>' . $part['id'] . '</td>';
                echo '<td>' . $part['user_eis'] . '</td>';
                $srv = $db->select('services', "id = '" . $part['service_id'] . "'");
                echo '<td>' . $srv['name'] . '</td>';
                echo '<td>' . $part['login'] . '</td>';
                echo '<td><a class="badge badge-danger" href="a_delete.php?delete=' . $part['id'] . '"><i class="fas fa-trash"></i> Удалить</a></td>';
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