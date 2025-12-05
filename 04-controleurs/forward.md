# Redirections Internes (Forward)

## Concept clé
Contrairement à une redirection HTTP (le navigateur fait une 2ème requête), une redirection interne (Forward) s'exécute entièrement côté serveur.
Le contrôleur A appelle le contrôleur B, et le contrôleur B retourne une réponse qui est renvoyée au client comme si elle venait de A. L'URL dans le navigateur ne change pas.

## Application dans Symfony 7.0
Utilisation de la méthode `forward()`.

## Exemple de code

```php
<?php

public function index(string $name): Response
{
    // Redirige le traitement vers une autre méthode de contrôleur
    $response = $this->forward('App\Controller\OtherController::fancy', [
        'name'  => $name,
        'color' => 'green',
    ]);

    return $response;
}
```

## Points de vigilance (Certification)
*   **Performance** : Le Forward redémarre un sous-cycle de vie (Request -> Kernel -> Controller). C'est plus lourd qu'un simple appel de méthode PHP, mais plus léger qu'une redirection HTTP.
*   **Usage** : Rarement utilisé dans les applications modernes. On préfère souvent extraire la logique commune dans un Service ou utiliser `twig:render` (Fragment rendering) pour intégrer un contrôleur dans une vue.
*   **Arguments** : Les arguments passés à `forward` sont passés comme attributs de requête, pas comme paramètres GET.

## Ressources
*   [Symfony Docs - Forwarding](https://symfony.com/doc/current/controller/forwarding.html)

