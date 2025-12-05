# Groupes de Validation

## Concept clé
Un même objet (Entité ou DTO) peut être validé différemment selon le contexte.
Exemple : L'email est obligatoire à l'inscription, mais optionnel lors d'une mise à jour de profil admin.
Les **Groupes** permettent d'activer/désactiver des contraintes dynamiquement.

## Application dans Symfony 7.0

### 1. Définir les groupes (Attributs)
```php
class User
{
    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Email(groups: ['registration', 'profile'])]
    public string $email;

    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Length(min: 8, groups: ['registration', 'password_change'])]
    public string $plainPassword;
}
```

### 2. Valider un groupe spécifique
Utilisation avec le service Validator :
```php
$validator->validate($user, null, ['registration']);
```

Utilisation dans un Formulaire :
```php
$builder->add('...', TextType::class, [
    // ...
]);

public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'validation_groups' => ['registration'],
        // Ou dynamique via callback/closure
        'validation_groups' => function (FormInterface $form) {
            return $form->getData()->isAdmin() ? ['admin_edit'] : ['user_edit'];
        },
    ]);
}
```

## Le Groupe `Default`
*   Si aucune option `groups` n'est spécifiée sur une contrainte, elle appartient au groupe **`Default`**.
*   Si vous validez sans spécifier de groupe (`$validator->validate($user)`), c'est le groupe `Default` qui est validé.
*   **Attention** : Si vous validez le groupe `['registration']`, le groupe `Default` n'est **PAS** validé. Si vous voulez les deux, passez `['Default', 'registration']`.

## Alias de Classe
Le nom de la classe est un alias du groupe `Default`.
Si vous êtes dans la classe `App\Entity\User`, le groupe `User` est équivalent à `Default`.

## 🧠 Concepts Clés
1.  **Intersection** : Une contrainte est validée si au moins un de ses groupes est demandé lors de la validation.
2.  **Formulaire** : L'option `validation_groups` est essentielle pour réutiliser les mêmes entités dans différents formulaires (Register, Edit, AdminAdd).

## ⚠️ Points de vigilance (Certification)
*   **Héritage** : Les groupes sont hérités.
*   **Performance** : Utiliser trop de groupes rend le code difficile à lire. Parfois, créer deux DTOs distincts (`RegistrationDto`, `ProfileDto`) est plus propre que de bourrer l'entité User de groupes conditionnels.

## Ressources
*   [Symfony Docs - Validation Groups](https://symfony.com/doc/current/validation/groups.html)
