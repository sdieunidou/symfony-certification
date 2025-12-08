# Upload de Fichiers (Formulaires)

## Concept clé
L'upload est géré par le type `FileType`.
Le défi principal est la **gestion de l'entité** : l'entité stocke le **nom** du fichier (string) en base de données, mais le formulaire manipule un objet **UploadedFile**.

## Stratégie `MapUploadedFile` (Symfony 6.3+)
Une nouvelle approche simplifiée utilisant un attribut PHP sur la propriété du contrôleur (pas besoin de FormType complexe pour des cas simples).

```php
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

public function upload(
    #[MapUploadedFile] UploadedFile $file
): Response
{
    // Le fichier est automatiquement validé et injecté
    // ...
}
```

## Stratégie `mapped: false` (Recommandée pour les Entités)
On ne mappe pas directement le champ file à l'entité pour éviter que Symfony essaie de mettre l'objet `UploadedFile` dans la propriété `string $filename` de l'entité.

### 1. Le Formulaire
```php
$builder->add('brochure', FileType::class, [
    'label' => 'Brochure (PDF)',
    'mapped' => false, // Découplage
    'required' => false,
    'constraints' => [
        new File([
            'maxSize' => '1024k',
            'mimeTypes' => ['application/pdf'],
            'mimeTypesMessage' => 'Please upload a valid PDF',
        ])
    ],
]);
```

### 2. Le Contrôleur
```php
if ($form->isSubmitted() && $form->isValid()) {
    /** @var UploadedFile $brochureFile */
    $brochureFile = $form->get('brochure')->getData();

    if ($brochureFile) {
        $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

        try {
            $brochureFile->move(
                $this->getParameter('brochures_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            // ...
        }

        // On met à jour l'entité manuellement
        $product->setBrochureFilename($newFilename);
    }
}
```

## Alternatives
*   **Data Transformer** : Créer un Transformer qui convertit `File <-> String`. C'est plus propre mais plus complexe à mettre en place.
*   **VichUploaderBundle** : Automatise tout (Namer, Listener Doctrine, Injection du fichier). Standard de facto en entreprise.

## 🧠 Concepts Clés
1.  **UploadedFile** : C'est un objet temporaire. Il est détruit à la fin de la requête PHP s'il n'a pas été déplacé (`move`).
2.  **Sécurité** : Toujours régénérer le nom du fichier. Le nom d'origine est une donnée utilisateur non fiable.

## ⚠️ Points de vigilance (Certification)
*   **Edition** : Lors de l'édition d'un formulaire existant, le champ `FileType` sera vide (car le navigateur ne peut pas pré-remplir un input file pour des raisons de sécurité). Il faut gérer le cas où l'utilisateur ne ré-uploade rien (garder l'ancien fichier). La stratégie `mapped: false` gère cela naturellement (si champ vide -> `$brochureFile` est null -> on ne fait rien -> l'ancien filename reste en DB).

## Ressources
*   [Symfony Docs - File Upload](https://symfony.com/doc/current/controller/upload_file.html)
