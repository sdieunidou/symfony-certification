# Échappement Automatique (Auto Escaping)

## Concept clé
La sécurité est "par défaut" dans Twig.
Pour prévenir les attaques XSS (Cross-Site Scripting), Twig échappe **automatiquement** toutes les variables affichées via `{{ ... }}`.

Si `user.bio` contient `<script>alert('Hacked')</script>`, Twig affichera le code HTML littéral (entités HTML) au lieu de l'exécuter.

## Stratégies d'Échappement
Twig adapte l'échappement selon le contexte (extension du fichier).
*   `.html.twig` -> Stratégie HTML (`<` devient `&lt;`).
*   `.js.twig` -> Stratégie JS.
*   `.txt.twig` -> Pas d'échappement.

## Désactiver l'échappement (Filtre `raw`)
Si vous affichez du HTML sûr (ex: généré par un éditeur Markdown parsé côté serveur, ou un helper Symfony qui génère du HTML), utilisez le filtre `|raw`.

```twig
{{ article.content_html|raw }}
```
**Attention** : N'utilisez `raw` que si vous êtes sûr à 100% que le contenu est sain (sanitized).

## Échappement Manuel (Filtre `escape` ou `e`)
Parfois on veut forcer un type d'échappement spécifique.

```twig
{# Par défaut (html) #}
{{ var|e }}

{# Pour insérer une variable PHP dans un script JS inline #}
<script>
    var username = "{{ user.name|e('js') }}";
</script>

{# Autres contextes : 'css', 'url', 'html_attr' #}
<a href="?q={{ query|e('url') }}">Link</a>
```

## 🧠 Concepts Clés
1.  **Double échappement** : Twig est intelligent. Si vous échappez manuellement (`|e`), il ne ré-échappera pas automatiquement par dessus.
2.  **Safe HTML** : Certaines extensions Twig (comme celle de Symfony pour les formulaires) marquent leur sortie comme "Safe". Twig ne les échappe pas. Vous n'avez pas besoin de `|raw` pour `{{ form_row(form.name) }}`.

## ⚠️ Points de vigilance (Certification)
*   **Désactivation globale** : On peut désactiver l'auto-échappement pour un bloc entier via `{% autoescape false %}`, mais c'est déconseillé pour des raisons de sécurité.
*   **Ordre** : `|raw` doit être le **dernier** filtre. `{{ var|raw|upper }}` n'a pas de sens (upper va ré-échapper ou non selon l'implémentation). C'est `{{ var|upper|raw }}`.

## Ressources
*   [Twig Docs - Autoescape](https://twig.symfony.com/doc/3.x/tags/autoescape.html)
