<?php

class Controller_set extends Controller
{
    /**
     * Action par défaut (exercice 4, question 1) : le formulaire d'ajout
     */
    public function action_default()
    {
        $this->action_form_add();
    }

    /**
     * Exercice 4, question 1 : le formulaire d'ajout d'un prix
     */
    public function action_form_add()
    {
        $data = [
            'categories' => Model::getModel()->getCategories(),
        ];
        $this->render("form_add", $data);
    }

    /**
     * Exercice 4, question 2 : ajout du prix envoyé par le formulaire
     */
    public function action_add()
    {
        if (empty($_POST)) {
            $message = "No form has been submitted.";
        } else {
            $infos = check_data();
            if ($infos === false) {
                $message = "Error: the name, the category and the year (a strictly positive integer) are required.";
            } else {
                // Une catégorie inconnue viole la clé étrangère : PDO lève alors une exception
                try {
                    $added = Model::getModel()->addNobelPrize($infos);
                } catch (PDOException $e) {
                    $added = false;
                }
                $message = $added ? "The nobel prize has been added." : "Error: the nobel prize has not been added.";
            }
        }

        $data = [
            'title' => "Add a Nobel Prize",
            'message' => $message,
        ];
        $this->render("message", $data);
    }

    /**
     * Exercice 5, question 1 : suppression du prix dont l'id est dans l'URL
     */
    public function action_remove()
    {
        $model = Model::getModel();

        if (!isset($_GET['id']) || !is_strictly_positive_integer($_GET['id'])) {
            $message = "There is no id in the url.";
        } elseif (!$model->isInDataBase((int) $_GET['id'])) {
            $message = "There is no nobel prize with such id.";
        } else {
            $model->removeNobelPrize((int) $_GET['id']);
            $message = "The nobel prize has been removed.";
        }

        $data = [
            'title' => "Remove a Nobel Prize",
            'message' => $message,
        ];
        $this->render("message", $data);
    }

    /**
     * Exercice 6, question 2 : le formulaire de modification, pré-rempli avec
     * les informations du prix dont l'id est dans l'URL
     */
    public function action_form_update()
    {
        $model = Model::getModel();
        $infos = false;
        if (isset($_GET['id']) && is_strictly_positive_integer($_GET['id'])) {
            $infos = $model->getNobelPrizeInformations((int) $_GET['id']);
        }

        if ($infos === false) {
            $this->action_error("There is no nobel prize with such id.");
        }

        // La vue reçoit $id, $year, $name... et la liste $categories
        $infos['categories'] = $model->getCategories();
        $this->render("form_update", $infos);
    }

    /**
     * Exercice 6, question 3 : mise à jour du prix envoyé par le formulaire
     */
    public function action_update()
    {
        $updated = false;

        if (isset($_POST['id']) && is_strictly_positive_integer($_POST['id'])) {
            // check_data() vérifie name, category et year comme pour l'ajout
            $infos = check_data();
            if ($infos !== false) {
                $infos['id'] = (int) $_POST['id'];
                try {
                    $updated = Model::getModel()->updateNobelPrize($infos);
                } catch (PDOException $e) {
                    $updated = false;
                }
            }
        }

        $data = [
            'title' => "Update a Nobel Prize",
            'message' => $updated ? "The nobel prize has been updated." : "Error: the nobel prize has not been updated.",
        ];
        $this->render("message", $data);
    }
}
