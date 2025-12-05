# Upload de Fichiers (Formulaires)

## Concept clé
Gérer l'envoi de fichiers via le composant Form.

## Application dans Symfony 7.0
Utiliser `FileType` (`Symfony\Component\Form\Extension\Core\Type\FileType`).

```php
$builder->add('brochure', FileType::class, [
    'label' => 'Brochure (PDF file)',
    // Unmapped signifie que ce champ n'est pas lié à une propriété de l'entité
    // (car l'entité stocke le nom du fichier (string), pas l'objet UploadedFile)
    'mapped' => false,
    'required' => false,
    'constraints' => [
        new File([
            'maxSize' => '1024k',
            'mimeTypes' => [
                'application/pdf',
                'application/x-pdf',
            ],
            'mimeTypesMessage' => 'Please upload a valid PDF document',
        ])
    ],
]);
```

## Points de vigilance (Certification)
*   **Mapped: false** : C'est le pattern le plus courant. On reçoit l'objet `UploadedFile` dans le formulaire, on le traite manuellement dans le contrôleur (move, rename), et on met à jour la propriété `filename` (string) de l'entité.
*   **VichUploaderBundle** : Dans la "vraie vie", on utilise souvent ce bundle pour automatiser tout ça. Mais pour la certif, il faut savoir le faire "à la main".
*   **Enctype** : Ne pas oublier `enctype="multipart/form-data"` dans la balise form, mais `form_start(form)` l'ajoute automatiquement s'il détecte un FileType.

## Ressources
*   [Symfony Docs - File Upload](https://symfony.com/doc/current/controller/upload_file.html)

