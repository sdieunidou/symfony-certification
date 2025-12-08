# Composant OptionsResolver

## Concept clé
Le composant **OptionsResolver** est un remplaçant amélioré de la fonction PHP `array_replace`. Il permet de créer un système d'options robuste avec des valeurs par défaut, des options requises, de la validation de type, de la validation de valeur et de la normalisation.

Il est massivement utilisé dans Symfony, notamment dans les **Form Types** (`configureOptions`), mais il est aussi très utile pour vos propres services ou classes configurables.

## Installation
```bash
composer require symfony/options-resolver
```

## Utilisation de base

Imaginez une classe `Mailer` qui prend un tableau d'options dans son constructeur.

```php
use Symfony\Component\OptionsResolver\OptionsResolver;

class Mailer
{
    private array $options;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'host' => 'smtp.example.org',
            'port' => 25,
            'encryption' => null,
        ]);
    }
}
```

Si vous instanciez `new Mailer(['port' => 587])`, `$this->options` contiendra toutes les clés par défaut, avec le port surchargé. Si vous passez une option inconnue (`'foo' => 'bar'`), une `UndefinedOptionsException` sera levée.

## Fonctionnalités Principales

### 1. Options Requises (`setRequired`)
Si une option n'a pas de valeur par défaut et doit absolument être fournie par l'utilisateur.

```php
$resolver->setRequired('username');
$resolver->setRequired(['password', 'api_key']);
```
Si l'option manque lors du `resolve()`, une `MissingOptionsException` est levée.

### 2. Validation de Type (`setAllowedTypes`)
Pour s'assurer qu'une option reçoit le bon type de donnée.

```php
$resolver->setAllowedTypes('port', 'int');
$resolver->setAllowedTypes('host', 'string');
$resolver->setAllowedTypes('transport', ['null', 'string']); // Union type
$resolver->setAllowedTypes('dates', 'DateTime[]'); // Tableau d'objets
```
Si le type ne correspond pas, une `InvalidOptionsException` est levée.

### 3. Validation de Valeur (`setAllowedValues`)
Pour restreindre une option à une liste de valeurs acceptables (Enum).

```php
$resolver->setAllowedValues('encryption', [null, 'ssl', 'tls']);

// Ou via un callback pour une validation complexe
$resolver->setAllowedValues('age', function ($value) {
    return $value >= 18;
});
```

### 4. Normalisation (`setNormalizer`)
Permet de transformer la valeur de l'option après qu'elle a été résolue.

```php
$resolver->setNormalizer('host', function (Options $options, string $value) {
    if (!str_starts_with($value, 'http://')) {
        return 'http://'.$value;
    }
    return $value;
});
```

### 5. Dépendance entre options
On peut définir une valeur par défaut qui dépend d'une autre option via une Closure.

```php
$resolver->setDefault('port', function (Options $options) {
    if ('ssl' === $options['encryption']) {
        return 465;
    }
    return 25;
});
```

## API Fluide (`define`)
Depuis Symfony 5.1, une syntaxe fluide est disponible pour rendre le code plus lisible.

```php
$resolver->define('port')
    ->required()
    ->default(25)
    ->allowedTypes('int')
    ->info('Le port SMTP');
```

## Options Imbriquées (Nested)
Vous pouvez valider des tableaux d'options imbriqués (comme dans `framework.yaml`).

```php
$resolver->setDefault('database', function (OptionsResolver $dbResolver) {
    $dbResolver
        ->setDefaults(['driver' => 'mysql'])
        ->setRequired(['host', 'password']);
});
```

## 🧠 Concepts Clés
1.  **Sécurité** : `resolve()` lance toujours une exception si une option inconnue est passée. Cela évite les typos silencieuses (`usernme` au lieu de `username`).
2.  **Centralisation** : Toute la logique de configuration est regroupée dans `configureOptions`.
3.  **Performance** : Si vous utilisez OptionsResolver dans une boucle (ex: traitement de 1000 lignes CSV), essayez de réutiliser l'instance du Resolver ou de cloner l'objet pour ne pas reconstruire les règles à chaque itération.

## ⚠️ Points de vigilance (Certification)
*   **Ordre** : La normalisation se produit *après* la validation.
*   **Prototype** : On peut utiliser `setPrototype(true)` dans une option imbriquée pour valider un tableau d'éléments identiques (ex: liste de connexions).
*   **Form Types** : Dans les formulaires Symfony, la méthode `configureOptions` utilise exactement ce composant. Toutes les méthodes (`setDefaults`, `setAllowedTypes`) y sont disponibles.

## Ressources
*   [Symfony Docs - OptionsResolver](https://symfony.com/doc/current/components/options_resolver.html)
