# Attributs de Routage Spéciaux

## Concept clé
Le routeur Symfony utilise des paramètres "magiques" commençant par un underscore `_` pour contrôler le comportement du framework.

## Application dans Symfony 7.0
*   `_controller` : Définit quel contrôleur exécuter (`Class::method` ou service ID). C'est ce que le routeur remplit quand il matche une URL.
*   `_format` : Définit le format de la requête (`html`, `json`, `xml`). Utilisé pour la Content Negotiation.
*   `_locale` : Définit la langue de la requête. Utilisé par le Translator.
*   `_fragment` : Utilisé pour les rendus de fragments ESI.
*   `_route` : Contient le nom de la route matchée.

## Exemple de code

```php
#[Route('/api/posts', defaults: ['_format' => 'json'])]
public function api(): Response
{
    // $request->getRequestFormat() retournera 'json' par défaut
}

#[Route('/{_locale}/about', requirements: ['_locale' => 'en|fr'])]
public function about(): Response
{
    // La locale est automatiquement set sur la Request
}
```

## Points de vigilance (Certification)
*   Ces attributs sont stockés dans `$request->attributes`.
*   Vous pouvez définir vos propres attributs commençant par `_`, mais évitez de surcharger ceux du système sauf si vous savez ce que vous faites.

## Ressources
*   [Symfony Docs - Special Routing Attributes](https://symfony.com/doc/current/routing.html#special-parameters)

