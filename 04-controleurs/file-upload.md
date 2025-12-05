# Upload de Fichiers

## Concept clé
Le traitement des fichiers uploadés (`multipart/form-data`) est une tâche courante mais risquée (sécurité).
Symfony encapsule le fichier PHP natif (`$_FILES`) dans un objet `Symfony\Component\HttpFoundation\File\UploadedFile` qui offre des méthodes orientées objet sécurisées.

## Méthode Moderne : Attribut `#[MapUploadedFile]` (Symfony 7.1+)
C'est la façon recommandée depuis Symfony 7.1. Elle permet d'injecter et de valider le fichier directement dans l'argument du contrôleur, sans passer par `$request->files`.

```php
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

public function upload(
    #[MapUploadedFile([
        new Assert\File(
            maxSize: '2M',
            mimeTypes: ['application/pdf', 'image/jpeg']
        )
    ])] UploadedFile $file
): Response
{
    // Si on arrive ici, le fichier est valide !
    
    // 1. Générer un nom sûr
    $newFilename = uniqid().'.'.$file->guessExtension();

    // 2. Déplacer
    $file->move(
        $this->getParameter('uploads_directory'), 
        $newFilename
    );

    return $this->json(['file' => $newFilename]);
}
```

## Méthode Manuelle (Raw Controller)
Si vous n'utilisez pas l'attribut (versions antérieures ou besoin spécifique), voici la méthode manuelle.

```php
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

public function manualUpload(Request $request, SluggerInterface $slugger): Response
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
                $this->getParameter('uploads_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            // Gestion erreur
        }
    }
}
```

## Validation
*   **Avec Attribut** : Les contraintes sont passées directement dans `#[MapUploadedFile]`.
*   **Sans Attribut** : Utilisez le service `ValidatorInterface` manuellement.

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
