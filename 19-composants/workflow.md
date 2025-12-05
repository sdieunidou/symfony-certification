# Composant Workflow

## Concept clé
Le composant **Workflow** permet de gérer le cycle de vie d'un objet (Machine à états).
Il définit des **Places** (états) et des **Transitions** (actions permettant de passer d'un état à un autre).
Il est idéal pour gérer des processus métier complexes (commande e-commerce, publication d'article, validation RH).

## Installation
```bash
composer require symfony/workflow
```

## Configuration (`framework.yaml`)

Il faut définir le workflow, les places et les transitions.

```yaml
framework:
    workflows:
        blog_publishing:
            type: 'workflow' # ou 'state_machine'
            audit_trail: { enabled: true }
            marking_store:
                type: 'method'
                property: 'currentPlace'
            supports:
                - App\Entity\BlogPost
            initial_marking: draft
            places:
                - draft
                - reviewed
                - rejected
                - published
            transitions:
                to_review:
                    from: draft
                    to:   reviewed
                publish:
                    from: reviewed
                    to:   published
                reject:
                    from: reviewed
                    to:   rejected
```

### Workflow vs State Machine
*   **State Machine** : L'objet est dans **UN SEUL** état à la fois. Transitions finies. Idéal pour une commande (panier -> payée -> livrée).
*   **Workflow** : L'objet peut être dans **PLUSIEURS** places en même temps (Petri Net). Idéal pour des processus parallèles (ex: document "relu" ET "traduit").

## Utilisation

L'objet géré (ex: `BlogPost`) doit avoir la propriété configurée (`currentPlace`) pour stocker l'état (array pour Workflow, string pour State Machine).

### Injection et Manipulation
On injecte `WorkflowInterface` (Registry) ou un workflow spécifique (Named Autowiring).

```php
use Symfony\Component\Workflow\WorkflowInterface;

public function publish(BlogPost $post, WorkflowInterface $blogPublishingWorkflow): Response
{
    // 1. Vérifier si une transition est possible (Guard)
    if ($blogPublishingWorkflow->can($post, 'publish')) {
        
        // 2. Appliquer la transition
        $blogPublishingWorkflow->apply($post, 'publish');
        
        // Persister l'entité en base...
    }
    
    return new Response('Statut mis à jour');
}
```

### Utilisation dans Twig
```twig
{# Vérifier si on peut transitionner #}
{% if workflow_can(post, 'publish') %}
    <a href="...">Publier</a>
{% endif %}

{# Afficher les transitions possibles #}
{% for transition in workflow_transitions(post) %}
    {{ transition.name }}
{% endfor %}

{# Vérifier l'état actuel #}
{% if workflow_has_marked_place(post, 'reviewed') %}
    <span class="badge">Relu</span>
{% endif %}
```

## Événements (Events)
Le composant dispatch de nombreux événements pour intercepter le processus.
Ordre d'exécution :
1.  `workflow.guard` : Bloquer une transition (Validation métier).
2.  `workflow.leave` : Quitter une place.
3.  `workflow.transition` : La transition a lieu.
4.  `workflow.enter` : Entrer dans une nouvelle place.
5.  `workflow.entered` : Une fois entré.
6.  `workflow.completed` : Tout est fini.

### Exemple : Guard Listener
Bloquer la publication si l'article n'a pas de titre.

```php
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

class BlogPostGuard implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.blog_publishing.guard.publish' => ['guardPublish'],
        ];
    }

    public function guardPublish(GuardEvent $event): void
    {
        $post = $event->getSubject();
        
        if (empty($post->getTitle())) {
            // Bloque la transition
            $event->setBlocked(true, "Le titre est vide.");
        }
    }
}
```

## Fonctionnalités Avancées

### 1. Metadata
On peut stocker des métadonnées (titre, couleur, description) sur les places et transitions pour les utiliser dans l'UI (Twig).

```yaml
transitions:
    publish:
        metadata:
            title: "Publier l'article"
            class: "btn-success"
```

```twig
{{ workflow_metadata(post, 'title', transition) }}
```

### 2. Marking Store Custom
Par défaut, Symfony utilise `MethodMarkingStore` (getter/setter sur l'entité). On peut créer son propre store pour stocker l'état ailleurs (Redis, Session, autre table).

### 3. Dump (Graphviz)
Pour visualiser le workflow, on peut générer un graphe.
```bash
php bin/console workflow:dump blog_publishing | dot -Tpng -o graph.png
```

## ⚠️ Points de vigilance (Certification)
*   **Support Multiple** : Un même objet peut être supporté par plusieurs workflows.
*   **Enums** : Depuis PHP 8.1, les places peuvent être des Enums.
*   **Validateurs** : Depuis Symfony 7.3, on peut créer des `DefinitionValidatorInterface` pour valider la logique structurelle du workflow à la compilation.

## Ressources
*   [Symfony Docs - Workflow](https://symfony.com/doc/current/workflow.html)

