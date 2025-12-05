# Extensions PHP

## Concept clé
PHP est modulaire. Le noyau ("Core") est léger, et la plupart des fonctionnalités proviennent d'extensions. Certaines sont compilées par défaut (Standard, Date, Reflection), d'autres doivent être activées (`php.ini`) ou installées.

## Application dans Symfony 7.0
Symfony a des pré-requis stricts concernant les extensions. Composer vérifie souvent leur présence (`ext-xml`, `ext-mbstring`, etc.).
Extensions critiques pour Symfony :
*   **Intl** : Pour l'internationalisation, la validation, et les opérations sur les chaînes Unicode.
*   **Mbstring** : Manipulation de chaînes multi-octets.
*   **PDO** : Accès base de données.
*   **Xml/Dom** : Parsing de configuration XML.

## Points de vigilance (Certification)
Il n'est pas nécessaire de connaître le fonctionnement interne des extensions C, mais il faut savoir à quoi elles servent :
*   **OPcache** : Cache de bytecode (indispensable pour la performance en prod). Savoir ce qu'est le "Preloading" (PHP 7.4+).
*   **APCu** : Cache de données en mémoire partagée (Userland cache).
*   **Xdebug** : Outil de débogage (ne jamais activer en prod !).
*   **PDO** : Interface d'abstraction d'accès aux données.
*   **Sodium** : Cryptographie moderne (utilisé pour signer/chiffrer).

L'examen peut poser des questions du type : "Quelle extension est requise pour utiliser `Collator` ?" (Réponse : `intl`).

## Ressources
*   [Manuel PHP - Liste des extensions](https://www.php.net/manual/fr/extensions.alphabetical.php)

