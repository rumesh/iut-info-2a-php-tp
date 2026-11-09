<?php


/**
 * Fonction échappant les caractères html dans $message
 * @param string $message chaîne à échapper
 * @return string chaîne échappée
 */
function e($message)
{
    return htmlspecialchars($message, ENT_QUOTES);
}


/**
 * Exercice 3 : vrai si $value est un entier strictement positif,
 * ex. "12" mais pas "-3", "1.5" ou "abc".
 * is_string() écarte le cas où l'URL contient un tableau (ex. ?id[]=1).
 * @param mixed $value valeur à tester (venue de $_GET ou $_POST)
 * @return bool
 */
function is_strictly_positive_integer($value)
{
    return is_string($value) && preg_match('/^[0-9]+$/', $value) && (int) $value > 0;
}


/**
 * Exercice 4 : le texte $tab[$key] sans les espaces autour, ou null s'il est
 * absent, vide ou composé uniquement d'espaces.
 * @param array $tab $_POST ou $_GET
 * @param string $key nom du champ
 * @return string|null
 */
function get_text($tab, $key)
{
    if (!isset($tab[$key]) || !is_string($tab[$key]) || trim($tab[$key]) === '') {
        return null;
    }
    return trim($tab[$key]);
}

/**
 * Exercice 4, question 2 : vérifie les données envoyées par le formulaire.
 * @return array|false false si name, category ou year sont invalides ; sinon
 *         le tableau attendu par addNobelPrize() et updateNobelPrize(), avec
 *         null pour les champs facultatifs absents ou invalides
 */
function check_data()
{
    if (get_text($_POST, 'name') === null || get_text($_POST, 'category') === null) {
        return false;
    }
    if (!isset($_POST['year']) || !is_strictly_positive_integer($_POST['year'])) {
        return false;
    }

    $birthdate = null;
    if (isset($_POST['birthdate']) && is_strictly_positive_integer($_POST['birthdate'])) {
        $birthdate = (int) $_POST['birthdate'];
    }

    return [
        'year' => (int) $_POST['year'],
        'category' => get_text($_POST, 'category'),
        'name' => get_text($_POST, 'name'),
        'birthdate' => $birthdate,
        'birthplace' => get_text($_POST, 'birthplace'),
        'county' => get_text($_POST, 'county'),
        'motivation' => get_text($_POST, 'motivation'),
    ];
}
