<?php

class Controller_search extends Controller
{
    /**
     * Action par défaut (exercice 8, question 2) : le formulaire de recherche
     */
    public function action_default()
    {
        $this->action_form();
    }

    /**
     * Exercice 8, question 2 : le formulaire de recherche
     */
    public function action_form()
    {
        $data = [
            'categories' => Model::getModel()->getCategories(),
        ];
        $this->render("form_search", $data);
    }

    /**
     * Exercice 8, question 3 : les résultats de la recherche, 25 par page.
     * Le formulaire est envoyé en GET : les critères sont donc dans l'URL, et
     * les liens vers les autres pages de résultats n'ont qu'à les recopier.
     */
    public function action_pagination()
    {
        $model = Model::getModel();

        // Les critères valides, au format attendu par findNobelPrizes()
        $filters = [];

        if (get_text($_GET, 'name') !== null) {
            $filters['name'] = get_text($_GET, 'name');
        }

        if (isset($_GET['year'], $_GET['sign']) && is_strictly_positive_integer($_GET['year'])
            && in_array($_GET['sign'], ['<=', '>=', '=='], true)) {
            $filters['year'] = (int) $_GET['year'];
            $filters['sign'] = $_GET['sign'];
        }

        // Question 4 : on ne garde que les catégories qui existent dans la base.
        // array_values() les renumérote 0, 1, 2... : findNobelPrizes() s'en sert
        // pour nommer ses marqueurs (:c0, :c1...).
        if (isset($_GET['categories']) && is_array($_GET['categories'])) {
            $chosen = array_filter($_GET['categories'], 'is_string');
            $categories = array_values(array_intersect($model->getCategories(), $chosen));
            if (count($categories) > 0) {
                $filters['categories'] = $categories;
            }
        }

        $nb_per_page = 25;
        $start = 1;
        if (isset($_GET['start']) && is_strictly_positive_integer($_GET['start'])) {
            $start = (int) $_GET['start'];
        }

        // Aucun résultat : on affiche quand même la page 1, qui le dit
        $nb_results = $model->nbFindNobelPrizes($filters);
        $nb_pages = max(1, (int) ceil($nb_results / $nb_per_page));
        if ($start > $nb_pages) {
            $this->action_error("This page does not exist.");
        }

        // L'adresse des pages reprend les critères : http_build_query()
        // assemble et encode le "name=...&year=..."
        $parameters = ['controller' => 'search', 'action' => 'pagination'] + $filters;

        $data = [
            'nobels' => $model->findNobelPrizes($filters, ($start - 1) * $nb_per_page, $nb_per_page),
            'nb_results' => $nb_results,
            'start' => $start,
            'nb_pages' => $nb_pages,
            'link' => '?' . http_build_query($parameters),
        ];
        $this->render("search", $data);
    }
}
