<?php
// Exercice 3, question 1 : les informations d'un prix.
// Les variables ($year, $name...) sont les clés du tableau passé à render().
// L'opérateur ?? remplace une valeur null (inconnue) par "???".
$title = "Nobel prize informations";
require "view_begin.php";
?>

<h1> Nobel prize informations </h1>

<ul>
    <li><strong>Name:</strong> <?= e($name ?? '???') ?></li>
    <li><strong>Year:</strong> <?= e($year ?? '???') ?></li>
    <li><strong>Category:</strong> <?= e(ucfirst($category ?? '???')) ?></li>
    <li><strong>Birth date:</strong> <?= e($birthdate ?? '???') ?></li>
    <li><strong>Birth place:</strong> <?= e($birthplace ?? '???') ?></li>
    <li><strong>County:</strong> <?= e($county ?? '???') ?></li>
    <li><strong>Motivation:</strong> <?= e($motivation ?? '???') ?></li>
</ul>

<?php require "view_end.php"; ?>
