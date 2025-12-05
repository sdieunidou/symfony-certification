# Protection CSRF

## Concept clé
CSRF (Cross-Site Request Forgery) est une attaque où un site malveillant force l'utilisateur à soumettre un formulaire sur votre site à son insu.
La protection consiste à inclure un jeton (token) secret unique dans chaque formulaire, et à le vérifier à la soumission.

## Application dans Symfony 7.0
Le composant Form active la protection CSRF **par défaut**.
Il génère un champ caché `_token`.

### Configuration (options par défaut)
```php
// Dans configureOptions d'un Type
$resolver->setDefaults([
    'csrf_protection' => true,
    'csrf_field_name' => '_token',
    'csrf_token_id'   => 'task_item', // Identifiant unique pour ce formulaire
]);
```

## Points de vigilance (Certification)
*   **Rendu** : Le champ `_token` doit être rendu. `{{ form_end(form) }}` le fait automatiquement (via `form_rest`). Si vous ne mettez pas `form_end`, le token manquera et la soumission échouera ("The CSRF token is invalid").
*   **API** : Pour les APIs stateless, on désactive souvent CSRF (`csrf_protection => false`) car l'authentification par token (Bearer) protège déjà ou nécessite une autre approche.

## Ressources
*   [Symfony Docs - CSRF Protection](https://symfony.com/doc/current/security/csrf.html)

