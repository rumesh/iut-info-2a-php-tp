<?php
require_once "Model.php";
require_once "functions.php";

// Exercice 8, question 2 : informations sur le prix dont l'id est donné dans l'URL
$infos = false;
$message = '';

if (!isset($_GET['id']) || !is_strictly_positive_integer($_GET['id'])) {
    $message = 'The id must be a strictly positive integer.';
} else {
    $infos = Model::get_model()->get_nobel_prize_informations((int) $_GET['id']);
    if ($infos === false) {
        $message = 'There is no nobel prize with such id.';
    }
}

$title = "Nobel prize informations";
require "begin.html";
?>
<h1> Nobel prize informations </h1>

<?php if ($infos === false) { ?>

    <p><?php echo $message; ?></p>

<?php } else { ?>

    <ul>
        <li><strong>Name:</strong> <?php echo htmlspecialchars($infos['name']); ?></li>
        <li><strong>Year:</strong> <?php echo $infos['year']; ?></li>
        <li><strong>Category:</strong> <?php echo htmlspecialchars(ucfirst($infos['category'])); ?></li>
        <li><strong>Birth date:</strong> <?php echo htmlspecialchars((string) $infos['birthdate']); ?></li>
        <li><strong>Birth place:</strong> <?php echo htmlspecialchars((string) $infos['birthplace']); ?></li>
        <li><strong>County:</strong> <?php echo htmlspecialchars((string) $infos['county']); ?></li>
        <li><strong>Motivation:</strong> <?php echo htmlspecialchars((string) $infos['motivation']); ?></li>
    </ul>

<?php } ?>

<?php require "end.html"; ?>
