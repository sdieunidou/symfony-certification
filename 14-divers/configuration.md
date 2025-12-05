# Configuration & Environnements

Ce document couvre les concepts clés de la [documentation officielle de configuration](https://symfony.com/doc/7.4/configuration.html).

## 1. Vue d'ensemble & Formats

La configuration dans Symfony peut être définie en **YAML** (recommandé), **XML**, ou **PHP**.
Elle se situe principalement dans le dossier `config/`.

| Type | Usage | Stockage | Exemple |
| :--- | :--- | :--- | :--- |
| **Infrastructure** | Identifiants, IPs, Clés API | Variables d'Env (`.env`, Secrets) | `DATABASE_URL`, `STRIPE_KEY` |
| **Comportement** | Règles métier globales | Paramètres (`parameters:`) | `app.admin_email` |
| **Services & Bundles** | Structure du code, Services | Configuration (`config/*.yaml`) | `framework.yaml`, `security.yaml` |

---

## 2. Environnements de Configuration

Symfony charge la configuration en fonction de l'environnement (`APP_ENV`: `dev`, `prod`, `test`).

### Structure de chargement
L'ordre de chargement des fichiers dans `config/packages/` est précis :

1.  `config/packages/*.yaml` (Configuration globale commune)
2.  `config/packages/{env}/*.yaml` (Surcharges spécifiques à l'environnement)

**Exemple :**
*   `config/packages/monolog.yaml` : Configure les logs de base.
*   `config/packages/dev/monolog.yaml` : Ajoute la sortie dans la console pour le dév.
*   `config/packages/prod/monolog.yaml` : Configure l'envoi d'emails d'erreur (FingersCrossed).

> **Note :** `services.yaml` suit la même logique (`services_test.yaml` est chargé en environnement de test).

---

## 3. Infrastructure : Variables d'Environnement (`.env`)

Symfony utilise le composant **Dotenv** pour charger des variables, respectant le principe "The Twelve-Factor App".

### Hiérarchie de chargement
L'ordre de priorité (le dernier gagne) :
1.  **Variables Système réelles** (Définies dans Apache/Nginx, Docker, ou `export` shell). **Gagnant absolu**.
2.  `.env.local.php` (Fichier de prod optimisé via `composer dump-env`).
3.  `.env.{env}.local` (Surcharges spécifiques, ex: `.env.test.local`).
4.  `.env.{env}` (Config spécifique committée, ex: `.env.test`).
5.  `.env.local` (Surcharges locales développeur, **non committé**).
6.  `.env` (Valeurs par défaut, **committé**).

### Processeurs de Variables
Symfony permet de transformer les variables d'environnement à la volée dans le YAML avec la syntaxe `%env(processor:VAR)%`.

| Processeur | Description | Exemple |
| :--- | :--- | :--- |
| `int`, `float`, `bool` | Cast le type | `%env(bool:DEBUG)%` |
| `string` | Force en chaîne | `%env(string:PORT)%` |
| `trim` | Supprime les espaces | `%env(trim:KEY)%` |
| `base64` | Décode du base64 | `%env(base64:KEY)%` |
| `json` | Décode du JSON (devient un array) | `%env(json:ROLES)%` |
| `csv` | Décode une liste séparée par virgules | `%env(csv:EMAILS)%` |
| `url` | Extrait une partie d'URL (host, scheme...) | `%env(key:host:url:DATABASE_URL)%` |
| `file` | Lit le contenu d'un fichier (Docker Secrets) | `%env(file:DATABASE_PASSWORD_FILE)%` |
| `resolve` | Remplace les params `%...%` dans la valeur | `%env(resolve:MY_VAR)%` |
| `default` | Valeur de repli si inexistant | `%env(default:fallback_val:MY_VAR)%` |
| `require` | Plante si la variable n'existe pas | `%env(require:CRITICAL_VAR)%` |

---

## 4. Paramètres & Accès à la Configuration

### Paramètres (`parameters`)
Valeurs statiques centralisées dans `config/services.yaml`.

```yaml
parameters:
    app.admin_email: 'admin@example.com'
    # Peut référencer une variable d'env
    app.secret: '%env(APP_SECRET)%'
```

### Accéder aux valeurs
Il existe trois méthodes principales pour récupérer la configuration dans vos services :

#### A. Injection explicite (Autowiring avec Attribut)
C'est la méthode recommandée depuis Symfony 6.3+.
```php
use Symfony\Component\DependencyInjection\Attribute\Autowire;

public function __construct(
    #[Autowire('%app.admin_email%')] private string $adminEmail,
    #[Autowire(env: 'json:MY_FLAGS')] private array $flags
) {}
```

#### B. Global Binding (`bind`)
Pour définir des arguments communs à tous les services dans `services.yaml`.
```yaml
services:
    _defaults:
        bind:
            $adminEmail: '%app.admin_email%'
            $projectDir: '%kernel.project_dir%'
```
*Dans le PHP, nommez simplement votre argument `$adminEmail` et il sera injecté automatiquement.*

#### C. ContainerBagInterface
Pour un accès programmatique (moins performant que l'injection directe, mais utile parfois).
```php
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

public function __construct(private ContainerBagInterface $params) {
    $email = $this->params->get('app.admin_email');
}
```

---

## 5. Configuration des Bundles (Semantic Configuration)

Les fichiers dans `config/packages/` configurent les bundles installés (FrameworkBundle, SecurityBundle, etc.).

### Déboguer la configuration
Deux commandes essentielles pour comprendre comment un bundle est configuré :

1.  **Voir la configuration actuelle (résolue) :**
    Affiche la configuration finale utilisée par Symfony (après merge des fichiers).
    ```bash
    php bin/console debug:config framework
    ```

2.  **Voir la configuration par défaut (Référence) :**
    Affiche toutes les options disponibles pour un bundle avec leurs valeurs par défaut et types. **Indispensable** pour découvrir des options sans aller sur le web.
    ```bash
    php bin/console config:dump-reference framework
    ```

---

## 6. Secrets Management (Vault)

Pour stocker des données sensibles chiffrées dans le dépôt Git.

### Commandes
```bash
# 1. Générer les clés (config/secrets/prod/...)
php bin/console secrets:generate-keys

# 2. Ajouter un secret
php bin/console secrets:set DATABASE_PASSWORD

# 3. Lister les secrets
php bin/console secrets:list --reveal
```
Usage transparent : `%env(DATABASE_PASSWORD)%` (Symfony décrypte automatiquement).

---

## Bonnes Pratiques

1.  **Ne définissez pas de paramètres pour tout** : Utilisez des constantes de classe (`const MAX_ITEMS = 10;`) si la valeur est interne à la logique d'une seule classe.
2.  **Performance** : En production, `composer dump-env prod` génère un fichier PHP optimisé.
3.  **Typez vos variables** : Utilisez les processeurs d'env (`int:`, `bool:`) pour éviter les erreurs de type dans vos services.
4.  **Utilisez `bind`** pour les paramètres très fréquents (ex: un répertoire d'upload).
