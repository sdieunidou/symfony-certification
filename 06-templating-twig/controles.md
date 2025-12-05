# Boucles et Conditions (Contrôles)

## Concept clé
Twig offre des structures de contrôle similaires à PHP mais optimisées pour les templates (lisibilité).

## Conditions (`if`)

```twig
{% if user.isActive and (user.role == 'ADMIN' or user.isSuperAdmin) %}
    <p>Admin actif</p>
{% elseif user.isBanned %}
    <p>Banni</p>
{% else %}
    <p>Utilisateur standard</p>
{% endif %}
```

### Tests (`is`)
Twig utilise l'opérateur `is` pour vérifier des états. C'est plus sûr que les comparaisons PHP.
*   `is defined` : La variable existe.
*   `is empty` : Vide (null, false, [], "").
*   `is null` : Strictement null.
*   `is iterable` : Tableau ou objet traversable.
*   `is same as(true)` : Comparaison stricte `===`.

## Boucles (`for`)

```twig
<ul>
    {% for user in users %}
        <li class="{{ loop.first ? 'first' : '' }}">
            {{ loop.index }} - {{ user.username }}
        </li>
    {% else %}
        {# Exécuté si 'users' est vide ou null #}
        <li>Aucun utilisateur.</li>
    {% endfor %}
</ul>
```

### La variable `loop`
Dans une boucle `for`, une variable spéciale `loop` est disponible :
*   `loop.index` : Index courant (commence à 1).
*   `loop.index0` : Index courant (commence à 0).
*   `loop.revindex` : Index inverse (compte à rebours jusqu'à 1).
*   `loop.first` : Vrai si première itération.
*   `loop.last` : Vrai si dernière itération.
*   `loop.length` : Taille totale de la collection.

### Filtres de boucle
On peut filtrer directement dans la boucle (moins performant que dans le contrôleur, mais pratique).

```twig
{% for user in users|filter(u => u.isActive)|slice(0, 5) %}
    ...
{% endfor %}
```

## 🧠 Concepts Clés
1.  **Scope** : Les variables définies dans une boucle (`{% set foo = 'bar' %}`) n'existent **PAS** en dehors de la boucle.
2.  **Else** : Le bloc `else` du `for` est une fonctionnalité géniale souvent oubliée. Elle évite de faire un `if users is empty` avant la boucle.

## ⚠️ Points de vigilance (Certification)
*   **Break / Continue** : Twig ne supporte pas `break` ou `continue` nativement comme PHP (historiquement). Il faut utiliser des filtres (`|filter`) ou des conditions `if` à l'intérieur. (Note: un polyfill existe peut-être dans certaines versions, mais le design de Twig encourage la séparation logique/vue).
*   **Keys** : Pour itérer sur les clés : `{% for key, value in myArray %}`.

## Ressources
*   [Twig Docs - Control Structures](https://twig.symfony.com/doc/3.x/tags/index.html)
