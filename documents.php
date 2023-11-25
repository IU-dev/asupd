<?php

require_once 'includes/global.inc.php';
$page = "documents.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$user = unserialize($_SESSION['user']);

if ($user->admin < 3) {
    header("Location: access_denied.php");
}

if (isset($_GET['action'])) {
    if($_GET['action'] == "sign"){
        $prikaz = Prikaz::get($_GET['id']);
        if($prikaz->sign()) $msg = $tool->toast("success", "Приказ подписан успешно!");
        else $msg = $tool->toast("error", "Приказ не подписан. У вас нет прав, или приказ не в нужном статусе.");
    }
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Приказы по обучающимся | <?php echo $pname; ?></title>
</head>
<body>
<center><br>
    <br><?php if (isset($msg)) echo $msg; ?><br>
</center>
<div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form action="mon_list.php" method="post">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Создание приказа</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="md-form mb-4">
                        <input type="text" id="defaultForm-name" name="name" class="form-control validate"
                               placeholder="Наименование мониторинга">
                    </div>
                    <div class="md-form mb-5">
                        <select class="mdb-select md-form" id="select" name="type">
                            <option value="rated">Рейтинговый</option>
                            <option value="notrated">Нерейтинговый</option>
                        </select>
                    </div>
                    <div class="md-form mb-4">
                        <input type="text" id="defaultForm-name" name="description" class="form-control validate"
                               placeholder="Описание мониторинга">
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-default" type="submit" name="submit">Создать</button>
                </div>
            </div>
        </div>
</div>
<div class="">
    <a href="" class="btn btn-primary mb-4" data-toggle="modal" data-target="#modalLoginForm">Добавить приказ</a>
</div>
<div class="card card-cascade narrower">
    <div
            class="view view-cascade gradient-card-header blue-gradient narrower py-2 mx-4 mb-3 d-flex justify-content-between align-items-center">
        <div>
        </div>
        <a href="" class="white-text mx-3">Приказы по обучающимся</a>

        <div>
        </div>
    </div>
    <div class="px-4">
        <div class="table-wrapper">
            <?php
            echo '<table id="participants" class="table table-sm table-hover">' .
                '<thead>' .
                '<tr>' .
                '<th>PRI-</th>' .
                '<th>Наименование</th>' .
                '<th>Тип</th>' .
                '<th>Статус</th>' .
                '<th>Действие</th>' .
                '</tr>' .
                '</thead>';
            $parts = $db->select_fs('prikazy', "id != '0'");
            foreach ($parts as $part) {
                echo '<tr>';
                echo '<td>' . $part['id'] . '</td>';
                echo '<td><strong>№ ' . $part['reg_n'] . ' от ' . $tool->date_short($part['date']) . '</strong>  ';
                if ($part['status'] == "0") echo '<span class="badge badge-primary">Подготовка</span>';
                else if ($part['status'] == "1") echo '<span class="badge badge-warning">Ожидает подписания</span>';
                else if ($part['status'] == "2") echo '<span class="badge badge-success">Подписан</span>';
                else if ($part['status'] == "3") echo '<span class="badge badge-light">Отозван подписантом</span>';
                echo '<br><a class="badge badge-primary" target="_blank" href="documents.php?action=getFile&id=' . $part['id'] . '"><i class="fas fa-download"> </i> Скачать файл</a></td>';
                echo '</td>';
                if ($part['type'] == "zachisleniye") echo '<td>Зачисление</td>';
                else if ($part['type'] == "otchisleniye") echo '<td>Отчисление</td>';
                echo '<td>Создан ' . $tool->date($part['created_when']) . ' / ' . $userTools->get($part['created_by'])->fio();
                echo '<br>Изменил ' . $tool->date($part['edited_when']) . ' / ' . $userTools->get($part['edited_by'])->fio();
                if ($part['status'] == "2") echo '<br>Подписал ' . $tool->date($part['signed_when']) . ' / ' . $userTools->get($part['signed_by'])->fio();
                echo '</td>';
                echo '<td><a class="badge badge-primary" target="_blank" href="document.php?type=prikaz&id=' . $part['id'] . '"><i class="fas fa-info"> </i> Перейти к приказу</a> ';
                if ($part['status'] == "1" && $user->admin >= 5) echo '<a class="badge badge-success" target="_blank" href="documents.php?action=sign&id=' . $part['id'] . '"><i class="fas fa-check"> </i> Подписать приказ</a>';
                echo '</td>';
            }
            echo '</table>';
            ?>
        </div>
    </div>
    <?php require_once 'includes/footer.inc.php'; ?>
    <script>
        $(document).ready(function () {
            $('#participants').DataTable({
                "order": [[0, "desc"]]
            });
            $('.dataTables_length').addClass('bs-select');
        });

        $(document).ready(function () {
            $('.mdb-select').materialSelect();
        });

    </script>
</body>
</html>