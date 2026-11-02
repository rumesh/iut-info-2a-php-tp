<?php
require_once "Model.php";

// Exercice 7 : les 25 derniers prix, récupérés grâce à la classe Model
$nobels = Model::get_model()->get_last();

$title = "List of the last 25 Nobel prizes";
require "begin.html";
?>
<h1> List of the last 25 Nobel prizes </h1>

<table>
    <tr> <th scope="col">Name</th> <th scope="col">Category</th> <th scope="col">Year</th> <th scope="col" class="sansBordure"><span class="visually-hidden">Remove</span></th> <th scope="col" class="sansBordure"><span class="visually-hidden">Edit</span></th> </tr>

    <?php foreach ($nobels as $nobel) { ?>
        <tr>
            <?php // Exercice 8, question 3 : le nom mène à la page d'informations du prix ?>
            <td><a href="informations.php?id=<?php echo $nobel['id']; ?>"><?php echo htmlspecialchars($nobel['name']); ?></a></td>
            <td><?php echo htmlspecialchars(ucfirst($nobel['category'])); ?></td>
            <td><?php echo $nobel['year']; ?></td>
            <?php // Exercice 11, question 3 : l'icône de suppression ?>
            <td class="sansBordure">
                <a href="remove.php?id=<?php echo $nobel['id']; ?>"><img class="icone" src="Content/img/remove-icon.png" alt="Remove <?php echo htmlspecialchars($nobel['name']); ?>"/></a>
            </td>
            <?php // Exercice 12, question 4 : l'icône de modification ?>
            <td class="sansBordure">
                <a href="form_update.php?id=<?php echo $nobel['id']; ?>"><img class="icone" src="Content/img/edit-icon.png" alt="Edit <?php echo htmlspecialchars($nobel['name']); ?>"/></a>
            </td>
        </tr>
    <?php } ?>
</table>

<?php require "end.html"; ?>
