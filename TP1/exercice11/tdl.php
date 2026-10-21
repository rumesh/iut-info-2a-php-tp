<?php
require_once "TODOList.php"; // avant session_start car on stocke un objet TODOList dans la session
session_start();

// Lien de test : supprime la TODO liste de la session pour vérifier le rechargement depuis le cookie
if (isset($_GET['del_session'])) {
    unset($_SESSION['tdl']);
}

if (!isset($_SESSION['tdl'])) {
    $_SESSION['tdl'] = new TODOList();
    if (isset($_COOKIE['tdl'])) {
        $_SESSION['tdl']->set_representation($_COOKIE['tdl']);
    }
}

if (isset($_GET['task'])) {
    $_SESSION['tdl']->add_to_do($_GET['task']);
}

// Question 4 : suppression d'une tâche via le lien généré par get_html()
if (isset($_GET['rm'])) {
    $_SESSION['tdl']->remove_to_do($_GET['rm']);
}

// Question 6 : sauvegarde de la représentation dans un cookie (1 mois)
if (isset($_GET['save'])) {
    setcookie('tdl', $_SESSION['tdl']->get_representation(), time() + 3600 * 24 * 30);
}

$title = "Exercice 11 - TODO liste";
require "debut_code_html.php";
?>

<h1>Ma TODO liste</h1>

<p>Cliquer sur une tâche pour la supprimer.</p>

<?php echo $_SESSION['tdl']->get_html(); ?>

<form action="tdl.php" method="get">
    <label for="task">Nouvelle tâche :</label>
    <input type="text" id="task" name="task" />
    <input type="submit" value="Ajouter" />
</form>

<p><a href="tdl.php?save=1">Sauvegarder la liste dans un cookie</a></p>
<p><a href="tdl.php?del_session=1">Supprimer la session (pour tester le chargement depuis le cookie)</a></p>

<?php require "fin_code_html.php"; ?>
