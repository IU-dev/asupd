<?php
//index.php 
require_once 'includes/global.inc.php';
$page = "show.php";
$msg = "";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$user = unserialize($_SESSION['user']);

require_once 'includes/header.inc.php';

if (isset($_GET['act'])) {
    if ($_GET['act'] == '1') {
        $data['from_eis'] = "'" . $user->id . "'";
        $data['acc_id'] = "'" . $_GET['acc'] . "'";
        $data['state'] = "'0'";
        $itog = $db->insert($data, 'tickets_restore');
        $msg = "Заявка на сброс пароля успешно отправлена!";
    } else if ($_GET['act'] == '2') {
        $data['from_eis'] = "'" . $user->id . "'";
        $data['service_id'] = "'" . $_GET['service_id'] . "'";
        $data['state'] = "'0'";
        $data['comment_user'] = "'" . $_GET['comment'] . "'";
        $itog = $db->insert($data, 'tickets_create');
        $msg = "Заявка на создание учетной записи успешно отправлена!";
    }
}
?>
<html>
<head>
    <title>Мои учетные записи | <?php echo $pname; ?></title>
</head>
<body>
<center>
    <div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Создание новой учетной записи</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <form action="show.php" method="get">
                        <select class="browser-default custom-select mb-4" id="select" name="service_id">
                            <?php
                            $sections = $db->select_fs('services', "id != '0'");
                            foreach ($sections as $section) {
                                echo '<option value="' . $section['id'] . '">' . $section['name'] . '</option>';
                            }
                            ?>
                        </select>
                        <div class="md-form mb-4">
                            <input type="text" id="defaultForm-email" class="form-control validate" name="comment"
                                   placeholder="Комментарий">
                        </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-default btn-primary" type="submit" name="act" value="2">Отправить заявку</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br>
    <?php if ($msg != "") echo '<div class="alert alert-info">' . $msg . '</div><br>'; ?>
    <h3>Мои учетные записи</h3><br>
    <br>
    <?php
    $accs = $db->select_fs('accounts', "user_eis = '" . $user->id . "'");
    $i = 0;
    foreach ($accs as $acc) {
        echo '<div class="alert alert-primary fixed-35em">';
        $service = $db->select('services', "id = '" . $acc['service_id'] . "'");
        echo '<h4>' . $service['name'] . '&nbsp;&nbsp;&nbsp;<span class="badge badge-info">ACC-' . $acc['id'] . '</span></h4>';
        echo '<details><summary>Посмотреть данные</summary>';
        echo '<hr><h5><strong>Логин: </strong>' . $acc['login'] . '<br>';
        echo '<strong>Пароль: </strong>' . $acc['password'] . '</h5>';
        echo '<hr>';
        echo '<strong>Адрес для входа: </strong><a href="' . $service['href'] . '">' . $service['href'] . '</a><br>';
        if ($service['only_local'] == "0") echo 'Система доступна из сети Интернет';
        else echo 'Система доступна только внутри сети Лицея';
        echo '<hr>';
        echo '<strong>Запись в ЕИС: </strong>' . $acc['id'] . '<br>';
        echo '<strong>Последнее обновление: </strong>' . date("d.m.Y H:i:s", strtotime($acc['last_update'] . " GMT")) . '<br>';
        $usr = $db->select('users', "id = '" . $acc['last_update_user_eis'] . "'");
        echo '<strong>Обновил: </strong>' . $usr['f'] . ' ' . $usr['i'] . ' ' . $usr['o'] . ' (ЕИС-' . $usr['id'] . ')<br>';
        echo '<hr>';
        echo '<a class="btn btn-sm btn-primary" role="button" href="show.php?act=1&acc=' . $acc['id'] . '">Подать заявку на сброс</a>';
        $tickets = $db->select_desc_fs('tickets_restore', "acc_id = '" . $acc['id'] . "'");
        foreach ($tickets as $ticket) {
            echo '<div class="alert alert-warning">';
            echo '<strong>Заявка на сброс пароля № </strong>' . $ticket['id'];
            echo '<br><strong>Статус заявки:</strong> ';
            if ($ticket['state'] == "0") echo 'Новая';
            else if ($ticket['state'] == "1") echo 'В работе';
            else if ($ticket['state'] == "2") echo 'Исполнена';
            else if ($ticket['state'] == "3") echo 'Отказано';
            echo '<br>';
            $isp = $db->select('users', "id = '" . $ticket['restored_by_eis'] . "'");
            echo '<strong>Исполнитель:</strong> ' . $isp['f'] . ' ' . $isp['i'] . ' ' . $isp['o'] . ' (ЕИС-' . $isp['id'] . ')<br>';
            echo '<strong>Комментарий исполнителя:</strong><br>' . $ticket['comment'];
            echo '</div>';
        }
        echo '</div></details><br>';
        $i = $i + 1;
    }
    if ($i == 0) {
        echo '<div class="alert alert-warning fixed-35em"><h5>Учетных записей нет.</h5></div><br>';
    }
    ?>
    <div class="alert alert-default fixed-35em">
        <h4>Создание учетной записи</h4>
        <details>
            <summary>Открыть меню</summary>
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalLoginForm">
                Подать заявку
            </button>
            <?php
            $tickets = $db->select_desc_fs('tickets_create', "from_eis = '" . $user->id . "'");
            foreach ($tickets as $ticket) {
                echo '<div class="alert alert-warning">';
                echo '<strong>Заявка на создание № </strong>' . $ticket['id'];
                echo '<br><strong>Статус заявки:</strong> ';
                if ($ticket['state'] == "0") echo 'Новая';
                else if ($ticket['state'] == "1") echo 'В работе';
                else if ($ticket['state'] == "2") echo 'Исполнена';
                else if ($ticket['state'] == "3") echo 'Отказано';
                echo '<br>';
                $isp = $db->select('users', "id = '" . $ticket['restored_by_eis'] . "'");
                $prod = $db->select('services', "id = '" . $ticket['service_id'] . "'");
                echo '<strong>Сервис: </strong>' . $prod['name'];
                echo '<br><strong>Исполнитель:</strong> ' . $isp['f'] . ' ' . $isp['i'] . ' ' . $isp['o'] . ' (ЕИС-' . $isp['id'] . ')<br>';
                echo '<strong>Комментарий исполнителя:</strong><br>' . $ticket['comment_admin'];
                echo '</div>';
            }
            ?>
    </div>
    </details><br>
    </div>
</center>
</body>
</html>