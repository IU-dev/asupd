<?php

require_once 'includes/global.inc.php';

$page = "applics.php";

$user = unserialize($_SESSION['user']);

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Подача заявления | <?php echo $pname; ?></title>
</head>
<body>
<center><br>
</center>
<div class="card card-cascade narrower">
    <div
            class="view view-cascade gradient-card-header blue-gradient narrower py-2 mx-4 mb-3 d-flex justify-content-between align-items-center">
        <div>
        </div>
        <h4><a href="" class="white-text mx-3">Доступные заявления</a></h4>

        <div>
        </div>
    </div>
    <div class="px-4">
        <div class="table-wrapper">
            <?php
            echo '<table id="participants2" class="table table-sm table-hover">' .
                '<thead>' .
                '<tr>' .
                '<th>ID</th>' .
                '<th>Наименование</th>' .
                '<th>Действие</th>' .
                '</tr>' .
                '</thead>';
            if (isset($user->id)) $parts = $db->select_fs('applic_forms', "state = '1'");
            else $parts = $db->select_fs('applic_forms', "state = '1' AND display_registred = '0'");
            foreach ($parts as $part) {
                echo '<tr>';
                echo '<td>' . $part['id'] . '</td>';
                echo '<td>' . $part['name'] . '</td>';
                echo '<td><a class="badge badge-success" href="applic_create.php?form=' . $part['id'] . '"><i class="fas fa-check"></i> Подать заявление</a></td>';
            }
            echo '</table>';
            ?>
        </div>
    </div>
</div>
<?php if (isset($user->id)) : ?>
    <br><br>

    <div class="card card-cascade narrower">
        <div
                class="view view-cascade gradient-card-header blue-gradient narrower py-2 mx-4 mb-3 d-flex justify-content-between align-items-center">
            <div>
            </div>
            <a href="" class="white-text mx-3">Мои заявления</a>

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
                    '<th>Наименование</th>' .
                    '<th>Дата создания</th>' .
                    '<th>Статус</th>' .
                    '<th>Действие</th>' .
                    '</tr>' .
                    '</thead>';
                $parts = $db->select_fs('applic_bids', "usr_id = '".$user->id."' AND state != '9'");
                foreach ($parts as $part) {
                    echo '<tr>';
                    echo '<td>' . $part['id'] . '</td>';
                    $frm = $db->select('applic_forms', "id = '" . $part['form_id'] . "'");
                    echo '<td>' . $frm['name'] . '</td>';
                    echo '<td>' . date("d.m.Y H:i:s", strtotime($part['time_create'] . " GMT")) . '</td>';
                    if($part['state'] == "0") echo '<td>Отправлено</td>';
                    else if($part['state'] == "1") echo '<td>В работе</td>';
                    else if($part['state'] == "2") echo '<td>Требуется уточнение</td>';
                    else if($part['state'] == "3") echo '<td>Завершено (Одобрено)</td>';
                    else if($part['state'] == "4") echo '<td>Завершено (Отклонено)</td>';
                    else if($part['state'] == "5") echo '<td>Завершено (Исполнено)</td>';
                    echo '<td><a class="badge badge-primary" href="applic_show.php?id=' . $part['id'] . '"><i class="fas fa-warning"></i> Просмотреть заявление</a></td>';
                }
                echo '</table>';
                ?>
            </div>
        </div>
    </div>
<?php endif ?>
<?php require_once 'includes/footer.inc.php'; ?>
<script>
    $(document).ready(function () {
        $('#participants').DataTable();
        $('.dataTables_length').addClass('bs-select');
    });

    $(document).ready(function () {
        $('#participants2').DataTable();
        $('.dataTables_length').addClass('bs-select');
    });

</script>
</body>
</html>