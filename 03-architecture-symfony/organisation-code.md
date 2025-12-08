# Organisation du Code

## Concept clé
Symfony propose une structure de répertoires standard (basée sur `symfony/skeleton` et Flex) mais reste flexible. L'objectif est de séparer clairement **Configuration**, **Code Source**, **Web Root** et **Fichiers Temporaires**.

## Arborescence Standard (Symfony 7)

*   **`bin/`** : Exécutables (Le binaire `console`, `phpunit`).
*   **`config/`** : Toute la configuration.
    *   `bundles.php` : Activation des bundles.
    *   `packages/` : Config des bundles (`doctrine.yaml`, `security.yaml`).
    *   `routes/` : Config du routing.
    *   `services.yaml` : Config du conteneur de services.
*   **`public/`** : **Racine Web**. Seul ce dossier doit être exposé par Nginx/Apache.
    *   `index.php` : Le Front Controller unique.
    *   `assets/`, `build/` : Images, CSS, JS compilés.
*   **`src/`** : Le code source PHP (Namespace `App\`).
    *   `Controller/`, `Entity/`, `Repository/`, `Form/`, `Security/` : Structure par couche technique (Layered Architecture).
*   **`templates/`** : Vues Twig.
*   **`tests/`** : Tests automatiques (`Unit`, `Functional`).
*   **`translations/`** : Fichiers de traduction (`messages.fr.yaml`).
*   **`var/`** : Fichiers générés par Symfony. Doit être en écriture (`chmod 777` ou ACL).
    *   `cache/` : Cache compilé du conteneur.
    *   `log/` : Logs applicatifs (`dev.log`, `prod.log`).
*   **`vendor/`** : Librairies tierces (Composer). Exclus du Git.
*   **`.env`** : Variables d'environnement par défaut (commité).
*   **`.env.local`** : Surcharges locales (non commité, ignoré).

## Architectures Alternatives

### 1. Layered Architecture (Défaut)
Organisation par type technique (`Controller`, `Repository`).
*   ✅ Simple pour les petits/moyens projets.
*   ✅ Standard Symfony.
*   ❌ Difficile à maintenir sur les très gros projets (Code éparpillé).

### 2. Domain Driven Design (DDD) / Hexagonal
Organisation par **Domaine Métier** (Feature).
*   `src/Catalog/` (Contient ses propres Entities, Repositories, Services).
*   `src/Cart/`
*   `src/User/`
*   ✅ Idéal pour les monoliths complexes.
*   ✅ Nécessite une configuration manuelle des namespaces et services.

## Kernel Class (`src/Kernel.php`)
C'est le cœur de l'application.
*   Il utilise le trait `MicroKernelTrait`.
*   Il configure le ContainerBuilder.
*   Il charge les routes, etc

## 🧠 Concepts Clés
1.  **Front Controller Pattern** : Tout le trafic passe par `public/index.php`. Cela centralise la sécurité et l'initialisation.
2.  **Séparation Public/Privé** : Le code PHP (`src/`), la config (`config/`) et les vendors ne sont **pas** accessibles via URL. C'est une sécurité fondamentale.
3.  **Environment Variables** : La configuration spécifique à la machine (DB user, API Key) ne doit jamais être dans le code, mais dans l'environnement (ou `.env.local`).

## ⚠️ Points de vigilance (Certification)
*   **Dossier `app/`** : Existait en Symfony 2/3. A disparu. Tout est dans `src/` ou `config/`.
*   **Dossier `web/`** : Renommé en `public/` depuis Symfony 4.
*   **Permissions** : Seul `var/` a besoin d'être accessible en écriture par l'utilisateur web (www-data).
*   **Assets** : Ne jamais mettre d'assets sources (SCSS, TS) dans `public/`. Mettez-les dans `assets/` à la racine et compilez-les (AssetMapper ou Webpack Encore) vers `public/`.

## Ressources
*   [Symfony Directory Structure](https://symfony.com/doc/current/best_practices.html#creating-the-project)
