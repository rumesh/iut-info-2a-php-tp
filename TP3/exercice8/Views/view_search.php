<?php
// Exercice 8, question 3 : les résultats de la recherche, 25 par page
$title = "Search results, page " . $start;
require "view_begin.php";
?>

<h1> Search results </h1>

<p> <?= e($nb_results) ?> nobel prize(s) found. <a href="?controller=search">New search</a> </p>

<?php if ($nb_results > 0) { ?>
    <?php
    require "view_list_nobel.php";
    require "view_pages_links.php";
    ?>
<?php } ?>

<?php require "view_end.php"; ?>
