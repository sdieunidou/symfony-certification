# Internationalisation (i18n) du Routage

## Concept clé
Pour le SEO et l'UX, on veut souvent traduire les URLs :
*   `/en/about-us`
*   `/fr/a-propos`

Symfony gère cela nativement via l'attribut `_locale`.

## Stratégies

### 1. Préfixe Global (Recommandé)
Toutes les URLs commencent par la locale.

```yaml
# config/routes.yaml
controllers:
    resource: ../src/Controller/
    type: attribute
    prefix: /{_locale}
    requirements:
        _locale: en|fr|de
```
Symfony détecte `{_locale}`, configure la requête, et le service Translator.

### 2. URLs Traduites (Localized Paths)
On peut définir un path différent par locale pour la **même** route.

```php
#[Route(path: [
    'en' => '/about-us',
    'fr' => '/a-propos',
    'de' => '/ueber-uns'
], name: 'about')]
public function about(): Response { ... }
```
Lors de la génération `path('about')`, Symfony choisit automatiquement le bon path selon la locale courante.

## 🧠 Concepts Clés
1.  **Sticky Locale** : Une fois le paramètre `_locale` identifié dans la route, il est stocké dans le contexte du routeur.
    *   Si je suis sur `/fr/blog`, générer un lien vers `path('contact')` générera `/fr/contact` automatiquement, sans avoir à repasser `{_locale: 'fr'}`.
2.  **Locale par défaut** : Si l'URL ne contient pas de locale, Symfony utilise `framework.default_locale`.

## ⚠️ Points de vigilance (Certification)
*   **JMSI18nRoutingBundle** : C'était la solution standard en Symfony 2/3. C'est obsolète. Symfony gère tout nativement maintenant.
*   **Doublons** : Si vous avez `/about` (sans locale) et `/en/about`, attention au Duplicate Content SEO. Redirigez toujours la racine vers la version localisée si nécessaire.

## Ressources
*   [Symfony Docs - Localized Routes](https://symfony.com/doc/current/routing.html#localized-routes-i18n)
