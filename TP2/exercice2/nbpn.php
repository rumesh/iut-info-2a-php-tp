<?php
require_once __DIR__ . '/../config.php';

$title = "Exercice 2 - Nombre de prix Nobel";
require "debut_code_html.php";

// Connexion à la base : en cas de problème, PDO lève une exception
$bd = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
$bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// La requête ne renvoie qu'une ligne d'une seule colonne : fetchColumn() la lit directement
$req = $bd->query('SELECT count(*) FROM nobels');
$nb = $req->fetchColumn();
?>

<p>La base de données contient <strong><?php echo $nb; ?></strong> prix Nobel.</p>

<?php require "fin_code_html.php"; ?>
