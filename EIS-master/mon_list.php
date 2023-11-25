<?php

require_once 'includes/global.inc.php';
$page = "mon_list.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$user = unserialize($_SESSION['user']);

if ($user->admin < 2) {
    header("Location: access_denied.php");
}

if (isset($_POST['submit'])) {
    $data['name'] = "'".$_POST['name']."'";
    $data['type'] = "'".$_POST['type']."'";
    $data['created_by'] = "'".$user->id."'";
    $data['description'] = "'".$_POST['description']."'";
    $ab = $db->insert($data, 'monitors');
    $msg = "Мониторинг создан успешно.";
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Список мониторингов | <?php echo $pname; ?></title>
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
                    <h4 class="modal-title w-100 font-weight-bold">Создание мониторинга</h4>
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
    <a href="" class="btn btn-primary mb-4" data-toggle="modal" data-target="#modalLoginForm">Добавить мониторинг</a>
</div>
<div class="card card-cascade narrower">
    <div
            class="view view-cascade gradient-card-header blue-gradient narrower py-2 mx-4 mb-3 d-flex justify-content-between align-items-center">
        <div>
        </div>
        <a href="" class="white-text mx-3">Список мониторингов</a>

        <div>
        </div>
    </div>
    <div class="px-4">
        <div class="table-wrapper">
            <?php
            echo '<table id="participants" class="table table-sm table-hover">' .
                '<thead>' .
                '<tr>' .
                '<th>MON-</th>' .
                '<th>Наименование</th>' .
                '<th>Тип</th>' .
                '<th>Описание</th>' .
                '<th>Действие</th>' .
                '</tr>' .
                '</thead>';
            $parts = $db->select_fs('monitors', "id != '0'");
            foreach ($parts as $part) {
                echo '<tr>';
                echo '<td>' . $part['id'] . '</td>';
                echo '<td>' . $part['name'] . '</td>';
                if($part['type'] == "rated") echo '<td>Рейтинговый</td>';
                else if($part['type'] == "notrated") echo '<td>Нерейтинговый</td>';
                $creator = $userTools->get($part['created_by']);
                echo '<td>' . $part['description'] . '<br><em>Создан пользователем ('.$creator->id.') '.$creator->f.' '.$creator->i.' '.$creator->o.'</em></td>';
                echo '<td><a class="badge badge-primary" target="_blank" href="mon_list_concrete.php?id=' . $part['id'] . '"><i class="fas fa-info"> </i> Посмотреть результаты</a></td>';
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