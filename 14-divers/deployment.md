# Bonnes Pratiques de Déploiement

## Concept clé
Mettre en production une application Symfony de manière fiable et performante.

## Application dans Symfony 7.0
Checklist typique :
1.  **Environment** : Définir `APP_ENV=prod`.
2.  **Dependencies** : `composer install --no-dev --optimize-autoloader`.
3.  **Cache** : `php bin/console cache:clear` (Le warmup est automatique).
4.  **Assets** : `php bin/console asset-map:compile` (ou `yarn build`).
5.  **OPcache** : Activer et configurer OPcache (preload).

## Points de vigilance (Certification)
*   **APP_SECRET** : Doit être changé en prod.
*   **Permissions** : Les dossiers `var/` doivent être accessibles en écriture par le serveur web.
*   **Secrets** : Utiliser `bin/console secrets:set` pour chiffrer les credentials sensibles (DB password, API keys) au lieu de les laisser en clair dans `.env`.

## Ressources
*   [Symfony Docs - Deployment](https://symfony.com/doc/current/deployment.html)

