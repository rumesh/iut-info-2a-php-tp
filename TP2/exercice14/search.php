<?php
require_once "Model.php";
require_once "functions.php";
session_start();

// Exercice 14, question 3 : résultats paginés de la recherche
$model = Model::get_model();

// Formulaire envoyé : on enregistre les filtres dans la session, pour les
// retrouver quand on passe d'une page de résultats à l'autre.
if (isset($_POST['Search'])) {
    $filters = array();

    if (get_text('Name') !== null) {
        $filters['Name'] = get_text('Name');
    }

    if (isset($_POST['Year'], $_POST['SignYear']) && is_strictly_positive_integer($_POST['Year'])) {
        $filters['Year'] = (int) $_POST['Year'];
        $filters['SignYear'] = $_POST['SignYear'];
    }

    // Question 4 : on ne garde que les catégories qui existent dans la base
    if (isset($_POST['Categories']) && is_array($_POST['Categories'])) {
        $filters['Categories'] = array_values(array_intersect($model->get_categories(), $_POST['Categories']));
    }

    $_SESSION['filters'] = $filters;
}

$filters = isset($_SESSION['filters']) ? $_SESSION['filters'] : array();

$nb_per_page = 25;
$nb_results = $model->count_search_results($filters);
$nb_pages = (int) ceil($nb_results / $nb_per_page);

$page = 1;
if (isset($_GET['page']) && is_strictly_positive_integer($_GET['page'])) {
    $page = (int) $_GET['page'];
}

$title = "Search results, page $page";
require "begin.html";
?>
<h1> Search results </h1>

<p><?php echo $nb_results; ?> nobel prize(s) found. <a href="form_search.php">New search</a></p>

<?php if ($nb_results > 0 && $page > $nb_pages) { ?>

    <p>This page does not exist.</p>

<?php } elseif ($nb_results > 0) { ?>

    <?php
    $nobels = $model->search_nobel_prizes($filters, ($page - 1) * $nb_per_page, $nb_per_page);
    require "list_table.php";
    display_pages_links($page, $nb_pages, 'search.php');
    ?>

<?php } ?>

<?php require "end.html"; ?>
