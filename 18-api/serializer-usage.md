# Sérialisation en API

## Le rôle du Serializer
Le composant Serializer transforme des objets complexes (Entités, DTOs) en formats spécifiques (JSON, XML, CSV) et inversement.
En API, c'est lui qui formate la réponse envoyée au client.

## Groupes de Sérialisation
C'est la fonctionnalité la plus critique pour une API. Elle permet de contrôler quels champs sont exposés pour une opération donnée, évitant la fuite de données sensibles (mots de passe) ou les boucles infinies.

```php
namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

class User
{
    #[Groups(['user:read', 'user:write'])]
    private string $email;

    #[Groups(['user:write'])] // Jamais exposé en lecture !
    private string $password;

    #[Groups(['user:read'])]
    private \DateTimeImmutable $createdAt;
    
    // ...
}
```

Dans le contrôleur :
```php
return $this->json($user, 200, [], ['groups' => 'user:read']);
```

## Problèmes courants et Solutions

### 1. Références Circulaires (Circular Reference)
Si User a des Posts, et Post a un User, le sérialiseur va boucler à l'infini.
**Solutions :**
1.  **Groupes** : Ne pas mettre le groupe `post:read` sur la propriété `User::$posts` ET le groupe `user:read` sur `Post::$author`. Casser la boucle via les groupes.
2.  **`AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER`** : Définir un callback pour gérer la boucle (ex: retourner juste l'ID).
3.  **`#[MaxDepth]`** : Attribut pour limiter la profondeur, mais nécessite d'activer le `enable_max_depth`.

### 2. Ignored Attributes
Parfois on veut exclure un champ sans toucher aux groupes de l'entité (cas ad-hoc).

```php
$json = $serializer->serialize($user, 'json', [
    AbstractNormalizer::IGNORED_ATTRIBUTES => ['password', 'salt']
]);
```

## Contextes de Normalisation
Le 3ème argument de `serialize()` (ou 4ème de `json()`) est le contexte. Il pilote le comportement.

*   `groups` : Les groupes actifs.
*   `datetime_format` : Format des dates (ex: `Y-m-d`).
*   `enable_max_depth` : Activer la gestion de la profondeur.

## Sérialisation des Relations
Par défaut, le sérialiseur tente de normaliser les objets imbriqués.
Pour une API, on veut souvent éviter de tout charger.
Si vous utilisez des **DTOs**, ce problème disparaît car vous contrôlez explicitement la structure plate ou hiérarchique.

## 🧠 Concepts Clés
1.  **Normalizer vs Encoder** :
    *   `Normalizer` : Objet PHP -> Array (Tableau associatif).
    *   `Encoder` : Array -> String (JSON, XML).
    *   API Platform ou `json()` font les deux étapes.
2.  **Performance** : Sérialiser de grosses collections d'entités avec beaucoup de relations peut être lent (Hydratation Doctrine + Réflexion). Pour les listes massives, préférez des DTOs légers ou une requête SQL/DQL optimisée retournant un tableau.

## Ressources
*   [Symfony Docs - Serializer](https://symfony.com/doc/current/components/serializer.html)
