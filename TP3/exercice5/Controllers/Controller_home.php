<?php

class Controller_home extends Controller
{
    /**
     * Action par défaut (exercice 1, question 4) : la page d'accueil
     */
    public function action_default()
    {
        $this->action_home();
    }

    /**
     * Exercice 1, question 4 : la page d'accueil, avec le nombre de prix Nobel
     */
    public function action_home()
    {
        $data = [
            'nb_nobels' => Model::getModel()->getNbNobelPrizes(),
        ];
        $this->render("home", $data);
    }
}
