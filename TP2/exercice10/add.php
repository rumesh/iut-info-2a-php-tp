<?php
require_once "Model.php";
require_once "functions.php";

// Exercice 10, question 3 : ajout du prix envoyé par form_add.php
if (empty($_POST)) {
    $message = 'No form has been submitted.';
} else {
    $infos = check_data();

    if ($infos === false) {
        $message = 'Error: the name, the category and the year (a strictly positive integer) are required.';
    } else {
        // Une catégorie qui n'existe pas viole la clé étrangère : PDO lève alors une exception
        try {
            if (Model::get_model()->add_nobel_prize($infos)) {
                $message = 'The nobel prize has been added.';
            } else {
                $message = 'Error: the nobel prize has not been added.';
            }
        } catch (PDOException $e) {
            $message = 'Error: the nobel prize has not been added.';
        }
    }
}

$title = "Add a Nobel Prize";
require "begin.html";
?>
<h1> Add a Nobel Prize </h1>

<p><?php echo $message; ?></p>

<?php require "end.html"; ?>
