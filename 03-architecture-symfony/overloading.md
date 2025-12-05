# Surcharge du Framework (Overloading)

## Concept clé
Symfony suit le principe **Open/Closed** : ouvert à l'extension, fermé à la modification.
On ne modifie jamais le code dans `vendor/`. On l'étend ou on le remplace via les mécanismes prévus.

## Mécanismes de Surcharge

### 1. Events (Le plus sûr)
Plutôt que de changer le cœur, on s'abonne aux événements (`kernel.request`, `security.interactive_login`) pour altérer le flux.
*   *Exemple* : Rediriger un utilisateur après login (ne pas modifier le contrôleur de login, utiliser un Listener).

### 2. Décoration de Service (Le plus puissant)
Remplace un service existant par le vôtre, tout en injectant l'original à l'intérieur (Pattern Decorator).
L'ID du service reste le même pour le reste de l'application.

```yaml
# config/services.yaml
App\Mailer\TraceableMailer:
    decorates: 'mailer.default_transport'
    decoration_priority: 10 # Optionnel : pour empiler les décorateurs
    arguments: ['@.inner']  # Injecte le service original (mailer.default_transport)
```

```php
class TraceableMailer implements TransportInterface {
    public function __construct(private TransportInterface $inner) {}
    public function send(...) {
        // Avant
        $this->inner->send(...);
        // Après
    }
}
```

### 3. Compiler Passes (Avancé)
Permet de manipuler la définition des services **avant** la compilation du conteneur (ex: changer la classe d'un service, appeler une méthode setter sur tous les services taggués).

```php
// src/Kernel.php
protected function build(ContainerBuilder $container): void
{
    $container->addCompilerPass(new CustomPass());
}
```

### 4. Remplacement de Paramètres
Beaucoup de services internes utilisent des classes définies dans les paramètres.
*   *Exemple* : Changer la classe de l'ExceptionListener (rarement utile aujourd'hui, préférer la décoration).

### 5. Héritage de Bundle (Supprimé)
Le mécanisme "Bundle Inheritance" (FOSUserBundleParent) n'existe plus.
Pour surcharger un template de bundle tiers :
*   Copier `vendor/acme/foo-bundle/Resources/views/index.html.twig`
*   Vers `templates/bundles/AcmeFooBundle/index.html.twig`.

Pour surcharger un contrôleur de bundle tiers :
*   Créer une route avec le **même path**, qui pointe vers votre contrôleur. Votre route doit être chargée **avant** celle du bundle (ordre dans `config/routes.yaml` ou priorité).

## 🧠 Concepts Clés
1.  **Composition > Héritage** : La décoration est supérieure à l'héritage de classe car elle fonctionne même si la classe originale est `final`, et elle respecte l'interface.
2.  **Priorité** :
    *   Events : Priorité élevée = exécuté avant.
    *   Routes : Première chargée = première servie (First Match Win).
    *   Services : L'alias local écrase l'alias du vendor.

## ⚠️ Points de vigilance (Certification)
*   **Classes Finales** : On ne peut pas étendre une classe `final`. La décoration est la seule solution.
*   **Services Privés** : On ne peut pas décorer un service qui a été "inliné" (supprimé) lors de la compilation. Mais la plupart des services utiles sont décorables.

## Ressources
*   [Symfony Service Decoration](https://symfony.com/doc/current/service_container/service_decoration.html)
*   [Overriding Templates](https://symfony.com/doc/current/bundles/override.html)
