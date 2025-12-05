# La Loi de Déméter (Law of Demeter - LoD)

La **Loi de Déméter** (ou Principe de Connaissance Minimale) est une règle de conception qui stipule qu'un module ne doit pas connaître les détails internes des objets qu'il manipule.

En résumé : **"Ne parlez qu'à vos amis immédiats."**

## Le Principe

Une méthode `m` d'un objet `O` ne doit invoquer que les méthodes des types suivants :
1.  L'objet `O` lui-même.
2.  Les paramètres passés à `m`.
3.  Les objets créés ou instanciés dans `m`.
4.  Les composants directs de `O` (ses propriétés).

Ce qu'il faut éviter : le **chaînage de méthodes** qui traverse plusieurs couches d'objets (ex: `$a->getB()->getC()->doSomething()`).

## Pourquoi est-ce important ?

*   **Couplage réduit** : Si la structure interne de `B` change (et ne retourne plus `C`), `A` n'est pas impacté.
*   **Meilleure maintenabilité** : Le code est moins fragile aux changements de structure.
*   **Testabilité accrue** : Il est plus facile de mocker les dépendances directes.

---

## Exemples en PHP / Symfony

### ❌ Mauvais (Violation de la Loi)

Imaginez un contrôleur qui veut récupérer le code postal d'un utilisateur connecté pour une livraison.

```php
class DeliveryController extends AbstractController
{
    public function estimate(User $user)
    {
        // VIOLATION : Le contrôleur "sait" que User a une Address, 
        // et que Address a un ZipCode.
        // Si demain Address devient une collection ou change de nom, ce code casse.
        $zipCode = $user->getAddress()->getZipCode();

        // ... calcul ...
    }
}
```

### ✅ Bon (Respect de la Loi)

On délègue la responsabilité à l'objet le plus proche (l'ami immédiat).

```php
// 1. On ajoute une méthode helper dans l'entité User
class User
{
    private Address $address;

    public function getDeliveryZipCode(): ?string
    {
        return $this->address->getZipCode();
    }
}

// 2. Le contrôleur appelle uniquement son ami direct (User)
class DeliveryController extends AbstractController
{
    public function estimate(User $user)
    {
        // RESPECT : On ne traverse plus les objets.
        $zipCode = $user->getDeliveryZipCode();
        
        // ... calcul ...
    }
}
```

### Exemple Symfony : Les Services

Une erreur fréquente est d'injecter le `Container` ou un service "Parent" pour accéder à un service "Enfant".

**❌ Mauvais**
```php
class OrderService
{
    public function __construct(private ContainerInterface $container) {}

    public function process()
    {
        // Violation : On parle à l'ami (Container) de l'ami (Mailer)
        // + Service Locator anti-pattern
        $this->container->get('mailer')->send(...);
    }
}
```

**✅ Bon**
```php
class OrderService
{
    // On injecte directement la dépendance nécessaire
    public function __construct(private MailerInterface $mailer) {}

    public function process()
    {
        $this->mailer->send(...);
    }
}
```

### Exemple Twig

La loi de Déméter s'applique aussi dans les templates !

**❌ Mauvais**
```twig
{# Le template connaît trop la structure interne #}
{{ app.user.subscription.plan.name }}
```

**✅ Bon**
```twig
{# L'entité User expose ce dont on a besoin #}
{{ app.user.subscriptionPlanName }}
```
