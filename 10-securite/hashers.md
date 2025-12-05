# Hacheurs de Mots de Passe (Password Hashers)

## Concept clé
Stockage sécurisé des mots de passe. Symfony utilise des algorithmes modernes (Argon2, Bcrypt) et gère le salage (salt) automatiquement.

## Interface `PasswordAuthenticatedUserInterface`
Votre classe User doit implémenter cette interface pour indiquer qu'elle possède un mot de passe.
*   `getPassword()` : Retourne le hash stocké.

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
Quand un utilisateur se connecte avec son vieux mot de passe MD5, Symfony :
1.  Vérifie avec l'algo MD5.
2.  Si valide, re-hache le mot de passe avec `auto` (Sodium).
3.  Appelle `$user->setPassword($newHash)`.
4.  Vous devez persister le changement (Listener Doctrine ou manuel).

## 🧠 Concepts Clés
1.  **Salt** : Avec Sodium et Bcrypt, le sel est intégré dans le hash résultant. La méthode `getSalt()` de l'interface `UserInterface` est désormais obsolète/inutile pour ces algos.
2.  **Work Factor** : Le hachage DOIT être lent pour empêcher les attaques brute-force.

## ⚠️ Points de vigilance (Certification)
*   **Test** : En environnement de test, on configure le coût algorithmique au minimum pour accélérer la suite de tests (voir `config/packages/test/security.yaml`).

## Ressources
*   [Symfony Docs - Passwords](https://symfony.com/doc/current/security/passwords.html)
