<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<?php // Chaque vue définit $title avant d'inclure ce fichier : le titre de l'onglet, annoncé en premier par les lecteurs d'écran, dit sur quelle page on est ?>
		<title><?= isset($title) ? e($title) . ' — ' : '' ?>Nobel Prizes</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="Content/css/nobel.css"/>
	</head>
	<body>
		<a class="skip-link" href="#content">Skip to main content</a>

		<header>
			<h1><a href="?"> Nobel Prizes </a></h1>
		</header>

		<nav aria-label="Main menu">
			<ul>
				<li><a href="?controller=list&amp;action=last"> Last Nobel Prizes</a></li>
				<li><a href="?controller=set&amp;action=form_add"> Add a Nobel prize</a></li>
			</ul>
		</nav>

		<main id="content">