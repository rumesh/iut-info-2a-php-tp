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
}
