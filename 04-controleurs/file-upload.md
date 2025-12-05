# Upload de Fichiers

## Concept clé
Gérer les fichiers envoyés par les formulaires (`multipart/form-data`).

## Application dans Symfony 7.0
Les fichiers sont accessibles via `$request->files` ou injectés dans les formulaires. Ils sont représentés par l'objet `UploadedFile`.

## Exemple de code

```php
<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

public function upload(Request $request): Response
{
    /** @var UploadedFile $file */
    $file = $request->files->get('image');
    
    if ($file) {
        // Nom d'origine (non sécurisé !)
        $originalName = $file->getClientOriginalName();
        
        // Déplacer le fichier (move)
        $newFilename = uniqid().'.'.$file->guessExtension();
        
        $file->move(
            $this->getParameter('upload_directory'), // config/services.yaml
            $newFilename
        );
    }
    
    // ...
}
```

## Points de vigilance (Certification)
*   **Sécurité** : Ne jamais faire confiance à `getClientOriginalName()` ou `getClientMimeType()` pour la sécurité (ce sont des données envoyées par le client, donc falsifiables). Utiliser `$file->getMimeType()` (qui lit le fichier) et générer un nom de fichier sécurisé côté serveur.
*   **Formulaires** : Il est recommandé d'utiliser le composant Form et le type `FileType` qui gère la transformation en `UploadedFile` automatiquement.
*   **Max Size** : Vérifier les limites `upload_max_filesize` et `post_max_size` du php.ini.

## Ressources
*   [Symfony Docs - File Uploads](https://symfony.com/doc/current/controller/upload_file.html)

