# TP2 — Les prix Nobel (PHP + PostgreSQL)

Ce dossier contient les corrigés du TP2 et tout ce qu'il faut pour les exécuter
sur sa propre machine : un serveur web PHP **et** une base de données
PostgreSQL déjà remplie avec les 839 prix Nobel, lancés en une seule commande
grâce à **Docker**.

> Énoncé du TP : <https://lipn.univ-paris13.fr/~lacroix/teaching/R301/TP/TP2/>

---

## Sommaire

1. [Pourquoi Docker ?](#1-pourquoi-docker-)
2. [Installer Docker](#2-installer-docker)
   - [Windows](#windows)
   - [macOS](#macos)
   - [Linux](#linux)
3. [Lancer le TP](#3-lancer-le-tp)
4. [Exercice 1 : utiliser psql](#4-exercice-1--utiliser-psql)
5. [Écrire son propre code](#5-écrire-son-propre-code)
6. [Commandes utiles](#6-commandes-utiles)
7. [En cas de problème](#7-en-cas-de-problème)
8. [Organisation du dossier](#8-organisation-du-dossier)

---

## 1. Pourquoi Docker ?

Le TP2 a besoin de deux logiciels qui tournent en même temps :

| Logiciel | Rôle |
| --- | --- |
| **PHP + Apache** | exécute les pages `.php` |
| **PostgreSQL** | stocke les prix Nobel |

Les installer à la main (et faire en sorte que PHP sache parler à PostgreSQL)
est long et différent sur chaque système. Docker fait tout cela à notre place :
il démarre chaque logiciel dans un **conteneur**, une sorte de mini-ordinateur
déjà configuré. Le fichier `docker-compose.yml` décrit ces conteneurs :

| Conteneur | Adresse | Contenu |
| --- | --- | --- |
| `web` | <http://localhost:8080> | les pages PHP du dossier `TP2` |
| `db` | (interne) | la base PostgreSQL `etudiants` |
| `adminer` | <http://localhost:8081> | une interface web pour explorer la base |

Au premier démarrage, la base est **créée et remplie automatiquement** avec le
script `database_nobel.sql`.

---

## 2. Installer Docker

Il suffit de le faire **une seule fois**. À la fin, ces deux commandes doivent
fonctionner dans un terminal :

```bash
docker --version
docker compose version
```

### Windows

1. **Vérifier la version de Windows** : Windows 10 (version 22H2 ou plus
   récente) ou Windows 11, en 64 bits.
2. **Installer WSL 2** (le sous-système Linux de Windows, utilisé par Docker) :
   - ouvrir **PowerShell en tant qu'administrateur**
     (clic droit sur le menu Démarrer → « Terminal (administrateur) ») ;
   - taper :

     ```powershell
     wsl --install
     ```

   - **redémarrer** l'ordinateur.
3. **Installer Docker Desktop** :
   - télécharger l'installateur sur
     <https://www.docker.com/products/docker-desktop/> (bouton
     « Download for Windows ») ;
   - lancer `Docker Desktop Installer.exe` et garder l'option
     **« Use WSL 2 instead of Hyper-V »** cochée ;
   - redémarrer si l'installateur le demande.
4. **Démarrer Docker Desktop** depuis le menu Démarrer. Accepter les
   conditions ; la création d'un compte Docker n'est **pas** nécessaire
   (« Continue without signing in »).
5. Attendre que l'icône de la baleine 🐳 en bas à droite indique
   **« Docker Desktop is running »**.
6. Vérifier dans **PowerShell** :

   ```powershell
   docker --version
   docker compose version
   ```

> **Erreur « virtualization is not enabled » ?** La virtualisation doit être
> activée dans le BIOS/UEFI de l'ordinateur (option appelée « Intel VT-x »,
> « AMD-V » ou « SVM »). Redémarrer, entrer dans le BIOS (souvent `F2`, `F10`,
> `Suppr` ou `Échap` au démarrage) et l'activer.

### macOS

1. Savoir quel processeur a le Mac : menu  → **« À propos de ce Mac »** :
   - « Puce Apple M1/M2/M3/M4… » → version **Apple Silicon** ;
   - « Processeur Intel… » → version **Intel**.
2. Télécharger **Docker Desktop** pour le bon processeur sur
   <https://www.docker.com/products/docker-desktop/>.
3. Ouvrir le fichier `Docker.dmg` et **glisser Docker dans le dossier
   Applications**.
4. Lancer **Docker** depuis le dossier Applications (ou Spotlight : `Cmd + Espace`
   puis « Docker »). Accepter les autorisations demandées ; la création d'un
   compte n'est pas nécessaire.
5. Attendre que l'icône de la baleine 🐳 dans la barre de menus soit stable.
6. Vérifier dans le **Terminal** :

   ```bash
   docker --version
   docker compose version
   ```

> Avec [Homebrew](https://brew.sh), on peut aussi installer Docker Desktop
> avec `brew install --cask docker`, puis le lancer depuis Applications.

### Linux

Sous Linux, on installe **Docker Engine** (pas besoin de Docker Desktop).
Le script officiel fonctionne sur Ubuntu, Debian, Linux Mint, Fedora…

1. Installer Docker et le plugin `docker compose` :

   ```bash
   curl -fsSL https://get.docker.com -o get-docker.sh
   sudo sh get-docker.sh
   ```

2. Démarrer Docker, et le lancer automatiquement au démarrage de l'ordinateur :

   ```bash
   sudo systemctl enable --now docker
   ```

3. Autoriser son utilisateur à utiliser Docker **sans `sudo`** :

   ```bash
   sudo usermod -aG docker $USER
   ```

   Puis **se déconnecter et se reconnecter** (ou redémarrer) pour que ce
   changement soit pris en compte.
4. Vérifier :

   ```bash
   docker --version
   docker compose version
   docker run --rm hello-world
   ```

   La dernière commande doit afficher « Hello from Docker! ».

---

## 3. Lancer le TP

> Sous Windows et macOS, **Docker Desktop doit être démarré** avant de taper
> les commandes ci-dessous.

1. **Récupérer le projet** (une seule fois) :

   ```bash
   git clone https://github.com/rumesh/iut-info-2-anne-tp1.git
   ```

   S'il est déjà sur la machine, récupérer simplement les nouveautés :

   ```bash
   git pull
   ```

2. **Ouvrir un terminal dans le dossier `TP2`** :

   ```bash
   cd iut-info-2-anne-tp1/TP2
   ```

   > Astuce Windows : dans l'Explorateur, ouvrir le dossier `TP2`, puis
   > clic droit dans le dossier → « Ouvrir dans le Terminal ».

3. **Démarrer les conteneurs** :

   ```bash
   docker compose up -d
   ```

   La première fois, Docker télécharge et prépare les images : cela peut
   prendre quelques minutes. Les fois suivantes, c'est quasi instantané.
   L'option `-d` lance tout en arrière-plan et rend la main au terminal.

4. **Ouvrir le navigateur** :

   | Adresse | Ce que l'on voit |
   | --- | --- |
   | <http://localhost:8080> | l'accueil du TP2 : la liste des exercices et de leurs pages |
   | <http://localhost:8080/exercice2/nbpn.php> | un exercice précis |
   | <http://localhost:8080/exercice14/home.php> | le site complet des prix Nobel |
   | <http://localhost:8081> | Adminer, pour explorer la base |

   Pour se connecter à **Adminer** :

   | Champ | Valeur |
   | --- | --- |
   | Système | PostgreSQL |
   | Serveur | `db` |
   | Utilisateur | `etudiant` |
   | Mot de passe | `etudiant` |
   | Base de données | `etudiants` |

5. **Arrêter** quand on a fini :

   ```bash
   docker compose down
   ```

   Les données de la base sont **conservées** : on les retrouve au prochain
   `docker compose up -d`.

---

## 4. Exercice 1 : utiliser psql

À l'IUT, l'exercice 1 se fait avec `psql -h aquabdd -d etudiants`. Avec Docker,
`psql` est déjà installé **dans** le conteneur `db` :

```bash
docker compose exec db psql -U etudiant -d etudiants
```

On arrive à l'invite `etudiants=#`. Le script est disponible dans le conteneur
sous le nom `/sql/database_nobel.sql` :

```sql
\i /sql/database_nobel.sql
SELECT count(*) FROM nobels;
```

Le résultat doit être **839**. Pour quitter `psql` : `\q`.

> Le script commence par supprimer les tables (`DROP TABLE IF EXISTS`) : on
> peut donc le relancer à tout moment pour **remettre la base à zéro**, par
> exemple après avoir supprimé ou modifié des prix pendant les exercices 10 à 12.

---

## 5. Écrire son propre code

Le dossier `TP2` est **partagé** avec le conteneur `web` : tout fichier créé ou
modifié dans `TP2` est immédiatement visible sur <http://localhost:8080>, il
suffit de recharger la page. Pas besoin de redémarrer Docker.

Par exemple, créer `TP2/mon_tp/test.php` puis ouvrir
<http://localhost:8080/mon_tp/test.php>. Les dossiers ajoutés ainsi
apparaissent aussi sur la page d'accueil <http://localhost:8080>, dans
« Autres dossiers ».

> Apache n'affiche pas le contenu d'un dossier : pour <http://localhost:8080/mon_tp/>,
> il faut soit ouvrir directement une page (`…/mon_tp/test.php`), soit créer un
> fichier `index.php` dans le dossier.

### Se connecter à la base

Les paramètres de connexion sont rangés dans **`TP2/config.php`**, partagé par
tous les exercices. Depuis un fichier placé dans un sous-dossier de `TP2` :

```php
<?php
require_once __DIR__ . '/../config.php';

$bd = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
$bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$nb = $bd->query('SELECT count(*) FROM nobels')->fetchColumn();
echo "La base contient $nb prix Nobel.";
```

| Où ? | Valeurs utilisées |
| --- | --- |
| **Avec Docker** | celles de `docker-compose.yml` : rien à modifier |
| **À l'IUT** (sans Docker) | celles écrites dans `config.php` : serveur `aquabdd`, base `etudiants`. **Remplacer** `numero_etudiant` et `code_INE` par son numéro étudiant et son code INE |

> Les erreurs PHP s'affichent directement dans la page : c'est voulu, pour
> aider à corriger son code.

---

## 6. Commandes utiles

Toutes ces commandes se tapent **dans le dossier `TP2`**.

| Commande | Effet |
| --- | --- |
| `docker compose up -d` | démarre tout |
| `docker compose down` | arrête tout (la base est conservée) |
| `docker compose ps` | affiche l'état des conteneurs |
| `docker compose logs web` | affiche le journal du serveur web (erreurs Apache/PHP) |
| `docker compose exec db psql -U etudiant -d etudiants` | ouvre `psql` sur la base |
| `docker compose down -v` | arrête tout **et efface la base** (elle sera recréée, avec les 839 prix, au prochain démarrage) |
| `docker compose up -d --build` | reconstruit l'image PHP (après une modification de `docker/Dockerfile`) |

---

## 7. En cas de problème

**« Cannot connect to the Docker daemon » / « error during connect »**
Docker n'est pas démarré. Sous Windows et macOS, lancer **Docker Desktop** et
attendre qu'il soit prêt. Sous Linux : `sudo systemctl start docker`.

**« permission denied while trying to connect to the Docker daemon socket » (Linux)**
L'étape `sudo usermod -aG docker $USER` n'a pas été faite, ou la session n'a
pas été rouverte depuis. Se déconnecter puis se reconnecter.

**« port is already allocated » / « address already in use »**
Un autre programme utilise déjà le port 8080 (ou 8081). Dans
`docker-compose.yml`, remplacer `"8080:80"` par exemple par `"8090:80"`, relancer
`docker compose up -d`, puis ouvrir <http://localhost:8090>.

**La page affiche « could not find driver »**
La page n'est pas exécutée par le conteneur Docker (par exemple avec
`php -S` sur sa machine, sans le pilote PostgreSQL). Passer par
<http://localhost:8080>.

**« relation "nobels" does not exist »**
La base est vide. Relancer le script (voir [l'exercice 1](#4-exercice-1--utiliser-psql)),
ou repartir de zéro avec `docker compose down -v` puis `docker compose up -d`.

**Le site est lent sous Windows**
Docker Desktop lit plus vite les fichiers rangés dans WSL que ceux du disque
Windows. Pour de meilleures performances, cloner le projet dans WSL (taper
`wsl` dans PowerShell, puis `git clone` depuis ce terminal Linux).

---

## 8. Organisation du dossier

```
TP2/
├── README.md              ce fichier
├── index.php              page d'accueil (http://localhost:8080)
├── docker-compose.yml     description des conteneurs (web, db, adminer)
├── docker/Dockerfile      image PHP + Apache + pilote PostgreSQL
├── config.php             paramètres de connexion à la base
├── database_nobel.sql     script de création de la base (839 prix)
├── exercice1/ … exercice4/   pages indépendantes (nbpn.php, last.php, infos.php)
├── exercice5/             la classe Model (+ une page de test)
└── exercice6/ … exercice14/  le site « PrixNobel », tel qu'il est à la fin de
                           chaque exercice : chaque dossier reprend le
                           précédent et y ajoute les nouveautés
```

Les dossiers `exercice6` à `exercice14` contiennent chacun une copie complète du
site (avec le dossier `Content` de l'archive `PrixNobel.zip` : CSS, polices,
icônes) : chacun peut donc être ouvert et testé indépendamment des autres.
