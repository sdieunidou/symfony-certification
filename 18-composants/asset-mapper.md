# Component AssetMapper

## Concept Clé
Le composant **AssetMapper** (introduit dans Symfony 6.3) est une alternative moderne à Webpack Encore. Il permet de gérer les assets (CSS, JS, Images) sans Node.js, sans npm et sans build step complexe, en utilisant les standards modernes du web (Import Maps).

## Fonctionnement
1.  **Import Maps** : Génère un fichier JSON listant les modules JavaScript disponibles.
2.  **Versioning** : Ajoute des hashs aux noms de fichiers pour le cache busting.
3.  **Vendor** : Télécharge les paquets tiers directement dans `assets/vendor` (pas de `node_modules`).

## Exemple `importmap.php`
```php
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.1',
    ],
];
```

## Commande CLI
```bash
# Installer un paquet
php bin/console importmap:require bootstrap
```

## Avantages
*   **Simplicité** : Pas de toolchain JS à configurer.
*   **Performance** : HTTP/2 permet de charger de nombreux petits fichiers efficacement.
*   **PHP-first** : Tout est géré par Symfony.

## Ressources
*   [Symfony Docs - AssetMapper](https://symfony.com/doc/current/frontend/asset_mapper.html)
