# Programmation Orientée Objet (POO)

## Concept clé
La POO structure le logiciel autour de données ("Objets") plutôt que de logique ("Fonctions").
Piliers :
1.  **Encapsulation** : Masquer les détails internes (`private`, `protected`).
2.  **Héritage** : Créer de nouvelles classes basées sur des existantes.
3.  **Polymorphisme** : Capacité d'un objet à prendre plusieurs formes (via Interfaces/Héritage).
4.  **Abstraction** : Simplifier la complexité en masquant les détails d'implémentation.

## Application dans Symfony 7.0
Tout est objet.
*   **Request** : Objet représentant la requête HTTP.
*   **Service** : Objet singleton effectuant un travail.
*   **Entity** : Objet représentant une donnée métier (souvent mappé en DB).
*   **Event** : Objet transportant des informations lors d'un événement.

## Méthodes Magiques (Magic Methods)
Méthodes spéciales commençant par `__` interceptant des événements du cycle de vie de l'objet.

```php
class Product
{
    // Constructeur (Instanciation)
    public function __construct(
        private string $name
    ) {}

    // Conversion en chaîne (ex: echo $product)
    public function __toString(): string
    {
        return $this->name;
    }

    // Appel de l'objet comme une fonction ($product())
    public function __invoke()
    {
        // Très utilisé dans Symfony pour les MessageHandlers ou Contrôleurs simples
    }

    // Sérialisation (PHP 7.4+) - Remplace Serializable interface
    public function __serialize(): array
    {
        return ['n' => $this->name];
    }

    // Désérialisation
    public function __unserialize(array $data): void
    {
        $this->name = $data['n'];
    }
    
    // Clone (Deep copy)
    public function __clone()
    {
        // Appelée quand on fait $p2 = clone $p1;
        // Utile pour cloner les sous-objets (ex: DateTime) pour éviter les références partagées
        $this->date = clone $this->date;
    }
}
```

## Visibilité et Mots-clés

| Mot-clé | Description |
| :--- | :--- |
| `public` | Accessible de partout. |
| `protected` | Accessible dans la classe et ses enfants (héritage). |
| `private` | Accessible uniquement dans la classe elle-même. |
| `final` | Classe : ne peut pas être héritée. Méthode : ne peut pas être surchargée. |
| `static` | Appartient à la classe, pas à l'instance. Partagé globalement. |
| `abstract` | Force l'implémentation dans les enfants. |
| `readonly` | (PHP 8.2) Classe immuable (toutes propriétés sont readonly). |

## Comparaison d'Objets (`==` vs `===`)
*   **`==` (Loose)** : Deux instances sont égales si elles sont de la même classe et ont les mêmes propriétés/valeurs.
*   **`===` (Strict)** : Deux instances sont identiques seulement si elles référencent le **MÊME** objet en mémoire (même ID d'instance).

## Late Static Binding (`static::` vs `self::`)
Concept crucial pour les méthodes statiques et les fabriques (Factories).

```php
class A {
    public static function who() {
        echo __CLASS__;
    }
    public static function testSelf() {
        self::who(); // Résolu à la compilation -> Classe A
    }
    public static function testStatic() {
        static::who(); // Résolu à l'exécution -> Classe Appelante (B)
    }
}

class B extends A {
    public static function who() {
        echo __CLASS__;
    }
}

B::testSelf();   // Affiche "A"
B::testStatic(); // Affiche "B" (C'est le Late Static Binding)
```

## 🧠 Concepts Clés
1.  **WeakMap / WeakReference** (PHP 8.0) : Permet de référencer des objets sans empêcher le Garbage Collector de les détruire. Utilisé pour des caches ou des associations temporaires.
2.  **Générateurs (`yield`)** : Permettent de parcourir de grands ensembles de données sans tout charger en mémoire. Une méthode avec `yield` renvoie un objet `Generator`.
3.  **Clonage** : Par défaut, `clone` fait une copie superficielle (shallow copy). Les propriétés objets sont copiées par référence. Utilisez `__clone` pour forcer une copie profonde (deep copy).

## ⚠️ Points de vigilance (Certification)
*   **Héritage de constructeur** : Si une classe enfant ne définit pas de `__construct`, elle hérite de celui du parent. Si elle en définit un, elle **DOIT** appeler `parent::__construct(...)` manuellement si l'initialisation du parent est nécessaire (ce qui est le cas 99% du temps).
*   **Destructeur** : `__destruct()` est appelé quand la dernière référence à l'objet est supprimée ou à la fin du script. Attention aux exceptions dans les destructeurs (mauvaise pratique).
*   **Final par défaut** : Une bonne pratique moderne (promue par certains architectes Symfony) est de déclarer les classes `final` par défaut pour favoriser la composition sur l'héritage et faciliter les mises à jour (Open/Closed Principle).

## Ressources
*   [Manuel PHP - Classes et Objets](https://www.php.net/manual/fr/language.oop5.php)
*   [Manuel PHP - Late Static Binding](https://www.php.net/manual/fr/language.oop5.late-static-bindings.php)
*   [Object Design Style Guide (Matthias Noback)](https://leanpub.com/object-design-style-guide)
