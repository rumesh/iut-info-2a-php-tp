<?php
require_once "Model.php";
require_once "functions.php";

// Exercice 12, question 3 : mise à jour du prix envoyé par form_update.php
$updated = false;

if (isset($_POST['Id']) && is_strictly_positive_integer($_POST['Id'])) {
    // check_data() vérifie Name, Category et Year comme pour l'ajout
    $infos = check_data();

    if ($infos !== false) {
        $infos['id'] = (int) $_POST['Id'];
        try {
            $updated = Model::get_model()->updateNobelPrize($infos);
        } catch (PDOException $e) {
            $updated = false;
        }
    }
}

$title = "Update a Nobel Prize";
require "begin.html";
?>
<h1> Update a Nobel Prize </h1>

<p>
    <?php echo $updated ? 'The database has been updated.' : 'The database has not been updated.'; ?>
</p>

<?php require "end.html"; ?>
