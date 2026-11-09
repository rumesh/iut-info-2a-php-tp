<?php
// Exercice 7, question 4 : les liens vers les pages, comme sur un moteur de
// recherche. Variables attendues : $start (la page affichée), $nb_pages, et
// $link (l'adresse des pages, sans le paramètre start).
// On affiche au plus 10 numéros autour de la page courante, et les flèches
// précédent / suivant quand elles ont un sens.
$first = max(1, $start - 5);
$last = min($nb_pages, $first + 9);
$first = max(1, $last - 9);
?>
<nav class="listePages" aria-label="Pages">
    <?php if ($start > 1) { ?>
        <a class="lienStart" href="<?= e($link . '&start=' . ($start - 1)) ?>"><img class="icone" src="Content/img/previous-icon.png" alt="Previous page"/></a>
    <?php } ?>

    <?php for ($i = $first; $i <= $last; $i++) { ?>
        <?php if ($i === $start) { ?>
            <a class="lienStart active" aria-current="page" href="<?= e($link . '&start=' . $i) ?>"><span class="visually-hidden">Page </span><?= $i ?></a>
        <?php } else { ?>
            <a class="lienStart" href="<?= e($link . '&start=' . $i) ?>"><span class="visually-hidden">Page </span><?= $i ?></a>
        <?php } ?>
    <?php } ?>

    <?php if ($start < $nb_pages) { ?>
        <a class="lienStart" href="<?= e($link . '&start=' . ($start + 1)) ?>"><img class="icone" src="Content/img/next-icon.png" alt="Next page"/></a>
    <?php } ?>
</nav>
