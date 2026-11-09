<?php
// Exercice 2 : les 25 derniers prix (la page last25.php du TP2, transformée en vue)
$title = "List of the last 25 Nobel prizes";
require "view_begin.php";
?>

<h1> List of the last 25 Nobel prizes </h1>

<table>
    <tr> <th scope="col">Name</th> <th scope="col">Category</th> <th scope="col">Year</th> </tr>

    <?php foreach ($nobels as $nobel) { ?>
        <tr>
            <?php // Exercice 3, question 4 : le nom mène aux informations du prix ?>
            <td><a href="?controller=list&amp;action=informations&amp;id=<?= e($nobel['id']) ?>"><?= e($nobel['name']) ?></a></td>
            <td><?= e(ucfirst($nobel['category'])) ?></td>
            <td><?= e($nobel['year']) ?></td>
        </tr>
    <?php } ?>
</table>

<?php require "view_end.php"; ?>
