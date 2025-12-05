# Cookies (Contrôleur)

## Concept clé
Les cookies ne sont pas gérés par un service global (comme la Session), mais sont attachés aux objets `Request` (lecture) et `Response` (écriture).

## Application dans Symfony 7.0
Pour définir un cookie, il faut manipuler l'objet `Response` **avant** de le retourner.

## Exemple de code

```php
<?php

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

public function setCookieAction(): Response
{
    $response = $this->render('index.html.twig');
    
    // Créer le cookie
    $cookie = Cookie::create('my_cookie', 'value', new \DateTime('+1 day'));
    
    // L'ajouter à la réponse
    $response->headers->setCookie($cookie);
    
    return $response;
}
```

## Points de vigilance (Certification)
*   **Timing** : Une erreur fréquente est de penser qu'on peut faire `$this->setCookie()`. Cela n'existe pas dans `AbstractController`. Il faut impérativement avoir l'instance de `Response`.
*   **Auto-login** : Les cookies "Remember Me" sont gérés automatiquement par le firewall de sécurité, pas manuellement dans le contrôleur.

## Ressources
*   [Symfony Docs - Cookies](https://symfony.com/doc/current/components/http_foundation.html#setting-cookies)

