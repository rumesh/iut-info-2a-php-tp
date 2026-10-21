<?php
require_once __DIR__ . '/../config.php';

$title = "Exercice 1 - Création de la base";
require "debut_code_html.php";
?>

<h1>Exercice 1 : création de la base de données</h1>

<!-- Question 1 : lecture du début du script database_nobel.sql -->
<h2>Clés du script</h2>
<ul>
    <li>2 clés primaires : <code>categories(category)</code> et <code>nobels(id)</code></li>
    <li>1 clé étrangère : <code>nobels(category)</code> qui référence <code>categories(category)</code></li>
</ul>

<!-- Question 2 : vérifier que la table contient bien 839 prix -->
<h2>Vérification</h2>
<p>Requête exécutée : <code>SELECT count(*) FROM nobels</code></p>

<?php
try {
    $bd = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nb = $bd->query('SELECT count(*) FROM nobels')->fetchColumn();
    echo '<p>La table <code>nobels</code> contient <strong>' . $nb . '</strong> prix Nobel.</p>';
} catch (PDOException $e) {
    echo '<p>Connexion à la base impossible : ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>

<?php require "fin_code_html.php"; ?>
