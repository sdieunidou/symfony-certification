# Bonnes Pratiques de Déploiement

## Concept clé
Le déploiement est le passage de l'état de développement à l'état de production.
L'objectif est la performance et la stabilité.

## Checklist de Production

### 1. Environnement
*   `APP_ENV=prod`
*   `APP_DEBUG=0`
*   Ceci désactive le Profiler, le re-parsing des configs à chaque requête, et active le cache agressif.

### 2. Dépendances
*   `composer install --no-dev --optimize-autoloader --classmap-authoritative`
*   Supprime les paquets de test/debug et optimise le chargement des classes (Map statique).

### 3. Cache Warmup
*   `php bin/console cache:clear`
*   Cette commande vide le cache ET le "réchauffe" (warmup) : compile le conteneur, les routes, les templates Twig, les annotations. Cela évite que le premier utilisateur ne subisse le temps de compilation.

### 4. Assets
*   Compilation des assets (Webpack Encore / AssetMapper).
*   `php bin/console asset-map:compile`

### 5. OPcache (PHP)
Crucial pour la performance.
*   `opcache.validate_timestamps=0` : PHP ne vérifie plus si les fichiers ont changé. Gain d'I/O énorme. (Implique de redémarrer PHP-FPM à chaque déploiement).
*   **Preloading** : Configurer le script de préchargement généré par Symfony (`var/cache/prod/App_KernelProdContainer.preload.php`) dans `opcache.preload`.

## Secrets (Vault)
Ne stockez pas les mots de passe en clair dans `.env` sur le serveur.
Utilisez le composant Secrets pour chiffrer les valeurs.
*   `php bin/console secrets:set DATABASE_URL`
*   Nécessite la clé de déchiffrement (`config/secrets/prod/prod.decrypt.private.php`) sur le serveur.

## 🧠 Concepts Clés
1.  **Atomicité** : Utilisez des déploiements atomiques (ex: lien symbolique vers le nouveau dossier de release) pour éviter de servir une application cassée pendant l'upload des fichiers.
2.  **Trust Proxies** : Configurez les IPs de vos Load Balancers (Cloudflare, AWS) dans `trusted_proxies` pour avoir la bonne IP client et le bon protocole HTTPS.

## ⚠️ Points de vigilance (Certification)
*   **Permissions** : Les dossiers `var/log` et `var/cache` doivent être accessibles en écriture par l'utilisateur web (www-data). Le reste doit être en lecture seule idéalement.
*   **Doctrine** : Ne jamais faire `doctrine:schema:update` en prod. Utilisez les migrations (`doctrine:migrations:migrate`).

## Ressources
*   [Symfony Docs - Deployment](https://symfony.com/doc/current/deployment.html)
