<?php
// Exercice 7, question 1 : le tableau des prix, partagé par les vues last,
// pagination et search. La vue qui l'inclut doit avoir reçu $nobels.
// Accessibilité : chaque colonne a un en-tête (scope="col"), même celles des
// icônes (texte caché), et chaque icône dit quel prix elle supprime ou modifie.
?>
<table>
    <tr> <th scope="col">Name</th> <th scope="col">Category</th> <th scope="col">Year</th> <th scope="col" class="sansBordure"><span class="visually-hidden">Remove</span></th> <th scope="col" class="sansBordure"><span class="visually-hidden">Edit</span></th> </tr>

    <?php foreach ($nobels as $nobel) { ?>
        <tr>
            <td><a href="?controller=list&amp;action=informations&amp;id=<?= e($nobel['id']) ?>"><?= e($nobel['name']) ?></a></td>
            <td><?= e(ucfirst($nobel['category'])) ?></td>
            <td><?= e($nobel['year']) ?></td>
            <td class="sansBordure">
                <a href="?controller=set&amp;action=remove&amp;id=<?= e($nobel['id']) ?>"><img class="icone" src="Content/img/remove-icon.png" alt="Remove <?= e($nobel['name']) ?>"/></a>
            </td>
            <td class="sansBordure">
                <a href="?controller=set&amp;action=form_update&amp;id=<?= e($nobel['id']) ?>"><img class="icone" src="Content/img/edit-icon.png" alt="Edit <?= e($nobel['name']) ?>"/></a>
            </td>
        </tr>
    <?php } ?>
</table>
