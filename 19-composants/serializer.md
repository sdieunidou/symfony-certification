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

## 3. Gestion des Attributs et Mapping

C'est la fonctionnalité la plus utilisée pour contrôler quelles propriétés sont exposées et comment.

### Groupes de Sérialisation
Utilisez l'attribut `#[Groups]`.

```php
use Symfony\Component\Serializer\Attribute\Groups;

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

### SerializedName & SerializedPath
Si la structure JSON diffère de votre objet.

*   `#[SerializedName('customer_id')]` : Renomme la clé JSON.
*   `#[SerializedPath('[address][city]')]` : Mappe une structure imbriquée vers une propriété à plat (Flattening).

### Ignorer des attributs
*   `#[Ignore]` : La propriété est totalement ignorée.
*   `#[MaxDepth(1)]` : Pour gérer les relations profondes (nécessite `enable_max_depth` dans le contexte).

---

## 4. Contexte et Options

Le 3ème argument de `serialize/deserialize` est un tableau de contexte `$context`. Vous pouvez aussi utiliser l'attribut `#[Context]` directement sur la classe ou la propriété.

```php
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class Meeting
{
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    public \DateTime $date;
}
```

### Options Courantes
*   `AbstractNormalizer::IGNORED_ATTRIBUTES` : `['password']`
*   `AbstractNormalizer::CALLBACKS` : Pour modifier une valeur à la volée.
*   `AbstractNormalizer::OBJECT_TO_POPULATE` : Mettre à jour un objet existant au lieu d'en créer un nouveau.
*   `ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER` : Gérer les boucles (A -> B -> A).

---

## 5. Fonctionnalités Avancées

### Named Serializers (Symfony 7.2+)
Vous pouvez configurer plusieurs instances de Serializer avec des configurations différentes (ex: un pour l'API publique, un pour l'export CSV interne).

```yaml
# config/packages/serializer.yaml
framework:
    serializer:
        named_serializers:
            api_client:
                name_converter: 'serializer.name_converter.camel_case_to_snake_case'
                default_context:
                    enable_max_depth: true
```
Injection : `#[Target('apiClient.serializer')] SerializerInterface $apiSerializer`.

### Discriminator Map (Polymorphisme)
Si vous avez une propriété qui peut contenir plusieurs types d'objets (héritage), le Serializer utilise un champ `type` pour savoir quelle classe instancier.

```php
#[DiscriminatorMap(typeProperty: 'type', mapping: [
    'user' => User::class,
    'admin' => Admin::class
])]
interface UserInterface {}
```

---

## 6. Points de vigilance pour la Certification

*   **Getters/Setters** : Par défaut, `ObjectNormalizer` utilise les getters (`getNom()`, `isActif()`, `hasRole()`) et setters. Si la propriété est `public`, il l'utilise directement.
*   **Constructeur** : Lors de la désérialisation, si l'objet a des arguments obligatoires dans le constructeur, le serializer essaie de mapper les champs du JSON aux arguments du constructeur (s'ils portent le même nom).
*   **Format vs Type** : Le 2ème argument de `serialize` est le **format** ('json'), le 2ème de `deserialize` est le **type** (classe cible).
