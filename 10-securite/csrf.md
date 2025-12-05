# Protection CSRF (Cross-Site Request Forgery)

## Concept clé
Le CSRF est une attaque où un utilisateur authentifié est piégé pour exécuter une action non désirée sur votre application (ex: changer son mot de passe via un formulaire caché sur un site malveillant).
La défense consiste à utiliser un **Jeton (Token) CSRF** unique et secret, généré par le serveur et validé lors de la soumission du formulaire.

## Installation
Le composant est généralement installé par défaut, mais si besoin :
```bash
composer require symfony/security-csrf
```
Activation dans `framework.yaml` :
```yaml
framework:
    csrf_protection: ~ # Active la protection (via session par défaut)
```

## Utilisation avec Symfony Forms
C'est automatique. Symfony génère un champ caché `_token` dans chaque formulaire et le vérifie lors du `handleRequest()`.

### Configuration Globale
```yaml
# config/packages/framework.yaml
framework:
    form:
        csrf_protection:
            enabled: true
            field_name: '_token'
```

### Configuration par Formulaire
```php
public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'csrf_protection' => true,
        'csrf_field_name' => '_token',
        'csrf_token_id'   => 'task_item', // Identifiant unique pour ce type de form
    ]);
}
```

## Génération et Vérification Manuelle
Si vous n'utilisez pas le composant Form (ex: un simple bouton Delete en HTML), vous devez gérer le token manuellement.

### 1. Génération (Twig)
Utilisez la fonction `csrf_token(token_id)`.
```html
<form action="{{ path('delete_item', {id: item.id}) }}" method="post">
    <input type="hidden" name="token" value="{{ csrf_token('delete-item') }}">
    <button type="submit">Supprimer</button>
</form>
```

### 2. Vérification (Controller)
#### Méthode Classique
```php
public function delete(Request $request): Response
{
    $submittedToken = $request->request->get('token');
    
    if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
        // Action autorisée
    }
}
```

#### Attribut `#[IsCsrfTokenValid]` (Symfony 7.1+)
Plus propre, valide automatiquement avant d'entrer dans l'action.
```php
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

#[IsCsrfTokenValid('delete-item', tokenKey: 'token')]
public function delete(): Response
{
    // Si on arrive ici, le token est valide !
}
```
*Note : Vous pouvez restreindre aux méthodes HTTP : `methods: ['POST']`.*

## Stateless CSRF (Symfony 7.2+)
Traditionnellement, les tokens CSRF sont stockés en **session** (Stateful). Cela pose problème pour le cache HTTP ou les applis stateless.
Symfony 7.2 introduit une protection CSRF sans état.

### Configuration
Déclarez les IDs de tokens qui doivent être stateless :
```yaml
# config/packages/framework.yaml
framework:
    csrf_protection:
        stateless_token_ids: ['submit', 'authenticate', 'logout']
```

### Fonctionnement
Symfony vérifie les en-têtes `Origin` et `Referer` de la requête HTTP. Si l'origine correspond au domaine de l'application, le token est considéré comme valide.
*Pour le "Defense in Depth", un cookie et un header `csrf-token` peuvent être utilisés via un script JS fourni par Symfony.*

## Login & Logout
*   **Login** : Le formulaire de login nécessite un token (champ `_csrf_token`). Configurez-le dans `security.yaml` -> `enable_csrf: true`.
*   **Logout** : Pour éviter qu'un attaquant ne déconnecte un utilisateur à son insu, la route `/logout` doit aussi être protégée par CSRF (sauf si configurée autrement).

## ⚠️ Points de vigilance (Certification)
*   **Token ID** : La chaîne passée à `csrf_token('mon-id')` doit être la même que celle passée à `isCsrfTokenValid('mon-id')`. C'est ce qui lie le formulaire à sa validation.
*   **Cache** : Attention aux formulaires CSRF sur des pages cachées publiquement. Le token étant unique par utilisateur (en mode Stateful), cacher la page HTML mettrait en cache le token du premier utilisateur pour tous les autres -> Erreurs CSRF. Solution : ESI, AJAX, ou Stateless CSRF.
