# Détection de la Langue

## Concept clé
Déterminer la locale (langue + région) préférée de l'utilisateur pour lui servir le contenu traduit.
Sources possibles :
1.  Paramètre d'URL (ex: `/fr/accueil`). **Recommandé**.
2.  Session utilisateur.
3.  Header HTTP `Accept-Language`.
4.  Configuration par défaut.

## Application dans Symfony 7.0
Symfony gère la locale dans l'objet `Request` (`$request->getLocale()`).
Le framework utilise un `LocaleSubscriber` qui fixe la locale de la requête avant d'arriver au contrôleur.

## Exemple de code

```php
<?php

// config/routes.yaml ou Attributs
// La locale fait partie de l'URL
// path: /{_locale}/blog
// requirements:
//     _locale: en|fr|de

public function index(Request $request): Response
{
    // Récupérer la locale courante
    $locale = $request->getLocale(); // 'fr'
    
    // C'est cette locale qui est utilisée par le service Translator
}

// Détection automatique pour redirection (Homepage)
public function root(Request $request): Response
{
    // Analyse le header Accept-Language
    // Retourne 'fr' si le navigateur préfère le français et qu'il est dispo, sinon 'en' par défaut.
    $locale = $request->getPreferredLanguage(['en', 'fr']);
    
    return $this->redirectToRoute('homepage', ['_locale' => $locale]);
}
```

## Points de vigilance (Certification)
*   **Sticky Locale** : La locale est "collante" pendant la requête. Si on la change via `$request->setLocale('de')`, le service de traduction (`Translator`) utilisera 'de' pour la suite de la requête.
*   **URL vs Session** : Stocker la locale en session est possible mais rend le cache HTTP moins efficace et le partage d'URL impossible (si je t'envoie un lien, tu le veux dans ma langue ou la tienne ? Avec l'URL, c'est explicite). Symfony recommande l'URL.
*   **Priorité** : `_locale` dans les attributs de requête > `Accept-Language` > `default_locale`.

## Ressources
*   [Symfony Docs - Locale](https://symfony.com/doc/current/translation/locale.html)

