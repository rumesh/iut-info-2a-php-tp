<?php
/**
 * Page d'accueil du TP3 (http://localhost:8080 avec Docker).
 *
 * Elle liste les dossiers "exercice1", "exercice2"... : chacun contient
 * l'application MVC telle qu'elle est à la fin de l'exercice, et s'ouvre par
 * son index.php. Les dossiers créés par l'élève (ex. "MVC") sont listés à part.
 */

$exercises = array();
$otherFolders = array();

foreach (scandir(__DIR__) as $entry) {
    if ($entry[0] === '.' || !is_dir(__DIR__ . '/' . $entry)) {
        continue;
    }

    if (preg_match('/^exercice\d+$/', $entry)) {
        $exercises[] = $entry;
    } else {
        $otherFolders[] = $entry;
    }
}

// Tri "naturel" : exercice2 avant exercice10
natsort($exercises);
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>TP3 — Application MVC</title>
<style>
    body { font-family: "Segoe UI", Roboto, sans-serif; max-width: 50rem; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; }
    h2 { font-size: 1rem; margin: 1.2rem 0 0.3rem; }
    ul { margin: 0; }
    code { background: #eee; padding: 0 0.3rem; border-radius: 3px; }
</style>
</head>
<body>
<main>

<h1>TP3 — Application MVC</h1>

<p>
    Base de données : <a href="http://localhost:8081">Adminer</a>
    (système <code>PostgreSQL</code>, serveur <code>db</code>, utilisateur et
    mot de passe <code>etudiant</code>, base <code>etudiants</code>).
</p>

<h2>Corrigés</h2>
<ul>
    <?php foreach ($exercises as $folder) { ?>
        <li><a href="<?php echo $folder; ?>/"><?php echo ucfirst(str_replace('exercice', 'exercice ', $folder)); ?></a></li>
    <?php } ?>
</ul>

<?php if (count($otherFolders) > 0) { ?>
    <h2>Autres dossiers</h2>
    <ul>
        <?php foreach ($otherFolders as $folder) { ?>
            <li><a href="<?php echo htmlspecialchars($folder); ?>/"><?php echo htmlspecialchars($folder); ?></a></li>
        <?php } ?>
    </ul>
<?php } ?>

</main>
</body>
</html>
