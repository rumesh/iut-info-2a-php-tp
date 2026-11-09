<?php
// Exercice 1, question 3 : la page d'accueil du TP2, transformée en vue.
// $nb_nobels est fourni par l'action home du contrôleur home.
require "view_begin.php";
?>

<h1> List of the nobel prizes </h1>

<p> Welcome to the website listing the different nobel prizes given until 2010. It references <strong><?= e($nb_nobels) ?></strong> nobel prizes. You can modify this list by adding new Nobel Prizes, and removing or updating the information contained in this database. You can also search among the nobel prizes on the name, the year and the different categories. </p>

<?php require "view_end.php"; ?>
