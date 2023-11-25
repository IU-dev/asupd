<?php

require_once 'includes/global.inc.php';
$page = "print.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}
$user = unserialize($_SESSION['user']);

if ($user->admin < 2) {
    header("Location: access_denied.php");
}

$link = "0";
$docname = "";
$action = [];

if (isset($_GET['sysdoc'])) {
    if ($_GET['sysdoc'] == "1") {
        $docname = "Соглашение об обработке ПД (стандартная форма)";
        $doc = $db->select('pdata_docs', "id = '" . $_GET['id'] . "'");
        $usr = $db->select('users', "id = '" . $doc['user_id'] . "'");
        $gr = $db->select('groups', "id = '" . $usr['group_id'] . "'");
        $docdata = "ID соглашения - " . $_GET['id'] . ", пользователь - " . $usr['f'] . " " . $usr['i'] . " " . $usr['o'] . " (ИД " . $usr['id'] . "), класс - " . $gr['name'];
        $document = new \PhpOffice\PhpWord\TemplateProcessor("print/sys/1/template.docx");
        $document->setValue('docid', $_GET['id']);
        $document->setValue('id', $usr['id']);
        $document->setValue('fio', $usr['f'] . ' ' . $usr['i'] . ' ' . $usr['o']);
        $document->setValue('group', $gr['name']);
        date_default_timezone_set("GMT");
        $document->setValue('date', date("d.m.Y", time()));
        $document->setValue('datetime', date("d.m.Y H:m:s", time()));
        date_default_timezone_set($tool->getGlobal('tz'));
        $document->saveAs("print/sys/1/Soglashenie-" . $_GET['id'] . "-".date("d-m-Y-H-m-s", time()).".docx");
        $link = 'https://' . $_SERVER['SERVER_NAME'] . "/print/sys/1/Soglashenie-" . $_GET['id'] . "-".date("d-m-Y-H-m-s", time()).".docx";
        $action['href'] = "info.php?uid=".$usr['id'];
        $action['text'] = "Вернуться к информационной карте";
    }
    else if ($_GET['sysdoc'] == "2") {
        $docname = "Заявление на изменение данных доступа к ЕИС";
        $doc = $db->select('pdata_docs', "id = '" . $_GET['id'] . "'");
        $usr = $db->select('users', "id = '" . $doc['user_id'] . "'");
        $gr = $db->select('groups', "id = '" . $usr['group_id'] . "'");
        $docdata = "ID соглашения - " . $_GET['id'] . ", пользователь - " . $usr['f'] . " " . $usr['i'] . " " . $usr['o'] . " (ИД " . $usr['id'] . "), класс - " . $gr['name'];
        $document = new \PhpOffice\PhpWord\TemplateProcessor("print/sys/2/template.docx");
        $document->setValue('docid', $_GET['id']);
        $document->setValue('id', $usr['id']);
        $document->setValue('fio', $usr['f'] . ' ' . $usr['i'] . ' ' . $usr['o']);
        $document->setValue('group', $gr['name']);
        date_default_timezone_set("GMT");
        $document->setValue('date', date("d.m.Y", time()));
        $document->setValue('datetime', date("d.m.Y H:m:s", time()));
        date_default_timezone_set($tool->getGlobal('tz'));
        $document->saveAs("print/sys/2/Zayavlenie-" . $_GET['id'] . "-".date("d-m-Y-H-m-s", time()).".docx");
        $link = 'https://' . $_SERVER['SERVER_NAME'] . "/print/sys/2/Zayavlenie-" . $_GET['id'] . "-".date("d-m-Y-H-m-s", time()).".docx";
        $action['href'] = "info.php?uid=".$usr['id'];
        $action['text'] = "Вернуться к информационной карте";
    }
}

if (isset($_GET['customdoc'])){
    $docinfo = $db->select('forms', "id = '".$_GET['customdoc']."'");
    $document = new \PhpOffice\PhpWord\TemplateProcessor("print/custom/".$_GET['customdoc']."/template.docx");
    $docname = $docinfo['name'];
    if($docinfo['type'] == "user"){
        $usr = $db->select('users', "id = '".$_GET['id']."'");
        $grp = $db->select('groups', "id = '".$usr['group_id']."'");
        $glob = $db->select_fs('globals', "field != ''");
        date_default_timezone_set("GMT");
        $document->setValue('date', date("d.m.Y", time()));
        $document->setValue('u_id', $usr['id']);
        $document->setValue('u_f', $usr['f']);
        $document->setValue('u_i', $usr['i']);
        $document->setValue('u_o', $usr['o']);
        $document->setValue('u_dr', date("d.m.Y", strtotime($usr['birthday'] . " GMT")));
        $document->setValue('gr_id', $grp['id']);
        $document->setValue('gr_name', $grp['name']);
        foreach($glob as $gl) {
            $document->setValue('global_'.$gl['field'], $gl['value']);
        }
        $pda = $db->select_fs('pdata_fields', "id != 0");
        foreach($pda as $pd){
            $val = $db->select('pdata', "field_id = '".$pd['id']."' AND eis_id = '".$usr['id']."'");
            $document->setValue('pd_'.$pd['id'], $val['data']);
        }
        $docdata = "Пользователь: ".$usr['f']." ".$usr['i']." ".$usr['o']." (".$usr['id'].")";
        date_default_timezone_set($tool->getGlobal('tz'));
        $document->saveAs("print/custom/".$_GET['customdoc']."/Custom-".$_GET['customdoc']."-" . $_GET['id'] . "-".date("d-m-Y-H-m-s", time()).".docx");
        $link = 'https://' . $_SERVER['SERVER_NAME'] . "/print/custom/".$_GET['customdoc']."/Custom-".$_GET['customdoc']."-" . $_GET['id'] . "-".date("d-m-Y-H-m-s", time()).".docx";
        $action['href'] = "info.php?uid=".$usr['id'];
        $action['text'] = "Вернуться к информационной карте";
    }
    else if($docinfo['type'] == "group"){
        $grp = $db->select('groups', "id = '".$_GET['id']."'");
        $glob = $db->select_fs('globals', "field != ''");
        date_default_timezone_set("GMT");
        $document->setValue('date', date("d.m.Y", time()));
        $document->setValue('gr_id', $grp['id']);
        $document->setValue('gr_name', $grp['name']);
        foreach($glob as $gl) {
            $document->setValue('global_'.$gl['field'], $gl['value']);
        }
        $cur = $db->select('users', "id = '".$grp['curator_id']."'");
        $document->setValue('cur_f', $cur['f']);
        $document->setValue('cur_i', $cur['i']);
        $document->setValue('cur_o', $cur['o']);
        $document->setValue('cur_in', $cur['f']." ".$cur['i'][0].".".$cur['o'][0].".");
        $usrs = $db->select_fs('users', "group_id = '".$_GET['id']."' AND state = '1' ORDER BY f ASC, i ASC");
        $cusrs = $db->counter('users', "group_id = '".$_GET['id']."' AND state = '1'");
        $document->cloneBlock('row', $cusrs, true, true);
        $i = 1;
        foreach($usrs as $usr){
            $document->setValue('n#'.$i, $i);
            $document->setValue('u_id#'.$i, $usr['id']);
            $document->setValue('u_f#'.$i, $usr['f']);
            $document->setValue('u_i#'.$i, $usr['i']);
            $document->setValue('u_o#'.$i, $usr['o']);
            $document->setValue('u_dr#'.$i, date("d.m.Y", strtotime($usr['birthday'] . " GMT")));
            $pda = $db->select_fs('pdata_fields', "id != 0");
            $j = 0;
            foreach($pda as $pd){
                $val = $db->select('pdata', "field_id = '".$pd['id']."' AND eis_id = '".$usr['id']."'");
                $document->setValue('pd_'.$pd['id'].'#'.$i, $val['data']);
                if($val['data'] != "") $j = $j + 1;
            }
            $document->setValue('u_pdn#'.$i, $j);
            if(count($db->select('pdata_docs', "user_id = '" . $usr['id'] . "' AND state = '1'")) == 0) $document->setValue('u_sogl#'.$i, "Отсутствует");
            else $document->setValue('u_sogl#'.$i, "Имеется");
            $i = $i + 1;
        }
        $docdata = "Группа: (".$grp['id'].") ".$grp['name'];
        date_default_timezone_set($tool->getGlobal('tz'));
        $document->saveAs("print/custom/".$_GET['customdoc']."/Custom-".$_GET['customdoc']."-" . $_GET['id'] . "-".date("d-m-Y-H-m-s", time()).".docx");
        $link = 'https://' . $_SERVER['SERVER_NAME'] . "/print/custom/".$_GET['customdoc']."/Custom-".$_GET['customdoc']."-" . $_GET['id'] . "-".date("d-m-Y-H-m-s", time()).".docx";
        $action['href'] = "myclass.php";
        $action['text'] = "Вернуться к выбору группы";
    }
}

require_once 'includes/header.inc.php';
?>
<html>
<head>
    <title>Печать документа | <?php echo $pname; ?></title>
    <?php require_once 'includes/footer.inc.php'; ?>
</head>
<body>
<br>
<div class="jumbotron">
    <h2 class="display-4">Модуль печати документов</h2>
    <hr class="my-4">
    <?php if ($link == "0") : ?>
        <p class="lead">Запрос на печать не был отправлен.</p>
    <?php else : ?>
        <p class="lead">Документ "<?php echo $docname; ?>" успешно изготовлен.</p>
        <p>Сведения о документе:</p>
        <p style="font-family:'Courier New'"><?php echo $docdata; ?></p>
        <hr class="my-4">
        <a class="btn btn-primary mb-4" href="<?php echo $link; ?>" target="_blank">Скачать документ</a>
    <?php endif ?>
    <?php if($action != []) : ?>
    <a class="btn btn-primary mb-4" href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a>
    <?php endif ?>
</div>
</body>
</html>
