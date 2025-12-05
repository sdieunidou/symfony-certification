# Edge Side Includes (ESI)

## Concept clé
ESI est une spécification (W3C) qui permet de déléguer l'assemblage d'une page au Gateway Cache (Reverse Proxy).
Cela résout le problème des pages "mixtes" (partiellement statiques, partiellement dynamiques/privées).

**Exemple** : Une Homepage (Cache Public 1h) contient un bloc "Panier" (Privé / Pas de cache).
*   Sans ESI : La Homepage entière doit être privée ou avoir un cache très court.
*   Avec ESI : La Homepage est cachée 1h. Le bloc "Panier" est un trou rempli dynamiquement par le proxy.

## Fonctionnement
1.  Symfony rend le template. Au lieu d'inclure le HTML du panier, il génère une balise `<esi:include src="..." />`.
2.  Le Reverse Proxy (Symfony HttpCache ou Varnish) intercepte la réponse.
3.  Il voit la balise ESI.
4.  Il effectue une **sous-requête** interne pour récupérer le contenu du panier.
5.  Il assemble le tout et envoie la page complète au client.

## Mise en œuvre dans Symfony

### 1. Activation
```yaml
# config/packages/framework.yaml
framework:
    esi: { enabled: true }
```

### 2. Utilisation dans Twig
Remplacer `include()` ou `render()` par `render_esi()`.

```twig
{# Génère <esi:include src="/_fragment?..." /> si un proxy ESI est détecté #}
{{ render_esi(controller('App\\Controller\\CartController::widget')) }}
```

### 3. Le Contrôleur de Fragment
Le contrôleur appelé doit retourner une `Response` avec ses propres règles de cache.

```php
public function widget(): Response
{
    $response = $this->render('cart/widget.html.twig');
    // Cache privé pour ce fragment, ou pas de cache du tout
    $response->setPrivate(); 
    return $response;
}
```

## Fallback
Si aucun Reverse Proxy compatible ESI n'est détecté (pas de header `Surrogate-Capability`), `render_esi()` se comporte exactement comme un `render()` classique (inclusion synchrone par PHP). L'application continue de fonctionner.

## 🧠 Concepts Clés
1.  **Fragments Indépendants** : Chaque fragment ESI a sa propre durée de vie de cache (`TTL`).
2.  **Performance** : Attention, trop d'ESI peut tuer les perfs (1 requête client = N sous-requêtes au backend si les fragments ne sont pas cachés).

## ⚠️ Points de vigilance (Certification)
*   **Trusted Proxies** : Pour que Symfony génère les tags ESI derrière Varnish, il faut que Varnish soit listé dans les `trusted_proxies` (sinon Symfony ne voit pas le header `Surrogate-Capability`).

## Ressources
*   [Symfony Docs - ESI](https://symfony.com/doc/current/http_cache/esi.html)
