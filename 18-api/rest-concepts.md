# Concepts REST & API

## Les Fondamentaux REST
REST (Representational State Transfer) est un style d'architecture pour les systèmes distribués.
Il ne s'agit pas d'un protocole, mais d'un ensemble de contraintes.

### Les 6 contraintes REST
1.  **Client-Serveur** : Séparation des préoccupations.
2.  **Stateless (Sans état)** : Le serveur ne garde pas de contexte client entre deux requêtes. Chaque requête doit contenir toute l'information nécessaire (ex: Token).
3.  **Cacheable** : Les réponses doivent définir si elles sont cachables ou non.
4.  **Layered System (Système à couches)** : Le client ne sait pas s'il est connecté directement au serveur final ou à un intermédiaire (Load Balancer).
5.  **Code on Demand** (Optionnel) : Le serveur peut envoyer du code exécutable (JS).
6.  **Uniform Interface** : La contrainte la plus importante.
    *   Identification des ressources (URI).
    *   Manipulation par représentations (JSON, XML).
    *   Messages auto-descriptifs (Content-Type).
    *   HATEOAS (Hypermedia As The Engine Of Application State).

## Modèle de Maturité de Richardson
Échelle pour évaluer la "RESTitude" d'une API.

*   **Niveau 0** : The Swamp of POX. HTTP utilisé comme tunnel de transport (ex: SOAP, XML-RPC). Une seule URI, un seul verbe (souvent POST).
*   **Niveau 1** : Ressources. Utilisation d'URIs distinctes pour chaque ressource (`/api/users/123`), mais verbes HTTP mal utilisés.
*   **Niveau 2** : Verbes HTTP. Utilisation correcte de GET, POST, PUT, DELETE et des codes de statut. **C'est le niveau de la plupart des "REST APIs" actuelles.**
*   **Niveau 3** : Hypermedia (HATEOAS). L'API guide le client via des liens dans la réponse pour découvrir les actions possibles.

## Codes HTTP spécifiques API

En plus des classiques (200, 404, 500), une API utilise souvent :

| Code | Signification | Usage |
| :--- | :--- | :--- |
| **201** | Created | Après un `POST` réussi. Doit retourner un header `Location` vers la ressource créée. |
| **204** | No Content | Après un `DELETE` réussi ou un `PUT` sans contenu retourné. |
| **400** | Bad Request | Erreur syntaxique ou sémantique dans la requête (ex: JSON invalide). |
| **401** | Unauthorized | Token manquant ou invalide (Qui êtes-vous ?). |
| **403** | Forbidden | Token valide mais droits insuffisants (Vous n'avez pas le droit). |
| **405** | Method Not Allowed | Méthode non supportée sur cette URI (ex: POST sur une ressource read-only). |
| **406** | Not Acceptable | Le serveur ne peut pas produire le format demandé par le header `Accept`. |
| **415** | Unsupported Media Type | Le serveur refuse le format envoyé dans le payload (ex: XML envoyé alors que JSON attendu). |
| **422** | Unprocessable Entity | Syntaxe correcte mais erreur de validation métier (ex: email invalide). Standardisé par WebDAV mais standard en API REST. |
| **429** | Too Many Requests | Rate limiting (Quota dépassé). |

## 🧠 Concepts Clés
1.  **Ressource vs Représentation** : Une ressource est un concept abstrait (un "Utilisateur"). Une représentation est une vue concrète de cette ressource à un instant T (un document JSON, un export XML).
2.  **Stateless** : C'est la différence majeure avec une appli web classique. Pas de `$_SESSION`. L'authentification se fait à chaque requête.

## Ressources
*   [Richardson Maturity Model (Martin Fowler)](https://martinfowler.com/articles/richardsonMaturityModel.html)
