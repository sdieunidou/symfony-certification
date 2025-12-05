# Composants Twig (Twig Components)

## Concept clé
Les **Twig Components** (paquet `symfony/ux-twig-component`) apportent une approche orientée composant au templating, similaire à React ou Vue, mais en PHP/Twig.
Ils permettent de lier un template à une classe PHP dédiée pour gérer la logique d'affichage complexe.

## Deux Types de Composants

### 1. Twig Components (Stateless)
Idéal pour les éléments d'interface réutilisables (Alertes, Badges, Cards).
*   **Classe** : `src/Twig/Components/Alert.php`
*   **Template** : `templates/components/alert.html.twig`

```php
// src/Twig/Components/Alert.php
#[AsTwigComponent]
class Alert
{
    public string $type = 'info';
    public string $message;
    
    public function getIcon(): string
    {
        return match($this->type) {
            'success' => 'check',
            'danger' => 'exclamation',
            default => 'info'
        };
    }
}
```

```twig
{# Usage #}
<twig:Alert type="success" message="Bravo !" />
```

### 2. Live Components (Stateful / AJAX)
Permet de créer des interfaces dynamiques (Recherche, Pagination, Formulaire) sans écrire de JavaScript.
Le composant se met à jour via AJAX automatiquement.
*   Nécessite `symfony/ux-live-component`.
*   Attribut `#[AsLiveComponent]`.

## 🧠 Concepts Clés
1.  **Props** : Les propriétés publiques de la classe sont accessibles dans le template.
2.  **Syntaxe HTML** : `<twig:Alert />` est la syntaxe moderne recommandée (supportée depuis Symfony 6.3). L'ancienne syntaxe `{{ component('Alert') }}` fonctionne toujours.

## ⚠️ Points de vigilance (Certification)
*   Ce n'est pas (encore) dans le Core standard de Symfony, c'est un paquet **Symfony UX**. Cependant, c'est poussé comme "Best Practice" moderne.
*   Savoir que ça existe et que ça remplace avantageusement les `include` avec trop de logique ou les `render(controller)` trop lourds.

## Ressources
*   [Symfony UX - Twig Components](https://symfony.com/bundles/ux-twig-component/current/index.html)
