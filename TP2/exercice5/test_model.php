<?php
// Exercice 5 : petite page pour tester les méthodes de la classe Model
require_once "Model.php";

$model = Model::get_model();
?>
<!doctype html>
<html lang="fr">
<head>
<title>Exercice 5 - Test de la classe Model</title>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
</head>
<body>
<main>

<h1>Test de la classe Model</h1>

<h2>get_nb_nobel_prizes()</h2>
<p><?php echo $model->get_nb_nobel_prizes(); ?> prix Nobel dans la base.</p>

<h2>Singleton</h2>
<p>
    Deux appels à Model::get_model() renvoient-ils le même objet ?
    <strong><?php echo Model::get_model() === $model ? 'oui' : 'non'; ?></strong>
</p>

<h2>get_last()</h2>
<ol>
    <?php foreach ($model->get_last() as $nobel) { ?>
        <li><?php echo htmlspecialchars($nobel['name']) . ' (' . $nobel['category'] . ', ' . $nobel['year'] . ')'; ?></li>
    <?php } ?>
</ol>

</main>
</body>
</html>
