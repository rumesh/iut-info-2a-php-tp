<?php

class TODOList {
    private $to_dos;

    public function __construct() {
        $this->to_dos = [];
    }

    public function add_to_do($tache) {
        if (trim($tache) !== '') {
            $this->to_dos[] = $tache;
        }
    }

    public function remove_to_do($indice) {
        if (isset($this->to_dos[$indice])) {
            unset($this->to_dos[$indice]);
            $this->to_dos = array_values($this->to_dos);
        }
    }

    public function is_empty() {
        return empty($this->to_dos);
    }

    // Question 3 (exercice 11) : chaque tâche devient un lien vers tdl.php?rm=<indice>
    public function get_html() {
        if ($this->is_empty()) {
            return '<p>Aucune tâche à faire !</p>';
        }
        $html = '<ul>';
        foreach ($this->to_dos as $indice => $tache) {
            $texte = htmlspecialchars($tache);
            // aria-label : un lecteur d'écran annonce "Supprimer : <tâche>", et pas seulement le nom de la tâche
            $html .= '<li><a href="tdl.php?rm=' . $indice . '" aria-label="Supprimer : ' . $texte . '">' . $texte . '</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }

    // Question 5 : représentation textuelle de la liste (séparateur "///")
    public function get_representation() {
        return implode('///', $this->to_dos);
    }

    public function set_representation($representation) {
        $this->to_dos = ($representation === '') ? [] : explode('///', $representation);
    }
}
