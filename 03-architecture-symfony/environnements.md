# Les Environnements & Configuration

## Concept clé

Symfony utilise la notion d'**environnements** pour exécuter la même application avec des configurations différentes selon le contexte (développement, test, production).

L'environnement est défini par une variable d'environnement système nommée **`APP_ENV`**.

## Fonctionnalités Principales

### 1. Les environnements standards
Par défaut, Symfony propose trois environnements :

*   **dev** : Pour le développement local.
    *   Pas de cache (ou très peu).
    *   Affichage détaillé des erreurs (Stack traces).
    *   Web Profiler activé.
    *   Outils de debug disponibles (`dump()`, etc.).
*   **prod** : Pour le déploiement en production.
    *   Cache maximal (Preloading, Services compilés).
    *   Pas d'affichage d'erreurs à l'utilisateur (Pages d'erreur génériques).
    *   Optimisation des performances.
*   **test** : Pour l'exécution des tests automatisés (PHPUnit).
    *   Similaire à `dev` mais optimisé pour la vitesse des tests.
    *   Services de test activés (ex: `test.client`).

### 2. La variable `APP_DEBUG`
Indépendamment de l'environnement, le mode de débogage est contrôlé par `APP_DEBUG` (0 ou 1).
*   En `dev`, il est à `1` par défaut (erreurs affichées, cache invalidé à chaque modification).
*   En `prod`, il est à `0` par défaut.

> **Note :** Il est possible (mais déconseillé) d'avoir `APP_ENV=prod` avec `APP_DEBUG=1` pour débugger un problème spécifique à la prod, mais cela ralentit l'application.

### 3. Gestion des variables d'environnement (Dotenv)
Symfony utilise le composant **Dotenv** pour charger les variables depuis des fichiers `.env` à la racine du projet.

**Hiérarchie de chargement (du moins prioritaire au plus prioritaire) :**

1.  `.env` : Variables par défaut (commité dans Git).
2.  `.env.local` : Surcharges locales spécifiques à la machine (NON commité, `.gitignore`).
3.  `.env.{env}` : Variables spécifiques à un environnement (ex: `.env.test`).
4.  `.env.{env}.local` : Surcharges locales pour un environnement spécifique (ex: `.env.test.local`).

**Vraies variables d'environnement :**
Si une "vraie" variable d'environnement est définie au niveau du serveur (Apache, Nginx, Docker, Shell), elle **écrase** toujours les valeurs définies dans les fichiers `.env`.

### 4. Configuration par environnement
La configuration des services et des bundles peut varier selon l'environnement grâce à la structure du dossier `config/` :

```text
config/
├── packages/
│   ├── framework.yaml        # Config globale
│   ├── dev/
│   │   ├── framework.yaml    # Surcharge pour DEV
│   │   └── monolog.yaml      # Config logs pour DEV
│   ├── prod/
│   │   ├── doctrine.yaml     # Cache Doctrine pour PROD
│   │   └── monolog.yaml      # Config logs pour PROD
│   └── test/
```

## 🧠 Concepts Clés

1.  **Isolation** : Chaque environnement est isolé. Le cache de `dev` est dans `var/cache/dev`, celui de `prod` dans `var/cache/prod`.
2.  **Secrets** : Pour les données sensibles (clés API, mots de passe BDD) en production, préférez l'utilisation des "Vraies" variables d'environnement ou le système de **Symfony Secrets** (Vault) plutôt que des fichiers `.env` en clair.
3.  **Processors** : Dans les fichiers `.env`, on peut utiliser des processeurs comme `file:` (lire le contenu d'un fichier) ou `base64:` (décoder).

## ⚠️ Points de vigilance (Certification)

*   **`APP_ENV` vs `APP_DEBUG`** : Ne confondez pas les deux. L'environnement définit la *configuration* chargée, le debug définit le *comportement* du noyau (gestion d'erreurs, recompile du container).
*   **Performance** : En `prod`, Symfony ne vérifie pas si les fichiers de config ont changé. Il faut vider le cache (`cache:clear`) après chaque déploiement.
*   **Dump** : La fonction `dump()` ne doit pas être utilisée en production (sauf si configurée spécifiquement via `debug:dump`). En `prod`, le `VarDumper` est souvent configuré pour ne rien afficher.
*   **Environnement par défaut** : Si `APP_ENV` n'est pas défini, Symfony utilise `dev` par défaut (dans le composant Dotenv, sauf configuration contraire).

## Ressources

*   [Configuration Environments](https://symfony.com/doc/current/configuration.html#configuration-environments)
*   [The Dotenv Component](https://symfony.com/doc/current/components/dotenv.html)
*   [Managing Secrets](https://symfony.com/doc/current/configuration/secrets.html)
