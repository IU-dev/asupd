<?php
//index.php 
require_once 'includes/global.inc.php';
$page = "news.php";
require_once 'includes/header.inc.php';
?>
<html>
<head>
    <title>Новости системы | <?php echo $pname; ?></title>
    <?php require_once 'includes/footer.inc.php'; ?>
</head>
<body>
    <br>
    <h1>Новости</h1><br>
    <div class="grid">
        <?php
    $news = $db->select_desc_fs_news("news", "display = '1'");
    foreach ($news as $article) {
        echo '<div class="grid-item"><div class="card"><div class="view overlay">';
        echo '</div>';
        echo '<div class="card-body"><h4 class="card-title">' . $article['header'] . '</h4><hr>';
        echo '<p class="card-text">' . $article['text'] . '</p>';
        echo '<hr><small>' . $article['footer'].'</small>';
        echo '</div></div></div>';
    }
    ?>
    </div>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>
<script type="text/javascript">
    $('.grid').masonry({
        // options
        itemSelector: '.grid-item',
        columnWidth: 300
    });
</script>
</body>
</html>