# Surcharge du Framework (Overloading)

## Concept clé
Symfony est conçu pour être extensible sans modifier le code du cœur ("Open/Closed Principle").
"Surcharger" signifie remplacer ou étendre une fonctionnalité par défaut.

## Mécanismes
1.  **Événements** : Le moyen le plus propre. Se brancher sur `kernel.request` ou `kernel.view` pour modifier le flux.
2.  **Décoration de service** : Remplacer un service natif par le vôtre, tout en gardant une référence à l'ancien.
3.  **Compiler Passes** : Modifier la définition des services avant que le conteneur ne soit compilé (ex: ajouter des tags, changer des classes).
4.  **Héritage** : Étendre une classe native et changer la configuration pour utiliser votre classe (moins recommandé si la décoration est possible).

## Exemple : Décoration
Vous voulez modifier le comportement du `Mailer`.

```yaml
# config/services.yaml
App\Mailer\MyCustomMailer:
    decorates: 'mailer.default_transport'
    arguments: ['@.inner'] # On injecte le service original
```

```php
class MyCustomMailer implements TransportInterface
{
    public function __construct(private TransportInterface $inner) {}

    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
    {
        // Logique avant
        $this->log('Sending email...');
        
        // Appel service original
        return $this->inner->send($message, $envelope);
    }
}
```

## Points de vigilance (Certification)
*   **Bundle Inheritance** : Anciennement (Symfony 2/3), on pouvait faire hériter un Bundle d'un autre pour écraser ses contrôleurs/templates. Ce mécanisme est **déprécié/supprimé** pour les contrôleurs/classes. On préfère la décoration et les événements. Pour les templates, on utilise simplement le chemin standard `templates/bundles/NomDuBundle/`.
*   **Paramètres** : Surcharger les paramètres dans `services.yaml` est le moyen le plus simple de configurer les bundles tiers.

## Ressources
*   [Symfony Docs - Service Decoration](https://symfony.com/doc/current/service_container/service_decoration.html)

