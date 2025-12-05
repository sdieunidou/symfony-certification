# Méthodes HTTP (Verbes)

## Concept clé
Les méthodes HTTP indiquent l'action souhaitée sur la ressource identifiée par l'URI.
Les plus courantes (REST) :
*   **GET** : Récupérer une ressource (Lecture). Doit être "safe" (pas de modif) et "idempotent".
*   **POST** : Créer une ressource ou traiter des données. Non idempotent.
*   **PUT** : Remplacer complètement une ressource (Mise à jour complète). Idempotent.
*   **PATCH** : Modifier partiellement une ressource.
*   **DELETE** : Supprimer une ressource. Idempotent.
*   **HEAD** : Comme GET, mais sans le corps (juste les headers).
*   **OPTIONS** : Décrire les options de communication (CORS).

## Application dans Symfony 7.0
Le routage Symfony permet de restreindre les routes à certaines méthodes.
Le formulaire Symfony utilise `POST` par défaut (et simule PUT/DELETE/PATCH).

## Exemple de code

```php
<?php
// Dans un contrôleur avec Attributs
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    // Uniquement accessible en GET
    #[Route('/product/{id}', methods: ['GET'])]
    public function show(int $id): Response
    {
        // ...
    }

    // Uniquement accessible en POST
    #[Route('/product', methods: ['POST'])]
    public function create(Request $request): Response
    {
        // ...
    }
    
    // Simulation de PUT via _method dans le formulaire
    #[Route('/product/{id}', methods: ['PUT'])]
    public function update(int $id): Response 
    {
        // ...
    }
}
```

## Points de vigilance (Certification)
*   **Safe vs Idempotent** :
    *   **Safe** (Sûr) : Ne modifie pas l'état du serveur (GET, HEAD).
    *   **Idempotent** : Faire la requête 1 fois ou 10 fois a le même effet final (GET, PUT, DELETE). POST n'est **pas** idempotent (crée 10 ressources si envoyé 10 fois).
*   **HEAD** : Symfony gère souvent HEAD automatiquement si une route GET existe (le routeur matche GET, mais la réponse n'envoie pas le contenu).
*   **OPTIONS** : Utilisé pour le "Preflight" dans les requêtes CORS (Cross-Origin).

## Ressources
*   [MDN - Méthodes HTTP](https://developer.mozilla.org/fr/docs/Web/HTTP/Methods)

