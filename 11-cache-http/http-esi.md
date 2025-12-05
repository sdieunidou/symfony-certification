# Edge Side Includes (ESI)

## Concept clé
ESI est une spécification permettant d'assembler des pages dynamiques à partir de fragments statiques (ou ayant des durées de cache différentes) au niveau du serveur de cache (Gateway).
Exemple : Une page "Article" cachée 1h, contenant un bloc "User Bar" (Bonjour Bob) caché 0s (privé) ou un bloc "Dernières news" caché 5min.

## Fonctionnement
1.  Symfony rend la page principale.
2.  Au lieu de rendre le bloc sidebar, il insère une balise `<esi:include src="/_fragment?..." />`.
3.  Le Reverse Proxy (Symfony HttpCache ou Varnish) voit la balise.
4.  Il fait une sous-requête pour récupérer le contenu du fragment.
5.  Il remplace la balise par le contenu et sert la page complète.

## Application dans Symfony 7.0

### 1. Configuration
```yaml
# config/packages/framework.yaml
framework:
    esi: { enabled: true }
```

### 2. Utilisation dans Twig
```twig
{# Au lieu de render() ou include() #}
{{ render_esi(controller('App\\Controller\\NewsController::latest')) }}
```

Si ESI est activé et qu'un Reverse Proxy est détecté (header `Surrogate-Capability`), Symfony génère le tag ESI. Sinon, il fait un `render()` classique (fallback synchrone).

### 3. Contrôleur du Fragment
Le contrôleur appelé doit retourner une `Response` avec ses propres règles de cache.

```php
public function latest(): Response
{
    $response = $this->render('news/latest.html.twig');
    // Ce fragment est public et caché 5 min, même si la page mère est privée
    $response->setSharedMaxAge(300);
    return $response;
}
```

## 🧠 Concepts Clés
1.  **Transparence** : Le développeur utilise `render_esi`, et Symfony gère la complexité. Si pas de cache, ça marche quand même.
2.  **Performance** : ESI permet de cacher des pages "presque" statiques. Sans ESI, une page contenant "Bonjour User" ne pourrait pas être cachée en public (Shared Cache). Avec ESI, la page est publique, et seul le petit fragment User est privé (ou chargé en AJAX, alternative à ESI).

## ⚠️ Points de vigilance (Certification)
*   **Symfony HttpCache** : Le reverse proxy natif de Symfony gère ESI.
*   **Trusted Proxies** : Pour que l'ESI fonctionne avec Varnish, il faut configurer les Trusted Proxies pour que Symfony sache qu'il est derrière un reverse proxy capable de parler ESI.

## Ressources
*   [Symfony Docs - ESI](https://symfony.com/doc/current/http_cache/esi.html)
