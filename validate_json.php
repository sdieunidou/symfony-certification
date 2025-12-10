#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Script de validation JSON récursif.
 *
 * Utilisation :
 *   php validate_json.php /chemin/vers/repertoire
 *
 * Code de retour :
 *   0 = tous les fichiers .json sont valides
 *   1 = au moins un fichier .json est invalide ou erreur de lecture
 */

// Active le rapport d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 'stderr');

// Récupération de l'argument (répertoire à scanner)
$directory = $argv[1] ?? getcwd();

if (!is_dir($directory)) {
    fwrite(STDERR, "Erreur : '$directory' n'est pas un répertoire valide." . PHP_EOL);
    exit(1);
}

$directory = realpath($directory);

echo "Analyse du répertoire : {$directory}" . PHP_EOL . PHP_EOL;

$invalidFiles = [];
$totalJsonFiles = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(
        $directory,
        FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS
    )
);

/** @var SplFileInfo $fileInfo */
foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile()) {
        continue;
    }

    $extension = strtolower($fileInfo->getExtension());
    if ($extension !== 'json') {
        continue;
    }

    $totalJsonFiles++;
    $filePath = $fileInfo->getPathname();

    $content = @file_get_contents($filePath);

    if ($content === false) {
        $invalidFiles[] = [
            'file' => $filePath,
            'error' => 'Impossible de lire le fichier (permissions ou autre erreur IO).',
        ];
        continue;
    }

    // Validation JSON avec exceptions (PHP 7.3+)
    try {
        json_decode($content, flags: JSON_THROW_ON_ERROR);
        // Si aucune exception, le JSON est valide
        echo "[OK]   $filePath" . PHP_EOL;
    } catch (JsonException $e) {
        echo "[FAIL] $filePath" . PHP_EOL;

        $invalidFiles[] = [
            'file' => $filePath,
            'error' => $e->getMessage(),
        ];
    }
}

echo PHP_EOL . "-----------------------------" . PHP_EOL;
echo "Résumé :" . PHP_EOL;
echo "  Fichiers .json trouvés : $totalJsonFiles" . PHP_EOL;
echo "  Fichiers invalides     : " . count($invalidFiles) . PHP_EOL;

if (!empty($invalidFiles)) {
    echo PHP_EOL . "Détail des fichiers invalides :" . PHP_EOL;
    foreach ($invalidFiles as $item) {
        echo " - " . $item['file'] . PHP_EOL;
        echo "     Erreur : " . $item['error'] . PHP_EOL;
    }
    exit(1);
}

echo PHP_EOL . "Tous les fichiers .json sont valides ✅" . PHP_EOL;
exit(0);
