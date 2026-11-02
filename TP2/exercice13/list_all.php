<?php
require_once "Model.php";
require_once "functions.php";

// Exercice 13 : la liste de tous les prix, 25 par page
$model = Model::get_model();
$nb_per_page = 25;
$nb_pages = (int) ceil($model->get_nb_nobel_prizes() / $nb_per_page);

// Page absente ou invalide : on affiche la première
$page = 1;
if (isset($_GET['page']) && is_strictly_positive_integer($_GET['page'])) {
    $page = (int) $_GET['page'];
}

$title = "List of all the Nobel prizes, page $page";
require "begin.html";
?>
<h1> List of all the Nobel prizes </h1>

<?php if ($page > $nb_pages) { ?>

    <p>This page does not exist.</p>

<?php } else { ?>

    <?php
    $nobels = $model->get_nobel_prizes_with_limit(($page - 1) * $nb_per_page, $nb_per_page);
    require "list_table.php";
    display_pages_links($page, $nb_pages, 'list_all.php');
    ?>

<?php } ?>

<?php require "end.html"; ?>
