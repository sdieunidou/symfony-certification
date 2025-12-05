# Hacheurs de Mots de Passe (Password Hashers)

## Concept clé
Stockage sécurisé des mots de passe. Symfony utilise des algorithmes modernes (Argon2, Bcrypt) et gère le salage (salt) automatiquement.

## Configuration (`security.yaml`)

```yaml
security:
    password_hashers:
        # Applique l'algo 'auto' à tous les objets implémentant l'interface
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
```

### Algorithme `auto`
Symfony choisit le meilleur algorithme disponible sur le serveur PHP :
1.  **Sodium** (Argon2i/id) : Le top du top (nécessite extension sodium).
2.  **Bcrypt** : Standard robuste.

## Utilisation du Service (`UserPasswordHasherInterface`)
Ne jamais utiliser `md5()` ou `sha1()`. Utilisez le service.

```php
public function changePassword(
    User $user, 
    string $newPlainPassword, 
    UserPasswordHasherInterface $hasher
): void
{
    // 1. Hashage
    $hash = $hasher->hashPassword($user, $newPlainPassword);
    
    // 2. Mise à jour
    $user->setPassword($hash);
}
```

### Vérification manuelle
```php
if ($hasher->isPasswordValid($user, $inputPassword)) {
    // OK
}
```

## Migration de Hash (`migrate_from`)
Si vous migrez d'un vieux projet (ex: MD5) vers Symfony moderne, vous pouvez configurer une migration progressive.

```yaml
password_hashers:
    App\Entity\User:
        algorithm: auto
        migrate_from:
            algorithm: md5
            encode_as_base64: false
            iterations: 1
```

### Mise à jour automatique (PasswordUpgraderInterface)
Pour que la migration fonctionne, votre **Repository** (si Doctrine) ou UserProvider doit implémenter `PasswordUpgraderInterface`.

```php
// src/Repository/UserRepository.php
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // Persister le nouveau mot de passe hashé automatiquement après le login
        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->flush();
    }
}
```

## Hashers Dynamiques (Named Hashers)
Si vous avez besoin de différents algorithmes selon l'utilisateur (ex: Admins en Argon2 très lent, Users en Bcrypt standard), vous pouvez utiliser des "Named Hashers" et implémenter `PasswordHasherAwareInterface` sur votre User.

```yaml
password_hashers:
    harsh:
        algorithm: auto
        cost: 15
```

```php
// src/Entity/User.php
class User implements PasswordHasherAwareInterface
{
    public function getPasswordHasherName(): ?string
    {
        // Retourne le nom du hasher à utiliser pour cet utilisateur
        return $this->isAdmin() ? 'harsh' : null; // null = défaut
    }
}
```

## 🧠 Concepts Clés
1.  **Salt** : Avec Sodium et Bcrypt, le sel est intégré dans le hash résultant. La méthode `getSalt()` de l'interface `UserInterface` est désormais obsolète/inutile pour ces algos.
2.  **Work Factor** : Le hachage DOIT être lent pour empêcher les attaques brute-force.

## ⚠️ Points de vigilance (Certification)
*   **Test** : En environnement de test, on configure le coût algorithmique au minimum pour accélérer la suite de tests (voir `config/packages/test/security.yaml`).
*   **Injection** : Depuis Symfony 7.4, vous pouvez injecter un hasher spécifique avec l'attribut `#[Target('my_hasher_name')]` sur `PasswordHasherInterface`.

## Ressources
*   [Symfony Docs - Passwords](https://symfony.com/doc/current/security/passwords.html)
