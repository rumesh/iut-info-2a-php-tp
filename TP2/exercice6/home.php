<?php
require_once "Model.php";

// Exercice 6 : le nombre de prix Nobel, affiché à la place de TO FILL
$nb = Model::get_model()->get_nb_nobel_prizes();

require "begin.html";
?>
<h1> List of the nobel prizes </h1>

<p> Welcome to the website listing the different nobel prizes given until 2010. It references <strong><?php echo $nb; ?></strong> nobel prizes. You can modify this list by adding new Nobel Prizes, and removing or updating the information contained in this database. You can also search among the nobel prizes on the name, the year and the different categories. </p>


<?php require "end.html"; ?>
