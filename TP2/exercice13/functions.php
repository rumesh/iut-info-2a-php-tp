<?php
/**
 * Fonctions utilitaires partagées par les pages du site.
 */

// Exercice 8 : vrai si $value est un entier strictement positif, ex. "12" mais pas "-3", "1.5" ou "abc".
// is_string() écarte le cas où l'URL contient un tableau (ex. ?id[]=1).
function is_strictly_positive_integer($value) {
    return is_string($value) && preg_match('/^[0-9]+$/', $value) && (int) $value > 0;
}

// Exercice 10 : le champ texte $key du formulaire, sans les espaces autour,
// ou null s'il est absent ou vide.
function get_text($key) {
    if (!isset($_POST[$key]) || !is_string($_POST[$key]) || trim($_POST[$key]) === '') {
        return null;
    }
    return trim($_POST[$key]);
}

// Exercice 10, question 2 : vérifie les données envoyées par le formulaire.
// Renvoie false si Name, Category ou Year sont invalides, sinon le tableau $infos
// attendu par add_nobel_prize() (null pour les champs facultatifs absents ou invalides).
function check_data() {
    if (get_text('Name') === null || get_text('Category') === null) {
        return false;
    }
    if (!isset($_POST['Year']) || !is_strictly_positive_integer($_POST['Year'])) {
        return false;
    }

    $birthdate = null;
    if (isset($_POST['Birthdate']) && is_strictly_positive_integer($_POST['Birthdate'])) {
        $birthdate = (int) $_POST['Birthdate'];
    }

    return array(
        'year'       => (int) $_POST['Year'],
        'category'   => get_text('Category'),
        'name'       => get_text('Name'),
        'birthdate'  => $birthdate,
        'birthplace' => get_text('BirthPlace'),
        'county'     => get_text('County'),
        'motivation' => get_text('Motivation'),
    );
}

// Exercice 13, question 3 : les liens vers les pages, comme sur un moteur de recherche.
// On affiche au plus 10 numéros autour de la page courante, et les flèches
// précédent / suivant quand elles ont un sens.
// Accessibilité : les liens sont dans un <nav> nommé "Pages", chaque numéro se
// lit "Page 3", et la page courante est signalée par aria-current.
function display_pages_links($page, $nb_pages, $url) {
    $first = max(1, $page - 5);
    $last = min($nb_pages, $first + 9);
    $first = max(1, $last - 9);

    echo '<nav class="listePages" aria-label="Pages">';

    if ($page > 1) {
        echo '<a class="lienStart" href="' . $url . '?page=' . ($page - 1) . '">';
        echo '<img class="icone" src="Content/img/previous-icon.png" alt="Previous page"/></a>';
    }

    for ($i = $first; $i <= $last; $i++) {
        $attributes = $i === $page ? 'class="lienStart active" aria-current="page"' : 'class="lienStart"';
        echo '<a ' . $attributes . ' href="' . $url . '?page=' . $i . '"><span class="visually-hidden">Page </span>' . $i . '</a>';
    }

    if ($page < $nb_pages) {
        echo '<a class="lienStart" href="' . $url . '?page=' . ($page + 1) . '">';
        echo '<img class="icone" src="Content/img/next-icon.png" alt="Next page"/></a>';
    }

    echo '</nav>';
}
