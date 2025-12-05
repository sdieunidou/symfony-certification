# Boucles et Conditions

## Concept clé
Structures de contrôle pour afficher des listes ou conditionner l'affichage.

## Application dans Symfony 7.0

### Boucle For
```twig
<ul>
    {% for user in users %}
        <li>{{ user.username }}</li>
    {% else %}
        <li>Aucun utilisateur trouvé.</li>
    {% endfor %}
</ul>
```

La variable spéciale `loop` existe dans la boucle :
*   `loop.index` (1-based), `loop.index0` (0-based)
*   `loop.first`, `loop.last` (booléens)
*   `loop.length`

### Condition If
```twig
{% if user.isActive and (user.role == 'ADMIN' or user.isSuperAdmin) %}
    <p>Admin actif</p>
{% elseif user.isBanned %}
    <p>Banni</p>
{% endif %}
```

### Tests (is)
Twig utilise l'opérateur `is` pour les tests.
*   `if variable is defined`
*   `if variable is empty`
*   `if variable is null`
*   `if variable is iterable`
*   `if variable is same as(false)` (comparaison stricte `===`)

## Points de vigilance (Certification)
*   **Else dans For** : Le bloc `{% else %}` d'une boucle `for` est exécuté si le tableau est vide (ou null). C'est un raccourci très utile pour éviter un `if length > 0` autour.
*   **Portée** : Les variables définies dans une boucle (`{% set %}`) ne sont pas accessibles en dehors (scoping).

## Ressources
*   [Twig - Control Structures](https://twig.symfony.com/doc/3.x/tags/index.html)

