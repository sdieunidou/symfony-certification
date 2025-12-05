# Attributs de Routage Spéciaux

## Concept clé
Symfony utilise des attributs de requête commençant par `_` (underscore) pour piloter le framework.
Ces attributs sont automatiquement remplis par le Routeur lorsqu'une route matche.

## Liste des Attributs Magiques

### 1. `_controller`
Détermine le code à exécuter.
*   Format : `App\Controller\BlogController::index` (ou Service ID).
*   C'est l'attribut le plus important. Sans lui, pas de page.

### 2. `_route`
Contient le **nom** de la route matchée (ex: `blog_show`).
*   Utile pour le débogage, les menus (active class), ou les logs.

### 3. `_route_params`
Tableau contenant tous les paramètres extraits de l'URL (ex: `['id' => '123']`).

### 4. `_format`
Force le format de la requête pour la Content Negotiation.
*   Si défini dans la route (`defaults: { _format: 'json' }`), `$request->getRequestFormat()` renverra 'json'.
*   Configure automatiquement le `Content-Type` de la réponse.

### 5. `_locale`
Force la locale de la requête.
*   Déclenche le `LocaleListener` qui fait `$request->setLocale(...)`.
*   Impacte les traductions et le formatage des dates/nombres.

### 6. `_fragment`
Utilisé pour les sous-requêtes ESI/Hinclude. Contient des informations de sécurité (signature) pour s'assurer que le fragment est appelé par le serveur et non par un utilisateur malveillant.

### 7. `_stateless` (Symfony 6+)
Indique que cette route ne doit pas utiliser de Session.
*   Si la route tente de démarrer la session, une exception est levée (en debug).
*   Utile pour sécuriser les APIs REST.

## Exemple d'Usage (Contrôleur)

```php
// On peut injecter ces attributs comme arguments de contrôleur
public function index(string $_route, string $_format): Response
{
    // $_route = 'blog_index'
    // $_format = 'html'
}
```

## 🧠 Concepts Clés
1.  **Réservés** : Ne créez pas vos propres paramètres commençant par `_` (ex: `_my_param`) dans les URLs (`/blog/{_my_param}`). C'est risqué.
2.  **Request Attributes** : Tous ces paramètres finissent dans `$request->attributes`.

## ⚠️ Points de vigilance (Certification)
*   **Priorité** : Si l'URL contient un paramètre `{_locale}` (ex: `/fr/...`), il écrase toute valeur par défaut.
*   **Controller Arguments** : Symfony mappe les attributs de requête aux arguments de la méthode contrôleur par nom. Donc `$request->attributes->get('slug')` est injecté dans `$slug`.

## Ressources
*   [Symfony Docs - Special Attributes](https://symfony.com/doc/current/routing.html#special-parameters)
