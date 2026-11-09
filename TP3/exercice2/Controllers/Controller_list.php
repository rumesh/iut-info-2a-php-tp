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
}
