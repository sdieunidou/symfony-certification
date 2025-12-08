# Twig : Fonctionnement Interne

## Concept clé
Twig n'est pas un simple interpréteur de texte. C'est un **compilateur**.
Il transforme vos templates (`.html.twig`) en classes PHP natives optimisées. Une fois compilé, le coût de performance est quasiment nul par rapport à du PHP pur.

## Le Pipeline de Compilation

Le processus de transformation d'un template se fait en 4 étapes majeures :

### 1. Loader (Chargement)
Le **Loader** (`LoaderInterface`) est responsable de trouver le code source du template.
*   **Entrée** : Le nom du template (ex: `base.html.twig`).
*   **Sortie** : Le code source (string).
*   *Classe clé* : `FilesystemLoader` (le plus courant dans Symfony).

### 2. Lexer (Tokenisation)
Le **Lexer** découpe le code source en petits morceaux appelés **Tokens**.
*   Il sépare le texte brut, les balises `{{ ... }}`, `{% ... %}`, les opérateurs, etc.
*   **Sortie** : Un flux de tokens (`TokenStream`).

### 3. Parser (Analyse Syntaxique)
Le **Parser** analyse le flux de tokens pour comprendre la structure et la logique.
*   Il construit un **AST** (Abstract Syntax Tree) composé de **Noeuds** (`Node`).
*   C'est ici que sont vérifiées les règles de syntaxe (ex: balise fermante manquante).
*   **Sortie** : Un objet `Node` (l'arbre).

### 4. Compiler (Compilation PHP)
Le **Compiler** parcourt l'arbre de nœuds (AST) et génère du code PHP valide.
*   **Sortie** : Une classe PHP (héritant de `use Twig\Template`) stockée dans le cache (`var/cache/prod/twig/...`).

## Architecture et Classes Clés

### 1. Environment (`Twig\Environment`)
C'est le "Dieu" de Twig. C'est la classe principale qui orchestre tout.
*   Il contient la configuration (debug, cache, charset).
*   Il détient le Loader et les Extensions.
*   C'est lui qu'on appelle via `$twig->render('index.html.twig')`.

### 2. Extensions (`ExtensionInterface`)
Tout ce qui n'est pas de la syntaxe de base est une extension.
*   **CoreExtension** : Contient `if`, `for`, `include`, etc.
*   Vos filtres et fonctions personnalisés sont ajoutés via des extensions.

### 3. Template (`Twig\Template`)
La classe de base dont héritent tous vos templates compilés. Elle contient la méthode `doDisplay()` qui contient le code PHP généré pour afficher le HTML.

## Cache et Performance

*   **En Prod** : Twig compile le template **une seule fois**. Ensuite, il utilise directement la classe PHP mise en cache. Il ne vérifie jamais si le fichier source a changé (pour la perf). Il faut vider le cache (`cache:clear`) au déploiement.
*   **En Dev** : Twig vérifie si le fichier source a été modifié (filemtime) et recompile si nécessaire (`auto_reload: true`).

## 🧠 Concepts Clés
1.  **AST (Abstract Syntax Tree)** : C'est la représentation intermédiaire du template. Les "Node Visitors" peuvent modifier cet arbre avant la compilation (optimisations, sécurité).
2.  **Sandbox** : Un mode sécurisé qui permet d'exécuter du code Twig non fiable (écrit par des utilisateurs) en limitant les fonctions/tags accessibles.

## ⚠️ Points de vigilance (Certification)
*   **Priorité** : Si vous utilisez un `ChainLoader` (plusieurs loaders), le premier qui trouve le template gagne.
*   **Logique** : Ne jamais mettre de logique métier lourde dans Twig. Si vous devez faire des requêtes SQL ou des calculs complexes, faites-le dans le contrôleur ou une Extension Twig (fonctions), pas dans le template.

## Ressources
*   [Twig Internals](https://twig.symfony.com/doc/3.x/internals.html)
*   [Creating an Extension](https://symfony.com/doc/current/templates.html#creating-twig-extensions)
