<?php
require_once __DIR__ . '/../config.php';

/**
 * Accès à la base des prix Nobel.
 *
 * Exercice 5, question 1 : la classe est un "singleton". Le constructeur est
 * privé, on ne peut donc pas écrire new Model() : on appelle Model::get_model(),
 * qui crée l'objet la première fois puis renvoie toujours le même. Ainsi, une
 * seule connexion à la base est ouverte, même si plusieurs fichiers s'en servent.
 */
class Model {
    private static $instance = null;
    private $bd;

    private function __construct() {
        $this->bd = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
        // Les erreurs SQL lèvent une exception au lieu de passer inaperçues
        $this->bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Les données échangées avec la base sont encodées en UTF-8
        $this->bd->query("SET NAMES 'utf8'");
    }

    public static function get_model() {
        if (self::$instance === null) {
            self::$instance = new Model();
        }
        return self::$instance;
    }

    // Exercice 5, question 2 : les 25 derniers prix Nobel.
    // ", id" départage les prix d'une même année : l'ordre est ainsi toujours le même.
    public function get_last() {
        $req = $this->bd->query('SELECT * FROM nobels ORDER BY year DESC, id LIMIT 25');
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Exercice 5, question 3 : le nombre total de prix Nobel
    public function get_nb_nobel_prizes() {
        $req = $this->bd->query('SELECT count(*) FROM nobels');
        return (int) $req->fetchColumn();
    }

    // Exercice 8, question 1 : toutes les informations d'un prix, ou false s'il n'existe pas
    public function get_nobel_prize_informations($id) {
        $req = $this->bd->prepare('SELECT * FROM nobels WHERE id = :id');
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    }
}
