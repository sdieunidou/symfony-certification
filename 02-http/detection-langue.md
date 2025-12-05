# Détection de la Langue (Locale)

## Concept clé
Dans une application internationale (i18n), déterminer la langue de l'utilisateur est la première étape critique.
Symfony utilise le concept de **Locale** (code langue ISO 639-1 + optionnellement code région ISO 3166-1 alpha-2, ex: `fr`, `fr_CA`, `en_US`).

## Stratégies de Détection
1.  **URL (Path)** : `/fr/accueil`, `/en/home`. **Best Practice**. Explicite, cacheable, partageable (SEO friendly).
2.  **Domaine** : `example.fr`, `example.com`.
3.  **Header HTTP** : `Accept-Language` (envoyé par le navigateur selon OS). Utile pour la redirection initiale (Homepage -> `/fr/`).
4.  **Session** : Stocker le choix utilisateur. Déconseillé car rend le cache HTTP complexe (Vary: Cookie) et les URLs non uniques.
5.  **User Account** : Préférence en base de données (pour utilisateurs loggués).

## Application dans Symfony 7.0

La locale est une propriété de l'objet `Request` (`$request->getLocale()`).
Elle est initialisée très tôt par le `LocaleListener` (Priority haute).

### Configuration du Routing (Sticky Locale)
L'approche standard est d'utiliser un paramètre spécial `_locale` dans les routes.

```yaml
# config/routes.yaml
controllers:
    resource:
        path: ../src/Controller/
        namespace: App\Controller
    type: attribute
    prefix: /{_locale}
    requirements:
        _locale: en|fr|de
```

Quand une route matche `_locale`, Symfony :
1.  Définit la locale de la `Request`.
2.  Configure le `Translator` avec cette locale.
3.  Garde cette locale en mémoire pour la génération d'URL (Sticky Locale : générer un lien vers une autre page conservera le préfixe `/fr/`).

## Exemple de code

### 1. Détection et Redirection (Homepage)

```php
// src/Controller/MainController.php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/')]
    public function index(Request $request): Response
    {
        // Détection basée sur le header Accept-Language
        // Compare les langues supportées par l'app (['en', 'fr']) 
        // avec les préférences du navigateur (q-factors)
        $preferredLocale = $request->getPreferredLanguage(['en', 'fr']);
        
        return $this->redirectToRoute('app_dashboard', ['_locale' => $preferredLocale]);
    }

    #[Route('/{_locale}/dashboard', name: 'app_dashboard', requirements: ['_locale' => 'en|fr'])]
    public function dashboard(): Response
    {
        // Ici, $request->getLocale() est automatiquement 'en' ou 'fr'
        return $this->render('main/dashboard.html.twig');
    }
}
```

### 2. Tag `kernel.locale_aware`
Si vous développez un service qui a besoin de connaître la locale courante (par exemple un générateur de PDF qui doit savoir en quelle langue écrire, ou un formateur de prix), vous pouvez implémenter `LocaleAwareInterface`.

Symfony détectera automatiquement votre service (grâce à l'autoconfiguration et au tag `kernel.locale_aware`) et appellera `setLocale($locale)` sur votre service à chaque fois que la locale change dans l'application (lors d'une requête, ou changement manuel).

Cela évite d'injecter `RequestStack` juste pour lire la locale (ce qui est une mauvaise pratique dans la couche service).

```php
namespace App\Service;

use Symfony\Contracts\Translation\LocaleAwareInterface;

class InvoiceGenerator implements LocaleAwareInterface
{
    private string $locale = 'en'; // Valeur par défaut

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function generate(): void
    {
        // Utilise $this->locale pour formater la facture
        if ($this->locale === 'fr') { ... }
    }
}
```

## 🧠 Concepts Clés
1.  **Sticky Locale** : Une fois définie via un paramètre de route `_locale`, la locale est stockée dans le `UrlGenerator`. Tous les liens générés (`path('route')`) incluront automatiquement cette locale, sauf surcharge explicite.
2.  **Locale par défaut** : Configurée dans `framework.default_locale` (souvent `en`). Utilisée si aucune locale n'est détectée.
3.  **Régions** : Symfony gère les fallbacks. Si l'utilisateur demande `fr_CA` (Français Canada) et que vous n'avez que `fr` (Français générique), Symfony utilisera `fr`.

## ⚠️ Points de vigilance (Certification)
*   **Impact Cache HTTP** : Si vous utilisez la session ou `Accept-Language` pour varier le contenu sur la **MÊME** URL, vous devez impérativement ajouter le header `Vary: Cookie` ou `Vary: Accept-Language`. Sinon, un utilisateur anglais pourrait recevoir la version française cachée. L'approche "Locale dans l'URL" évite ce problème (1 URL = 1 Contenu).
*   **`$request->setLocale()`** : Change la locale pour le reste de la requête PHP, mais ne redirige pas l'utilisateur.
*   **Traduction** : La locale de la requête pilote le service `translator`.

## Ressources
*   [Symfony Docs - Locale](https://symfony.com/doc/current/translation/locale.html)
*   [Symfony Docs - Routing Internationalization](https://symfony.com/doc/current/routing.html#routing-internationalization)
