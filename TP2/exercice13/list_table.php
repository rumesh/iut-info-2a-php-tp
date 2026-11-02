<?php
// Exercice 13, question 2 : le tableau des prix, partagé par last25.php, list_all.php et search.php.
// La page qui inclut ce fichier doit avoir rempli $nobels au préalable.
// Accessibilité : chaque colonne a un en-tête (scope="col"), même celles des
// icônes (texte caché), et chaque icône dit quel prix elle supprime ou modifie.
?>
<table>
    <tr> <th scope="col">Name</th> <th scope="col">Category</th> <th scope="col">Year</th> <th scope="col" class="sansBordure"><span class="visually-hidden">Remove</span></th> <th scope="col" class="sansBordure"><span class="visually-hidden">Edit</span></th> </tr>

    <?php foreach ($nobels as $nobel) { ?>
        <tr>
            <td><a href="informations.php?id=<?php echo $nobel['id']; ?>"><?php echo htmlspecialchars($nobel['name']); ?></a></td>
            <td><?php echo htmlspecialchars(ucfirst($nobel['category'])); ?></td>
            <td><?php echo $nobel['year']; ?></td>
            <td class="sansBordure">
                <a href="remove.php?id=<?php echo $nobel['id']; ?>"><img class="icone" src="Content/img/remove-icon.png" alt="Remove <?php echo htmlspecialchars($nobel['name']); ?>"/></a>
            </td>
            <td class="sansBordure">
                <a href="form_update.php?id=<?php echo $nobel['id']; ?>"><img class="icone" src="Content/img/edit-icon.png" alt="Edit <?php echo htmlspecialchars($nobel['name']); ?>"/></a>
            </td>
        </tr>
    <?php } ?>
</table>
