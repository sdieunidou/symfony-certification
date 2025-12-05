# Génération d'URLs (Twig)

## Concept clé
Générer des hyperliens vers les contrôleurs en utilisant le nom logique des routes.
Cela garantit que si le pattern URL change (`/blog` -> `/news`), les liens restent valides.

## Fonctions Twig

### 1. `path()` : URL Relative
Génère un chemin relatif à la racine du domaine. **À utiliser par défaut** pour la navigation interne.

```twig
<a href="{{ path('blog_show', {slug: 'mon-article'}) }}">
    Lire
</a>
{# Résultat : /blog/mon-article #}
```

### 2. `url()` : URL Absolue
Génère une URL complète (avec protocole et domaine).
**Obligatoire** pour :
*   Emails.
*   Flux RSS / Sitemaps.
*   Redirections de paiement externe.
*   Partage réseaux sociaux (OpenGraph).

```twig
<a href="{{ url('blog_show', {slug: 'mon-article'}) }}">
    Partager
</a>
{# Résultat : https://www.example.com/blog/mon-article #}
```

## Gestion des Paramètres
*   **Paramètres de Route** : Remplacent les placeholders (`{slug}`).
*   **Paramètres de Query** : Les paramètres en trop sont ajoutés en Query String (`?foo=bar`).

```twig
{{ path('search', { q: 'symfony', page: 2 }) }}
{# Résultat : /search?q=symfony&page=2 #}
```

## 🧠 Concepts Clés
1.  **Asset vs Path** :
    *   `path()` pointe vers une **Route** (Contrôleur PHP).
    *   `asset()` pointe vers un **Fichier** (CSS, JS, Image) dans `public/`.
2.  **Fragment** : Pour ajouter une ancre (`#top`), il faut le faire manuellement hors de la fonction : `{{ path(...) }}#top`.

## ⚠️ Points de vigilance (Certification)
*   **Erreur** : Si la route n'existe pas ou s'il manque un paramètre obligatoire, `path()` déclenche une erreur critique lors du rendu (page 500).
*   **Performance** : La génération est très rapide (tableau PHP optimisé en cache).

## Ressources
*   [Symfony Docs - Linking to Pages](https://symfony.com/doc/current/templates.html#linking-to-pages)
