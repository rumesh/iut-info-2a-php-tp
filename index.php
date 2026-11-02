<?php
/**
 * Page d'accueil du TP.
 *
 * Elle affiche trois zones côte à côte :
 *   - à gauche   : la liste des exercices déjà corrigés, avec les pages disponibles pour chacun
 *   - au centre  : le RÉSULTAT de l'exercice choisi, dans une iframe
 *   - à droite   : le CODE SOURCE, coloré, des fichiers de cet exercice
 *
 * La séparation entre le résultat et le code se déplace à la souris ou au
 * clavier, et l'on peut aussi n'afficher que l'un des deux, en pleine largeur.
 *
 * Aucune liste n'est écrite "en dur" : la page regarde le contenu du dossier
 * dans lequel elle se trouve, repère les dossiers de TP ("TP1", "TP2"...) puis,
 * dans chacun, les dossiers "exercice1", "exercice2"... et construit les liens
 * automatiquement. Dès qu'un nouveau dossier d'exercice ou de TP est ajouté
 * (ou récupéré depuis GitHub), il apparaît ici tout seul.
 *
 * Le code est volontairement découpé en 4 étapes bien visibles :
 *   1. Configuration   : les réglages que l'on peut modifier
 *   2. Functions       : les outils réutilisables
 *   3. Processing      : on construit la liste et on lit le choix de l'élève
 *   4. Display         : le HTML, tout en bas
 */


/* =====================================================================
 * ÉTAPE 1 — CONFIGURATION
 * ===================================================================== */

// Dossier dans lequel se trouve CE fichier (index.php).
// __DIR__ est une "constante magique" de PHP : elle vaut toujours le chemin
// du dossier du fichier en cours, quel que soit l'endroit d'où on l'appelle.
$rootFolder = __DIR__;

// Les dossiers de TP commencent par ce préfixe, suivi d'un numéro (TP1, TP2...).
$tpPrefix = 'TP';

// Dans chaque TP, les dossiers d'exercice commencent par ce préfixe, suivi d'un numéro.
$folderPrefix = 'exercice';

// Fichiers PHP qui ne sont PAS des pages à ouvrir dans le navigateur :
//  - debut_code_html.php / fin_code_html.php : morceaux de page inclus (require)
//  - TODOList.php, Model.php                 : des classes, pas des pages
//  - functions.php, list_table.php           : des fonctions et un morceau de page (TP2)
// Ils restent visibles dans la colonne "code source" : on ne cache que le lien
// qui servirait à les exécuter tout seuls.
$notRunnableFiles = array(
    'debut_code_html.php',
    'fin_code_html.php',
    'TODOList.php',
    'Model.php',
    'functions.php',
    'list_table.php',
);

// Page ouverte par défaut quand l'exercice en contient une de ce nom
// (l'accueil du site des prix Nobel, dans le TP2). Sinon : la première page.
$defaultPage = 'home.php';

// Les panneaux que l'on peut afficher à droite de la liste : le résultat et
// le code côte à côte (le premier, choisi par défaut), ou l'un des deux seul.
$views = array(
    'both'   => 'Résultat + code',
    'result' => 'Résultat seul',
    'source' => 'Code seul',
);

// Couleurs utilisées par highlight_file() pour colorer le code source.
// PHP utilise par défaut des couleurs prévues pour un fond blanc : on les
// remplace ici par des couleurs lisibles sur le fond sombre de la page.
ini_set('highlight.html',    '#d4d4d4'); // le HTML, hors des balises PHP
ini_set('highlight.default', '#9cdcfe'); // les variables, les fonctions...
ini_set('highlight.keyword', '#569cd6'); // if, foreach, echo, function...
ini_set('highlight.string',  '#ce9178'); // le contenu des guillemets
ini_set('highlight.comment', '#6a9955'); // les commentaires


/* =====================================================================
 * ÉTAPE 2 — FUNCTIONS
 * ===================================================================== */

/**
 * Protège un texte avant de l'écrire dans la page HTML.
 * Indispensable dès qu'on affiche une donnée qui ne vient pas de nous
 * (ici : des noms de fichiers et des valeurs reçues dans l'URL).
 */
function escape($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Construit une adresse vers cette même page, avec les paramètres choisis.
 * http_build_query() se charge d'assembler et d'encoder le "?a=1&b=2".
 */
function buildUrl($parameters)
{
    return escape('index.php?' . http_build_query($parameters));
}

/**
 * Retourne la liste des sous-dossiers dont le nom est le préfixe suivi d'un
 * numéro, triés dans l'ordre des numéros (exercice2 avant exercice10).
 * Sert à trouver les dossiers de TP, puis les exercices de chaque TP.
 *
 * @return array ex. array('exercice1', 'exercice2', 'exercice10')
 */
function findNumberedFolders($parentFolder, $folderPrefix)
{
    $folders = array();

    // scandir() liste TOUT ce que contient le dossier : fichiers ET dossiers.
    $entries = scandir($parentFolder);

    foreach ($entries as $entry) {

        // On ne garde que les vrais dossiers (on ignore index.php, .git, etc.).
        if (!is_dir($parentFolder . '/' . $entry)) {
            continue;
        }

        // On ne garde que ceux qui s'appellent "exercice" (ou "TP") suivi d'un numéro.
        // Le \d+ du motif signifie "un ou plusieurs chiffres".
        if (!preg_match('/^' . $folderPrefix . '\d+$/', $entry)) {
            continue;
        }

        $folders[] = $entry;
    }

    // Tri "naturel" : sans lui, PHP trierait comme du texte et placerait
    // "exercice10" juste après "exercice1".
    natsort($folders);

    // natsort() conserve les anciens indices : array_values() les renumérote.
    return array_values($folders);
}

/**
 * Retourne tous les fichiers PHP d'un dossier, puis ceux de ses sous-dossiers
 * (le TP3 range son code dans Controllers/, Models/, Views/...) : ce sont les
 * fichiers dont on affichera le code source.
 *
 * @return array ex. array('index.php', 'Controllers/Controller.php', ...)
 */
function listSourceFiles($folderPath)
{
    $files = array();

    // glob() récupère directement tous les fichiers dont le nom finit par .php
    foreach (glob($folderPath . '/*.php') as $filePath) {

        // basename() ne garde que le nom du fichier, sans le chemin.
        $files[] = basename($filePath);
    }

    sort($files);

    // Les fichiers des sous-dossiers, précédés du nom de leur sous-dossier
    // (ex. "Views/view_home.php"), sont placés après ceux du dossier.
    $subFolderFiles = array();

    foreach (glob($folderPath . '/*/*.php') as $filePath) {
        $subFolderFiles[] = basename(dirname($filePath)) . '/' . basename($filePath);
    }

    sort($subFolderFiles);

    return array_merge($files, $subFolderFiles);
}

/**
 * Parmi les fichiers d'un dossier, retourne ceux qui sont de vraies pages,
 * c'est-à-dire celles que l'on peut ouvrir directement dans le navigateur.
 *
 * @return array ex. array('code.php')
 */
function keepRunnableFiles($files, $notRunnableFiles)
{
    $pages = array();

    foreach ($files as $fileName) {

        // in_array() vérifie si le nom fait partie des fichiers non exécutables.
        if (in_array($fileName, $notRunnableFiles)) {
            continue;
        }

        // Les fichiers d'un sous-dossier ne s'ouvrent pas seuls : dans le
        // TP3, une vue ou un contrôleur ne s'affiche qu'à travers index.php.
        if (strpos($fileName, '/') !== false) {
            continue;
        }

        $pages[] = $fileName;
    }

    return $pages;
}

/**
 * Extrait le numéro contenu dans un nom de dossier.
 * "exercice10" devient 10.
 */
function getExerciseNumber($folderName, $folderPrefix)
{
    return (int) substr($folderName, strlen($folderPrefix));
}

/**
 * Choisit une valeur SÛRE parmi une liste autorisée.
 *
 * Règle de sécurité fondamentale : une valeur qui vient de l'URL ($_GET) ne
 * doit jamais être utilisée telle quelle dans un chemin de fichier. Sans cette
 * vérification, un visiteur pourrait demander "../../un/autre/fichier" et lire
 * n'importe quel fichier du serveur.
 *
 * @param  string|null $requested      la valeur demandée dans l'URL
 * @param  array       $allowedValues  la seule liste de valeurs acceptées
 * @param  string|null $fallback       valeur à utiliser si la demande est invalide
 * @return string|null
 */
function chooseFromList($requested, $allowedValues, $fallback = null)
{
    if ($requested !== null && in_array($requested, $allowedValues)) {
        return $requested;
    }

    if ($fallback !== null && in_array($fallback, $allowedValues)) {
        return $fallback;
    }

    // Sinon : la première valeur de la liste, ou rien si la liste est vide.
    return count($allowedValues) > 0 ? $allowedValues[0] : null;
}

/**
 * Lit un paramètre de l'URL, ou null s'il n'a pas été transmis.
 */
function readParameter($name)
{
    return isset($_GET[$name]) ? $_GET[$name] : null;
}


/* =====================================================================
 * ÉTAPE 3 — PROCESSING
 * ===================================================================== */

/* --- 3a. On construit la liste des exercices ------------------------- */

// $exercises est un tableau associatif : la clé est le chemin du dossier
// (TP + exercice), et la valeur décrit l'exercice :
//   'TP1/exercice9' => array(
//        'tp'     => 'TP1',
//        'number' => 9,
//        'folder' => 'TP1/exercice9',
//        'files'  => array('debut_code_html.php', 'fin_code_html.php', 'nombre.php'),
//        'pages'  => array('nombre.php'),
//   )
// Dans le TP3, 'files' contient aussi les sous-dossiers : 'Views/view_home.php'...
$exercises = array();

$tpFolders = findNumberedFolders($rootFolder, $tpPrefix);

foreach ($tpFolders as $tpName) {

    foreach (findNumberedFolders($rootFolder . '/' . $tpName, $folderPrefix) as $folderName) {

        $folderPath  = $tpName . '/' . $folderName;
        $sourceFiles = listSourceFiles($rootFolder . '/' . $folderPath);

        $exercises[$folderPath] = array(
            'tp'     => $tpName,
            'number' => getExerciseNumber($folderName, $folderPrefix),
            'folder' => $folderPath,
            'files'  => $sourceFiles,
            'pages'  => keepRunnableFiles($sourceFiles, $notRunnableFiles),
        );
    }
}

// count() compte les éléments d'un tableau : utile pour l'affichage du total.
$exerciseCount = count($exercises);
$tpCount       = count($tpFolders);


/* --- 3b. On lit le choix de l'élève dans l'URL ----------------------- */

// Quatre paramètres, tous facultatifs :
//   exercise = le dossier choisi        (ex. TP1/exercice9)
//   page     = la page à exécuter       (ex. nombre.php)
//   file     = le fichier source à lire (ex. debut_code_html.php)
//   view     = les panneaux affichés    (both, result ou source)
$selectedExercise = null;
$selectedPage     = null;
$selectedFile     = null;

$requestedExercise = readParameter('exercise');

// isset() sur le tableau suffit à valider le dossier : s'il n'est pas une clé
// de $exercises, c'est qu'il n'existe pas dans notre liste.
if ($requestedExercise !== null && isset($exercises[$requestedExercise])) {

    $selectedExercise = $exercises[$requestedExercise];

    // La page exécutée : celle demandée si elle est valide, sinon la page par
    // défaut si l'exercice en a une, sinon la première.
    $selectedPage = chooseFromList(
        readParameter('page'),
        $selectedExercise['pages'],
        $defaultPage
    );

    // Le source affiché : celui demandé s'il est valide, sinon par défaut
    // celui de la page en cours d'exécution.
    $selectedFile = chooseFromList(
        readParameter('file'),
        $selectedExercise['files'],
        $selectedPage
    );
}

// Les panneaux affichés : une clé de $views, sinon les deux (la première).
$selectedView = chooseFromList(readParameter('view'), array_keys($views));
$showResult   = $selectedView !== 'source';
$showSource   = $selectedView !== 'result';

// Ce que l'on regarde en ce moment : les liens qui changent seulement les
// panneaux affichés le conservent. http_build_query() ignore les valeurs
// null : ce qui n'a pas encore été choisi n'apparaît pas dans l'adresse.
$currentChoice = array(
    'exercise' => $selectedExercise !== null ? $selectedExercise['folder'] : null,
    'page'     => $selectedPage,
    'file'     => $selectedFile,
);

?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php
    // Le titre de l'onglet dit ce que l'on regarde : c'est aussi la première
    // chose qu'un lecteur d'écran annonce quand la page se charge.
    if ($selectedPage !== null) {
        echo escape($selectedExercise['folder'] . '/' . $selectedPage) . ' — ';
    }
?>TP PHP — Sommaire des exercices</title>
<style>
    /* Accessibilité : toutes les couleurs de texte ci-dessous ont un contraste
       d'au moins 4.5:1 avec leur fond (niveau AA des règles WCAG). */
    :root {
        --bg: #1e1e2e;
        --panel: #252538;
        --code-bg: #14141f;
        --border: #3a3a55;
        --text: #e4e4f0;
        --text-muted: #9a9ac0;
        --accent: #6c8cff;
        --accent-hover: #86a1ff;
        --accent-strong: #3b5bdb;  /* fond des liens actifs : le texte blanc y reste lisible */
        --focus: #ffd166;          /* contour de l'élément qui a le focus clavier */
    }
    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body {
        margin: 0;
        display: flex;
        flex-direction: column;
        font-family: "Segoe UI", Roboto, sans-serif;
        background: var(--bg);
        color: var(--text);
        line-height: 1.5;
    }

    /* --- Navigation au clavier --- */
    /* L'élément qui a le focus doit toujours être bien visible. */
    a:focus-visible,
    summary:focus-visible,
    [tabindex]:focus-visible {
        outline: 3px solid var(--focus);
        outline-offset: 2px;
    }
    /* Lien d'évitement : caché hors de l'écran, il apparaît dès qu'on y arrive
       avec Tab, et permet de sauter directement au contenu. */
    .skip-link {
        position: absolute;
        left: 1rem;
        top: -10rem;
        z-index: 10;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        background: var(--accent-strong);
        color: #fff;
    }
    .skip-link:focus { top: 0.5rem; }
    /* Texte lu par les lecteurs d'écran mais invisible à l'écran. */
    .visually-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip-path: inset(50%);
        white-space: nowrap;
    }

    /* --- En-tête --- */
    header {
        display: flex;
        flex-wrap: wrap;       /* le texte agrandi passe à la ligne au lieu de déborder */
        align-items: baseline;
        column-gap: 1rem;
        padding: 0.7rem 1.25rem;
        background: var(--panel);
        border-bottom: 1px solid var(--border);
    }
    header h1 { font-size: 1.05rem; margin: 0; font-weight: 600; }
    header .subtitle { margin: 0; font-size: 0.875rem; color: var(--text-muted); }

    /* --- Les trois colonnes : la liste (nav), puis le résultat et le code (main) --- */
    .layout {
        display: grid;
        grid-template-columns: 250px 1fr;
        grid-template-rows: minmax(0, 1fr);
        flex: 1;               /* prend toute la hauteur sous l'en-tête */
        min-height: 0;
    }
    main {
        display: grid;
        /* Résultat | séparation | code. La largeur du résultat est une variable :
           le script du bas de page la change quand on déplace la séparation. */
        grid-template-columns: var(--result-width, 50%) auto 1fr;
        grid-template-rows: minmax(0, 1fr);
        min-width: 0;
    }
    /* Un seul panneau affiché : il prend toute la largeur. */
    main.single-panel { grid-template-columns: 1fr; }
    .column {
        display: flex;
        flex-direction: column;
        min-width: 0;          /* sans cela, le code trop large déforme la grille */
        min-height: 0;
        border-left: 1px solid var(--border);
    }
    .layout > .column:first-child { border-left: none; }
    .column-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.4rem 1rem;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        background: var(--panel);
        border-bottom: 1px solid var(--border);
    }
    .column-title h2 { margin: 0; font-size: inherit; font-weight: 600; }
    /* position: relative : les textes cachés (.visually-hidden, en position
       absolue) restent DANS la zone qui défile. Sans cela, ceux du bas de la
       liste dépassaient de l'écran et faisaient défiler toute la page. */
    .column-body { flex: 1; overflow: auto; position: relative; }

    /* --- Colonne 1 : la liste des exercices --- */
    .exercise { padding: 0.55rem 1rem; border-bottom: 1px solid var(--border); }
    .exercise h3 {
        font-size: 0.85rem;
        margin: 0 0 0.35rem;
        color: var(--text-muted);
        font-weight: 600;
    }
    .exercise ul,
    .tabs ul,
    .view-switch ul { list-style: none; margin: 0; padding: 0; }
    .exercise a {
        display: block;
        padding: 0.3rem 0.5rem;
        margin-bottom: 0.2rem;
        border-radius: 5px;
        color: var(--text);
        text-decoration: none;
        font-family: Consolas, monospace;
        font-size: 0.875rem;
    }
    .exercise a:hover { background: var(--border); }
    /* Le lien actif est repéré par aria-current (annoncé "page actuelle" par
       les lecteurs d'écran) : le style s'appuie sur ce même attribut. */
    .exercise a[aria-current] { background: var(--accent-strong); color: #fff; }
    .exercise .empty { font-size: 0.85rem; color: var(--text-muted); font-style: italic; }
    .tp-title {
        padding: 0.45rem 1rem;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--accent);
        background: var(--code-bg);
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        user-select: none;
        list-style: none;              /* on remplace le triangle par défaut... */
    }
    .tp-title::-webkit-details-marker { display: none; }
    .tp-title::before {                /* ...par une flèche qui tourne */
        content: "▸";
        content: "▸" / "";             /* décorative : ignorée par les lecteurs d'écran */
        display: inline-block;
        width: 1em;
        transition: transform 0.15s;
    }
    .tp[open] > .tp-title::before { transform: rotate(90deg); }
    .tp-title:hover { color: var(--accent-hover); }

    /* --- Colonne 2 : le résultat --- */
    /* display: block retire le petit espace laissé sous une iframe "en ligne",
       qui rendait la colonne inutilement défilante. */
    iframe { display: block; width: 100%; height: 100%; border: none; background: #fff; }

    /* --- Colonne 3 : le code source --- */
    .tabs {
        padding: 0.45rem 1rem;
        background: var(--panel);
        border-bottom: 1px solid var(--border);
    }
    .tabs ul,
    .view-switch ul { display: flex; flex-wrap: wrap; gap: 0.3rem; }
    .tabs a,
    .view-switch a {
        display: block;
        padding: 0.25rem 0.55rem;
        border-radius: 5px;
        border: 1px solid var(--border);
        color: var(--text-muted);
        text-decoration: none;
        font-family: Consolas, monospace;
        font-size: 0.85rem;
    }
    .tabs a:hover,
    .view-switch a:hover { color: var(--text); background: var(--border); }
    .tabs a[aria-current],
    .view-switch a[aria-current] { background: var(--accent-strong); border-color: var(--accent-strong); color: #fff; }
    .source { background: var(--code-bg); padding: 0.75rem 1rem; }
    /* Le code peut défiler : la zone reçoit donc le focus (tabindex="0") pour
       qu'on puisse la faire défiler au clavier, avec les flèches. Le contour
       est placé à l'intérieur pour ne pas être coupé par les bords. */
    .source:focus-visible { outline-offset: -3px; }
    /* highlight_file() produit lui-même un <pre> : on ne fait que le styler. */
    .source pre {
        margin: 0;
        font-family: "Fira Code", Consolas, monospace;
        font-size: 0.875rem;
        line-height: 1.55;
    }

    /* --- Les panneaux : leur choix (dans l'en-tête) et leur séparation --- */
    /* Mêmes boutons que les onglets du code, mais poussés à droite de
       l'en-tête et écrits avec la police du texte : ce ne sont pas des fichiers. */
    .view-switch { margin-left: auto; }
    .view-switch a { font-family: inherit; }
    .splitter {
        width: 6px;
        background: var(--border);
        cursor: col-resize;
        touch-action: none;    /* sur écran tactile, le doigt la déplace au lieu de faire défiler */
    }
    .splitter:hover,
    .splitter:focus-visible,
    main.resizing .splitter { background: var(--accent); }
    /* La séparation remplace le trait qui borde la colonne du code. */
    .splitter + .column { border-left: none; }
    /* Pendant le déplacement, l'iframe ne doit pas "avaler" la souris : sinon,
       le mouvement s'arrête dès que le pointeur passe au-dessus du résultat. */
    main.resizing { cursor: col-resize; user-select: none; }
    main.resizing iframe { pointer-events: none; }

    /* --- Message affiché quand rien n'est sélectionné --- */
    .placeholder {
        padding: 2rem 1.5rem;
        color: var(--text-muted);
        font-size: 0.9rem;
    }
    .open-link {
        display: inline-block;
        padding: 0.2rem 0;
        color: var(--accent);
        text-decoration: none;
        font-size: 0.8rem;
    }
    .open-link:hover { color: var(--accent-hover); text-decoration: underline; }

    /* --- Écrans étroits (ou page zoomée) : on empile les trois zones --- */
    @media (max-width: 950px) {
        html, body { height: auto; }
        .layout, main { grid-template-columns: 1fr; grid-template-rows: none; }
        .layout { flex: none; }
        .column { border-left: none; border-top: 1px solid var(--border); }
        .splitter { display: none; }   /* les panneaux sont l'un sous l'autre */
        .column-body { max-height: 70vh; }
        iframe { height: 70vh; }
    }

    /* --- Préférences du système de l'utilisateur --- */
    /* "Réduire les animations" : la flèche des TP ne tourne plus. */
    @media (prefers-reduced-motion: reduce) {
        .tp-title::before { transition: none; }
    }
    /* Mode "contraste élevé" de Windows : le navigateur impose ses propres
       couleurs et supprime les fonds, on encadre donc le lien actif. */
    @media (forced-colors: active) {
        .exercise a[aria-current],
        .tabs a[aria-current],
        .view-switch a[aria-current] { outline: 2px solid Highlight; }
        .splitter { border-inline: 1px solid CanvasText; }
    }
</style>
</head>
<body>

<?php
/* =====================================================================
 * ÉTAPE 4 — DISPLAY
 * ===================================================================== */
?>

<?php
// Accessibilité : la page est découpée en "régions" (<header>, <nav>, <main>,
// <section>) et en titres (h1, h2, h3). Les lecteurs d'écran permettent de
// sauter directement de l'une à l'autre, comme on parcourt la page des yeux.
?>
<a class="skip-link" href="#main">Aller au contenu principal</a>

<header>
    <h1>TP PHP — Sommaire des exercices</h1>
    <p class="subtitle">
        <?php echo $exerciseCount; ?> exercice(s) détecté(s) automatiquement
        dans <?php echo $tpCount; ?> TP
    </p>

    <?php
    // Les panneaux à afficher : les deux côte à côte, ou un seul en pleine
    // largeur. Chaque lien garde l'exercice, la page et le fichier en cours.
    ?>
    <nav class="view-switch" aria-label="Panneaux affichés">
        <ul>
        <?php foreach ($views as $view => $label) { ?>
            <li>
                <a <?php echo $view === $selectedView ? 'aria-current="true"' : ''; ?>
                   href="<?php echo buildUrl(array_merge($currentChoice, array('view' => $view))); ?>">
                    <?php echo escape($label); ?>
                </a>
            </li>
        <?php } ?>
        </ul>
    </nav>
</header>

<div class="layout">

    <?php /* ---------- Colonne 1 : la liste des exercices ---------- */ ?>
    <nav class="column" aria-labelledby="exercises-title">
        <div class="column-title"><h2 id="exercises-title">Exercices</h2></div>
        <div class="column-body">

            <?php if ($exerciseCount === 0) { ?>

                <p class="placeholder">
                    Aucun dossier « <?php echo escape($tpPrefix); ?>N/<?php echo escape($folderPrefix); ?>N »
                    n'a été trouvé à côté de index.php.
                </p>

            <?php } ?>

            <?php foreach ($tpFolders as $tpName) { ?>

                <?php
                // Chaque TP est un bloc repliable (<details>) : un clic sur son
                // titre (<summary>) l'ouvre ou le referme, sans JavaScript.
                // Il est ouvert au départ si aucun exercice n'est choisi, ou
                // s'il contient l'exercice affiché.
                $isOpen = $selectedExercise === null || $selectedExercise['tp'] === $tpName;
                ?>
                <details class="tp" <?php echo $isOpen ? 'open' : ''; ?>>
                <summary class="tp-title"><?php echo escape($tpName); ?></summary>

            <?php foreach ($exercises as $exercise) { ?>

                <?php
                // On n'affiche ici que les exercices de ce TP.
                if ($exercise['tp'] !== $tpName) {
                    continue;
                }
                ?>

                <div class="exercise">

                    <h3>Exercice <?php echo $exercise['number']; ?></h3>

                    <?php if (count($exercise['pages']) === 0) { ?>

                        <p class="empty">Aucune page à ouvrir.</p>

                    <?php } else { ?>

                        <ul>
                        <?php foreach ($exercise['pages'] as $page) { ?>
                            <?php
                            // Le lien est "actif" si c'est exactement ce que l'on
                            // est en train de regarder.
                            $isActive = $selectedExercise !== null
                                && $selectedExercise['folder'] === $exercise['folder']
                                && $selectedPage === $page;

                            // Le "#main" à la fin de l'adresse : après le
                            // chargement, la navigation au clavier reprend
                            // directement au résultat (ou au code s'il est
                            // affiché seul), sans repasser par la liste.
                            ?>
                            <li>
                                <a <?php echo $isActive ? 'aria-current="page"' : ''; ?>
                                   href="<?php echo buildUrl(array(
                                       'exercise' => $exercise['folder'],
                                       'page'     => $page,
                                       'view'     => $selectedView,
                                   )); ?>#main">
                                    <?php
                                    // Plusieurs exercices ont une page "code.php" : le
                                    // texte caché précise laquelle, pour les lecteurs
                                    // d'écran qui listent les liens hors de leur contexte.
                                    ?>
                                    <span class="visually-hidden"><?php echo escape($exercise['tp']); ?>, exercice <?php echo $exercise['number']; ?> :</span>
                                    <?php echo escape($page); ?>
                                </a>
                            </li>
                        <?php } ?>
                        </ul>

                    <?php } ?>

                </div>

            <?php } ?>

                </details>

            <?php } ?>

        </div>
    </nav>

    <main id="main" <?php echo $selectedView !== 'both' ? 'class="single-panel"' : ''; ?>>

    <?php /* ---------- Colonne 2 : le résultat dans une iframe ---------- */ ?>
    <?php if ($showResult) { ?>
    <section class="column" id="result" aria-labelledby="result-title">
        <div class="column-title">
            <h2 id="result-title">Résultat</h2>
            <?php if ($selectedPage !== null) { ?>
                <?php
                // On prévient que le lien ouvre un nouvel onglet : sinon, un
                // utilisateur de lecteur d'écran ne comprend pas pourquoi le
                // bouton "Précédent" du navigateur ne fonctionne plus.
                // La flèche est décorative (aria-hidden) : elle n'est pas lue.
                ?>
                <a class="open-link" target="_blank" rel="noopener"
                   href="<?php echo escape($selectedExercise['folder'] . '/' . $selectedPage); ?>">
                    ouvrir dans un onglet<span class="visually-hidden"> (nouvel onglet)</span>
                    <span aria-hidden="true">↗</span>
                </a>
            <?php } ?>
        </div>
        <div class="column-body">

            <?php if ($selectedPage === null) { ?>

                <p class="placeholder">
                    Choisis un exercice dans la colonne de gauche : sa page
                    s'exécutera ici<?php echo $showSource ? ", et son code source s'affichera à droite" : ''; ?>.
                </p>

            <?php } else { ?>

                <?php
                // L'iframe demande la page au serveur exactement comme si on
                // l'ouvrait dans un onglet : le PHP y est donc bien exécuté.
                ?>
                <?php // Le titre de l'iframe est ce que les lecteurs d'écran annoncent en y entrant. ?>
                <iframe
                    title="Résultat de <?php echo escape($selectedExercise['folder'] . '/' . $selectedPage); ?>"
                    src="<?php echo escape($selectedExercise['folder'] . '/' . $selectedPage); ?>"></iframe>

            <?php } ?>

        </div>
    </section>
    <?php } ?>

    <?php if ($selectedView === 'both') { ?>
        <?php
        // La séparation entre les deux panneaux : on la fait glisser pour
        // élargir l'un ou l'autre (voir le script en bas de page). Elle reçoit
        // le focus (tabindex="0") pour se déplacer aussi au clavier, avec les
        // flèches, et aria-valuenow dit aux lecteurs d'écran où elle se trouve.
        ?>
        <div class="splitter" role="separator" tabindex="0"
             aria-orientation="vertical" aria-controls="result"
             aria-label="Largeur du résultat, en pourcentage"
             aria-valuenow="50" aria-valuemin="15" aria-valuemax="85"
             title="Glisser pour redimensionner — double-clic : moitié-moitié"></div>
    <?php } ?>

    <?php /* ---------- Colonne 3 : le code source coloré ---------- */ ?>
    <?php if ($showSource) { ?>
    <section class="column" aria-labelledby="source-title">
        <div class="column-title">
            <h2 id="source-title">Code source</h2>
            <?php if ($selectedFile !== null) { ?>
                <span><?php echo escape($selectedExercise['folder'] . '/' . $selectedFile); ?></span>
            <?php } ?>
        </div>

        <?php if ($selectedExercise !== null) { ?>
            <?php // Un onglet par fichier du dossier, y compris ceux inclus. ?>
            <nav class="tabs" aria-label="Fichiers de l'exercice">
                <ul>
                <?php foreach ($selectedExercise['files'] as $file) { ?>
                    <li>
                        <?php // "#source" : après le chargement, on reprend directement au code. ?>
                        <a <?php echo $file === $selectedFile ? 'aria-current="true"' : ''; ?>
                           href="<?php echo buildUrl(array(
                               'exercise' => $selectedExercise['folder'],
                               'page'     => $selectedPage,
                               'file'     => $file,
                               'view'     => $selectedView,
                           )); ?>#source">
                            <span class="visually-hidden">Code de</span>
                            <?php echo escape($file); ?>
                        </a>
                    </li>
                <?php } ?>
                </ul>
            </nav>
        <?php } ?>

        <?php if ($selectedFile === null) { ?>

            <div class="column-body source">
                <p class="placeholder">Aucun fichier à afficher pour le moment.</p>
            </div>

        <?php } else { ?>

            <div class="column-body source" id="source" tabindex="0" role="region"
                 aria-label="Code de <?php echo escape($selectedFile); ?>">


                <?php
                // highlight_file() lit le fichier et renvoie du HTML déjà
                // colorié. On l'affiche donc tel quel, SANS escape() : ici le
                // HTML est produit par PHP lui-même, pas par le visiteur.
                // Le chemin est sûr : $selectedFile a été validé à l'étape 3b.
                echo highlight_file(
                    $rootFolder . '/' . $selectedExercise['folder'] . '/' . $selectedFile,
                    true
                );
                ?>

            </div>

        <?php } ?>

    </section>
    <?php } ?>

    </main>

</div>

<script>
    // Chaque clic recharge la page, et la liste des exercices repart alors tout
    // en haut : l'élève devrait la refaire défiler pour retrouver où il en est.
    // On la fait donc défiler jusqu'au lien actif, placé au milieu de la liste.
    // On change seulement le défilement de la liste (scrollTop) : la page, elle,
    // ne bouge pas, et le "#result" de l'adresse garde son effet.
    var activeLink = document.querySelector('.exercise a[aria-current]');

    if (activeLink) {
        var list = activeLink.closest('.column-body');
        list.scrollTop = activeLink.offsetTop - list.clientHeight / 2;
    }

    // La séparation entre le résultat et le code (seulement quand les deux
    // sont affichés) : on la fait glisser à la souris ou au doigt, ou on la
    // déplace au clavier. Sa position est la largeur du résultat, en %.
    var splitter = document.querySelector('.splitter');

    if (splitter) {
        var main = document.getElementById('main');

        // Les limites ne sont écrites qu'une fois, dans le HTML.
        var minWidth = Number(splitter.getAttribute('aria-valuemin'));
        var maxWidth = Number(splitter.getAttribute('aria-valuemax'));
        var resultWidth;

        // Donne au résultat "percent" % de la largeur : le code prend le reste.
        function setResultWidth(percent) {
            resultWidth = Math.min(maxWidth, Math.max(minWidth, percent));
            main.style.setProperty('--result-width', resultWidth + '%');
            splitter.setAttribute('aria-valuenow', Math.round(resultWidth));

            // Chaque clic recharge la page : on retient donc la largeur dans
            // le navigateur, pour la retrouver au prochain affichage.
            // try/catch : le stockage peut être bloqué (navigation privée...).
            try { localStorage.setItem('resultWidth', resultWidth); } catch (error) {}
        }

        var savedWidth = NaN;
        try { savedWidth = parseFloat(localStorage.getItem('resultWidth')); } catch (error) {}
        setResultWidth(isNaN(savedWidth) ? 50 : savedWidth);

        // Souris ou doigt : setPointerCapture() envoie à la séparation tous
        // les mouvements du pointeur jusqu'au relâchement, même hors d'elle.
        splitter.addEventListener('pointerdown', function (event) {
            splitter.setPointerCapture(event.pointerId);
            main.classList.add('resizing');
        });
        splitter.addEventListener('pointermove', function (event) {
            if (!main.classList.contains('resizing')) {
                return;
            }
            var box = main.getBoundingClientRect();
            setResultWidth((event.clientX - box.left) / box.width * 100);
        });
        splitter.addEventListener('lostpointercapture', function () {
            main.classList.remove('resizing');
        });

        // Double-clic : retour au partage moitié-moitié.
        splitter.addEventListener('dblclick', function () {
            setResultWidth(50);
        });

        // Clavier : les flèches déplacent la séparation, Début et Fin
        // l'envoient tout au bout.
        splitter.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowLeft') {
                setResultWidth(resultWidth - 5);
            } else if (event.key === 'ArrowRight') {
                setResultWidth(resultWidth + 5);
            } else if (event.key === 'Home') {
                setResultWidth(minWidth);
            } else if (event.key === 'End') {
                setResultWidth(maxWidth);
            } else {
                return;
            }
            // La touche a servi : la page ne doit pas défiler en plus.
            event.preventDefault();
        });
    }
</script>

</body>
</html>
