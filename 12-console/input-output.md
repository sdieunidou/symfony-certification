# Options et Arguments (Input)

## Concept clé
Pour rendre une commande flexible, on lui passe des paramètres.
Symfony distingue deux types d'entrées :
1.  **Arguments** : Positionnels, obligatoires (souvent).
2.  **Options** : Nommés (drapeaux), optionnels, désordonnés.

## Arguments (`addArgument`)
Définis par leur ordre.
*   `InputArgument::REQUIRED` : La commande échoue s'il manque.
*   `InputArgument::OPTIONAL` : Peut être omis (valeur par défaut null).
*   `InputArgument::IS_ARRAY` : Accepte plusieurs valeurs (`cmd item1 item2 item3`). Doit être le **dernier** argument.

```php
$this->addArgument('name', InputArgument::REQUIRED, 'Description');
// Usage: php bin/console app:cmd Toto
```

## Options (`addOption`)
Définies par un nom (`--option`) ou un raccourci (`-o`).
*   `InputOption::VALUE_NONE` : Booléen/Drapeau (présent ou pas). Ex: `--yell`.
*   `InputOption::VALUE_REQUIRED` : Attend une valeur. Ex: `--iterations=10`.
*   `InputOption::VALUE_OPTIONAL` : Valeur optionnelle. Ex: `--yell` ou `--yell=loud`.
*   `InputOption::VALUE_NEGATABLE` : Accepte `--no-foo`.

```php
$this->addOption('iterations', 'i', InputOption::VALUE_REQUIRED, 'Combien ?', 1);
// Usage: php bin/console app:cmd --iterations=5
// Usage: php bin/console app:cmd -i 5
```

## Lecture (`InputInterface`)
Dans `execute()` :

```php
$name = $input->getArgument('name');
$iter = $input->getOption('iterations');
```

### Injection via Attributs (Symfony 7.x)
Vous pouvez injecter directement les arguments/options dans la méthode `__invoke` ou `execute` via l'attribut `#[Argument]`.

```php
use Symfony\Component\Console\Attribute\Argument;

public function __invoke(
    #[Argument('Description')] string $username, 
    OutputInterface $output
): int {
    $output->writeln("User: $username");
    return Command::SUCCESS;
}
```

## Output Sections (Sections de Sortie)
Permet de diviser la sortie en plusieurs zones indépendantes pour effacer/réécrire une partie spécifique (ex: barres de progression multiples, tableau dynamique).

```php
$section1 = $output->section();
$section2 = $output->section();

$section1->writeln('Téléchargement...');
$section2->writeln('Vérification...');

// Écrase le contenu de la section 1 uniquement
$section1->overwrite('Téléchargement terminé.');
// Efface le contenu de la section 2
$section2->clear(); 
```

## 🧠 Concepts Clés
1.  **--** : L'opérateur double tiret `--` permet de stopper le parsing des options. Tout ce qui suit sera considéré comme des arguments. Utile si un argument commence par un tiret.
2.  **Validation** : La console ne valide pas le format des données (email, int), juste leur présence. Pour valider, faites-le manuellement dans `execute`.

## ⚠️ Points de vigilance (Certification)
*   **Ordre** : On ne peut pas mettre un argument REQUIRED après un argument OPTIONAL.
*   **Shortcuts** : Les raccourcis options (`-i`) peuvent être combinés (`-iv` pour `-i` et `-v` si `-v` est None).

## Ressources
*   [Symfony Docs - Console Input](https://symfony.com/doc/current/console/input.html)
