# Hacheurs de Mots de Passe (Password Hashers)

## Concept clé
On ne stocke jamais un mot de passe en clair. On le hache (one-way).
Symfony a renommé "Encoders" en "Hashers" en version 5.3 pour être plus précis (l'encodage est réversible, le hachage non).

## Application dans Symfony 7.0
Configuration dans `security.yaml`. L'algorithme recommandé est `auto` (choisit le meilleur dispo : Sodium ou Bcrypt).

```yaml
password_hashers:
    Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
```

### Utilisation (Service)
Le service `UserPasswordHasherInterface`.

```php
public function register(User $user, string $plainPassword, UserPasswordHasherInterface $hasher): void
{
    // Hacher le mot de passe
    $hashed = $hasher->hashPassword($user, $plainPassword);
    $user->setPassword($hashed);
}
```

## Points de vigilance (Certification)
*   **Migration** : Symfony gère la migration automatique des algorithmes. Si vous configurez `auto` et qu'un utilisateur se connecte avec un vieux hash (ex: SHA1), Symfony le détecte, vérifie le mot de passe, et si OK, le re-hache avec le nouvel algo (Sodium/Bcrypt) à la volée.
*   **Cost** : Pour les tests, on réduit le coût algorithmique pour que ça aille vite. En prod, on veut que ce soit lent (pour contrer le brute-force).

## Ressources
*   [Symfony Docs - Password Hashing](https://symfony.com/doc/current/security/passwords.html)

