<?php

require_once 'includes/global.inc.php';
$page = "settings.php";
require_once 'includes/header.inc.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$user = unserialize($_SESSION['user']);

?>
<html lang="ru">
<head>
    <title>Аккаунт | <?php echo $pname; ?></title>
</head>
<body>
<center><br>
    <h1><?php echo $user->f . " " . $user->i . " " . $user->o; ?></h1>
    <h3>ID ЕИС: <?php echo $user->id; ?></h3>
    <br>
    <h3>Группа: <?php
        $group = $db->select('groups', "id = '" . $user->group_id . "'");
        echo $group['name'];
        ?></h3>
    <br><br>
</center>
<?php
echo '<table id="dtBasicExample" class="table table-sm table-hover">' .
    '<thead>' .
    '<tr>' .
    '<th>Дата, время</th>' .
    '<th>От кого</th>' .
    '<th>Содержание</th>' .
    '</tr>' .
    '</thead>';
$notifs = $db->select_desc_fs('logs', "userid = '" . $user->id . "'");
foreach ($notifs as $row) {
    echo '<tr>';
    if ($row['displayed'] == '0') {
        $data['displayed'] = '1';
        echo '<td class="table-warning">' . date("d.m.Y H:i:s", strtotime($row['datetime'] . " GMT")) . '</td>' .
            '<td class="table-warning">' . $row['ot'] . '</td>' .
            '<td class="table-warning">' . $row['text'] . '</td>' .
            '</tr>';
        $db->update($data, 'logs', "id = '" . $row['id'] . "'");
    } else {
        echo '<td>' . date("d.m.Y H:i:s", strtotime($row['datetime'] . " GMT")) . '</td>' .
            '<td>' . $row['ot'] . '</td>' .
            '<td>' . $row['text'] . '</td>' .
            '</tr>';
    }
}
echo '</table>';
?>

<?php require_once 'includes/footer.inc.php'; ?>

<script>
    $(document).ready(function () {
        $('#dtBasicExample').DataTable();
        $('.dataTables_length').addClass('bs-select');
    });
</script>
</body>
</html>