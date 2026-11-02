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
