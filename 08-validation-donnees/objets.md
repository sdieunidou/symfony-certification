# Validation d'Objets PHP (Service Validator)

## Concept clé
Le composant Validator (`symfony/validator`) est un service autonome. Bien qu'intégré aux Formulaires, il peut (et doit) être utilisé seul pour valider des DTOs, des Entités API, ou des paramètres de commande.

## Utilisation du Service

```php
// Injection
use Symfony\Component\Validator\Validator\ValidatorInterface;

public function index(ValidatorInterface $validator): Response
{
    $user = new User();
    $user->email = 'invalid-email';

    // Retourne une ConstraintViolationList
    $errors = $validator->validate($user);

    if (count($errors) > 0) {
        // Il y a des erreurs
        $errorString = (string) $errors; // Casting string pour debug rapide
        
        // Accès détaillé
        foreach ($errors as $violation) {
            echo $violation->getMessage(); // "This value is not a valid email."
            echo $violation->getPropertyPath(); // "email"
            echo $violation->getInvalidValue(); // "invalid-email"
        }
    }
}
```

## Valider une valeur simple
On peut valider une valeur scalaire sans créer de classe, en passant les contraintes à la volée.

```php
use Symfony\Component\Validator\Constraints as Assert;

$email = 'test@example.com';
$constraints = [
    new Assert\NotBlank(),
    new Assert\Email(),
];

$errors = $validator->validate($email, $constraints);
```

## Sources de Métadonnées
Comment le Validator sait-il quelles règles appliquer à la classe `User` ?
1.  **Attributs PHP** (Recommandé en Symfony 7).
2.  **YAML** (`config/validator/*.yaml`).
3.  **XML**.
4.  **Méthode statique** `loadValidatorMetadata` (Rare).

## 🧠 Concepts Clés
1.  **JSR-303** : Le Validator Symfony est inspiré de la spécification Bean Validation de Java (JSR-303).
2.  **Violation** : Une erreur est une instance de `ConstraintViolation`.
3.  **Service** : Le validateur est stateless et réutilisable.

## ⚠️ Points de vigilance (Certification)
*   **Exceptions** : Le Validator ne lance **pas** d'exception s'il y a des erreurs de validation. Il retourne une liste d'erreurs (que vous pouvez compter). C'est à vous de décider si vous devez lancer une Exception (ex: API) ou afficher le formulaire (HTML).
*   **Autowiring** : `ValidatorInterface` injecte le service principal.

## Ressources
*   [Symfony Docs - Validation](https://symfony.com/doc/current/validation.html)
