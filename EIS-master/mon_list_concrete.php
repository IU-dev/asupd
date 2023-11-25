<?php

require_once 'includes/global.inc.php';
$page = "mon_list_concrete.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$user = unserialize($_SESSION['user']);

if ($user->admin < 2) {
    header("Location: access_denied.php");
}

if (!isset($_GET['id'])) {
    die('Отсутствует ID.');
} else {
    $mon_id = $_GET['id'];
    $mon = $db->select('monitors', "id = '" . $mon_id . "'");
    $monr = $db->select_fs('monitors_bids', "monitor_id = '" . $mon_id . "'");
}

if (isset($_POST['submit'])) {
    $data['monitor_id'] = "'".$mon_id."'";
    $data['usr_id'] = "'".$_POST['usr_id']."'";
    $data['type'] = "'".$mon['type']."'";
    $data['value'] = "'".$_POST['value']."'";
    $data['text'] = "'".$_POST['text']."'";
    $ab = $db->insert($data, 'monitors_bids');
    $msg = "Работа внесена успешно.";
    sleep(2);
    $mon = $db->select('monitors', "id = '" . $mon_id . "'");
    $monr = $db->select_fs('monitors_bids', "monitor_id = '" . $mon_id . "'");
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Результаты мониторинга | <?php echo $pname; ?></title>
</head>
<body>
<center>
    <br><?php if (isset($msg)) echo $msg; ?><br>
</center>
<div class="modal fade" id="manual" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form action="mon_list_concrete.php?id=<?php echo $mon_id; ?>" method="post">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Внесение данных ручным способом</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="md-form mb-5">
                        <select class="mdb-select md-form" id="select" name="usr_id" searchable="Поиск по участникам"">
                        <?php
                        $users = $db->select_fs('users', "id != '0'");
                        foreach($users as $user){
                            echo '<option value="'.$user['id'].'">('.$user['id'].') '.$user['f'].' '.$user['i'].' '.$user['o'].'</option>';
                        }
                        ?>
                        </select>
                    </div>
                    <div class="md-form mb-4">
                        <input type="text" id="defaultForm-name" name="value" class="form-control validate"
                               placeholder="Балл (в процентном отношении - от 0 до 100)">
                    </div>
                    <div class="md-form mb-4">
                        <textarea name="text" class="form-control validate" placeholder="Комментарий к работе" rows="5"></textarea>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-default" type="submit" name="submit">Внести</button>
                </div>
            </div>
        </div>
</div>
<a href="mon_list.php" class="btn btn-primary mb-4">Вернуться к списку</a>
<a href="" class="btn btn-primary mb-4" data-toggle="modal" data-target="#manual">Внести результат вручную</a>
<a href="" class="btn btn-primary mb-4">Внести результат из СОРТ</a>
<div class="card card-cascade narrower">
    <div
            class="view view-cascade gradient-card-header blue-gradient narrower py-2 mx-4 mb-3 d-flex justify-content-between align-items-center">
        <div>
        </div>
        <a href="" class="white-text mx-3">Результаты мониторинга<br><?php echo $mon['name']; ?></a>

        <div>
        </div>
    </div>
    <div class="px-4">
        <div class="table-wrapper">
            <?php
            $val = 0.0;
            $counter = 0;
            echo '<table id="participants" class="table table-sm table-hover">' .
                '<thead>' .
                '<tr>' .
                '<th>MBD-</th>' .
                '<th>Участник мониторинга</th>' .
                '<th>Балл</th>' .
                '<th>Действие</th>' .
                '</tr>' .
                '</thead>';
            foreach ($monr as $part) {
                echo '<tr>';
                echo '<td>' . $part['id'] . '</td>';
                $partc = $userTools->get($part['usr_id']);
                echo '<td>' . $partc->f . ' ' . $partc->i . ' ' . $partc->o . ' (' . $partc->id . ')' . '</td>';
                echo '<td>' . $part['value'] . '</td>';
                echo '<td><a class="badge badge-primary" target="_blank" href="mjob.php?id=' . $part['id'] . '"><i class="fas fa-info"> </i> Посмотреть подробно</a></td>';
                $val = $val + (float)$part['value'];
                $counter = $counter + 1;
            }
            echo '</table>';
            echo '<strong>Средний балл по мониторингу: ' . $val / $counter . '</strong><br><br>';
            ?>
        </div>
    </div>
    <?php require_once 'includes/footer.inc.php'; ?>
    <script>
        $(document).ready(function () {
            $('#participants').DataTable({
                "order": [[1, "asc"]],
                "iDisplayLength": 100
            });
            $('.dataTables_length').addClass('bs-select');
        });

        $(document).ready(function () {
            $('.mdb-select').materialSelect();
        });

    </script>
</body>
</html>