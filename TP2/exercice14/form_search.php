<?php
require_once "Model.php";

// Exercice 14, question 2 : le formulaire de recherche
// (question 4 : une case à cocher par catégorie)
$categories = Model::get_model()->get_categories();

$title = "Search Nobel Prizes";
require "begin.html";
?>
<h1> Search Nobel Prizes </h1>

<form action="search.php" method="post">
    <p> <label> Name: <input type="text" name="Name"/> </label> </p>
    <?php
    // Accessibilité : un <label> ne nomme qu'un seul champ. La liste et la
    // zone de texte de l'année ont donc chacune le leur, regroupés sous une
    // même légende ; les cases à cocher aussi.
    ?>
    <fieldset>
        <legend> Year: </legend>
        <label> Comparison:
            <select name="SignYear">
                <option value=">=">&gt;= (from this year on)</option>
                <option value="<=">&lt;= (up to this year)</option>
                <option value="==">== (this year only)</option>
            </select>
        </label>
        <label> Year: <input type="text" name="Year" inputmode="numeric"/> </label>
    </fieldset>

    <fieldset>
        <legend> Categories: </legend>
        <?php foreach ($categories as $category) { ?>
            <label> <input type="checkbox" name="Categories[]" value="<?php echo htmlspecialchars($category); ?>" /> <?php echo htmlspecialchars(ucfirst($category)); ?> </label>
        <?php } ?>
    </fieldset>

    <p> <input type="submit" name="Search" value="Search"/> </p>
</form>

<?php require "end.html"; ?>
