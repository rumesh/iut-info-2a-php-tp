# TP3 — Le site des prix Nobel en MVC

Ce dossier contient les corrigés du TP3 : le site des prix Nobel du TP2,
réorganisé selon le modèle **MVC** (Modèle – Vue – Contrôleur), à partir de
l'archive `MVC.zip` fournie avec l'énoncé.

> Énoncé du TP : <https://lipn.univ-paris13.fr/~lacroix/teaching/R301/TP/TP3/>

Comme le TP2, il a besoin de PHP **et** d'une base PostgreSQL : tout se lance en
une commande avec **Docker**.

---

## Sommaire

1. [Installer Docker](#1-installer-docker)
2. [Lancer le TP](#2-lancer-le-tp)
3. [Comprendre l'organisation MVC](#3-comprendre-lorganisation-mvc)
4. [Se connecter à la base : credentials.php](#4-se-connecter-à-la-base--credentialsphp)
5. [Ce qui a été corrigé dans le modèle fourni](#5-ce-qui-a-été-corrigé-dans-le-modèle-fourni)
6. [Commandes utiles](#6-commandes-utiles)
7. [En cas de problème](#7-en-cas-de-problème)
8. [Organisation du dossier](#8-organisation-du-dossier)

---

## 1. Installer Docker

C'est exactement la même installation que pour le TP2 : si Docker fonctionne
déjà pour le TP2, il n'y a rien à faire. Sinon, suivre
[TP2/README.md, partie 2](../TP2/README.md#2-installer-docker) (Windows, macOS
et Linux).

---

## 2. Lancer le TP

> Sous Windows et macOS, **Docker Desktop doit être démarré** avant de taper
> les commandes ci-dessous.

1. **Récupérer les nouveautés** du dépôt (ou le cloner, voir le TP2) :

   ```bash
   git pull
   ```

2. **Arrêter le TP2** s'il tourne encore : les deux TP utilisent les mêmes
   adresses (ports 8080 et 8081).

   ```bash
   cd TP2
   docker compose down
   cd ..
   ```

3. **Démarrer le TP3**, depuis le dossier `TP3` :

   ```bash
   cd TP3
   docker compose up -d
   ```

   Le TP3 réutilise l'image PHP du TP2 (dossier `TP2/docker`) : le premier
   démarrage est donc rapide si le TP2 a déjà été lancé.

4. **Ouvrir le navigateur** :

   | Adresse | Ce que l'on voit |
   | --- | --- |
   | <http://localhost:8080> | l'accueil du TP3 : la liste des corrigés |
   | <http://localhost:8080/exercice8/> | le site complet, à la fin du TP |
   | <http://localhost:8081> | Adminer, pour explorer la base (système PostgreSQL, serveur `db`, utilisateur et mot de passe `etudiant`, base `etudiants`) |

5. **Arrêter** quand on a fini :

   ```bash
   docker compose down
   ```

### Travailler sur sa propre version

Le dossier `TP3` est partagé avec le serveur : il suffit d'y décompresser
l'archive `MVC.zip` de l'énoncé. Le dossier `TP3/MVC` s'ouvre alors à l'adresse
<http://localhost:8080/MVC/>, et il apparaît aussi sur la page d'accueil, dans
« Autres dossiers ».

Les adresses de l'énoncé s'écrivent alors ainsi :

| Dans l'énoncé | Avec Docker |
| --- | --- |
| `http://localhost/~VOTRE_NUMERO_ETUDIANT/MVC/` | <http://localhost:8080/MVC/> |
| `…/MVC/?controller=list&action=informations&id=1` | <http://localhost:8080/MVC/?controller=list&action=informations&id=1> |

---

## 3. Comprendre l'organisation MVC

Dans le TP2, chaque page était un fichier (`last25.php`, `form_add.php`…). En
MVC, **toutes les pages passent par `index.php`**, et c'est l'adresse qui dit
quoi afficher :

```
index.php?controller=list&action=informations&id=1
          └─ contrôleur ─┘ └──── action ─────┘ └ paramètre ┘
```

1. **`index.php`** (le routeur) vérifie que le contrôleur demandé fait partie de
   la liste `$controllers`, puis crée l'objet `Controller_list`.
2. **Le contrôleur** (`Controllers/Controller_list.php`) appelle la méthode
   `action_informations()`. Sans paramètre `action`, c'est
   `action_default()` qui est appelée.
3. L'action demande les données au **modèle** (`Models/Model.php`, la seule
   classe qui parle à la base), puis les passe à une **vue** avec
   `$this->render("informations", $data)`.
4. **La vue** (`Views/view_informations.php`) affiche le HTML. Chaque clé du
   tableau `$data` y devient une variable : `$data['name']` devient `$name`.

| Dossier | Contenu | Exercice où il apparaît |
| --- | --- | --- |
| `Controllers/` | `Controller_home` (accueil), `Controller_list` (listes, détails), `Controller_set` (ajout, suppression, modification), `Controller_search` (recherche) | 1, 2, 4, 8 |
| `Models/` | `Model.php`, fourni avec l'archive | — |
| `Views/` | une vue `view_xxx.php` par page, plus les morceaux communs (`view_begin`, `view_end`, `view_list_nobel`, `view_pages_links`) | tous |
| `Utils/` | `functions.php` : `e()` pour échapper le HTML, et les fonctions de vérification des données | 3, 4 |

> **À retenir** : `render()` se termine par `die()`. Après un
> `$this->action_error(...)`, la suite de l'action n'est donc jamais exécutée.

---

## 4. Se connecter à la base : credentials.php

Le modèle inclut **`TP3/credentials.php`**, partagé par tous les exercices.
Il définit les trois variables demandées par l'énoncé : `$dsn`, `$login` et
`$mdp`.

| Où ? | Valeurs utilisées |
| --- | --- |
| **Avec Docker** | celles de `docker-compose.yml` : rien à modifier |
| **À l'IUT** (sans Docker) | celles écrites dans `credentials.php` : serveur `aquabdd`, base `etudiants`. **Remplacer** `numero_etudiant` et `code_INE` par son numéro étudiant et son code INE |

> À l'IUT, l'énoncé range ce fichier dans `/home/Web/Auth/`, **en dehors** du
> dossier publié sur le web : un mot de passe ne doit jamais se trouver dans un
> fichier que l'on peut atteindre avec une adresse.

---

## 5. Ce qui a été corrigé dans le modèle fourni

`Models/Model.php` est celui de l'archive, à trois changements près, signalés
par un commentaire `Correction` (ou `Exercice 1`) dans le code :

| Changement | Pourquoi |
| --- | --- |
| `include` de `credentials.php` | Exercice 1, question 2 |
| `ORDER BY year DESC, id` au lieu de `ORDER BY year DESC` | Plusieurs prix ont la même année : sans `id` pour les départager, PostgreSQL peut les renvoyer dans un ordre différent à chaque requête, et la pagination (exercice 7) répéterait ou sauterait des prix. |
| `==` remplacé par `=` dans la recherche | `==` n'existe pas en SQL : PostgreSQL refusait la requête (exercice 8, recherche « année == … »). |

La recherche par nom utilise `LIKE`, qui distingue les majuscules : chercher
`Curie` trouve les Curie, mais pas `curie`.

---

## 6. Commandes utiles

Toutes ces commandes se tapent **dans le dossier `TP3`**.

| Commande | Effet |
| --- | --- |
| `docker compose up -d` | démarre tout |
| `docker compose down` | arrête tout (la base est conservée) |
| `docker compose logs web` | affiche le journal du serveur web (erreurs Apache/PHP) |
| `docker compose exec db psql -U etudiant -d etudiants` | ouvre `psql` sur la base |
| `docker compose exec db psql -U etudiant -d etudiants -f /sql/database_nobel.sql` | remet la base à zéro (après des ajouts, suppressions ou modifications) |
| `docker compose down -v` | arrête tout **et efface la base** (elle sera recréée au prochain démarrage) |
| `docker compose build --pull` | met à jour l'image PHP (correctifs de sécurité) |

---

## 7. En cas de problème

**« port is already allocated » / « address already in use »**
Le TP2 (ou un autre programme) utilise déjà le port 8080 ou 8081. Lancer
`docker compose down` dans le dossier `TP2`, puis relancer le TP3.

**« Error 404: not found! »**
Le contrôleur demandé dans l'adresse n'est pas dans la liste `$controllers`
d'`index.php`, ou son fichier `Controllers/Controller_xxx.php` est introuvable
(attention aux majuscules : `Controller_list.php`).

**« La vue n'existe pas ! »**
Le nom donné à `render()` ne correspond à aucun fichier : `render("last")`
cherche `Views/view_last.php`.

**Une page blanche, ou « could not find driver »**
Voir [TP2/README.md, partie 7](../TP2/README.md#7-en-cas-de-problème) : les
autres problèmes sont les mêmes qu'au TP2.

---

## 8. Organisation du dossier

```
TP3/
├── README.md              ce fichier
├── index.php              page d'accueil (http://localhost:8080)
├── docker-compose.yml     description des conteneurs (image PHP du TP2)
├── credentials.php        identifiants de connexion à la base
├── database_nobel.sql     script de création de la base (839 prix)
└── exercice1/ … exercice8/   l'application MVC telle qu'elle est à la fin de
                           chaque exercice : chaque dossier reprend le
                           précédent et y ajoute les nouveautés
```

Chaque dossier `exerciceN` est complet (avec le dossier `Content` : CSS,
polices, icônes) et s'ouvre par son `index.php`.
