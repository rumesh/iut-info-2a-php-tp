<?php
require_once __DIR__ . '/../config.php';

$title = "Exercice 3 - Les 25 derniers prix (fetchAll)";
require "debut_code_html.php";

$bd = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
$bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$req = $bd->query('SELECT * FROM nobels ORDER BY year DESC LIMIT 25');

// Question 2 : fetchAll() récupère toutes les lignes d'un coup dans un tableau
$nobels = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Les 25 derniers prix Nobel</h1>

<table border="1">
    <tr><th>Nom</th><th>Catégorie</th><th>Année</th></tr>

    <?php foreach ($nobels as $ligne) { ?>
        <tr>
            <td><?php echo htmlspecialchars($ligne['name']); ?></td>
            <td><?php echo htmlspecialchars($ligne['category']); ?></td>
            <td><?php echo $ligne['year']; ?></td>
        </tr>
    <?php } ?>
</table>

<p><a href="last.php">Voir la version avec fetch</a></p>

<?php require "fin_code_html.php"; ?>
