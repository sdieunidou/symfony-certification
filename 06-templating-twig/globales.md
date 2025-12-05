# Variables Globales

## Concept clé
Certaines variables sont disponibles dans tous les templates sans avoir besoin de les passer depuis le contrôleur.

## Application dans Symfony 7.0
La variable globale la plus importante fournie par Symfony est `app`.
C'est une instance de `Symfony\Bridge\Twig\AppVariable`.

### Propriétés de `app`
*   `app.user` : L'utilisateur connecté (ou null).
*   `app.request` : L'objet Request courant.
*   `app.session` : La session.
*   `app.flashes` : Les messages flash.
*   `app.environment` : L'environnement (dev, prod).
*   `app.debug` : Booléen (mode debug).

### Définir vos propres globales
Dans `config/packages/twig.yaml` :

```yaml
twig:
    globals:
        admin_email: 'admin@example.com'
        # Service injection (préfixe @)
        uuid_generator: '@App\Service\UuidGenerator'
```

## Points de vigilance (Certification)
*   **Performance** : Les globales définies dans `twig.yaml` sont injectées dans tous les templates. Évitez d'injecter des services lourds s'ils ne sont pas utilisés partout.
*   **Accès** : `app.user` retourne l'objet User (UserInterface). Si non connecté, c'est `null`. Toujours vérifier `{% if app.user %}`.

## Ressources
*   [Symfony Docs - Global Variables](https://symfony.com/doc/current/templates.html#global-variables)

