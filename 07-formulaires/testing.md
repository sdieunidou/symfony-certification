# Tests de Formulaires
     
## Concept clé
Les formulaires contiennent souvent beaucoup de logique (DataTransformers, Listeners, Options). Il est crucial de les tester unitairement pour éviter les régressions.
Symfony fournit une classe de base dédiée : `Symfony\Component\Form\Test\TypeTestCase`.

## Configuration du Test

```php
namespace App\Tests\Form;

use App\Form\TaskType;
use App\Entity\Task;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        // 1. Les données simulées (Format Vue/HTTP)
        $formData = [
            'title' => 'Test Task',
            'priority' => 'High',
        ];

        // 2. L'objet attendu (Format Modèle)
        $model = new Task();
        // $model->setTitle('Test Task'); ... (Si le formulaire mappe sur un objet existant)
        // Ou on laisse le formulaire hydrater un nouvel objet
        
        // 3. Création du formulaire
        $form = $this->factory->create(TaskType::class, $model);

        // 4. Soumission (Simule handleRequest/submit)
        $form->submit($formData);

        // 5. Assertions
        $this->assertTrue($form->isSynchronized()); // La transformation n'a pas échoué
        $this->assertTrue($form->isValid()); // La validation (si activée dans le test) est OK
        
        // Vérifier que l'objet modèle a été bien hydraté
        $this->assertSame('Test Task', $model->getTitle());
        $this->assertSame('High', $model->getPriority()->value);

        // 6. Vérifier la structure de la vue (Optionnel mais utile pour les options)
        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
```

## Tester avec des Dépendances (Extensions)
Si votre `FormType` utilise des services (via `__construct`) ou des types personnalisés, le `TypeTestCase` de base échouera car il utilise un `FormFactory` isolé.
Il faut pré-charger vos extensions via `getExtensions()`.

```php
use Symfony\Component\Form\PreloadedExtension;

protected function getExtensions(): array
{
    // Mocker les dépendances
    $transformer = $this->createMock(MyDataTransformer::class);
    
    // Instancier le Type avec ses dépendances
    $type = new TaskType($transformer);

    return [
        // Enregistrer le type dans une PreloadedExtension
        new PreloadedExtension([$type], []),
    ];
}
```

## Tester les DataTransformers
Il est souvent plus simple de tester les DataTransformers isolément (comme une classe PHP normale) plutôt que via le formulaire complet. Voir `data-transformers.md`.

## 🧠 Concepts Clés
1.  **Isolation** : `TypeTestCase` n'utilise pas le Kernel complet. C'est très rapide.
2.  **Validator** : Par défaut, `TypeTestCase` **NE valide PAS** les contraintes (`Assert\...`). Il vérifie juste que la soumission et la transformation des données fonctionnent (`isSynchronized`). Pour tester la validation, il faut soit intégrer le `ValidatorExtension` (complexe), soit faire des tests d'intégration (`KernelTestCase` / `WebTestCase`).

## ⚠️ Points de vigilance (Certification)
*   **isSynchronized()** : Vérifie que les DataTransformers n'ont pas levé d'exception (`TransformationFailedException`). C'est la première chose à vérifier.
*   **Types natifs** : `TypeTestCase` charge déjà les types natifs (`TextType`, etc.). Pas besoin de les ajouter.

## Ressources
*   [Symfony Docs - Unit Testing Forms](https://symfony.com/doc/current/form/unit_testing.html)
