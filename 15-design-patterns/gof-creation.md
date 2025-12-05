# Design Patterns GoF - Création

Les patterns de création abstraient le processus d'instanciation. Ils aident à rendre le système indépendant de la manière dont ses objets sont créés, composés et représentés.

## 1. Singleton
**Concept** : Garantir qu'une classe n'a qu'une seule instance et fournir un point d'accès global à celle-ci.

### Application Symfony
Dans Symfony, le **Service Container** gère les services comme des singletons par défaut. Quand vous injectez `RouterInterface` à 10 endroits, c'est la *même* instance qui est passée partout.
Cependant, implémenter le pattern Singleton "pur" (méthode `getInstance()` statique) est considéré comme un anti-pattern dans Symfony car cela rend le test difficile (couplage fort). On préfère laisser le conteneur gérer l'unicité.

## 2. Factory Method
**Concept** : Définir une interface pour créer un objet, mais laisser les sous-classes décider de la classe à instancier.

### Application Symfony
De nombreux composants utilisent ce pattern.
*   **FormFactory** : `createForm()` utilise une factory interne pour créer le formulaire.
*   **Controller** : La méthode `createForm()` dans un contrôleur est une Factory Method simplifiée.

```php
// Exemple conceptuel
class MyController extends AbstractController {
    public function new() {
        // On ne fait pas "new Form()", on passe par une factory method
        $form = $this->createForm(TaskType::class);
    }
}
```

## 3. Abstract Factory
**Concept** : Fournir une interface pour créer des familles d'objets liés ou dépendants sans spécifier leurs classes concrètes.

### Application Symfony
Moins visible directement dans l'API utilisateur, mais présent dans le cœur.
Exemple : Les **Doctrine Platforms**. Selon que vous êtes sur MySQL ou PostgreSQL, Doctrine utilise une factory abstraite pour créer les bons générateurs de SQL, les bons types de données, etc. Le code client (votre repo) n'a pas besoin de savoir si c'est MySQL ou PG.

## 4. Builder
**Concept** : Séparer la construction d'un objet complexe de sa représentation.

### Application Symfony
Le **FormBuilder** est l'exemple canonique.
Au lieu d'instancier un objet `Form` complexe avec 50 arguments, on utilise un builder fluent.

```php
// Pattern Builder
$builder
    ->add('name', TextType::class)
    ->add('price', MoneyType::class)
    ->add('save', SubmitType::class);

// À la fin, le builder construit l'objet Form complexe
$form = $builder->getForm(); 
```

Le composant **QueryBuilder** de Doctrine suit aussi ce pattern.

## 5. Prototype
**Concept** : Créer de nouveaux objets en copiant une instance existante (clonage).

### Application Symfony
Utilisé implicitement quand on travaille avec des objets qui conservent un état.
Par exemple, l'objet `Request` n'est pas cloné à chaque fois, mais certains services internes peuvent l'être.
Un exemple plus concret : **Form Type Options**. Quand on crée un formulaire, Symfony clone la configuration de base du type pour éviter de modifier la définition globale du type.
PHP fournit le mot-clé `clone` pour supporter ce pattern nativement (`__clone`).

