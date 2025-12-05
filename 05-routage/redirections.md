# Redirections dans le Routage

## Concept clé
Pour les redirections simples (Legacy, SEO, Shortcuts), inutile de créer un contrôleur PHP. Configurez-les directement dans `routes.yaml` via le `RedirectController` natif.

## Types de Redirection

### 1. Vers une URL ou Path (`urlRedirectAction`)
Redirige vers une chaîne statique.

```yaml
# config/routes.yaml
legacy_redirection:
    path: /v1/doc
    controller: Symfony\Bundle\FrameworkBundle\Controller\RedirectController::urlRedirectAction
    defaults:
        path: /v2/doc
        permanent: true # 301
```

### 2. Vers une Route Interne (`redirectAction`)
Redirige vers une route existante. Plus robuste car suit les changements de path.

```yaml
home_shortcut:
    path: /accueil
    controller: Symfony\Bundle\FrameworkBundle\Controller\RedirectController::redirectAction
    defaults:
        route: 'app_home' # Nom de la route cible
        permanent: true
        # Les paramètres (ex: {id}) sont transmis automatiquement
```

## Trailing Slash (Slash de fin)
Symfony est strict : `/blog` et `/blog/` sont deux URLs différentes.
Depuis Symfony 5/6, le comportement par défaut est de **rediriger** (301) automatiquement les URLs avec slash vers sans slash (Canonical).
Cela se configure, mais c'est souvent automatique.

## 🧠 Concepts Clés
1.  **Performance** : Le `RedirectController` est très léger.
2.  **SEO** : Utilisez toujours `permanent: true` (301) pour les migrations d'URL définitives pour transférer le "Jus SEO". Utilisez `permanent: false` (302) pour les redirections temporaires.

## ⚠️ Points de vigilance (Certification)
*   **Keep Query Params** : Par défaut, `ignoreAttributes: false` (défaut) conserve les attributs de route typés, mais la Query String (`?foo=bar`) est conservée par le comportement standard des redirections HTTP, sauf si vous reconstruisez l'URL manuellement. Avec `RedirectController`, la Query String est généralement préservée.

## Ressources
*   [Symfony Docs - Redirect Controller](https://symfony.com/doc/current/routing.html#redirecting-to-urls-and-routes-directly-from-config)
