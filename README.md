# TP PHP — Corrigés des exercices

## 👉 Voir les corrigés en ligne

**<https://static.rumesh.net/iut-info-2-anne-tp1/index.php>**

Rien à installer : les exercices s'exécutent et leur code source s'affiche
directement dans le navigateur.

---

Ce dépôt contient les corrigés des TP, un dossier par TP et un dossier par
exercice, ainsi que la page d'accueil (`index.php`) qui permet de tous les
parcourir facilement.

| TP | Sujet | Ce qu'il faut pour l'exécuter |
| --- | --- | --- |
| [TP1](TP1/) | Premières pages PHP, formulaires, sessions, cookies | PHP seul |
| [TP2](TP2/) | Site des prix Nobel avec une base de données | PHP + PostgreSQL : **voir [TP2/README.md](TP2/README.md)** (installation avec Docker) |
| [TP3](TP3/) | Le même site, réorganisé en MVC (modèle, vues, contrôleurs) | PHP + PostgreSQL : **voir [TP3/README.md](TP3/README.md)** |

## Lancer le projet en local

Pour travailler sur sa propre machine plutôt qu'en ligne.

> Les exercices des **TP2 et TP3** ont besoin d'une base PostgreSQL : pour eux,
> suivre plutôt [TP2/README.md](TP2/README.md) et [TP3/README.md](TP3/README.md),
> qui expliquent comment tout lancer avec Docker sous Windows, macOS et Linux.
> La méthode ci-dessous suffit pour le TP1.

Une page PHP doit être exécutée par un serveur : il ne suffit pas de
double-cliquer sur un fichier `.php`, sinon le navigateur affiche le code au lieu
de l'exécuter. PHP contient un petit serveur intégré, c'est tout ce dont on a
besoin ici.

1. Ouvrir un terminal dans le dossier du projet.
2. Lancer le serveur :

   ```bash
   php -S localhost:8000
   ```

3. Ouvrir cette adresse dans le navigateur :

   **<http://localhost:8000>**

Pour arrêter le serveur : `Ctrl + C` dans le terminal.

> Si le port 8000 est déjà utilisé, on peut en choisir un autre, par exemple
> `php -S localhost:8080`, puis ouvrir <http://localhost:8080>.

## Ce que l'on voit sur la page

La page d'accueil est découpée en trois colonnes :

| Colonne | Contenu |
| --- | --- |
| **Exercices** (gauche) | La liste des exercices disponibles, rangés par TP, avec les pages de chacun |
| **Résultat** (centre) | La page choisie, réellement exécutée par le serveur |
| **Code source** (droite) | Le code des fichiers de l'exercice, avec la coloration syntaxique |

Dans la colonne de droite, un onglet par fichier : on y retrouve aussi les
fichiers inclus (`debut_code_html.php`, `fin_code_html.php`) et les classes
(`TODOList.php`, `Model.php`), même s'ils ne s'ouvrent pas directement dans le
navigateur. Pour le TP3, les onglets montrent aussi les fichiers des
sous-dossiers : `Controllers/…`, `Models/…`, `Views/…`, `Utils/…`.

Le lien « ouvrir dans un onglet ↗ » affiche l'exercice seul, en pleine page.

La séparation entre le résultat et le code se fait glisser pour élargir l'un ou
l'autre (au clavier : flèches gauche et droite ; double-clic pour revenir à
moitié-moitié). En haut à droite, « Résultat seul » ou « Code seul » n'affiche
qu'un des deux panneaux, en pleine largeur.

## Organisation du dépôt

```
index.php          la page d'accueil
TP1/
  exercice1/       un dossier par exercice
  exercice2/
  ...
TP2/
  README.md        installation de PHP + PostgreSQL avec Docker
  exercice1/
  ...
TP3/
  README.md        lancer le TP3 et comprendre l'organisation MVC
  exercice1/       l'application MVC complète, un dossier par exercice
  ...
```

La liste n'est écrite nulle part : `index.php` lit le contenu des dossiers `TP1`,
`TP2`, `TP3`… à chaque affichage et construit les liens tout seul. Les exercices sont publiés au fur et
à mesure de l'avancement du TP : la version en ligne se complète donc toute
seule, il suffit de recharger la page.

En local, pour récupérer les exercices qui viennent d'être ajoutés :

```bash
git pull
```

Il n'y a rien à modifier dans `index.php` : les nouveaux dossiers apparaissent
automatiquement dans la colonne de gauche.

## Prérequis

PHP 8 ou plus récent. Pour vérifier qu'il est installé :

```bash
php -v
```

Pour les TP2 et TP3 : Docker (voir [TP2/README.md](TP2/README.md)).
