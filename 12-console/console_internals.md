# Console : Fonctionnement Interne

## Concept clé
Le composant Console fournit une structure pour créer des interfaces en ligne de commande (CLI). Il gère le parsing des arguments/options et l'exécution des commandes.

## Architecture et Classes Clés

### 1. Application (`Symfony\Component\Console\Application`)
C'est le point d'entrée (le script `bin/console`).
*   Elle enregistre toutes les commandes disponibles.
*   Elle détermine quelle commande exécuter en fonction du premier argument (`argv[1]`).
*   Elle gère les options globales (`--help`, `--env`, `--verbose`).

### 2. Command (`Symfony\Component\Console\Command\Command`)
La classe de base de vos commandes.
*   **configure()** : Définit le nom, la description, les arguments et options.
*   **execute()** : Contient la logique métier.
*   **interact()** : (Optionnel) Permet de poser des questions à l'utilisateur avant l'exécution (si arguments manquants).

### 3. Input (`InputInterface`)
Représente l'entrée utilisateur (ce qui a été tapé).
*   **Arguments** : Valeurs positionnelles (`cp source dest`).
*   **Options** : Valeurs nommées avec tirets (`--force`, `-v`).

### 4. Output (`OutputInterface`)
Représente la sortie standard.
*   Permet d'écrire des messages (`writeln()`).
*   Gère la verbosité (`isVerbose()`).
*   Gère la coloration (ANSI).

### 5. Style (SymfonyStyle)
Une surcouche sur Input/Output (`$io = new SymfonyStyle($input, $output)`).
*   Fournit des helpers de haut niveau : `title()`, `section()`, `success()`, `table()`, `ask()`.

## Le Cycle de Vie d'une Commande

1.  **Boot** : `bin/console` démarre le Kernel Symfony et crée l'Application.
2.  **Find** : L'Application cherche la commande correspondante (ex: `app:create-user`).
3.  **Run** :
    *   **Interact** : Si implémenté, pose des questions interactives.
    *   **Initialize** : (Optionnel) Initialisation avant exécution.
    *   **Execute** : Le code principal est lancé.
4.  **Exit** : La méthode `execute()` retourne un entier (Code de retour/Exit code).
    *   `Command::SUCCESS` (0)
    *   `Command::FAILURE` (1)
    *   `Command::INVALID` (2)

## 🧠 Concepts Clés
1.  **Isolation** : Chaque exécution de commande est un processus PHP distinct. La mémoire est libérée à la fin.
2.  **Process** : Le composant Console est fait pour *être appelé*. Pour *appeler* une commande système depuis PHP, on utilise le composant **Process**.

## ⚠️ Points de vigilance (Certification)
*   **Code de retour** : Il est impératif de retourner `int` dans `execute()`. Retourner `void` (rien) est déprécié et provoquera des erreurs.
*   **Services** : Les commandes sont des services. Le constructeur est le bon endroit pour injecter des dépendances. Attention au **Lazy Loading** : configurez les propriétés (nom, description) dans un attribut PHP ou `configure()` plutôt que dans le constructeur pour éviter d'instancier la commande inutilement lors du listage (`bin/console list`).

## Ressources
*   [Symfony Docs - Console](https://symfony.com/doc/current/components/console.html)
*   [Symfony Style](https://symfony.com/doc/current/console/style.html)
