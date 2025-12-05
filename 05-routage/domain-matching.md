# Correspondance de Nom de Domaine (Host Matching)

## Concept clé
Le routage Symfony peut matcher non seulement sur le chemin (`/path`) mais aussi sur le **domaine** (`api.example.com`).
C'est essentiel pour les applications multi-domaines ou multi-tenants.

## Application dans Symfony 7.0

### 1. Restreindre à un sous-domaine
```php
// Matche uniquement sur mobile.monsite.com/
#[Route('/', host: 'mobile.monsite.com')]
public function mobileIndex(): Response { ... }
```

### 2. Sous-domaine dynamique (Placeholders)
Vous pouvez capturer une partie du domaine comme paramètre.

```php
// Matche {user}.monsite.com/
#[Route('/', host: '{username}.monsite.com')]
public function userHome(string $username): Response
{
    // $username est disponible comme un paramètre d'URL classique
}
```

### 3. Restriction de Protocole (Schemes)
Forcer HTTPS ou HTTP.

```php
#[Route('/login', schemes: ['https'])]
```
*Note : Aujourd'hui, on force souvent HTTPS globalement au niveau du serveur web ou du Load Balancer, donc cette option est moins utilisée par route.*

## 🧠 Concepts Clés
1.  **Priorité** : Le Host Matching s'ajoute au Path Matching. Pour que la route matche, **TOUT** doit correspondre (Host + Path + Method).
2.  **Tests Locaux** : Pour tester ça en local, vous devez modifier votre fichier `/etc/hosts` (Linux/Mac) ou `hosts` (Windows) pour mapper `mobile.localhost` vers `127.0.0.1`.
3.  **Requirements** : On peut valider les placeholders du host avec des regex, comme pour le path.
    *   `requirements: ['username' => '[a-z]+']`

## ⚠️ Points de vigilance (Certification)
*   **Génération d'URL** : Si vous générez une URL vers une route qui a un `host` différent de la requête courante, Symfony générera automatiquement une URL **absolue** (http://...). Si le host est le même, il génère une URL relative (/...).
*   **Context** : Le routeur a besoin de connaître le host de la requête actuelle (`RequestContext`) pour faire ce matching.

## Ressources
*   [Symfony Docs - Sub-Domain Routing](https://symfony.com/doc/current/routing.html#matching-a-host)
