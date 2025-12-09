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
Traditionnellement, les tokens CSRF sont stockés en **session** (Stateful). Cela pose problème pour le cache HTTP (Varnish, CDN) car chaque utilisateur a besoin d'un token unique stocké sur le serveur.
Symfony 7.2 introduit une protection CSRF sans état (Stateless).

### Configuration
Déclarez les IDs de tokens qui doivent être stateless dans `framework.yaml`.

```yaml
# config/packages/framework.yaml
framework:
    csrf_protection:
        # Liste des IDs de tokens qui ne doivent pas être stockés en session
        stateless_token_ids: ['contact_form', 'newsletter_sub']
```

### Exemple Complet : Formulaire de Contact Caché

Imaginons une page de contact publique que vous souhaitez mettre en cache, mais qui contient un formulaire.

**1. Formulaire (PHP)**
Définissez explicitement le `csrf_token_id` pour qu'il matche la configuration.

```php
// src/Form/ContactType.php
public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        // Doit correspondre à une valeur dans stateless_token_ids
        'csrf_token_id' => 'contact_form', 
    ]);
}
```

**2. Contrôleur (Avec Cache)**
Vous pouvez maintenant activer le cache HTTP sur cette page !

```php
#[Route('/contact', name: 'app_contact')]
// La page peut être mise en cache partagé (CDN/Varnish) pendant 1h
#[Cache(smaxage: 3600, public: true)] 
public function index(Request $request): Response
{
    $form = $this->createForm(ContactType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Traitement...
        return $this->redirectToRoute('app_contact_success');
    }

    return $this->render('contact/index.html.twig', [
        'form' => $form,
    ]);
}
```

### Fonctionnement
Au lieu de générer un token aléatoire et de le stocker en session, Symfony génère un token **signé** (HMAC) contenant :
*   L'ID du token (ex: 'contact_form')
*   Une seed (généralement vide pour les anonymes ou liée à l'utilisateur)
*   Le secret de l'application (`APP_SECRET`)

Lors de la soumission, Symfony recalcule le token avec les mêmes paramètres et compare. Si ça matche, c'est valide. Plus besoin de session !

## Login & Logout
*   **Login** : Le formulaire de login nécessite un token (champ `_csrf_token`). Configurez-le dans `security.yaml` -> `enable_csrf: true`.
*   **Logout** : Pour éviter qu'un attaquant ne déconnecte un utilisateur à son insu, la route `/logout` doit aussi être protégée par CSRF (sauf si configurée autrement).

## ⚠️ Points de vigilance (Certification)
*   **Token ID** : La chaîne passée à `csrf_token('mon-id')` doit être la même que celle passée à `isCsrfTokenValid('mon-id')`. C'est ce qui lie le formulaire à sa validation.
*   **Cache** : Attention aux formulaires CSRF sur des pages cachées publiquement. Le token étant unique par utilisateur (en mode Stateful), cacher la page HTML mettrait en cache le token du premier utilisateur pour tous les autres -> Erreurs CSRF. Solution : ESI, AJAX, ou Stateless CSRF.
