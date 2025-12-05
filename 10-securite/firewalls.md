# Firewalls (Pare-feu)

## Concept clé
Un Firewall intercepte la requête HTTP au tout début (`kernel.request`) pour gérer l'authentification.
Une application peut avoir plusieurs firewalls (ex: un pour l'API, un pour le Front, un pour l'Admin).

## Options Importantes

### 1. `pattern` (Regex)
Définit quelles URLs sont gérées par ce firewall.
*   `^/api` : Tout ce qui commence par /api.
*   `^/` : Tout le reste (Default).

### 2. `security: false`
Désactive complètement la couche sécurité (listeners) pour ce pattern.
Indispensable pour les assets et le profiler en dev pour éviter de charger la session et l'user inutilement.

### 3. `stateless: true`
Indique que ce firewall ne doit pas utiliser de session.
*   Symfony ne tentera pas de lire/écrire le cookie `PHPSESSID`.
*   L'authentification doit être fournie à **chaque** requête (ex: API Token).
*   Si `false` (défaut), l'utilisateur est stocké en session après le login.

### 4. `lazy: true`
Recommandé pour les firewalls stateful.
Ne démarre la session et ne charge l'utilisateur que si l'application en a réellement besoin (ex: appel à `is_granted` ou `getUser`). Si la page est publique, aucune ressource DB n'est consommée.

### 5. `switch_user` (Impersonnation)
Permet de se faire passer pour un autre utilisateur (utile pour le support client).
*   Config : `switch_user: true`
*   Usage : Ajouter `?_switch_user=email@user.com` dans l'URL (nécessite le rôle `ROLE_ALLOWED_TO_SWITCH`).
*   Sortie : `?_switch_user=_exit`.

## 🧠 Concepts Clés
1.  **Isolation** : Par défaut, l'authentification n'est pas partagée entre les firewalls.
2.  **Ordre** : Toujours du plus spécifique au plus générique. Le firewall `main` (`pattern: ^/`) doit être le dernier.

## ⚠️ Points de vigilance (Certification)
*   **Firewall vs Access Control** : Le firewall gère "Qui je suis" (AuthN). Access Control gère "Où je peux aller" (AuthZ). Un firewall peut très bien laisser passer un utilisateur anonyme sur une page publique (si `anonymous: true` ou `lazy: true`).

## Ressources
*   [Symfony Docs - Firewalls](https://symfony.com/doc/current/security.html#firewalls)
