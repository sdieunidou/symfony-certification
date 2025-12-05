# Voters (Système de Vote)

## Concept clé
Les Voters permettent une gestion fine et centralisée des permissions (Business Logic Security).
Au lieu de disséminer des `if ($user->getId() === $post->getAuthor()->getId())` partout dans les contrôleurs, on encapsule cette logique dans une classe Voter réutilisable.

## Fonctionnement
Quand on appelle `is_granted($attribute, $subject)`, le `AccessDecisionManager` interroge tous les Voters enregistrés.
Chaque Voter doit répondre :
*   **ACCESS_ABSTAIN** : "Je ne gère pas ça".
*   **ACCESS_GRANTED** : "Je suis d'accord".
*   **ACCESS_DENIED** : "Je refuse".

## Création d'un Voter

```php
namespace App\Security;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    const VIEW = 'POST_VIEW';
    const EDIT = 'POST_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Ce voter ne s'intéresse qu'aux Posts et aux actions VIEW/EDIT
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false; // Accès refusé si anonyme
        }

        /** @var Post $post */
        $post = $subject;

        return match($attribute) {
            self::VIEW => true, // Tout le monde peut voir
            self::EDIT => $user === $post->getAuthor() || in_array('ROLE_ADMIN', $user->getRoles()),
            default => false,
        };
    }
}
```

## Utilisation
```php
// Dans le contrôleur
#[IsGranted(PostVoter::EDIT, subject: 'post')]
public function edit(Post $post): Response { ... }

// Dans Twig
{% if is_granted('POST_EDIT', post) %}
    <a href="...">Editer</a>
{% endif %}
```

## 🧠 Concepts Clés
1.  **Centralisation** : Toute la logique de sécurité métier est dans `App\Security`.
2.  **Stratégie** : Par défaut (`affirmative`), si un seul Voter accorde l'accès, c'est gagné (même si un autre refuse). Si vous voulez que le refus soit prioritaire, passez en stratégie `unanimous`.

## ⚠️ Points de vigilance (Certification)
*   **Service** : Les Voters sont des services standards. Vous pouvez injecter `Security`, `RequestStack` ou des Repositories dans le constructeur du Voter.
*   **Supports** : La méthode `supports` est cruciale pour la performance. Elle doit être rapide et ne pas faire de requêtes DB.

## Ressources
*   [Symfony Docs - Voters](https://symfony.com/doc/current/security/voters.html)
