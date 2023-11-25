<?php

require_once 'includes/global.inc.php';
$page = "a_give_group.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$display = 0;

$user = unserialize($_SESSION['user']);

if ($user->admin < 9) {
    header("Location: access_denied.php");
}

if (isset($_POST['submit'])) {
    $display = 1;
    $cont = $db->select('groups', "id = '" . $_POST['section'] . "'");
}

if (isset($_POST['submit-create'])) {
    foreach ($_POST['eis'] as $key => $usr) {
        if ($_POST['login'][$key] != "") {
            $data['user_eis'] = "'" . $usr . "'";
            $data['service_id'] = "'" . $_POST['service_id'] . "'";
            $data['login'] = "'" . $_POST['login'][$key] . "'";
            $data['password'] = "'" . $_POST['password'][$key] . "'";
            date_default_timezone_set("GMT");
            $data['last_update'] = "'" . date("Y-m-d H:i:s", time()) . "'";
            date_default_timezone_set($tool->getGlobal('tz'));
            $data['last_update_user_eis'] = "'" . $user->id . "'";
            $data['created_by_eis'] = "'" . $user->id . "'";
            $res = $db->insert($data, 'accounts');
            $userTools->notify($data['user_eis'], "Система", "Вам был выдан аккаунт № ACC-" . $res . " (групповая выдача)");
        }
    }
    echo 'Процедура групповой выдачи аккаунтов произведена успешно.';
}

require_once 'includes/header.inc.php';
?>
<html>
<head>
    <title>Выдача аккаунтов группе | <?php echo $pname; ?></title>
</head>
<body>
<center><br>
    <?php if ($display == 0) : ?>
        <form class="md-form border border-light p-5" action="a_give_group.php" method="post">
            <p class="h4 mb-4 text-center">Выберите группу</p>
            <select class="browser-default custom-select mb-4" id="select" name="section">
                <?php
                if ($user->admin >= 3) $sections = $db->select_fs('groups', "id != '0' ORDER BY parallel ASC, name ASC");
                else $sections = $db->select_fs('groups', "curator_id = '" . $user->id . "' ORDER BY parallel ASC, name ASC");
                foreach ($sections as $section) {
                    $cur = $db->select('users', "id = '" . $section['curator_id'] . "'");
                    echo '<option value="' . $section['id'] . '">' . $section['name'] . ' (куратор ' . $cur['f'] . ' ' . $cur['i'] . ' ' . $cur['o'] . ' (ЕИС-' . $cur['id'] . '))</option>';
                }
                ?>
            </select>
            <button class="btn btn-info btn-block" type="submit" name="submit">Выбрать</button>
        </form>
    <?php else : ?>
    <h3>Список группы</h3><br>
    Выберите сервис:
    <form class="md-form" action="a_give_group.php" method="post">
        <select class="browser-default custom-select mb-4 fixed-15em" id="select" name="service_id">
            <?php
            $sections = $db->select_fs('services', "id != '0'");
            foreach ($sections as $section) {
                echo '<option value="' . $section['id'] . '">' . $section['name'] . '</option>';
            }
            ?>
        </select>
        <br>
</center>
<div class="card card-cascade narrower">
    <div
            class="view view-cascade gradient-card-header blue-gradient narrower py-2 mx-4 mb-3 d-flex justify-content-between align-items-center">
        <div>
        </div>
        <a href="" class="white-text mx-3"><?php echo "(" . $cont['id'] . ") " . $cont['name']; ?></a>

        <div>
        </div>
    </div>
    <div class="px-4">
        <div class="table-wrapper">
            <?php
            echo '<table id="participants" class="table table-sm table-hover">' .
                '<thead>' .
                '<tr>' .
                '<th>№</th>' .
                '<th>ЕИС</th>' .
                '<th>ФИО участника</th>' .
                '<th>Логин</th>' .
                '<th>Пароль</th>' .
                '</tr>' .
                '</thead>';
            $parts = $db->select_fs('users', "group_id = '" . $cont['id'] . "' ORDER BY f ASC, i ASC");
            $i = 1;
            foreach ($parts as $part) {
                echo '<input type="hidden" name="eis[]" value="' . $part['id'] . '">';
                echo '<tr>';
                echo '<td>' . $i . '</td>';
                echo '<td>' . $part['id'] . '</td>';
                echo '<td>' . $part['f'] . ' ' . $part['i'] . ' ' . $part['o'] . '</td>';
                echo '<td><input type="text" id="textInput" name="login[]" class="form-control mb-4" placeholder="Логин"></td>';
                echo '<td><input type="text" id="textInput" name="password[]" class="form-control mb-4" placeholder="Пароль"></td>';
                $i = $i + 1;
            }
            echo '</table>';
            ?>
        </div>
    </div>
    <button class="btn btn-info btn-block" type="submit" name="submit-create">Создать УЗ</button>
    </form>
</div>
<?php require_once 'includes/footer.inc.php'; ?>
<script>

</script>
<?php endif ?>
</body>
</html>