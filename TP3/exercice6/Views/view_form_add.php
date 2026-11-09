<?php
// Exercice 4, question 1 : le formulaire d'ajout (comme form_add.php au TP2).
// Les boutons radio sont générés à partir de $categories.
$title = "Add a Nobel Prize";
require "view_begin.php";
?>

<h1> Add a Nobel Prize </h1>

<?php
// Accessibilité : chaque champ a un libellé (<label>), les boutons radio sont
// regroupés sous une légende (<fieldset>/<legend>) et les champs obligatoires
// sont signalés dans le texte et par aria-required (sans bloquer l'envoi :
// c'est l'action add qui vérifie les données).
?>
<form action="?controller=set&amp;action=add" method="post">
    <p> <label> Name (required): <input type="text" name="name" aria-required="true"/> </label> </p>
    <p> <label> Year (required): <input type="text" name="year" inputmode="numeric" aria-required="true"/> </label></p>
    <p> <label> Birth Date: <input type="text" name="birthdate" inputmode="numeric"/></label> </p>
    <p> <label> Birth Place: <input type="text" name="birthplace"/> </label></p>
    <p> <label> County: <input type="text" name="county"/></label> </p>

    <fieldset>
        <legend> Category (required): </legend>
        <?php foreach ($categories as $category) { ?>
            <label> <input type="radio" name="category" value="<?= e($category) ?>" /> <?= e(ucfirst($category)) ?> </label>
        <?php } ?>
    </fieldset>
    <p>
        <label for="motivation"> Motivation: </label>
        <textarea id="motivation" name="motivation" cols="70" rows="10"></textarea>
    </p>
    <p> <input type="submit" value="Add in database"/> </p>
</form>

<?php require "view_end.php"; ?>
