# Cache HTTP et Sessions Utilisateur

## Le Conflit Fondamental
Le cache HTTP et les sessions utilisateur sont par nature opposés.
*   **Cache HTTP** : Veut servir le même contenu à tout le monde.
*   **Session** : Rend le contenu unique pour chaque utilisateur.

Par défaut, dès que Symfony démarre une session (pour lire ou écrire), il désactive automatiquement le cache public pour éviter les fuites de données (Information Leakage).
Il ajoute : `Cache-Control: private, must-revalidate`.

## Stratégies de Cache avec Session

### 1. Contenu Privé (User Specific)
C'est le cas standard (Mon Compte, Panier).
*   **Solution** : Utiliser le cache `private` (Navigateur) ou pas de cache du tout.
*   Symfony le fait par défaut.

### 2. Contenu Public avec Session (Le piège)
Exemple : Un site de News où l'utilisateur est loggué (Session active), mais le contenu de l'article est le même pour tous.
Si vous forcez `$response->setPublic()` alors qu'une session est active :
*   **Danger** : Si vous affichez "Bonjour [User]" dans le header, Varnish va cacher cette page avec "Bonjour Pierre" et la servir à Paul.
*   **Solution** : Ne JAMAIS afficher de données utilisateur sur une page publique cachée.

### 3. Gestion des données utilisateur sur page cachée
Pour afficher des infos utilisateur (Barre de login, Panier) sur une page cachée publiquement (ESI, Cache Partagé), il faut séparer le contenu.

**Approche A : ESI (Edge Side Includes)**
La page principale est publique. Le bloc "User Bar" est un fragment ESI privé (ou non caché).
*   Le proxy cache la page.
*   Pour chaque requête, il demande le fragment "User Bar" à l'application (avec le cookie de session).

**Approche B : AJAX / Hinclude**
La page est statique et publique.
Le navigateur fait une requête JS (`fetch('/api/user-info')`) pour remplir les zones utilisateur après le chargement.

## Désactiver l'automatisation Symfony
Dans des cas très avancés, vous pouvez vouloir empêcher Symfony de rendre la réponse privée automatiquement lors de l'usage de la session.

```php
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;

// Dit à Symfony : "Ne touche pas au header Cache-Control, je sais ce que je fais"
$response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
```

## Ressources
*   [Symfony Docs - HTTP Cache & User Sessions](https://symfony.com/doc/current/http_cache.html#http-caching-and-user-sessions)

