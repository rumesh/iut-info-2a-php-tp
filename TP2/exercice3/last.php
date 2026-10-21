<?php
require_once __DIR__ . '/../config.php';

$title = "Exercice 3 - Les 25 derniers prix (fetch)";
require "debut_code_html.php";

$bd = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
$bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$req = $bd->query('SELECT * FROM nobels ORDER BY year DESC LIMIT 25');
?>

<h1>Les 25 derniers prix Nobel</h1>

<table border="1">
    <tr><th>Nom</th><th>Catégorie</th><th>Année</th></tr>

    <?php
    // Question 1 : fetch() renvoie une ligne à chaque appel, puis false quand il n'y en a plus
    while ($ligne = $req->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($ligne['name']) . '</td>';
        echo '<td>' . htmlspecialchars($ligne['category']) . '</td>';
        echo '<td>' . $ligne['year'] . '</td>';
        echo '</tr>';
    }
    ?>
</table>

<p><a href="last_fetchall.php">Voir la version avec fetchAll</a></p>

<?php require "fin_code_html.php"; ?>
