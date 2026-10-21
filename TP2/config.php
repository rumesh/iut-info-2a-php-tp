<?php
/**
 * Paramètres de connexion à la base PostgreSQL, partagés par tous les exercices.
 *
 * - Avec Docker : les valeurs viennent de docker-compose.yml (variables
 *   d'environnement), il n'y a rien à modifier.
 * - À l'IUT, sans Docker : ce sont les valeurs par défaut ci-dessous qui sont
 *   utilisées. Remplacer login et mot de passe par son numéro étudiant et son
 *   code INE.
 *
 * getenv() renvoie false si la variable n'existe pas : l'opérateur ?: prend
 * alors la valeur de droite.
 */
define('DB_HOST', getenv('DB_HOST') ?: 'aquabdd');
define('DB_NAME', getenv('DB_NAME') ?: 'etudiants');
define('DB_USER', getenv('DB_USER') ?: 'numero_etudiant');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'code_INE');

// Le "DSN" décrit la base à laquelle PDO doit se connecter.
define('DB_DSN', 'pgsql:host=' . DB_HOST . ';dbname=' . DB_NAME);
