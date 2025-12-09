# Débogage du Conteneur

## Concept clé
Comprendre comment inspecter le conteneur est crucial pour résoudre les erreurs "Service not found", les problèmes d'autowiring ou de configuration. Symfony fournit une suite de commandes puissantes via la console.

## 1. Lister les Services (`debug:container`)
Affiche la liste de tous les services **publics** et les alias.

```bash
php bin/console debug:container
```

Pour voir un service spécifique (même privé) :
```bash
php bin/console debug:container App\Service\MyService
```
Cela affiche :
*   La classe
*   Si il est shared / public / lazy / autowired
*   Les arguments
*   Les tags

Pour chercher par nom :
```bash
php bin/console debug:container --name=mailer
```

Pour lister les paramètres (`parameters` dans yaml) :
```bash
php bin/console debug:container --parameters
```

## 2. Déboguer l'Autowiring (`debug:autowiring`)
Affiche la liste des **types** (classes/interfaces) que vous pouvez utiliser en Type Hint dans vos constructeurs, et quel service sera injecté.

```bash
php bin/console debug:autowiring
```

Filtrer par mot clé :
```bash
php bin/console debug:autowiring log
# Affiche: Psr\Log\LoggerInterface (logger)
```

## 3. Linter le Container (`lint:container`)
Vérifie que la configuration (YAML/XML) est valide et que les arguments injectés correspondent aux types attendus. C'est souvent exécuté dans les pipelines CI.

```bash
php bin/console lint:container
```

## 4. Introspection (Service Definition Objects)
C'est la partie "Programmation" du débogage, utilisée dans les **Compiler Passes**.
L'objet `Symfony\Component\DependencyInjection\Definition` permet d'inspecter un service avant la compilation finale.

```php
// Dans un CompilerPass
$definition = $container->getDefinition('app.mailer');
dump($definition->getClass());
dump($definition->getArguments());
dump($definition->hasTag('twig.extension'));
```

## 🧠 Concepts Clés
1.  **Services Privés** : Ils n'apparaissent pas dans la liste par défaut de `debug:container` (sauf si on filtre).
2.  **Environment** : Les commandes de debug utilisent l'environnement (souvent `dev`). Le résultat peut différer de `prod` (où des optimisations/removals ont lieu).
3.  **Deprecations** : `debug:container --deprecations` affiche les services dépréciés utilisés.

## ⚠️ Points de vigilance (Certification)
*   **Autowirable vs Available** : Un service peut exister dans le conteneur (`debug:container`) sans être autowirable (il n'apparaîtra pas dans `debug:autowiring` s'il n'a pas d'alias de type).
*   **Erreurs** : Une erreur "Service not found" signifie souvent que le service est privé, ou que l'autowiring n'a pas trouvé de correspondance unique.

## Ressources
*   [Symfony Docs - Debugging the Container](https://symfony.com/doc/current/service_container/debug.html)
