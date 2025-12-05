# Négociation de Contenu (Content Negotiation)

## Concept clé
La négociation de contenu est le mécanisme par lequel le client et le serveur s'accordent sur la **meilleure représentation** d'une ressource. Une même URL (`/api/products/1`) peut renvoyer du JSON, du XML ou du HTML selon qui le demande.

Cela se joue sur 4 axes via les headers `Accept-*` :
1.  **Type de média** (`Accept`): `application/json`, `text/html`, `image/webp`.
2.  **Jeu de caractères** (`Accept-Charset`): `utf-8` (obsolète, géré par Content-Type aujd).
3.  **Encodage/Compression** (`Accept-Encoding`): `gzip`, `br` (Brotli), `deflate`.
4.  **Langue** (`Accept-Language`): `fr-FR`, `en-US`.

## Application dans Symfony 7.0

Symfony ne "devine" pas le format magiquement, mais fournit des outils pour décider quel format servir.

### Priorité de Détection du Format
Symfony détermine le format de requête (`$request->getRequestFormat()`) selon cet ordre :
1.  **Attribut de Request `_format`** : Souvent issu du routing (ex: `/page.{_format}`). C'est la méthode prioritaire et recommandée car cache-friendly.
2.  **Header `Accept`** : Si aucun `_format` n'est défini, Symfony analyse le header `Accept` pour déduire le format (ex: `application/json` -> `json`).

### Configuration Routing

```yaml
# routes.yaml
api_users:
    path: /api/users.{_format}
    defaults: { _format: json } # Par défaut JSON si pas d'extension
    requirements:
        _format: json|xml|csv
```

### Contrôleur et Réponses Multi-formats

```php
public function show(int $id, Request $request): Response
{
    $data = ['id' => $id, 'name' => 'Produit X'];

    // 1. Négociation explicite via le format détecté
    $format = $request->getRequestFormat(); // 'json', 'xml', ou 'html'

    return match ($format) {
        'json' => new JsonResponse($data),
        'xml'  => new Response($this->serializer->serialize($data, 'xml'), 200, ['Content-Type' => 'text/xml']),
        'csv'  => new Response($this->serializer->serialize($data, 'csv'), 200, ['Content-Type' => 'text/csv']),
        default => $this->render('product/show.html.twig', $data),
    };
}
```

### Ajout de Formats Personnalisés
Si vous devez gérer un format binaire ou propriétaire, vous pouvez l'enregistrer dans le listener `kernel.request` ou avant l'utilisation.

```php
// Associe le mime-type 'application/x-msgpack' au format court 'msgpack'
$request->setFormat('msgpack', 'application/x-msgpack');
```

## 🧠 Concepts Clés
1.  **Driver par l'URL vs Header** :
    *   **URL (.json)** : Facile à tester, explicite, cache facile. Recommandé par Symfony.
    *   **Header (Accept)** : "Puriste" REST. L'URL est unique. Mais le cache est plus complexe (nécessite `Vary: Accept`).
2.  **Qualité (q-factor)** : Le header `Accept` peut contenir des poids : `Accept: application/json;q=1.0, text/html;q=0.8`. Symfony (`getPreferredFormat`) respecte ces priorités.
3.  **Erreur 406 (Not Acceptable)** : Si le serveur ne peut pas produire un format demandé par le client, il devrait théoriquement renvoyer une 406. En pratique, les API renvoient souvent un format par défaut (JSON) pour éviter de bloquer.

## ⚠️ Points de vigilance (Certification)
*   **Serializer** : La négociation de contenu va souvent de pair avec le composant **Serializer**. Ne confondez pas *Négocier* (choisir le format) et *Sérialiser* (transformer l'objet en string).
*   **FOSRestBundle** : Historiquement, ce bundle gérait tout (ViewListener). En Symfony moderne, on préfère souvent la simplicité du code natif ou **API Platform** qui gère la négociation de contenu de manière transparente et automatique.

## Ressources
*   [RFC 7231 - Content Negotiation](https://tools.ietf.org/html/rfc7231#section-5.3)
*   [Symfony Docs - Request Formats](https://symfony.com/doc/current/components/http_foundation.html#request-formats-and-mime-types)
