# Composant Routing : Fonctionnement Interne

## Concept clé
Le composant **Routing** de Symfony est responsable de l'analyse de l'URL entrante pour déterminer quel code exécuter (le Contrôleur). Il fonctionne dans deux sens :
1.  **Match** : URL vers Paramètres (Requête Entrante).
2.  **Generate** : Paramètres vers URL (Génération de liens).

## Architecture et Classes Clés

Le processus repose sur quelques classes fondamentales que vous devez connaître pour la certification.

### 1. RouteCollection
C'est le conteneur qui stocke toutes les définitions de routes de l'application.
*   Chaque route est une instance de la classe `Symfony\Component\Routing\Route`.
*   Elle contient : `path`, `defaults` (controller), `requirements` (regex), `options`, `host`, `schemes`, `methods`.

### 2. RequestContext
Cet objet contient des informations sur la requête *courante* nécessaires au routeur pour matcher ou générer des URLs.
*   **Base URL** : Le dossier où est installé le site (si pas à la racine).
*   **Method** : GET, POST, etc.
*   **Host** : `example.com`.
*   **Scheme** : http ou https.

> Sans `RequestContext`, le routeur ne peut pas savoir si l'URL générée doit être absolue ou relative, ni si la méthode HTTP correspond.

### 3. UrlMatcher
C'est la classe qui fait le travail de correspondance ("Matching").
*   **Entrée** : Le `pathinfo` de l'URL (ex: `/blog/my-post`).
*   **Processus** : Il parcourt la `RouteCollection` (ou une version compilée optimisée) pour trouver la première route qui correspond.
*   **Sortie** : Un tableau de paramètres (`_controller`, `_route`, `slug`, etc.).

### 4. UrlGenerator
C'est la classe inverse du Matcher.
*   **Entrée** : Le nom de la route (`blog_show`) et des paramètres (`['slug' => 'my-post']`).
*   **Sortie** : Une URL string (`/blog/my-post`).

### 5. Router (La façade)
La classe `Symfony\Bundle\FrameworkBundle\Routing\Router` (service `router`) est celle que vous utilisez au quotidien. Elle implémente `RouterInterface` qui combine `UrlMatcherInterface` et `UrlGeneratorInterface`.

## Le Flux de Requête (Interne)

1.  **HttpKernel** reçoit la `Request`.
2.  L'événement `kernel.request` est déclenché.
3.  Le **RouterListener** intercepte cet événement.
4.  Il appelle le `Router::match()` avec l'URL de la requête.
5.  Le `Router` retourne un tableau de paramètres (ex: `['_controller' => '...', 'id' => 12]`).
6.  Le `RouterListener` injecte ces paramètres dans `$request->attributes`.
7.  Le **ControllerResolver** (plus tard dans le cycle) lit `$request->attributes->get('_controller')` pour savoir quelle classe instancier.

## Compilation et Cache
Pour des raisons de performance, Symfony ne lit pas les fichiers YAML/Attributs à chaque requête en production.

1.  Le routeur compile toutes les routes en une seule grosse classe PHP optimisée :
    *   `var/cache/prod/UrlMatcher.php`
    *   `var/cache/prod/UrlGenerator.php`
2.  Cette classe contient une énorme expression régulière (Regex) combinée qui permet de matcher l'URL très rapidement.

## 🧠 Concepts Clés
1.  **Matching vs Generating** : Le même fichier de configuration sert aux deux opérations.
2.  **RequestContext** : Indispensable pour générer des URLs absolues (`scheme://host...`).
3.  **Strict Requirements** : Par défaut, si une route exige `GET` et que vous arrivez en `POST`, le matcher lance une `MethodNotAllowedException` (405) au lieu de continuer à chercher une autre route (sauf configuration contraire).

## ⚠️ Points de vigilance (Certification)
*   **Ordre** : Le matching s'arrête à la première route trouvée ("First Match Wins").
*   **Paramètres spéciaux** : Les paramètres commençant par `_` (underscore) sont réservés par Symfony (`_controller`, `_route`, `_locale`, `_format`).

## Ressources
*   [Symfony Docs - Routing Component](https://symfony.com/doc/current/components/routing.html)
