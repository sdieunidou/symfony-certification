# Génération d'URLs

## Concept clé
Le routage est bidirectionnel :
1.  **Match** : URL -> Contrôleur.
2.  **Generate** : Nom de route + Paramètres -> URL.

Ne **JAMAIS** concaténer des chaînes pour créer des URLs (`/blog/` . $slug). Utilisez toujours le générateur.

## Application dans Symfony 7.0

### Dans le Contrôleur (`AbstractController`)

```php
// 1. URL Relative (Défaut) -> /blog/my-post
$url = $this->generateUrl('blog_show', ['slug' => 'my-post']);

// 2. URL Absolue -> https://example.com/blog/my-post
// Indispensable pour les emails, les APIs, les paiements.
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
$url = $this->generateUrl('blog_show', ['slug' => 'my-post'], UrlGeneratorInterface::ABSOLUTE_URL);
```

### Dans Twig

```twig
{# Relative #}
<a href="{{ path('blog_show', {slug: 'my-post'}) }}">Lien</a>

{# Absolue (fonction url()) #}
<a href="{{ url('blog_show', {slug: 'my-post'}) }}">Lien Absolu</a>

{# Génération JS Safe #}
<script>
    const url = "{{ path('api_get', {id: 123})|escape('js') }}";
</script>
```

### Dans un Service
Injectez `Symfony\Component\Routing\Generator\UrlGeneratorInterface` (ou `RouterInterface` qui l'étend).

### Dans une Commande (CLI)
En CLI, il n'y a pas de requête HTTP, donc Symfony ne connaît pas le domaine (`localhost`).
Il faut configurer `framework.router.default_uri` dans `config/packages/routing.yaml` :
```yaml
framework:
    router:
        default_uri: 'https://example.org/my/app'
```

## Gestion des Paramètres
*   **Paramètres de Route** : Remplacent les placeholders (`{slug}`).
*   **Paramètres Extra** : Sont ajoutés en **Query String**.
    *   Route : `/blog/{slug}`
    *   Appel : `generateUrl('blog_show', ['slug' => 'abc', 'ref' => 'twitter'])`
    *   Résultat : `/blog/abc?ref=twitter`

## Signer des URIs (`UriSigner`)
Pour sécuriser des liens sensibles (reset password, email validation) sans base de données, on peut signer l'URL avec un hash.

```php
// Service: Symfony\Component\HttpFoundation\UriSigner

// Générer
$url = 'https://example.com/reset?user=123';
$signedUrl = $uriSigner->sign($url, new \DateInterval('PT1H')); // Expire dans 1h (Nouveauté 7.1)
// Ajoute & _hash=... & _expiration=...

// Vérifier
if ($uriSigner->check($signedUrl)) {
    // OK
}
```
*   **Nouveauté 7.3** : Méthode `verify($uri)` qui lance des exceptions précises (`ExpiredSignedUriException`, `UnsignedUriException`).
*   **Nouveauté 7.4** : Attribut `#[IsSignatureValid]` pour sécuriser un contrôleur automatiquement.

## 🧠 Concepts Clés
1.  **Découplage** : Changer le path d'une route dans la config (`/blog/{slug}` -> `/article/{slug}`) met à jour instantanément toutes les URLs du site.
2.  **Missing Params** : Si vous oubliez un paramètre obligatoire (`slug`), une `MissingMandatoryParametersException` est levée.
3.  **HTTPS** : On peut forcer le HTTPS sur les URLs générées via `router.request_context.scheme` ou l'option `schemes: ['https']` sur la route.

## ⚠️ Points de vigilance (Certification)
*   **Scheme Relative** : `UrlGeneratorInterface::NETWORK_PATH` génère des URLs commençant par `//example.com/...` (hérite du protocole courant, http ou https).
*   **Performance** : La génération d'URL est très rapide (PHP pur), ne pas hésiter à l'utiliser.

## Ressources
*   [Symfony Docs - Generating URLs](https://symfony.com/doc/current/routing.html#generating-urls)
