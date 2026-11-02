<?php
require_once "Model.php";

// Exercice 7 : les 25 derniers prix, récupérés grâce à la classe Model
$nobels = Model::get_model()->get_last();

$title = "List of the last 25 Nobel prizes";
require "begin.html";
?>
<h1> List of the last 25 Nobel prizes </h1>

<?php
// Exercice 13 : le tableau est maintenant dans list_table.php, partagé avec list_all.php
require "list_table.php";
?>

<?php require "end.html"; ?>
