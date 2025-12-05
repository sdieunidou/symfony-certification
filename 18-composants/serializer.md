# Component Serializer

## Concept Clé
Le composant **Serializer** transforme des objets complexes en format spécifique (XML, JSON, YAML, CSV) et inversement.
*   **Serializing** : Objet -> Format (ex: JSON).
*   **Deserializing** : Format (ex: JSON) -> Objet.

## Architecture
Il fonctionne en deux étapes :
1.  **Normalization** : Objet <-> Tableau (Array).
2.  **Encoding** : Tableau <-> Format (String).

```php
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

$encoders = [new JsonEncoder()];
$normalizers = [new ObjectNormalizer()];

$serializer = new Serializer($normalizers, $encoders);

// Serialize
$jsonContent = $serializer->serialize($person, 'json');

// Deserialize
$person = $serializer->deserialize($jsonContent, Person::class, 'json');
```

## Fonctionnalités Avancées
*   **Groups** : Limiter les propriétés exportées (`#[Groups(['list'])]`).
*   **MaxDepth** : Gérer les références circulaires.
*   **Context** : Passer des options lors de la sérialisation.
*   **Custom Normalizers** : Pour des transformations spécifiques.

## Ressources
*   [Symfony Docs - Serializer](https://symfony.com/doc/current/components/serializer.html)
