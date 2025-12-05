# Voters et Stratégies de Vote

## Concept clé
Quand `ROLE_ADMIN` ne suffit pas (ex: "L'utilisateur peut-il éditer CET article ?"), on utilise des **Voters**.
Un Voter est une classe qui vote (ACCESS_GRANTED, ACCESS_DENIED, ACCESS_ABSTAIN) pour un attribut donné sur un sujet donné.

## Application dans Symfony 7.0

```php
class PostVoter extends Voter
{
    const EDIT = 'POST_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) return false;
        
        // Logique métier
        return $subject->getAuthor() === $user;
    }
}
```

### Stratégies (AccessDecisionManager)
Qui gagne si j'ai 3 voters ?
1.  `affirmative` (Défaut) : Il suffit d'un GRANT.
2.  `consensus` : La majorité l'emporte.
3.  `unanimous` : Il faut l'unanimité (et au moins un GRANT).
4.  `priority` : Le premier qui vote décide.

## Points de vigilance (Certification)
*   **Abstain** : Si le voter ne supporte pas l'attribut, il doit s'abstenir (retourner false dans `supports`).
*   **Configuration** : La stratégie se change dans `security.yaml` (`access_decision_manager`).

## Ressources
*   [Symfony Docs - Voters](https://symfony.com/doc/current/security/voters.html)

