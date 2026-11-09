<?php
// Exercice 7, question 1 : tous les prix, 25 par page
$title = "List of all the Nobel prizes, page " . $start;
require "view_begin.php";
?>

<h1> List of all the Nobel prizes </h1>

<?php
require "view_list_nobel.php";
require "view_pages_links.php";
?>

<?php require "view_end.php"; ?>
