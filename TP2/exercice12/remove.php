<?php
require_once "Model.php";
require_once "functions.php";

// Exercice 11, question 2 : suppression du prix dont l'id est donné dans l'URL
$model = Model::get_model();

if (!isset($_GET['id']) || !is_strictly_positive_integer($_GET['id'])) {
    $message = 'There is no id in the url.';
} elseif ($model->get_nobel_prize_informations((int) $_GET['id']) === false) {
    $message = 'There is no nobel prize with such id.';
} else {
    $model->remove_nobel_prize((int) $_GET['id']);
    $message = 'The nobel prize has been removed.';
}

$title = "Remove a Nobel Prize";
require "begin.html";
?>
<h1> Remove a Nobel Prize </h1>

<p><?php echo $message; ?></p>

<?php require "end.html"; ?>
