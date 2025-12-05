# Mise en cache Client-side (Navigateur)

## Concept clé
C'est le cache le plus proche de l'utilisateur.

## Application dans Symfony 7.0
On utilise principalement `private` et `max-age`.

```php
// Données utilisateur sensibles (Panier, Compte)
// Cacheable uniquement par le navigateur de l'utilisateur
$response->setPrivate();
$response->setMaxAge(600);
```

## Points de vigilance (Certification)
*   **Comportement par défaut** : Si une session est démarrée, Symfony ajoute automatiquement `Cache-Control: private, must-revalidate`. C'est une sécurité pour ne pas cacher des données privées sur un proxy public.
*   **No-Cache** : `no-cache` ne veut pas dire "ne pas cacher", mais "valider avec le serveur avant d'utiliser la version cachée" (Validation Model forcé).
*   **No-Store** : `no-store` veut dire "ne jamais stocker sur le disque". C'est ce qu'il faut pour les données bancaires.

## Ressources
*   [MDN - Cache-Control](https://developer.mozilla.org/fr/docs/Web/HTTP/Headers/Cache-Control)

