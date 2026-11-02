<?php
require_once "Model.php";

// Exercice 9, question 2 : les boutons radio sont générés à partir des catégories de la base
$categories = Model::get_model()->get_categories();

$title = "Add a Nobel Prize";
require "begin.html";
?>
<h1> Add a Nobel Prize </h1>

<?php
// Accessibilité : chaque champ a un libellé (<label>), les boutons radio sont
// regroupés sous une légende (<fieldset>/<legend>) et les champs obligatoires
// sont signalés dans le texte et par aria-required (sans bloquer l'envoi :
// c'est add.php qui vérifie les données, voir l'exercice 10).
?>
<form action="add.php" method="post">
    <p> <label> Name (required): <input type="text" name="Name" aria-required="true"/> </label> </p>
    <p> <label> Year (required): <input type="text" name="Year" inputmode="numeric" aria-required="true"/> </label></p>
    <p> <label> Birth Date: <input type="text" name="Birthdate" inputmode="numeric"/></label> </p>
    <p> <label> Birth Place: <input type="text" name="BirthPlace"/> </label></p>
    <p> <label> County: <input type="text" name="County"/></label> </p>

    <fieldset>
        <legend> Category (required): </legend>
        <?php foreach ($categories as $category) { ?>
            <label> <input type="radio" name="Category" value="<?php echo htmlspecialchars($category); ?>" /> <?php echo htmlspecialchars(ucfirst($category)); ?> </label>
        <?php } ?>
    </fieldset>
    <p>
        <label for="motivation"> Motivation: </label>
        <textarea id="motivation" name="Motivation" cols="70" rows="10"></textarea>
    </p>
    <p> <input type="submit" value="Add in database"/> </p>
</form>

<?php require "end.html"; ?>
