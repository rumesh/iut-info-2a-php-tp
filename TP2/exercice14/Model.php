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

    // Exercice 9, question 1 : la liste des catégories, ex. array('chemistry', 'economics', ...)
    public function get_categories() {
        $req = $this->bd->query('SELECT category FROM categories ORDER BY category');
        // FETCH_COLUMN : on récupère directement les valeurs de la colonne, pas des tableaux
        return $req->fetchAll(PDO::FETCH_COLUMN);
    }

    // Exercice 10, question 1 : ajoute un prix. $infos contient les clés
    // year, category, name, birthdate, birthplace, county et motivation.
    public function add_nobel_prize($infos) {
        $req = $this->bd->prepare('INSERT INTO nobels (year, category, name, birthdate, birthplace, county, motivation)
                                   VALUES (:year, :category, :name, :birthdate, :birthplace, :county, :motivation)');
        $req->execute(array(
            ':year'       => $infos['year'],
            ':category'   => $infos['category'],
            ':name'       => $infos['name'],
            ':birthdate'  => $infos['birthdate'],
            ':birthplace' => $infos['birthplace'],
            ':county'     => $infos['county'],
            ':motivation' => $infos['motivation'],
        ));
        // rowCount() : le nombre de lignes ajoutées
        return $req->rowCount() === 1;
    }

    // Exercice 11, question 1 : supprime le prix d'identifiant $id
    public function remove_nobel_prize($id) {
        $req = $this->bd->prepare('DELETE FROM nobels WHERE id = :id');
        $req->bindValue(':id', $id, PDO::PARAM_INT);
        $req->execute();
        return $req->rowCount() === 1;
    }

    // Exercice 12, question 1 : met à jour tous les champs du prix $infos['id']
    // (nom imposé par l'énoncé, d'où le camelCase)
    public function updateNobelPrize($infos) {
        $req = $this->bd->prepare('UPDATE nobels SET year = :year, category = :category, name = :name,
                                          birthdate = :birthdate, birthplace = :birthplace,
                                          county = :county, motivation = :motivation
                                   WHERE id = :id');
        $req->execute(array(
            ':id'         => $infos['id'],
            ':year'       => $infos['year'],
            ':category'   => $infos['category'],
            ':name'       => $infos['name'],
            ':birthdate'  => $infos['birthdate'],
            ':birthplace' => $infos['birthplace'],
            ':county'     => $infos['county'],
            ':motivation' => $infos['motivation'],
        ));
        return $req->rowCount() === 1;
    }

    // Exercice 13, question 1 : $limit prix à partir du rang $offset, du plus récent au plus ancien.
    // get_nobel_prizes_with_limit(0, 25) renvoie la même chose que get_last().
    public function get_nobel_prizes_with_limit($offset, $limit) {
        $req = $this->bd->prepare('SELECT * FROM nobels ORDER BY year DESC, id LIMIT :limit OFFSET :offset');
        $req->bindValue(':limit', $limit, PDO::PARAM_INT);
        $req->bindValue(':offset', $offset, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Exercice 14 : construit la partie WHERE de la recherche à partir des filtres.
     * Renvoie la condition SQL et les valeurs à associer à ses marqueurs.
     *
     * Filtres possibles (tous facultatifs) :
     *   'Name'       => texte contenu dans le nom
     *   'Year'       => année, avec 'SignYear' => '<=', '>=' ou '=='
     *   'Categories' => tableau de catégories (question 4)
     */
    private function build_search_conditions($filters) {
        $conditions = array();
        $params = array();

        if (isset($filters['Name'])) {
            // ILIKE : le LIKE de PostgreSQL qui ignore les majuscules/minuscules
            $conditions[] = 'name ILIKE :name';
            $params[':name'] = '%' . $filters['Name'] . '%';
        }

        // L'opérateur vient de l'utilisateur : on ne le met dans le SQL que s'il
        // fait partie de cette liste (== s'écrit = en SQL)
        $signs = array('<=' => '<=', '>=' => '>=', '==' => '=');
        if (isset($filters['Year'], $filters['SignYear']) && isset($signs[$filters['SignYear']])) {
            $conditions[] = 'year ' . $signs[$filters['SignYear']] . ' :year';
            $params[':year'] = (int) $filters['Year'];
        }

        // Question 4 : un marqueur par catégorie choisie, ex. category IN (:cat0, :cat1)
        if (!empty($filters['Categories'])) {
            $markers = array();
            foreach (array_values($filters['Categories']) as $i => $category) {
                $markers[] = ':cat' . $i;
                $params[':cat' . $i] = $category;
            }
            $conditions[] = 'category IN (' . implode(', ', $markers) . ')';
        }

        $where = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return array($where, $params);
    }

    // Exercice 14, question 1 : une page de résultats de la recherche.
    // Sans filtre, renvoie la même chose que get_nobel_prizes_with_limit($offset, $limit).
    public function search_nobel_prizes($filters, $offset, $limit) {
        list($where, $params) = $this->build_search_conditions($filters);

        $req = $this->bd->prepare("SELECT * FROM nobels $where ORDER BY year DESC, id LIMIT :limit OFFSET :offset");
        foreach ($params as $marker => $value) {
            $req->bindValue($marker, $value);
        }
        $req->bindValue(':limit', $limit, PDO::PARAM_INT);
        $req->bindValue(':offset', $offset, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Exercice 14 : nombre total de résultats, pour calculer le nombre de pages
    public function count_search_results($filters) {
        list($where, $params) = $this->build_search_conditions($filters);

        $req = $this->bd->prepare("SELECT count(*) FROM nobels $where");
        $req->execute($params);
        return (int) $req->fetchColumn();
    }
}
