<?php
//index.php 
require_once 'includes/global.inc.php';
$page = "visits.php";
$msg = "";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$user = unserialize($_SESSION['user']);

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Посещаемость | <?php echo $pname; ?></title>
</head>
<body>
<center>
    <br>
    <h3>Сведения о пропущенных днях</h3><br>
    <br>
    <div class="card card-cascade narrower">
        <div
                class="view view-cascade gradient-card-header blue-gradient narrower py-2 mx-4 mb-3 d-flex justify-content-between align-items-center">
            <div>
            </div>
            <a href="" class="white-text mx-3">Сведения о пропущенных днях</a>

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
                    '<th>Дата</th>' .
                    '<th>Причина</th>' .
                    '<th>Отметил пользователь</th>' .
                    '</tr>' .
                    '</thead>';
                $parts = $db->select_fs('visits', "eis_id = '" . $user->id . "'");
                foreach ($parts as $part) {
                    echo '<tr>';
                    echo '<td>' . $part['id'] . '</td>';
                    echo '<td>' . date("d.m.Y", strtotime($part['date'] . " GMT")) . '</td>';
                    if ($part['reason'] == '0') echo '<td>Не установлена</td>';
                    else if ($part['reason'] == '1') echo '<td>Пропуск по болезни</td>';
                    else if ($part['reason'] == '2') echo '<td>Уважительная причина (Заявление родителей)</td>';
                    else if ($part['reason'] == '3') echo '<td>Уважительная причина (Мероприятие)</td>';
                    $by = $db->select('users', "id = '" . $part['set_by'] . "'");
                    echo '<td>' . $by['f'] . ' ' . $by['i'] . ' ' . $by['o'] . ' <a class="badge badge-primary">ЕИС-' . $by['id'] . '</a></td>';
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

        </script>
        <br>
    </div>
</center>
</body>
</html>