<?php

require_once 'includes/global.inc.php';
$page = "zachisleniye.php";

$display = 0;

$msg = "";


if (isset($_POST['submit'])) {
    $z = $db->select('applic_1class', "rf = '".$_POST['familiya']."' AND seria = '".$_POST['seriya']."' AND nomer = '".$_POST['nomer']."'");
    if(isset($z['period_id'])){
        $display = 1;
    }
    else $msg = "1";
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Заявление в 1 класс | <?php echo $pname; ?></title>
</head>
<body>
    <?php if ($display == "0") : ?>
        <form id="form" class="md-form border border-light p-5" action="zachisleniye.php" method="post">
            <p class="h4 mb-4 text-center">Просмотр заявления</p>
            Для просмотра заявления необходимо ввести следующие данные <strong>свидетельства о рождении ребёнка</strong>:
            <br><br>
            <div class="row">
                <div class="col-md-6">
                    <input type="text" id="f1_fam" name="familiya" class="form-control mb-4"
                           placeholder="Фамилия ребёнка">
                </div>
                <div class="col-md-3">
                    <input type="text" id="f1_ser" name="seriya" class="form-control mb-4" placeholder="Серия" pattern="(^[IVXLCDM]{1,6}[\-][А-Я]{2}$)">
                </div>
                <div class="col-md-3">
                    <input type="text" id="f1_nom" name="nomer" class="form-control mb-4" placeholder="Номер" pattern="(^[0-9]{6}$)">
                </div>
            </div>
            <button class="btn btn-info btn-block" type="submit" name="submit">Проверить</button>
        </form>
    <?php if($msg != "") : ?>
    <div class="alert alert-danger">
        <strong>Ошибка запроса!</strong><br>
        Запрашиваемые данные отсутствуют в базе. Проверьте правильность ввода.
    </div>
    <?php endif ?>
    <?php else : ?>
    <br>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h2 class="card-title"><strong>ФИО ребёнка: </strong><?php echo $z['rf']." ".$z['ri']." ".$z['ro'] ?></h2>
                <hr>
                <h5><strong>ФИО заявителя:</strong> <?php echo $z['zf']." ".$z['zi']." ".$z['zo'] ?><br></h5>
                <h5><strong>Серия и номер свидетельства о рождении:</strong> <?php echo $z['seria'] ?> <?php echo $z['nomer'] ?><br></h5>
                <?php date_default_timezone_set('GMT') ?>
                <h5><strong>Дата, время подачи:</strong> <?php echo date("d.m.Y H:i:s" ,  strtotime($z['datetime'] . " GMT")).".".explode(".", $z['datetime'])[1] ?><br></h5>
                <?php date_default_timezone_set($tool->getGlobal('tz')); ?>
                <hr>
                <strong>Канал поступления:</strong> <?php echo $z['canal'] ?><br>
                <strong>Номер заявления (ID):</strong> 2<?php echo $z['external_id'] ?><br>
                <strong>Цель подачи:</strong> <?php if($z['target'] == "1") echo 'приём в 1-й класс'; else echo 'перевод в другую школу'; ?><br>
                <strong>Дата рождения ребёнка:</strong> <?php echo date("d.m.Y" ,  strtotime($z['dr'] . " GMT")) ?><br>
                <strong>Адрес прописки ребёнка:</strong> <?php echo $z['propiska'] ?><br>
                <strong>Тип и категория особого права:</strong>
                <?php
                $period = $db->select('periods', "id = '".$tool->getGlobal('1class_period')."'");
                $dann = json_decode($period['settings'], true);
                $sch = $db->select('applic_1class_priority', "id = '".$dann['1class_priority_scheme_id']."'");
                $dann = json_decode($sch['scheme'], true);
                echo $dann[$z['spec_cat']];
                ?>
                <br>
                <strong>Вариант адаптированной программы:</strong> <?php echo $z['adaptive'] ?><br>
                <strong>Комментарий:</strong> <?php echo $z['comment'] ?>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body"><h6 class="card-title">Место в очереди:</h6>
                        <?php if($z['state'] == "1") : ?>
                        <h1><strong>
                                <?php
                                echo $tool->first_class_order_position($z['id']); ?>
                            </strong> из
                        <?php
                        echo $db->counter('applic_1class', "state = '1' AND period_id = '".$tool->getGlobal('1class_period')."'") ?>
                        </h1>
                        <?php else : ?>
                        <strong>Отсутствует</strong><br>
                        (заявление либо не внесено в реестр, либо отклонено, либо по данному заявлению ребенок зачислен в ОО)
                        <?php endif ?>
                    </div>
                </div>
                <ul class="stepper stepper-vertical">
                    <?php if($z['state'] == "0") : ?>
                        <li class="active">
                            <a href="#!">
                                <span class="circle"><i class="fas fa-check"></i></span>
                                <span class="label">Заявление получено</span>
                            </a>
                            <div class="step-content grey lighten-3">
                                <p>Ваше заявление получено образовательной организацией.</p>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="">
                            <a href="#!">
                                <span class="circle"><i class="fas fa-check"></i></span>
                                <span class="label">Заявление получено</span>
                            </a>
                        </li>
                    <?php endif ?>
                    <?php if($z['state'] == "1") : ?>
                        <li class="active">
                            <a href="#!">
                                <span class="circle"><i class="fas fa-check"></i></span>
                                <span class="label">Заявление принято</span>
                            </a>
                            <div class="step-content grey lighten-3">
                                <p>Ваше заявление успешно зарегистрировано и учтено в реестре.</p>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="">
                            <a href="#!">
                                <span class="circle"><i class="fas fa-check"></i></span>
                                <span class="label">Заявление принято</span>
                            </a>
                        </li>
                    <?php endif ?>
                    <?php if($z['state'] == "2") : ?>
                        <li class="active warning">
                            <a href="#!">
                                <span class="circle"><i class="fas fa-exclamation"></i></span>
                                <span class="label">Отказано</span>
                            </a>
                            <div class="step-content grey lighten-3">
                                <p>По данному заявлению принято решение об отказе в зачислении.</p>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="">
                            <a href="#!">
                                <span class="circle"><i class="fas fa-exclamation"></i></span>
                                <span class="label">Отказано</span>
                            </a>
                        </li>
                    <?php endif ?>
                    <?php if($z['state'] == "3") : ?>
                        <li class="active success">
                            <a href="#!">
                                <span class="circle"><i class="fas fa-check-circle"></i></span>
                                <span class="label">Зачислен</span>
                            </a>
                            <div class="step-content grey lighten-3">
                                <p>Ребёнок зачислен в МБОУ "ИТ-лицей №24".</p>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="">
                            <a href="#!">
                                <span class="circle"><i class="fas fa-check-circle"></i></span>
                                <span class="label">Зачислен</span>
                            </a>
                        </li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif ?>
    <?php require_once 'includes/footer.inc.php'; ?>
    <script>

        $(document).ready(function () {
            $('.mdb-select').materialSelect();
        });

        $('.datepicker').pickadate();

        $(document).ready(function () {
            $('#participants').DataTable();
            $('.dataTables_length').addClass('bs-select');
        });


        $(function() {
            //задание заполнителя с помощью параметра placeholder
            //задание заполнителя с помощью параметра placeholder
            $("#f1_nom").mask("999999", {placeholder: " " });
            $("#f1_ser").mask("A-AA", {placeholder: " " });
        });

    </script>
</body>
</html>