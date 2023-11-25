<?php
//register.php

require_once 'includes/global.inc.php';
$page = "register.php";
require_once 'includes/header.inc.php';
//инициализируем php переменные, которые используются в форме
$username = "";
$password = "";
$password_confirm = "";
$email = "";
$f = "";
$i = "";
$o = "";
$error = "";
$correct = false;

//проверить отправлена ли форма
if (isset($_POST['submit-form'])) {

//получить переменные $_POST
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password-confirm'];
    $email = $_POST['email'];
    $f = $_POST['f'];
    $i = $_POST['i'];
    $o = $_POST['o'];
//инициализировать переменные для проверки формы
    $success = true;
    $userTools = new UserTools();

//проверить правильность заполнения формы
//проверить не занят ли этот логин
    if ($userTools->checkUsernameExists($username)) {
        $error = '<div class="alert alert-danger" role="alert">Логин уже существует.</div>';
        $success = false;
    }


    if ($success) {
//подготовить информацию для сохранения объекта нового пользователя
        $data['username'] = $username;
        $data['password'] = md5($password); //зашифровать пароль для хранения
        $data['email'] = $email;
        $data['f'] = $f;
        $data['i'] = $i;
        $data['o'] = $o;

//создать новый объект пользователя
        $newUser = new User($data);

//сохранить нового пользователя в БД
        $newUser->save(true);

//редирект на страницу приветствия
        $error = '<br><div class="alert alert-success" role="alert">Регистрация прошла успешно.<br>Для входа <a href="login.php">перейдите по кнопке "Вход"</a></div>';
        $correct = true;
    }
}

//Если форма не отправлена или не прошла проверку, тогда показать форму снова

?>
<html>
<head>
    <title>Регистрация | <?php echo $pname; ?></title>
</head>
<body>
<?php if ($error) echo $error;
?>
<?php if ($correct != true) : ?>
    <form class="md-form border border-light p-5" action="register.php" method="post">

        <p class="h4 mb-4 text-center">Регистрация</p>

        <input type="text" class="form-control" placeholder="Фамилия" name="f">

        <input type="text" class="form-control" placeholder="Имя" name="i">

        <input type="text" class="form-control mb-4" placeholder="Отчество" name="o">

        <input type="text" class="form-control" placeholder="Логин" name="username">

        <input type="email" class="form-control mb-4" placeholder="E-mail" name="email">

        <input type="password" class="form-control" placeholder="Пароль" name="password">

        <input type="password" class="form-control" placeholder="Повторите пароль" name="password-confirm">

        <button class="btn btn-info my-4 btn-block" type="submit" name="submit-form">Зарегистрироваться</button>

        <hr>

        <p>Нажимая кнопку
            <em>Зарегистрироваться</em>, вы соглашаетесь с
            <a href="terms.php" target="_blank">условиями работы в системе</a>.
        </p>
        </div>
    </form>
<?php endif; ?>
</body>
<?php require_once 'includes/footer.inc.php'; ?>
</html>