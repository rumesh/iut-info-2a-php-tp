<?php
// Exercice 6, question 1 : le formulaire de modification, pré-rempli avec les
// informations du prix ($id, $year, $category, $name...) ; $categories donne
// la liste des boutons radio.
$title = "Update a Nobel Prize";
require "view_begin.php";
?>

<h1> Update a Nobel Prize </h1>

<form action="?controller=set&amp;action=update" method="post">
    <?php // L'id est transmis dans un champ caché : l'utilisateur ne le voit pas ?>
    <input type="hidden" name="id" value="<?= e($id) ?>"/>

    <?php // Accessibilité : mêmes libellés, légende et champs obligatoires que dans view_form_add.php ?>
    <p> <label> Name (required): <input type="text" name="name" aria-required="true" value="<?= e($name) ?>"/> </label> </p>
    <p> <label> Year (required): <input type="text" name="year" inputmode="numeric" aria-required="true" value="<?= e($year) ?>"/> </label></p>
    <p> <label> Birth Date: <input type="text" name="birthdate" inputmode="numeric" value="<?= e($birthdate ?? '') ?>"/></label> </p>
    <p> <label> Birth Place: <input type="text" name="birthplace" value="<?= e($birthplace ?? '') ?>"/> </label></p>
    <p> <label> County: <input type="text" name="county" value="<?= e($county ?? '') ?>"/></label> </p>

    <fieldset>
        <legend> Category (required): </legend>
        <?php // $cat et non $category : $category est déjà la catégorie du prix ?>
        <?php foreach ($categories as $cat) { ?>
            <label>
                <input type="radio" name="category" value="<?= e($cat) ?>" <?= $cat === $category ? 'checked' : '' ?> />
                <?= e(ucfirst($cat)) ?>
            </label>
        <?php } ?>
    </fieldset>
    <p>
        <label for="motivation"> Motivation: </label>
        <textarea id="motivation" name="motivation" cols="70" rows="10"><?= e($motivation ?? '') ?></textarea>
    </p>
    <p> <input type="submit" value="Update"/> </p>
</form>

<?php require "view_end.php"; ?>
