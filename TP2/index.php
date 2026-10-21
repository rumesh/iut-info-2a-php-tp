<?php
/**
 * Page d'accueil du TP2 (http://localhost:8080 avec Docker).
 *
 * Elle liste les dossiers "exercice1", "exercice2"... et les pages PHP de
 * chacun. Les dossiers créés par l'élève (ex. "mon_tp") sont listés à part.
 */

// Fichiers qui ne sont pas des pages à ouvrir : morceaux inclus et classes
$notRunnableFiles = array(
    'debut_code_html.php',
    'fin_code_html.php',
    'Model.php',
    'functions.php',
    'list_table.php',
);

$exercises = array();
$otherFolders = array();

foreach (scandir(__DIR__) as $entry) {
    if ($entry[0] === '.' || $entry === 'docker' || !is_dir(__DIR__ . '/' . $entry)) {
        continue;
    }

    if (preg_match('/^exercice\d+$/', $entry)) {
        $pages = array();
        foreach (glob(__DIR__ . '/' . $entry . '/*.php') as $filePath) {
            if (!in_array(basename($filePath), $notRunnableFiles)) {
                $pages[] = basename($filePath);
            }
        }
        $exercises[$entry] = $pages;
    } else {
        $otherFolders[] = $entry;
    }
}

// Tri "naturel" : exercice2 avant exercice10
uksort($exercises, 'strnatcmp');
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>TP2 — Les prix Nobel</title>
<style>
    body { font-family: "Segoe UI", Roboto, sans-serif; max-width: 50rem; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; }
    h2 { font-size: 1rem; margin: 1.2rem 0 0.3rem; }
    ul { margin: 0; }
    code { background: #eee; padding: 0 0.3rem; border-radius: 3px; }
</style>
</head>
<body>
<main>

<h1>TP2 — Les prix Nobel</h1>

<p>
    Base de données : <a href="http://localhost:8081">Adminer</a>
    (système <code>PostgreSQL</code>, serveur <code>db</code>, utilisateur et
    mot de passe <code>etudiant</code>, base <code>etudiants</code>).
</p>

<?php foreach ($exercises as $folder => $pages) { ?>
    <h2><?php echo ucfirst(str_replace('exercice', 'exercice ', $folder)); ?></h2>
    <ul>
        <?php foreach ($pages as $page) { ?>
            <li><a href="<?php echo $folder . '/' . $page; ?>"><?php echo $page; ?></a></li>
        <?php } ?>
    </ul>
<?php } ?>

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
