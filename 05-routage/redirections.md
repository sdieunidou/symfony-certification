# Redirections dans le Routage

## Concept clé
Pour les redirections simples (Legacy, SEO, Shortcuts), inutile de créer un contrôleur PHP. Configurez-les directement dans `routes.yaml` via le `RedirectController` natif.

## Types de Redirection

### 1. Vers une URL ou Path (`RedirectController`)
Redirige vers une chaîne statique.

```yaml
# config/routes.yaml
legacy_redirection:
    path: /v1/doc
    controller: Symfony\Bundle\FrameworkBundle\Controller\RedirectController
    defaults:
        path: /v2/doc # Cible (URL absolue ou path)
        permanent: true # 301 Moved Permanently
```

### 2. Vers une Route Interne (`RedirectController`)
Redirige vers une route existante (par son nom). Plus robuste car suit les changements de path.

```yaml
home_shortcut:
    path: /accueil
    controller: Symfony\Bundle\FrameworkBundle\Controller\RedirectController
    defaults:
        route: 'app_home' # Nom de la route cible
        permanent: true
        keepQueryParams: true # Garder ?foo=bar (Défaut: false pour 'route', true pour 'path' ?)
        ignoreAttributes: true # Ne pas passer les attributs de route actuels à la cible
```

### 3. Options Avancées (Defaults)
*   `permanent`: `true` (301/308) ou `false` (302/307).
*   `keepRequestMethod`: `true` (307/308) ou `false` (301/302). Si true, un POST redirigé restera un POST.
*   `keepQueryParams`: Ajoute la Query String originale à la cible.

## Trailing Slash (Slash de fin)
Symfony est strict : `/blog` et `/blog/` sont deux URLs différentes.
*   Si une route matche `/blog` :
    *   Requête `/blog` -> OK (200).
    *   Requête `/blog/` -> Redirection 301 vers `/blog`.
*   Si une route matche `/blog/` :
    *   Requête `/blog/` -> OK (200).
    *   Requête `/blog` -> Redirection 301 vers `/blog/`.

C'est géré automatiquement par Symfony pour les requêtes GET/HEAD.

## 🧠 Concepts Clés
1.  **Performance** : Le `RedirectController` est très léger.
2.  **SEO** : Utilisez toujours `permanent: true` (301) pour les migrations d'URL définitives pour transférer le "Jus SEO". Utilisez `permanent: false` (302) pour les redirections temporaires.

## ⚠️ Points de vigilance (Certification)
*   **RedirectController** : Savoir que c'est le contrôleur "magique" pour faire ça. Dans les anciennes versions, on appelait des méthodes statiques (`RedirectController::redirectAction`), maintenant on appelle la classe (`RedirectController::class`) ou le service `Symfony\Bundle\FrameworkBundle\Controller\RedirectController` via son ID.

## Ressources
*   [Symfony Docs - Redirect Controller](https://symfony.com/doc/current/routing.html#redirecting-to-urls-and-routes-directly-from-route)
