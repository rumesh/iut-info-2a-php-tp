<?php
require_once __DIR__ . '/../config.php';

$title = "Exercice 4 - Informations sur un prix";
require "debut_code_html.php";

if (!isset($_GET['id'])) {
    echo '<p>Aucun identifiant</p>';
} else {
    $bd = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête préparée : l'id venant de l'URL n'est jamais collé dans le SQL
    $ligne = false;
    if (is_string($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
        $req = $bd->prepare('SELECT * FROM nobels WHERE id = :id');
        $req->bindValue(':id', (int) $_GET['id'], PDO::PARAM_INT);
        $req->execute();
        $ligne = $req->fetch(PDO::FETCH_ASSOC);
    }

    // fetch() renvoie false si aucune ligne ne correspond
    if ($ligne === false) {
        echo '<p>Identifiant invalide</p>';
    } else {
        echo '<ul>';
        foreach ($ligne as $colonne => $valeur) {
            echo '<li><strong>' . $colonne . '</strong> : ' . htmlspecialchars((string) $valeur) . '</li>';
        }
        echo '</ul>';
    }
}
?>

<form action="infos.php" method="get">
    <label for="id">Identifiant :</label>
    <input type="text" id="id" name="id" />
    <input type="submit" value="Afficher" />
</form>

<?php require "fin_code_html.php"; ?>
