# Correspondance de Nom de Domaine (Host Matching)

## Concept clé
Restreindre une route à un sous-domaine ou un domaine spécifique. Utile pour les applications multi-tenants ou ayant une API sur `api.example.com` et le site sur `www.example.com`.

## Application dans Symfony 7.0
Utilisation de l'option `host`.

```php
// Matche uniquement sur mobile.example.com
#[Route('/', host: 'mobile.example.com')]
public function mobileHome(): Response { ... }

// Avec placeholder
#[Route('/', host: '{subdomain}.example.com')]
public function tenantHome(string $subdomain): Response { ... }
```

## Points de vigilance (Certification)
*   **Paramètres** : Les placeholders du host (`{subdomain}`) sont passés comme arguments au contrôleur, tout comme les paramètres d'URL.
*   **Environnement** : En dev local (`localhost`), cela peut être difficile à tester. Il faut souvent configurer le fichier `/etc/hosts`.
*   **HTTP vs HTTPS** : Le host matching ne vérifie pas le protocole (utiliser `schemes: ['https']` pour ça).

## Ressources
*   [Symfony Docs - Sub-Domain Routing](https://symfony.com/doc/current/routing.html#matching-a-host)

