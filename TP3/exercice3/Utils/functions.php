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
