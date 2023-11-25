<?php

require_once 'includes/global.inc.php';
$page = "myclass.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$display = 0;

if (isset($_POST['submit'])) {
    $display = 1;
    $cont = $db->select('groups', "id = '" . $_POST['section'] . "'");
}

if (isset($_POST['visits'])) {
    $display = 1;
    $cont = $db->select('groups', "id = '" . $_POST['section'] . "'");
    $i = 0;
    foreach ($_POST['users'] as $key => $uname) {
        if ($_POST['marks'][$key] != '9') {
            $data['eis_id'] = "'" . $uname . "'";
            $data['date'] = "'" . $_POST['date'] . "'";
            $data['hrs'] = "'" . $_POST['hrs'][$key] . "'";
            $data['reason'] = "'" . $_POST['marks'][$key] . "'";
            $data['set_by'] = "'" . $user->id . "'";
            $g = $db->insert($data, 'visits');
            $i = $i + 1;
        }
    }
    echo '<center><strong>Сведения по пропускам успешно отправлены.</strong><br>Отправлена информация в количестве: ' . $i . ' человек.</center>';
}

$user = unserialize($_SESSION['user']);

if ($user->admin < 2) {
    header("Location: access_denied.php");
}

require_once 'includes/header.inc.php';
?>
<html>
<head>
    <title>Список участников | <?php echo $pname; ?></title>
</head>
<body>
<br>
<?php if ($display == 0) : ?>
    <form class="md-form border border-light p-5" action="myclass.php" method="post">
        <p class="h4 mb-4 text-center">Выберите группу обучающихся</p>
        <select class="browser-default custom-select mb-4" id="select" name="section">
            <?php
            if ($user->admin >= 3) $sections = $db->select_fs('groups', "id != '0' ORDER BY parallel ASC, name ASC");
            else $sections = $db->select_fs('groups', "curator_id = '" . $user->id . "' ORDER BY parallel ASC, name ASC");
            foreach ($sections as $section) {
                $cur = $db->select('users', "id = '" . $section['curator_id'] . "'");
                echo '<option value="' . $section['id'] . '">' . $section['name'] . ' (кл. рук. (' . $cur['id'] . ') ' . $cur['f'] . ' ' . $cur['i'] . ' ' . $cur['o'] . ')</option>';
            }
            ?>
        </select>
        <button class="btn btn-info btn-block" type="submit" name="submit">Выбрать</button>
    </form>
<?php else : ?>
<div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h4 class="modal-title w-100 font-weight-bold">Данные об отсутствующих</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="myclass.php" method="post">
                <div class="modal-body mx-3">
                    Введите дату:
                    <input type="date" id="inputMDEx" class="form-control" name="date" width="75%">
                    <br>
                    <div class="card card-cascade narrower">
                        <div
                                class="view view-cascade gradient-card-header blue-gradient narrower py-2 mx-4 mb-3 d-flex justify-content-between align-items-center">
                            <div>
                            </div>
                            <a href=""
                               class="white-text mx-3"><?php echo "(" . $cont['id'] . ") " . $cont['name']; ?></a>

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
                                    '<th>Отметка посещаемости</th>' .
                                    '</tr>' .
                                    '</thead>';
                                $parts = $db->select_fs('users', "group_id = '" . $cont['id'] . "' ORDER BY f ASC, i ASC");
                                $i = 1;
                                foreach ($parts as $part) {
                                    echo '<tr>';
                                    echo '<td>' . $i . '</td>';
                                    echo '<td>' . $part['id'] . '</td>';
                                    echo '<input type="hidden" name="users[]" value="' . $part['id'] . '">';
                                    echo '<td>' . $part['f'] . ' ' . $part['i'] . ' ' . $part['o'] . '</td>';
                                    echo '<td><select class="browser-default custom-select" name="marks[]"><option value="9" selected>Был</option><option value="0">Не установлена</option><option value="1">Пропуск по болезни</option><option value="2">Заявление родителей</option><option value="3">Мероприятие</option></select></td>';
                                    $i = $i + 1;
                                }
                                echo '</table>';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <input type="hidden" name="section" value="<?php echo $_POST['section'] ?>">
                    <button class="btn btn-default" type="submit" name="visits">Внести сведения</button>
            </form>
        </div>
    </div>
</div>
</div>
<div class="modal fade" id="Spiski" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h4 class="modal-title w-100 font-weight-bold">Печать списков</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                echo '<ul>';
                $forms = $db->select_fs('forms', "type = 'group' AND state = '1'");
                foreach ($forms as $form) {
                    echo '<li><a target="_blank" href="print.php?customdoc=' . $form['id'] . '&id=' . $cont['id'] . '">' . $form['name'] . '</a></li>';
                }
                echo '</ul>';
                ?>
            </div>
        </div>
    </div>
</div>
    <button type="button" class="btn btn-rounded btn-sm btn-primary" data-toggle="modal" data-target="#Spiski">
        Печать списков
    </button>
    <button type="button" class="btn btn-rounded btn-sm btn-primary" data-toggle="modal" data-target="#modalLoginForm">
        Посещаемость
    </button>
    <a type="button" class="btn btn-rounded btn-sm btn-primary" target="_blank"
       href="api.php?act=getLinksForParents&gid=<?php echo $_POST['section'] ?>&ruid=<?php echo $user->id ?>&token2=<?php echo $user->token2 ?>">
        Генерация QR-кодов
    </a>
    <a type="button" class="btn btn-rounded btn-sm btn-primary" target="_blank"
       href="api.php?act=getLinksForParents&gid=<?php echo $_POST['section'] ?>">
        Таблица персональных данных
    </a>
    <br><br>
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
                    '<th style="width: 5%!important;">№</th>' .
                    '<th style="width: 5%!important;">ЕИС</th>' .
                    '<th style="width: 65%!important;">ФИО участника</th>' .
                    '<th style="width: 25%!important;">Действие</th>' .
                    '</tr>' .
                    '</thead>';
                $parts = $db->select_fs('users', "group_id = '" . $cont['id'] . "' ORDER BY f ASC, i ASC");
                $i = 1;
                foreach ($parts as $part) {
                    echo '<tr>';
                    echo '<td>' . $i . '</td>';
                    echo '<td>' . $part['id'] . '</td>';
                    echo '<td>' . $part['f'] . ' ' . $part['i'] . ' ' . $part['o'] . '</td>';
                    echo '<td><a class="badge badge-success" target="_blank" href="info.php?uid=' . $part['id'] . '"><i class="fas fa-check"></i> Информационная карта</a> <a class="badge badge-warning" target="_blank" href="api.php?act=getLinkForParent&uid=' . $part['id'] . '&ruid='.$user->id.'&token2='.$user->token2.'"><i class="fas fa-qrcode"></i> QR-код родителя</a></td>';
                    $i = $i + 1;
                }
                echo '</table>';
                ?>
            </div>
        </div>
    </div>
    <?php require_once 'includes/footer.inc.php'; ?>
    <script>

        $(document).ready(function () {
            $('.mdb-select').materialSelect();
        });

        $('.datepicker').pickadate();

    </script>
    <?php endif ?>
</body>
</html>