# Transformateurs de Données (Data Transformers)

Les **Data Transformers** sont une fonctionnalité puissante et essentielle du composant Form de Symfony. Ils permettent de convertir les données d'un format à un autre, faisant le pont entre la représentation de vos données dans votre application (votre modèle domaine) et leur représentation dans le formulaire HTML (la vue).

## 1. Les Trois Types de Données

Pour maîtriser les Data Transformers, il est crucial de comprendre les trois formats de données manipulés par un formulaire Symfony :

1.  **Model Data (Données du Modèle)** :
    *   C'est le format utilisé dans votre application (ex: une entité `Product`, un objet `DateTimeImmutable`, un tableau d'objets `Tag`).
    *   C'est ce que `getData()` renvoie et ce que `setData()` attend sur l'objet formulaire racine.

2.  **Norm Data (Données Normalisées)** :
    *   Une version "standardisée" de vos données (ex: l'ID du produit `123`, une chaîne `2023-10-27`).
    *   C'est un format intermédiaire pivot. Si vous n'avez pas de View Transformer, le *Norm Data* est souvent identique au *View Data*.

3.  **View Data (Données de la Vue)** :
    *   Le format affiché et soumis dans le formulaire HTML (ex: la chaîne `"123"` dans un `<input type="text">`, ou `"27/10/2023"`).
    *   C'est presque toujours une `string` ou un `array` de chaînes.

Un **Data Transformer** s'intercale entre ces couches pour convertir les données dans les deux sens.

---

## 2. Architecture et Flux de Transformation

Il existe deux types de transformateurs selon l'endroit où ils agissent dans la chaîne :

### A. Model Transformers (Model ↔ Norm)
Ils convertissent les données entre le **Model Data** et le **Norm Data**.
*   **Usage typique :** Transformer une Entité en son ID (et inversement).
*   **Exemple :** Un champ texte attend un code SKU (string), mais votre entité attend un objet `Product` lié à ce SKU.

### B. View Transformers (Norm ↔ View)
Ils convertissent les données entre le **Norm Data** et le **View Data**.
*   **Usage typique :** Formatage purement visuel (affichage d'une date, format monétaire avec virgule).
*   **Exemple :** Le système stocke un prix en centimes (integer `1000`), mais l'utilisateur saisit "10,00 €".

### Le Flux d'Exécution

1.  **Affichage du formulaire (`setData` ou initialisation) :**
    *   `Model Data` → **Model Transformer::transform()** → `Norm Data`
    *   `Norm Data` → **View Transformer::transform()** → `View Data` (affiché dans la value HTML)

2.  **Soumission du formulaire (`handleRequest` / `submit`) :**
    *   `View Data` (input utilisateur) → **View Transformer::reverseTransform()** → `Norm Data`
    *   `Norm Data` → **Model Transformer::reverseTransform()** → `Model Data` (injecté dans l'objet)

---

## 3. Implémenter un Data Transformer Personnalisé

Dans Symfony 7 avec PHP 8.2, on crée une classe qui implémente `Symfony\Component\Form\DataTransformerInterface`.

### Exemple Complet : Sélecteur d'Entité via un Champ Texte (IssueToNumberTransformer)

Imaginons que vous vouliez sélectionner une "Issue" (Ticket) via son numéro, au lieu d'une liste déroulante géante. Nous utilisons ici l'injection de dépendance via le constructeur pour accéder aux données.

```php
namespace App\Form\DataTransformer;

use App\Entity\Issue;
use App\Repository\IssueRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforme un objet Issue en string (numéro) et inversement.
 * 
 * @implements DataTransformerInterface<Issue, string>
 */
readonly class IssueToNumberTransformer implements DataTransformerInterface
{
    public function __construct(
        private IssueRepository $issueRepository
    ) {}

    /**
     * Transforme l'objet (Issue) en chaîne (numéro) pour l'affichage.
     * Model -> View (via Norm)
     * 
     * @param Issue|null $value
     */
    public function transform(mixed $value): string
    {
        if (null === $value) {
            return '';
        }

        if (!$value instanceof Issue) {
            throw new TransformationFailedException('Expected an instance of App\Entity\Issue.');
        }

        return (string) $value->getNumber();
    }

    /**
     * Transforme la chaîne (numéro) en objet (Issue) lors de la soumission.
     * View -> Model
     * 
     * @param string|null $value
     */
    public function reverseTransform(mixed $value): ?Issue
    {
        // La valeur est nulle si le champ est laissé vide
        if (!$value) {
            return null;
        }

        $issue = $this->issueRepository->findOneBy(['number' => $value]);

        if (null === $issue) {
            // Cette exception génère une erreur de validation sur le champ
            // Le message sera affiché à l'utilisateur si 'invalid_message' n'est pas surchargé
            throw new TransformationFailedException(sprintf(
                'Le ticket avec le numéro "%s" n\'existe pas !',
                $value
            ));
        }

        return $issue;
    }
}
```

### Utilisation dans le FormType

L'injection de dépendance se fait via le constructeur du FormType. Grâce à l'autowiring de Symfony, le Transformer sera automatiquement injecté.

```php
namespace App\Form;

use App\Form\DataTransformer\IssueToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    public function __construct(
        private readonly IssueToNumberTransformer $transformer
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('issue', TextType::class, [
                // Message personnalisé si TransformationFailedException est levée
                'invalid_message' => "Ce numéro de ticket n'est pas valide.", 
            ]);

        // On attache le transformer au champ 'issue'
        $builder->get('issue')
            ->addModelTransformer($this->transformer);
    }
}
```

---

## 4. Utilisation Rapide : CallbackTransformer

Pour des transformations simples qui ne nécessitent pas de dépendances externes (comme l'EntityManager), le `CallbackTransformer` est idéal car il évite de créer une classe dédiée.

```php
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;

// ...

$builder->add('tags', TextType::class, [
    'help' => 'Séparez les tags par des virgules',
]);

$builder->get('tags')
    ->addModelTransformer(new CallbackTransformer(
        // Transform : Array tags -> String "tag1, tag2" pour l'affichage
        function (mixed $tagsAsArray): string {
            if (!is_array($tagsAsArray)) {
                return '';
            }
            // Vérification de type PHP 8.2 pour la sécurité
            return implode(', ', $tagsAsArray);
        },
        
        // Reverse Transform : String "tag1, tag2" -> Array tags pour le modèle
        function (mixed $tagsAsString): array {
            if (null === $tagsAsString || '' === $tagsAsString) {
                return [];
            }
            
            // Nettoyage et conversion
            return array_values(array_filter(array_map(
                fn($tag) => trim($tag), 
                explode(',', $tagsAsString)
            )));
        }
    ));
```

---

## 5. Transformateurs Natifs Symfony

Symfony fournit déjà de nombreux transformateurs utiles dans `Symfony\Component\Form\Extension\Core\DataTransformer`. Ne réinventez pas la roue :

*   **`DateTimeToStringTransformer`** / **`DateTimeToTimestampTransformer`** : Gestion fine des dates.
*   **`IntegerToLocalizedStringTransformer`** : Gestion des nombres avec locales (virgules, espaces).
*   **`ChoicesToValuesTransformer`** : Utilisé en interne par `ChoiceType`.
*   **`MoneyToLocalizedStringTransformer`** : Pour le `MoneyType`.

---

## 6. Chaînage de Transformateurs

Vous pouvez ajouter plusieurs transformateurs sur un même champ. L'ordre d'ajout est crucial.

### Ordre d'exécution

1.  **addModelTransformer** :
    *   `transform` : Exécuté dans l'ordre d'ajout (1er ajouté, 1er exécuté).
    *   `reverseTransform` : Exécuté dans l'ordre inverse (LIFO - Last In, First Out).

2.  **addViewTransformer** :
    *   Même logique.

**Exemple :**
Vous voulez un champ qui prend un prix en centimes (Model), le convertit en float (Transformer 1), puis formatte ce float avec une virgule (Transformer 2).

```php
$builder->get('price')
    ->addModelTransformer($centsToFloatTransformer) // Transforme 1000 -> 10.00
    ->addViewTransformer($floatToMoneyStringTransformer); // Transforme 10.00 -> "10,00"
```

---

## 7. Tests Unitaires des Transformateurs

Pour un projet expert, tester la logique de transformation est obligatoire. Comme les DataTransformers sont des classes pures (ou avec peu de dépendances), ils sont faciles à tester unitairement avec PHPUnit.

```php
namespace App\Tests\Form\DataTransformer;

use App\Entity\Issue;
use App\Form\DataTransformer\IssueToNumberTransformer;
use App\Repository\IssueRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IssueToNumberTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        $issue = new Issue();
        $issue->setNumber('ISSUE-123');

        // Mock du repository (non utilisé dans transform mais requis par le constructeur)
        $repo = $this->createMock(IssueRepository::class);
        $transformer = new IssueToNumberTransformer($repo);

        $this->assertSame('ISSUE-123', $transformer->transform($issue));
    }

    public function testReverseTransform(): void
    {
        $issue = new Issue();
        $repo = $this->createMock(IssueRepository::class);
        $repo->expects($this->once())
            ->method('findOneBy')
            ->with(['number' => 'ISSUE-123'])
            ->willReturn($issue);

        $transformer = new IssueToNumberTransformer($repo);

        $this->assertSame($issue, $transformer->reverseTransform('ISSUE-123'));
    }

    public function testReverseTransformThrowsExceptionIfNotFound(): void
    {
        $repo = $this->createMock(IssueRepository::class);
        $repo->method('findOneBy')->willReturn(null);

        $transformer = new IssueToNumberTransformer($repo);

        $this->expectException(TransformationFailedException::class);
        $transformer->reverseTransform('UNKNOWN');
    }
}
```

---

## 🧠 Concepts Clés

1.  **Rôle Pivot** : Les Transformers sont le mécanisme standard pour modifier la représentation d'une donnée entre l'objet PHP et le champ HTML.
2.  **Direction** : `transform()` va vers l'affichage (Export), `reverseTransform()` va vers le modèle (Import).
3.  **Model vs View Transformers** :
    *   Utilisez `addModelTransformer` pour la logique métier (Entité ↔ ID).
    *   Utilisez `addViewTransformer` pour le formatage visuel (Date ↔ String FR).
4.  **Validation implicite** : Une `TransformationFailedException` dans `reverseTransform` bloque la soumission et invalide le champ automatiquement. Le message d'erreur peut être customisé via l'option `invalid_message`.
5.  **Atomicité** : Un Transformer doit faire une seule chose bien. Combinez-les plutôt que d'en créer un "monstrueux".

## ⚠️ Points de vigilance

1.  **Gestion du NULL** : Dans `transform()` et `reverseTransform()`, la valeur d'entrée peut être `null`. Gérez ce cas explicitement (souvent en retournant `null` ou une chaîne vide) pour éviter des `TypeError` en PHP 8.
2.  **Types de retour stricts** : Soyez rigoureux sur les types de retour PHP 8.2. Si votre modèle attend un `array` et que le transformer renvoie `null`, cela provoquera une erreur critique si la propriété de l'entité n'est pas nullable.
3.  **Pas de validation métier** : Ne faites pas de validation complexe (ex: "l'utilisateur a-t-il le droit d'utiliser ce tag ?") dans un Transformer. Le Transformer doit juste s'assurer que la donnée est *convertible*. La validation métier se fait via les `Constraints` ou les `Events`.
4.  **Performance** : Si vous transformez une liste d'IDs en entités, attention au problème "N+1 queries". Préférez une requête personnalisée dans votre Repository (ex: `findByIds`) injecté dans le Transformer plutôt que de faire une boucle de `findOneBy`.
5.  **Compound Forms** : Les DataTransformers ne s'appliquent généralement pas de la même manière sur les formulaires composés (`compound => true`). Ils agissent sur les données globales du formulaire parent, pas sur les enfants individuellement.

## Ressources
*   [Documentation Officielle Symfony - Data Transformers](https://symfony.com/doc/current/form/data_transformers.html)
*   [API CallbackTransformer](https://symfony.com/doc/current/form/data_transformers.html#using-the-callbacktransformer)
