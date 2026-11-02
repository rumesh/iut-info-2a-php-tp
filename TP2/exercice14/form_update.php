<?php
require_once "Model.php";
require_once "functions.php";

// Exercice 12, question 2 : formulaire de modification, pré-rempli avec les valeurs du prix
$model = Model::get_model();
$infos = false;

if (isset($_GET['id']) && is_strictly_positive_integer($_GET['id'])) {
    $infos = $model->get_nobel_prize_informations((int) $_GET['id']);
}

$title = "Update a Nobel Prize";
require "begin.html";
?>
<h1> Update a Nobel Prize </h1>

<?php if ($infos === false) { ?>

    <p>There is no nobel prize with such id.</p>

<?php } else { ?>

    <form action="update.php" method="post">
        <?php // L'id est transmis dans un champ caché : l'utilisateur ne le voit pas ?>
        <input type="hidden" name="Id" value="<?php echo $infos['id']; ?>"/>

        <?php // Accessibilité : mêmes libellés, légende et champs obligatoires que dans form_add.php ?>
        <p> <label> Name (required): <input type="text" name="Name" aria-required="true" value="<?php echo htmlspecialchars($infos['name']); ?>"/> </label> </p>
        <p> <label> Year (required): <input type="text" name="Year" inputmode="numeric" aria-required="true" value="<?php echo $infos['year']; ?>"/> </label></p>
        <p> <label> Birth Date: <input type="text" name="Birthdate" inputmode="numeric" value="<?php echo htmlspecialchars((string) $infos['birthdate']); ?>"/></label> </p>
        <p> <label> Birth Place: <input type="text" name="BirthPlace" value="<?php echo htmlspecialchars((string) $infos['birthplace']); ?>"/> </label></p>
        <p> <label> County: <input type="text" name="County" value="<?php echo htmlspecialchars((string) $infos['county']); ?>"/></label> </p>

        <fieldset>
            <legend> Category (required): </legend>
            <?php foreach ($model->get_categories() as $category) { ?>
                <label>
                    <input type="radio" name="Category" value="<?php echo htmlspecialchars($category); ?>"
                        <?php echo $category === $infos['category'] ? 'checked' : ''; ?> />
                    <?php echo htmlspecialchars(ucfirst($category)); ?>
                </label>
            <?php } ?>
        </fieldset>
        <p>
            <label for="motivation"> Motivation: </label>
            <textarea id="motivation" name="Motivation" cols="70" rows="10"><?php echo htmlspecialchars((string) $infos['motivation']); ?></textarea>
        </p>
        <p> <input type="submit" value="Update"/> </p>
    </form>

<?php } ?>

<?php require "end.html"; ?>
