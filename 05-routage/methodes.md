# Correspondance de Méthodes HTTP

## Concept clé
Une route peut être restreinte à certains verbes HTTP (GET, POST, PUT, DELETE).

## Application dans Symfony 7.0

```php
#[Route('/api/posts', methods: ['GET'])]
public function list(): Response { ... }

#[Route('/api/posts', methods: ['POST'])]
public function create(): Response { ... }
```

## Points de vigilance (Certification)
*   **405 Method Not Allowed** : Si l'URL matche mais pas la méthode, Symfony renvoie une 405 (et non une 404), avec le header `Allow` listant les méthodes supportées.
*   **Formulaires HTML** : Rappel, les formulaires HTML ne supportent que GET et POST. Pour PUT/DELETE, il faut utiliser `_method` (voir section HTTP).

## Ressources
*   [Symfony Docs - Method Restriction](https://symfony.com/doc/current/routing.html#matching-http-methods)

