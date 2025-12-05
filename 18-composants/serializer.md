# Le Composant Serializer

Le composant **Serializer** est un outil puissant pour transformer des objets complexes en formats d'échange (JSON, XML, CSV, YAML) et inversement. Il est crucial pour les APIs (API Platform repose entièrement dessus).

---

## 1. Architecture Interne

Le processus de sérialisation se fait en deux étapes distinctes :

1.  **Normalization** : Objet PHP complexe -> Tableau (Array) de scalaires.
    *   Géré par les **Normalizers**.
2.  **Encoding** : Tableau -> Chaîne de caractères formatée (String JSON).
    *   Géré par les **Encoders**.

La **Désérialisation** fait l'inverse : Decoding (String -> Array) puis Denormalization (Array -> Objet).

### Initialisation Manuelle
```php
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

$encoders = [new JsonEncoder()];
$normalizers = [new ObjectNormalizer()];

$serializer = new Serializer($normalizers, $encoders);
```
Dans une app Symfony, injectez simplement `SerializerInterface`.

---

## 2. Utilisation Basique

### Sérialiser (Object -> JSON)
```php
$json = $serializer->serialize($user, 'json');
```

### Désérialiser (JSON -> Object)
```php
$user = $serializer->deserialize($jsonData, User::class, 'json');
```

---

## 3. Gestion des Attributs (Groups & Ignore)

C'est la fonctionnalité la plus utilisée pour contrôler quelles propriétés sont exposées.

### Groupes de Sérialisation
Utilisez l'attribut `#[Groups]` (ou annotations).

```php
use Symfony\Component\Serializer\Annotation\Groups;

class User
{
    #[Groups(['user:read', 'user:write'])]
    public string $email;

    #[Groups(['user:read'])]
    public string $username;

    public string $password; // Jamais exposé car pas de groupe
}
```

Lors de la sérialisation, spécifiez le contexte :
```php
$json = $serializer->serialize($user, 'json', ['groups' => 'user:read']);
// Résultat : {"email": "...", "username": "..."}
```

### Ignorer des attributs
*   `#[Ignore]` : La propriété est totalement ignorée.
*   `#[MaxDepth(1)]` : Pour gérer les relations profondes.

---

## 4. Contexte et Options

Le 3ème argument de `serialize/deserialize` est un tableau de contexte `$context`.

*   `AbstractNormalizer::IGNORED_ATTRIBUTES` : `['password']`
*   `AbstractNormalizer::CALLBACKS` : Pour modifier une valeur à la volée.
*   `DateTimeNormalizer::FORMAT_KEY` : `['Y-m-d']`
*   `ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER` : Fonction appelée si une boucle est détectée (A -> B -> A).
    ```php
    'circular_reference_handler' => function ($object) {
        return $object->getId();
    }
    ```

---

## 5. Fonctionnalités Avancées

### Discriminator Map (Polymorphisme)
Si vous avez une propriété qui peut contenir plusieurs types d'objets (héritage), le Serializer utilise un champ `type` pour savoir quelle classe instancier lors de la désérialisation.

```php
#[DiscriminatorMap(typeProperty: 'type', mapping: [
    'user' => User::class,
    'admin' => Admin::class
])]
interface UserInterface {}
```

### SerializedName
Si le nom de la propriété PHP diffère du nom dans le JSON (ex: legacy API).
```php
#[SerializedName('customer_id')]
private int $id;
```

---

## 6. Points de vigilance pour la Certification

*   **Getters/Setters** : Par défaut, `ObjectNormalizer` utilise les getters (`getNom()`, `isActif()`) et setters. Si la propriété est `public`, il l'utilise directement.
*   **Constructeur** : Lors de la désérialisation, si l'objet a des arguments obligatoires dans le constructeur, le serializer essaie de mapper les champs du JSON aux arguments du constructeur (s'ils portent le même nom).
*   **Format vs Type** : Le 2ème argument de `serialize` est le **format** ('json'), le 2ème de `deserialize` est le **type** (classe cible).
