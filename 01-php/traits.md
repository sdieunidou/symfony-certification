# Traits

## Concept clé
Les Traits permettent la **composition horizontale** de comportement.
PHP utilise l'héritage simple (une classe ne peut étendre qu'une seule classe). Les Traits contournent cette limitation en permettant de "copier-coller" des méthodes et propriétés dans plusieurs classes indépendantes.

## Application dans Symfony 7.0
Symfony utilise les traits pour :
1.  **Comportements optionnels** : `LoggerAwareTrait` (ajoute `setLogger`).
2.  **Helpers dans les tests** : `KernelTestCase`, `WebTestCase` utilisent des traits pour fournir des assertions.
3.  **Composants découplés** : Dans Doctrine, `TimestampableTrait` (via extensions) est l'exemple canonique.

## Syntaxe et Fonctionnalités Avancées

### 1. Résolution de Conflits (`insteadof`, `as`)
Si deux traits fournissent la même méthode, il y a collision.

```php
trait A {
    public function smallTalk() { echo 'a'; }
    public function bigTalk() { echo 'A'; }
}

trait B {
    public function smallTalk() { echo 'b'; }
    public function bigTalk() { echo 'B'; }
}

class Talker {
    use A, B {
        // 1. Résolution : Choisir B pour smallTalk
        B::smallTalk insteadof A;
        
        // 2. Résolution : Choisir A pour bigTalk
        A::bigTalk insteadof B;
        
        // 3. Aliasing : Garder smallTalk de A sous un autre nom
        A::smallTalk as talkASide;
    }
}
```

### 2. Changement de Visibilité
On peut changer la visibilité d'une méthode importée.

```php
class MyClass {
    use SomeTrait {
        // La méthode publique devient privée dans cette classe
        someMethod as private; 
    }
}
```

### 3. Traits Composés
Un trait peut utiliser d'autres traits.

```php
trait Hello { function sayHello() {} }
trait World { function sayWorld() {} }

trait HelloWorld {
    use Hello, World;
}
```

### 4. Méthodes Abstraites dans les Traits
Un trait peut forcer la classe utilisatrice à implémenter une méthode.

```php
trait LoggerTrait {
    // La classe QUI UTILISE le trait DOIT définir cette méthode
    abstract public function getLogPrefix(): string;

    public function log(string $msg): void {
        echo $this->getLogPrefix() . $msg;
    }
}
```

## 🧠 Concepts Clés
1.  **Précédence (Ordre d'écrasement)** :
    *   **Classe Courante** > **Trait** > **Classe Parente**.
    *   Une méthode définie dans la classe *écrase* celle du trait.
    *   Une méthode du trait *écrase* celle héritée du parent.
2.  **Constantes (PHP 8.2)** : Les traits peuvent définir des constantes. Si la classe définit la même constante, elle doit avoir la même valeur et visibilité, sinon erreur fatale.
3.  **Propriétés** : Les traits peuvent définir des propriétés.
    *   Si la classe définit la même propriété, elle doit être **strictement identique** (type, valeur par défaut, visibilité, readonly). Sinon : Fatal Error (avant PHP 8 c'était un warning E_STRICT).

## ⚠️ Points de vigilance (Certification)
*   **`__TRAIT__`** : Constante magique qui contient le nom du trait.
*   **État (State)** : Ajouter des propriétés (`private $logger`) dans un trait est techniquement valide mais architecturalement risqué (conflits de noms, état caché). Préférez les traits qui n'apportent que du comportement (méthodes).
*   **Polymorphisme** : `instanceof` **NE FONCTIONNE PAS** avec les traits. `$obj instanceof MyTrait` est faux ou erreur. Un trait n'est pas un type. C'est un morceau de code.

## Ressources
*   [Manuel PHP - Traits](https://www.php.net/manual/fr/language.oop5.traits.php)
