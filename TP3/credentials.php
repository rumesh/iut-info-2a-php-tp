<?php
/**
 * Exercice 1, question 2 : les identifiants de connexion à la base PostgreSQL.
 * Models/Model.php inclut ce fichier, qui doit définir $dsn, $login et $mdp.
 *
 * Il est rangé en dehors des dossiers d'exercice, et partagé par tous. À l'IUT,
 * l'énoncé le place dans /home/Web/Auth/, hors du dossier publié sur le web :
 * un mot de passe ne doit jamais se trouver dans un dossier accessible par
 * une adresse.
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
$dsn = 'pgsql:host=' . (getenv('DB_HOST') ?: 'aquabdd') . ';dbname=' . (getenv('DB_NAME') ?: 'etudiants');
$login = getenv('DB_USER') ?: 'numero_etudiant';
$mdp = getenv('DB_PASSWORD') ?: 'code_INE';
