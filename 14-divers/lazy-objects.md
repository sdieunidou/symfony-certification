# Lazy Objects : Lazy Ghost & Virtual Proxy

## Concept Clé
Le **Lazy Loading** (chargement paresseux) est un patron de conception qui diffère l'initialisation d'un objet jusqu'au moment où l'on a réellement besoin d'accéder à l'une de ses propriétés ou méthodes.

Avant Symfony 6.2, cette fonctionnalité reposait souvent sur des bibliothèques externes (comme `ocramius/proxy-manager`). Depuis Symfony 6.2, le composant **VarExporter** fournit deux traits natifs pour implémenter ce pattern de manière performante :
1.  **Lazy Ghost** (`LazyGhostTrait`) : L'objet s'auto-initialise en interne.
2.  **Virtual Proxy** (`LazyProxyTrait`) : Un objet "enveloppe" (wrapper) qui délègue à l'instance réelle.

Ces mécanismes sont massivement utilisés par le **Conteneur de Services** (pour les services `lazy: true`) et par **Doctrine** (pour les relations et entités proxy).

---

## 1. Lazy Ghost (`LazyGhostTrait`)
C'est l'implémentation recommandée et par défaut dans Symfony moderne.

### Fonctionnement
Un "Fantôme" (Ghost) est l'instance réelle de la classe. Elle est créée vide (non initialisée).
*   Dès qu'une propriété est accédée (lecture/écriture), le trait intercepte l'appel.
*   Il déclenche une fonction d'initialisation (initializer).
*   Il hydrate l'objet courant avec les données réelles.
*   L'opération initiale reprend de manière transparente.

### Avantages
*   **Performance** : Moins de surcharge mémoire car il n'y a pas d'objet "wrapper" intermédiaire.
*   **Identité** : `$obj === $realObj` est vrai, car le proxy *est* l'objet.
*   **Complexité** : Pas besoin de générer une classe héritée complexe qui surcharge toutes les méthodes publiques.

### Exemple
```php
use Symfony\Component\VarExporter\LazyGhostTrait;

class HeavyService
{
    use LazyGhostTrait;

    public function __construct(private string $data)
    {
        // Simulation d'un travail lourd (ex: connexion API, lecture fichier)
        sleep(2); 
    }
    
    public function getData(): string { return $this->data; }
}

// Création de l'instance fantôme (instantanée, le sleep n'est pas exécuté)
$ghost = HeavyService::createLazyGhost(function (HeavyService $instance) {
    // C'est ici que le "vrai" constructeur est appelé ou que l'état est défini
    $instance->__construct('Données chargées');
});

// Le sleep(2) se déclenche ICI, au premier accès
echo $ghost->getData(); 
```

---

## 2. Virtual Proxy (`LazyProxyTrait`)
C'est l'approche "classique" du pattern Proxy.

### Fonctionnement
Le proxy est un objet distinct qui *ressemble* à l'objet réel (même interface ou héritage). Il contient une référence vers l'objet réel (le "sujet").
*   Au départ, le sujet est nul.
*   Lors d'un appel de méthode, le proxy instancie le sujet si nécessaire.
*   Il délègue (forward) l'appel au sujet.

### Inconvénients
*   **Identité** : Le proxy n'est pas l'objet réel. Cela peut poser problème si le code compare des références d'objets (`===`).
*   **Surcharge** : Nécessite deux objets en mémoire (le proxy + le sujet).
*   **Limitations** : Difficile à utiliser si la classe réelle est `final`.

---

## 3. Utilisation dans le Conteneur (Dependency Injection)

Dans `services.yaml`, quand vous déclarez un service comme *lazy* :

```yaml
services:
    App\Service\HeavyService:
        lazy: true
```

Depuis Symfony 6.3+, le conteneur utilise par défaut la stratégie **Ghost Object** pour générer ce service lazy. Cela signifie que l'objet injecté dans vos contrôleurs est une instance de votre classe, mais non initialisée.

Si vous avez besoin de forcer l'ancienne stratégie (Virtual Proxy), vous pouvez le configurer, mais c'est rarement nécessaire.

---

## ⚠️ Points de vigilance (Certification)

*   **Composant** : Ces traits appartiennent au composant `symfony/var-exporter`, pas à `dependency-injection` directement (bien que DI les utilise).
*   **Différence clé** : Le Ghost Object modifie l'objet *in-place*. Le Virtual Proxy délègue à une *autre* instance.
*   **Propriétés privées** : Le `LazyGhostTrait` gère correctement l'initialisation des propriétés `private` et `readonly` sans utiliser de réflexion lourde au runtime.
*   **Final** : On ne peut pas créer de Ghost ou de Proxy pour une classe `final` (sauf si elle implémente une interface et qu'on proxy l'interface, ce qui est le cas des Virtual Proxies basés sur interface).
