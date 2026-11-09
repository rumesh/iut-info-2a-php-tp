<?php
// Exercice 8, question 2 : le formulaire de recherche (question 4 : une case
// à cocher par catégorie). Il est envoyé en GET : les critères apparaissent
// dans l'adresse des résultats.
$title = "Search Nobel Prizes";
require "view_begin.php";
?>

<h1> Search Nobel Prizes </h1>

<form action="index.php" method="get">
    <?php // Ces deux champs cachés choisissent le contrôleur et l'action, comme dans un lien ?>
    <input type="hidden" name="controller" value="search"/>
    <input type="hidden" name="action" value="pagination"/>

    <p> <label> Name: <input type="text" name="name"/> </label> </p>
    <?php
    // Accessibilité : un <label> ne nomme qu'un seul champ. La liste et la
    // zone de texte de l'année ont donc chacune le leur, regroupés sous une
    // même légende ; les cases à cocher aussi.
    ?>
    <fieldset>
        <legend> Year: </legend>
        <label> Comparison:
            <select name="sign">
                <option value=">=">&gt;= (from this year on)</option>
                <option value="<=">&lt;= (up to this year)</option>
                <option value="==">== (this year only)</option>
            </select>
        </label>
        <label> Year: <input type="text" name="year" inputmode="numeric"/> </label>
    </fieldset>

    <fieldset>
        <legend> Categories: </legend>
        <?php foreach ($categories as $category) { ?>
            <label> <input type="checkbox" name="categories[]" value="<?= e($category) ?>" /> <?= e(ucfirst($category)) ?> </label>
        <?php } ?>
    </fieldset>

    <p> <input type="submit" value="Search"/> </p>
</form>

<?php require "view_end.php"; ?>
