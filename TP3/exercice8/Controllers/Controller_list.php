<?php

class Controller_list extends Controller
{
    /**
     * Action par défaut (exercice 2) : les 25 derniers prix
     */
    public function action_default()
    {
        $this->action_last();
    }

    /**
     * Exercice 2 : les 25 derniers prix Nobel (comme last25.php au TP2)
     */
    public function action_last()
    {
        $data = [
            'nobels' => Model::getModel()->getLast(),
        ];
        $this->render("last", $data);
    }

    /**
     * Exercice 3, question 2 : les informations du prix dont l'id est dans l'URL.
     * action_error() affiche la page d'erreur puis arrête le script (render()
     * se termine par die()) : après une erreur, la suite n'est pas exécutée.
     */
    public function action_informations()
    {
        if (!isset($_GET['id']) || !is_strictly_positive_integer($_GET['id'])) {
            $this->action_error("There is no valid id in the url.");
        }

        $infos = Model::getModel()->getNobelPrizeInformations((int) $_GET['id']);
        if ($infos === false) {
            $this->action_error("There is no nobel prize with such id.");
        }

        // Les clés du tableau (year, name...) deviennent les variables de la vue
        $this->render("informations", $infos);
    }

    /**
     * Exercice 7, question 3 : tous les prix, 25 par page.
     * Le numéro de la page est dans le paramètre start (1 s'il est absent ou invalide).
     */
    public function action_pagination()
    {
        $nb_per_page = 25;
        $start = 1;
        if (isset($_GET['start']) && is_strictly_positive_integer($_GET['start'])) {
            $start = (int) $_GET['start'];
        }

        $model = Model::getModel();
        $nb_pages = (int) ceil($model->getNbNobelPrizes() / $nb_per_page);
        if ($start > $nb_pages) {
            $this->action_error("This page does not exist.");
        }

        $data = [
            'nobels' => $model->getNobelPrizesWithLimit(($start - 1) * $nb_per_page, $nb_per_page),
            'start' => $start,
            'nb_pages' => $nb_pages,
            // Adresse des pages, sans le paramètre start (ajouté par view_pages_links.php)
            'link' => '?controller=list&action=pagination',
        ];
        $this->render("pagination", $data);
    }
}
