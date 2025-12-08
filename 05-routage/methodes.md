# Restriction de Méthodes HTTP

## Concept clé
Une route ne doit matcher que les verbes HTTP pour lesquels elle est conçue.
*   Afficher un formulaire : `GET`.
*   Traiter un formulaire : `POST`.
*   Supprimer : `DELETE` (ou POST avec `_method`).

## Application dans Symfony 7.0
Utilisez l'option `methods`.

```php
// GET uniquement
#[Route('/blog/{id}', name: 'blog_show', methods: ['GET'])]

// POST uniquement
#[Route('/blog', name: 'blog_create', methods: ['POST'])]

// Multiples
#[Route('/blog/{id}', methods: ['GET', 'POST'])]
```

## Comportement du Routeur
1.  Le routeur cherche les routes qui matchent le **Path** (`/blog/1`).
2.  Parmi celles-ci, il filtre celles qui matchent la **Method**.
3.  Si une route matche le Path mais **pas la Méthode** :
    *   Le routeur continue de chercher.
    *   Si aucune autre route ne matche, il lance une `MethodNotAllowedException` (Code 405).
    *   Il ajoute automatiquement le header `Allow: GET, POST` à la réponse 405.

## 🧠 Concepts Clés
1.  **Sécurité** : Restreindre les méthodes réduit la surface d'attaque. Une action destructrice (`deleteAction`) ne doit jamais être accessible en GET (risque CSRF via une simple image ou lien).
2.  **API REST** : Indispensable pour les APIs où la même URL `/api/articles` fait des choses différentes selon GET (List) ou POST (Create).

## ⚠️ Points de vigilance (Certification)
*   **Par défaut** : Si `methods` n'est pas spécifié, la route accepte **TOUTES** les méthodes.
*   **Formulaire** : Un formulaire HTML `<form method="POST">` ne peut faire que POST. Pour simuler PUT/DELETE, Symfony utilise le champ caché `_method`. Le routeur, lui, verra bien du PUT ou DELETE grâce à cette surcharge.

## Ressources
*   [Symfony Docs - Method Restriction](https://symfony.com/doc/current/routing.html#matching-http-methods)
