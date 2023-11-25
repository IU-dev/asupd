<?php

$msg = '';
require_once 'includes/global.inc.php';
$page = "mjob.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$user = unserialize($_SESSION['user']);

if (!isset($_GET['id'])) {
    die('Отсутствует ID.');
} else {
    $mjb = $db->select('monitors_bids', 'id = "' . $_GET['id'] . '"');
    $mon = $db->select('monitors', "id = '" . $mjb['monitor_id'] . "'");
}

if ($user->admin < 4) {
    header("Location: access_denied.php");
}

require_once 'includes/header.inc.php';
?>
<html>
<head>
    <title> Сведения по работе | <?php echo $pname; ?></title>
</head>
<body><br>
<h1>Сведения о мониторинговой работе</h1>
<h3>Сведения о мониторинге</h3>
<table id="dtBasicExample" class="table table-sm table-hover">
    <tbody>
    <tr>
        <td>MON-ID</td>
        <td><?php echo $mon['id']; ?></td>
    </tr>
    <tr>
        <td><strong>Наименование</strong></td>
        <td><strong><?php echo $mon['name']; ?></strong></td>
    </tr>
    <tr>
        <td>Тип</td>
        <?php if ($mon['type'] == "rated") echo '<td>Рейтинговый</td>'; ?>
        <?php if ($mon['type'] == "notrated") echo '<td>Нерейтинговый</td>'; ?>
    </tr>
    <tr>
        <td>Описание</td>
        <td><?php echo $mon['description']; ?></td>
    </tr>
    </tbody>
</table>
<br>
<h3>Сведения о написании работы</h3>
<table id="dtBasicExample" class="table table-sm table-hover">
    <tbody>
    <tr>
        <td>MBD-ID</td>
        <td><?php echo $mjb['id']; ?></td>
    </tr>
    <tr>
        <td>Работа написана</td>
        <?php $stud = $db->select('users', "id = '" . $mjb['usr_id'] . "'");
        echo '<td>' . $stud['f'] . ' ' . $stud['i'] . ' ' . $stud['o'] . '</td>';
        ?>
    </tr>
    <tr>
        <td>Балл</td>
        <td><strong><?php echo $mjb['value'] ?></strong></td>
    </tr>
    <tr>
        <td><strong>Текст</strong></td>
        <td><?php $string = $mjb['text'];
                substr_replace($string,"<br>", "\n"); echo $string; ?></td>
    </tr>
    </tbody>
</table>
</iframe>
<?php require_once 'includes/footer.inc.php'; ?>
<script>
</script>
<?php if ($msg != '') {
    echo $msg;
}
?>
</body>
</html>