# Installation & Configuration de Symfony

## Prérequis Techniques
Avant de démarrer, l'environnement doit disposer de :
*   **PHP 8.2+**
*   Extensions PHP : Ctype, iconv, PCRE, Session, SimpleXML, Tokenizer.
*   **Composer** : Gestionnaire de paquets.
*   **Symfony CLI** (Optionnel mais recommandé) : Outil binaire pour créer, gérer et servir l'application.

```bash
symfony check:requirements
```

## Création d'un Projet

### Via Symfony CLI (Recommandé)
```bash
# Application Web complète (Monolithe)
symfony new my_project_directory --version="7.4.x-dev" --webapp

# Microservice / API / Console (Minimal)
symfony new my_project_directory --version="7.4.x-dev"
```
*   `--webapp` : Installe automatiquement les paquets "standards" (Twig, Doctrine, Profiler, etc.).
*   `--version` : Permet de cibler une version (`lts`, `next`, `6.4.*`).

### Via Composer
```bash
# Skeleton minimal
composer create-project symfony/skeleton:"7.4.x-dev" my_project_directory

# Ajouter le pack Webapp ensuite
cd my_project_directory
composer require webapp
```

## Serveur de Développement
Symfony CLI fournit un serveur web local optimisé (HTTP/2, TLS automatique).

```bash
symfony server:start
```
Accessible sur `http://localhost:8000`.

## Symfony Flex
Flex est un plugin Composer installé par défaut qui **automatise** la configuration des paquets.
*   **Recipes** : Scripts de configuration exécutés lors du `composer require`.
    *   *Main Repository* : Recettes officielles et auditées.
    *   *Contrib Repository* : Recettes communautaires.
*   Génère les fichiers par défaut dans `config/` et met à jour `.env`.

### Symfony Packs
Des méta-paquets Composer qui installent un groupe de dépendances logiques.
Exemple : `composer require debug` installe `symfony/debug-pack` (Profiler, VarDumper, Monolog...).

## Sécurité
Vérification des vulnérabilités dans les dépendances installées.

```bash
symfony check:security
# ou en CI
composer audit
```

## LTS & Versioning
Symfony suit un cycle de release strict :
*   **LTS (Long Term Support)** : Supporté 3 ans (+1 an sécurité). Publié tous les 2 ans (ex: 6.4).
*   **Versions Standard** : Supporté 8 mois. Publié tous les 6 mois (ex: 7.0, 7.1, 7.2...).

## Démo
Pour apprendre, on peut installer l'application de démonstration officielle :
```bash
symfony new my_project --demo
```

## Ressources
*   [Symfony Docs - Setup](https://symfony.com/doc/current/setup.html)
*   [Symfony CLI](https://symfony.com/download)

