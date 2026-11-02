<?php
/**
 * Fonctions utilitaires partagées par les pages du site.
 */

// Exercice 8 : vrai si $value est un entier strictement positif, ex. "12" mais pas "-3", "1.5" ou "abc".
// is_string() écarte le cas où l'URL contient un tableau (ex. ?id[]=1).
function is_strictly_positive_integer($value) {
    return is_string($value) && preg_match('/^[0-9]+$/', $value) && (int) $value > 0;
}
