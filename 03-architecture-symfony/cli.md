# Symfony CLI

## Concept clé
Le **Symfony CLI** est un outil binaire (exécutable) écrit en Go, à installer sur la machine de développement.
Contrairement à la `bin/console` qui est spécifique à un projet PHP, le CLI est un outil global pour gérer l'écosystème Symfony.

Il remplace avantageusement l'ancien "Symfony Installer" et offre un environnement de développement local complet.

## Fonctionnalités Principales

### 1. Serveur Web Local (Web Server)
C'est la fonctionnalité phare. Il lance un serveur web haute performance (basé sur le serveur web interne de PHP mais boosté) supportant HTTP/2 et TLS automatiquement.

```bash
symfony server:start
symfony server:start -d  # En arrière-plan (daemon)
symfony server:log       # Voir les logs (access + error + app)
```

**Avantages :**
*   **TLS (HTTPS)** : Génère et installe un certificat local valide automatiquement (`symfony server:ca:install`). Fini les avertissements de sécurité en dev.
*   **Version PHP** : Détecte et utilise la version PHP configurée ou requise par le projet.
*   **Parallélisme** : Gère mieux les requêtes concurrentes que le serveur PHP natif basique.

### 2. Gestion de Projet
Création rapide de nouveaux projets.

```bash
# Crée un micro-service (Skeleton)
symfony new my_project

# Crée une application web complète (Webapp)
symfony new my_project --webapp

# Spécifier la version
symfony new my_project --version=lts
```

### 3. Vérification de Sécurité
Analyse le fichier `composer.lock` pour détecter les failles de sécurité connues dans les dépendances.

```bash
symfony check:security
```
Cette commande contacte la base de données de sécurité de Symfony (Security Advisories Database).

### 4. Intégration Symfony Cloud (Upsun)
Le CLI est aussi l'outil officiel pour interagir avec SymfonyCloud (anciennement Platform.sh).
*   `symfony deploy`
*   `symfony env:create`
*   `symfony tunnel:open` (pour se connecter à la BDD de prod depuis localhost).

### 5. Proxy de commandes PHP
Le CLI agit comme un proxy intelligent pour exécuter PHP ou Composer avec la bonne configuration (php.ini, variables d'env).

```bash
# Au lieu de "php bin/console"
symfony console make:controller

# Au lieu de "composer require"
symfony composer require logger

# Au lieu de "php -v"
symfony php -v
```
Cela garantit que vous utilisez la même version de PHP que celle utilisée par le serveur web local.

## 🧠 Concepts Clés
1.  **Local Web Server** : Il supporte le **Domain Name** local (ex: `mon-projet.wip`) via un proxy interne, évitant de modifier le fichier `/etc/hosts`.
2.  **Worker** : Le CLI peut lancer des workers en arrière-plan (Messenger) en mimant le comportement de production.

## ⚠️ Points de vigilance (Certification)
*   **Distinction** : Ne pas confondre `symfony` (le binaire Go) avec `bin/console` (le script PHP du projet).
*   **Non obligatoire** : Le CLI n'est **PAS** obligatoire pour faire du Symfony. On peut très bien utiliser Apache/Nginx ou `php -S`. C'est juste un outil de productivité recommandé.
*   **Architecture** : Le serveur web local supporte l'exécution parallèle via PHP-FPM s'il est détecté, sinon il utilise FastCGI.

## Ressources
*   [Download Symfony CLI](https://symfony.com/download)
*   [Symfony Local Web Server](https://symfony.com/doc/current/setup/symfony_server.html)
