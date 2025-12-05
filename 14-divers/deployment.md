# Bonnes Pratiques de Déploiement

## Concept clé
Le déploiement est le passage de l'état de développement à l'état de production.
L'objectif est la performance, la stabilité et la sécurité.

## Stratégies de Déploiement
1.  **Basic File Transfer (FTP/SCP)** : Copie manuelle. Risqué et déconseillé (fichiers incohérents pendant l'upload, rollback difficile).
2.  **Source Control (Git)** : `git pull` sur le serveur. Mieux, mais nécessite une gestion manuelle des dépendances et migrations.
3.  **Build Scripts / Outils** :
    *   **Deployer** (PHP) : Outil standard, scriptable en PHP.
    *   **Ansistrano** (Ansible) : Automatisation via YAML.
    *   **Plateformes PaaS** (Platform.sh / Upsun) : Déploiement intégré et géré par l'hébergeur.

## Checklist de Production (Common Tasks)

### 1. Vérification des Prérequis
Sur le serveur de production, il est recommandé de vérifier la compatibilité de l'environnement.
*   En dev : `symfony check:requirements`
*   En prod (sans CLI) : `composer require symfony/requirements-checker` (génère un script PHP web ou CLI à exécuter).

### 2. Environnement & Variables
*   **Définition** : Variables d'environnement réelles (Nginx, Systemd) OU fichier `.env.prod.local`.
*   **Optimisation** : `composer dump-env prod`.
    *   Génère un fichier `.env.local.php` optimisé (tableau PHP statique).
    *   Évite le parsing coûteux des fichiers `.env` à chaque requête.
*   **Configuration** :
    *   `APP_ENV=prod`
    *   `APP_DEBUG=0` (Désactive le Profiler et le re-parsing des configs).

### 3. Dépendances (Composer)
*   `composer install --no-dev --optimize-autoloader --classmap-authoritative`
*   `--no-dev` : Exclut les paquets de test/profiling.
*   `--optimize-autoloader` : Génère une classmap pour accélérer l'autoloading.
*   **Note** : Si erreur "class not found", vérifier que `APP_ENV=prod` est bien défini avant l'install (pour les scripts post-install).

### 4. Cache Warmup
*   `APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear`
*   Vide le cache ET le "réchauffe" (warmup) : compile le conteneur, les routes, les templates Twig, les annotations. Évite la lenteur pour le premier utilisateur.

### 5. Base de Données
*   **Migrations** : `php bin/console doctrine:migrations:migrate --no-interaction`
*   *Jamais de `doctrine:schema:update` en production.*

### 6. Assets (Frontend)
*   **Webpack Encore** : `npm run build` puis upload du dossier `public/build`.
*   **AssetMapper** : `php bin/console asset-map:compile`.

### 7. Performance PHP (OPcache)
*   `opcache.validate_timestamps=0` : PHP ne vérifie plus si les fichiers ont changé sur le disque (Gain I/O).
    *   *Implique un redémarrage obligatoire de PHP-FPM à chaque déploiement.*
*   **Preloading** : Charger le script généré `var/cache/prod/App_KernelProdContainer.preload.php` dans la directive `opcache.preload` du `php.ini`.

## Secrets (Vault)
Pour sécuriser les identifiants sensibles (API Keys, DB password) :
*   Ne pas les stocker en clair dans `.env`.
*   Utiliser `php bin/console secrets:set DATABASE_URL`.
*   Nécessite la clé de déchiffrement (`config/secrets/prod/prod.decrypt.private.php`) sur le serveur de production.

## Troubleshooting & Cas Particuliers
*   **Absence de `composer.json`** : Si vous déployez uniquement l'artifact (sans `composer.json` à la racine), surchargez la méthode `Kernel::getProjectDir()` pour indiquer la bonne racine du projet, sinon Symfony risque de ne pas trouver les répertoires `var/` ou `config/`.
*   **Apache** : Sur un hébergement mutualisé, le paquet `symfony/apache-pack` peut être nécessaire pour générer le `.htaccess`.

## 🧠 Concepts Clés (Certification)
1.  **Atomicité** : Le déploiement ne doit pas casser le site en cours de route. L'idéal est de préparer un nouveau dossier (release), de tout construire dedans (warmup), puis de changer un **lien symbolique** `current` pointant vers ce dossier.
2.  **Trust Proxies** : Si derrière un Load Balancer (AWS, Cloudflare), configurer `trusted_proxies` pour récupérer la bonne IP client et le protocole HTTPS.

## Ressources
*   [Symfony Docs - Deployment](https://symfony.com/doc/current/deployment.html)
