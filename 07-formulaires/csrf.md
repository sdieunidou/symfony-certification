# Protection CSRF (Cross-Site Request Forgery)

## Concept clé
La CSRF est une attaque où un site malveillant force le navigateur de l'utilisateur à soumettre un formulaire sur votre application alors qu'il est authentifié.
La protection consiste à générer un **Jeton (Token) Secret** unique par session et par formulaire, et à vérifier sa présence lors de la soumission.

## Application dans Symfony 7.0
La protection est activée **par défaut** et transparente pour le développeur si les bonnes pratiques sont respectées.

### Configuration par défaut
Dans n'importe quel `FormType`, les options par défaut (via `OptionsResolver`) sont :
*   `csrf_protection`: `true`
*   `csrf_field_name`: `_token`
*   `csrf_token_id`: Le nom de la classe (ex: `task_item`)

### Comment ça marche ?
1.  **Génération** : À l'affichage, Symfony génère un champ caché `<input type="hidden" name="_token" value="...">`.
2.  **Validation** : Lors du `handleRequest()`, Symfony vérifie que le token envoyé correspond à celui attendu.
3.  **Erreur** : Si le token est invalide ou manquant, une erreur de formulaire est ajoutée ("The CSRF token is invalid"), et `$form->isValid()` retourne `false`.

## Rendu du Token
C'est le point critique. Le token **doit** être présent dans le HTML.
Si vous utilisez `{{ form_end(form) }}`, Symfony affiche automatiquement tous les champs non rendus, y compris le champ caché `_token`.

Si vous fermez la balise `</form>` manuellement, vous devez afficher le token manuellement :
```twig
{{ form_row(form._token) }}
{# ou #}
{{ form_rest(form) }}
```

## Désactiver CSRF (APIs)
Pour une API REST sans session (Stateless), la protection CSRF basée sur la session est inutile (et impossible).

### 1. Désactivation par Formulaire (FormType)
```php
public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'csrf_protection' => false,
    ]);
}
```

### 2. Désactivation Globale (YAML)
Si toute votre application est une API, ou si vous voulez désactiver le CSRF par défaut pour tous les formulaires (pour les réactiver au cas par cas) :

```yaml
# config/packages/framework.yaml
framework:
    form:
        csrf_protection: false
    # ou
    csrf_protection: false # Désactive le composant complet
```

### 3. Désactivation Conditionnelle (Ex: API Platform)
Souvent, on veut garder CSRF pour le front (Admin, App) mais le désactiver pour `/api`.
Le plus simple est de désactiver CSRF globalement et de l'activer manuellement dans les formulaires Web, OU d'utiliser des DTOs sans CSRF pour l'API.

Cependant, Symfony ne permet pas nativement de désactiver CSRF par URL via `framework.yaml`. Il faut utiliser une extension de formulaire ou configurer les `options` par défaut.

## 🧠 Concepts Clés
1.  **Token ID** : Chaque formulaire a un ID différent. Un token généré pour le formulaire de login ne fonctionnera pas pour le formulaire de contact.
2.  **SameSite Cookie** : L'utilisation de cookies `SameSite: Lax` ou `Strict` (défaut Symfony) atténue déjà considérablement le risque CSRF, mais le token reste une défense en profondeur recommandée.

## ⚠️ Points de vigilance (Certification)
*   **Caching** : Si vous cachez vos formulaires avec un cache HTTP public (Varnish), le token CSRF (qui est spécifique à l'utilisateur) sera caché et servi à tout le monde -> **Erreur CSRF pour tous**. Solution : Charger le formulaire en AJAX ou utiliser ESI, ou désactiver CSRF pour les formulaires publics.

## Ressources
*   [Symfony Docs - CSRF Protection](https://symfony.com/doc/current/security/csrf.html)
