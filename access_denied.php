<?php
//index.php 
require_once 'includes/global.inc.php';
$page = "access_denied.php";
require_once 'includes/header.inc.php';
?>
<html>
<head>
    <title>Доступ запрещен! | <?php echo $pname; ?></title>
</head>
<body>
<center>
    <br>
    <br><br>
    <div class="alert alert-danger">
        <h2>Доступ запрещен!</h2>
    </div>
</center>
</body>
<?php require_once 'includes/footer.inc.php'; ?>
</html>