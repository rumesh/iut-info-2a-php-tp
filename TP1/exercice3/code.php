<?php

$title = "Exercice 3";
require "debut_code_html.php";

// Chaque page commence par un titre principal (h1) : les lecteurs d'écran s'en servent pour se repérer
echo '<h1>' . $title . '</h1>';


$chats = 3;
$chiens = 2;

// Version avec guillemets simples : pas d'interpolation, on concatène (.)
echo '<p>J\'ai ' . $chats . ' chats et ' . $chiens . ' chiens, ce qui me fait ' . ($chats + $chiens) . ' animaux</p>';

// Version avec guillemets doubles : les variables sont interpolées directement
echo "<p>J'ai $chats chats et $chiens chiens, ce qui me fait " . ($chats + $chiens) . " animaux</p>";

require "fin_code_html.php";
