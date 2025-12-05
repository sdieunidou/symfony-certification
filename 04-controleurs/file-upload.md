# Upload de Fichiers

## Concept clé
Le traitement des fichiers uploadés (`multipart/form-data`) est une tâche courante mais risquée (sécurité).
Symfony encapsule le fichier PHP natif (`$_FILES`) dans un objet `Symfony\Component\HttpFoundation\File\UploadedFile` qui offre des méthodes orientées objet sécurisées.

## Flux de Traitement Standard

1.  **Récupération** : Via `$request->files` ou un formulaire (`FileType`).
2.  **Validation** : Vérifier le type MIME, la taille, l'extension.
3.  **Nommage** : Générer un nom unique et sûr (safe filename).
4.  **Déplacement** : `move()` vers le dossier final.

## Exemple Sans Composant Form (Raw Controller)

```php
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

public function upload(Request $request, SluggerInterface $slugger): Response
{
    /** @var UploadedFile $file */
    $file = $request->files->get('document'); // 'document' est le name de l'input

    if ($file) {
        // 1. Sécurisation du nom
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // 2. Déplacement
        try {
            $file->move(
                $this->getParameter('uploads_directory'), // Configuré dans services.yaml
                $newFilename
            );
        } catch (FileException $e) {
            // Gestion erreur (disque plein, permissions...)
        }
        
        // 3. Sauvegarde du chemin en DB...
    }
}
```

## Validation (Constraints)
Si vous n'utilisez pas le composant Form, validez manuellement via le service `Validator`.

```php
use Symfony\Component\Validator\Constraints\File;

// ...
$errors = $validator->validate($file, [
    new File([
        'maxSize' => '1024k',
        'mimeTypes' => ['application/pdf', 'image/jpeg'],
        'mimeTypesMessage' => 'Please upload a valid PDF or JPEG',
    ])
]);
```

## 🧠 Concepts Clés
1.  **guessExtension()** : Ne jamais utiliser `$file->getClientOriginalExtension()` (fourni par l'utilisateur, donc falsifiable genre `virus.exe` renommé `virus.jpg`). `guessExtension()` inspecte le contenu binaire du fichier (Magic Bytes) pour déduire la vraie extension.
2.  **UploadedFile** : Hérite de `SplFileInfo` (SPL). Une fois déplacé avec `move()`, l'objet `UploadedFile` représente le fichier à son nouvel emplacement.

## ⚠️ Points de vigilance (Certification)
*   **DoS Attack** : Uploader des fichiers géants peut saturer la RAM/Disque. Configurez `upload_max_filesize` et `post_max_size` dans `php.ini`.
*   **VichUploaderBundle** : Dans le monde réel, on utilise souvent ce bundle qui automatise tout (mapping DB <-> Fichier, suppression automatique, Namer). Mais pour la certif, il faut connaître la méthode native.
*   **Public** : Les fichiers doivent être déplacés dans `public/uploads` pour être accessibles via URL, ou dans un dossier privé (`var/uploads`) si l'accès est restreint (et servi via un contrôleur `BinaryFileResponse`).

## Ressources
*   [Symfony Docs - Uploading Files](https://symfony.com/doc/current/controller/upload_file.html)
*   [File Constraint](https://symfony.com/doc/current/reference/constraints/File.html)
