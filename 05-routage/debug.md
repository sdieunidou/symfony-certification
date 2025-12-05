# Débogage du Routeur

## Concept clé
Le routage peut devenir complexe (centaines de routes, regex, priorités). Symfony fournit des outils CLI puissants pour introspecter et tester le routage sans navigateur.

## Commandes Essentielles

### 1. Lister les routes (`debug:router`)
Affiche la table de toutes les routes enregistrées.

```bash
php bin/console debug:router

# Filtrer par nom
php bin/console debug:router app_blog

# Filtrer par méthode (Nouveauté 7.3)
php bin/console debug:router --method=POST

# Afficher les alias (Nouveauté 7.3)
php bin/console debug:router --show-aliases

# Afficher les détails complets (Contrôleur, Regex, Defaults)
php bin/console debug:router app_blog_show
```

### 2. Tester une URL (`router:match`)
Simule une requête et affiche quelle route matche (ou l'erreur 404/405).
**Indispensable** pour comprendre pourquoi une URL ne va pas là où on pense (problème de priorité).

```bash
php bin/console router:match /blog/mon-article

# Résultat :
# [OK] Route "app_blog_show" matches
```

### 3. Warmup (`cache:warmup`)
En production, le routeur est compilé. Si vous déployez et que les routes ne semblent pas à jour, c'est souvent un problème de cache.

## Points de vigilance (Certification)
*   **Routes masquées** : Si `debug:router` liste votre route mais que `router:match` en trouve une autre, c'est un problème d'**ordre**. Déplacez votre méthode de contrôleur plus haut dans le fichier ou ajustez le chargement YAML.
*   **Méthode HTTP** : `router:match` teste en GET par défaut. Pour tester une API POST :
    ```bash
    php bin/console router:match /api/users --method=POST
    ```
*   **Condition** : Les routes avec `condition` (ExpressionLanguage) sont difficiles à debugger statiquement car elles dépendent du runtime (headers, ip).

## Ressources
*   [Symfony Docs - Debugging Routes](https://symfony.com/doc/current/routing.html#debugging-routes)
