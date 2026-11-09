<?php
// Exercice 2 : les 25 derniers prix (la page last25.php du TP2, transformée en vue)
$title = "List of the last 25 Nobel prizes";
require "view_begin.php";
?>

<h1> List of the last 25 Nobel prizes </h1>

<?php
// Exercice 7 : le tableau est maintenant dans view_list_nobel.php, partagé avec les vues pagination et search
require "view_list_nobel.php";
?>

<?php require "view_end.php"; ?>
