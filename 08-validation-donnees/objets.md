# Validation d'Objets PHP

## Concept clé
Le composant Validator permet de valider que les données d'un objet (ou un tableau) respectent certaines règles (Contraintes). Il est indépendant des formulaires, mais souvent utilisé avec.

## Application dans Symfony 7.0
On utilise des Attributs PHP pour définir les règles directement sur les propriétés de la classe.

```php
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class User
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    private string $name;

    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas valide.')]
    private string $email;
}
```

### Utilisation du Service Validator
```php
public function register(ValidatorInterface $validator): Response
{
    $user = new User();
    // ... remplir l'objet ...

    $errors = $validator->validate($user);

    if (count($errors) > 0) {
        $errorsString = (string) $errors;
        return new Response($errorsString);
    }

    return new Response('Valid User!');
}
```

## Points de vigilance (Certification)
*   **Indépendance** : Le validateur valide des objets, pas des formulaires. Le formulaire utilise le validateur en interne.
*   **Configuration** : Symfony supporte YAML et XML pour la configuration de validation (`config/validator/`), mais les Attributs sont recommandés.

## Ressources
*   [Symfony Docs - Validation](https://symfony.com/doc/current/validation.html)

