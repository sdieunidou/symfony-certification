# Configuration & Environnements

## Vue d'ensemble
La configuration d'une application Symfony se divise en trois catégories distinctes. Savoir choisir la bonne est crucial pour la sécurité et la maintenabilité.

| Type | Usage | Stockage | Exemple |
| :--- | :--- | :--- | :--- |
| **Infrastructure** | Identifiants, IPs, Clés API | Variables d'Env (`.env`, Secrets) | `DATABASE_URL`, `STRIPE_KEY` |
| **Comportement** | Règles métier globales | Paramètres (`parameters:`) | `app.default_tax_rate`, `app.admin_email` |
| **Interne** | Structure du code, Services | Configuration (`config/*.yaml`) | Routing, Security, Services |

---

## 1. Infrastructure : Variables d'Environnement (`.env`)

Symfony utilise le composant **Dotenv** pour charger des variables. C'est le standard moderne (Factor 12 App).

### Hiérarchie de chargement
L'ordre de priorité (le dernier gagne) :
1.  **Variables Système réelles** (Définies dans Apache/Nginx, Docker, ou `export` shell). **Gagnant absolu**.
2.  `.env.local.php` (Fichier de prod optimisé via `composer dump-env`).
3.  `.env.{env}.local` (Surcharges spécifiques, ex: `.env.test.local`).
4.  `.env.{env}` (Config spécifique committée, ex: `.env.test`).
5.  `.env.local` (Surcharges locales développeur, **non committé**).
6.  `.env` (Valeurs par défaut, **committé**).

### Processeurs de Variables
Symfony permet de transformer les variables d'environnement à la volée dans le YAML.
*   `%env(int:MAX_ITEMS)%` : Cast en entier.
*   `%env(bool:DEBUG)%` : Cast en booléen.
*   `%env(json:PAGES)%` : Décode du JSON.
*   `%env(file:SECRET_FILE)%` : Lit le contenu du fichier dont le chemin est dans la var (utile pour Docker Secrets).
*   `%env(base64:KEY)%` : Décode du base64.
*   `%env(trim:VAR)%` : Supprime les espaces.
*   `%env(require:VAR)%` : Plante si la variable n'existe pas.

---

## 2. Secrets Management (Vault)

Pour stocker des données sensibles (clés API, certificats) de manière sécurisée **dans le dépôt Git**.
Au lieu de `.env` en clair, on utilise le **Secrets Vault**.

### Fonctionnement
1.  Symfony génère une paire de clés cryptographiques (`config/secrets/prod/prod.encrypt.public.php` et `prod.decrypt.private.php`).
2.  La clé publique est committée. La clé privée reste sur le serveur de prod (ou chez le lead dev).
3.  Les valeurs sont chiffrées et stockées dans le dépôt.

### Commandes
```bash
# Générer les clés
php bin/console secrets:generate-keys

# Ajouter un secret (Interactif)
php bin/console secrets:set DATABASE_PASSWORD

# Lister les secrets
php bin/console secrets:list --reveal
```

Ensuite, on l'utilise comme une variable d'env normale : `%env(DATABASE_PASSWORD)%`. Symfony déchiffre automatiquement si la variable système n'existe pas déjà.

---

## 3. Paramètres d'Application (`parameters`)

Ce sont des constantes de configuration **propres à l'application**, qui ne changent pas selon l'infrastructure (dev/prod/test), mais qui doivent être centralisées.

```yaml
# config/services.yaml
parameters:
    app.admin_email: 'admin@example.com'
    app.supported_locales: ['en', 'fr', 'es']
```

### Quand les utiliser ?
*   Pour des valeurs utilisées à plusieurs endroits (DRY).
*   Pour des valeurs métier (Taux de TVA, Email de contact).
*   **MAIS** : Ne pas y mettre de secrets ou d'IPs (sauf si vous injectez une variable d'env dedans : `app.db_url: '%env(DATABASE_URL)%'`).

---

## 4. Constantes de Classe PHP

Parfois, la configuration YAML est "overkill". Si une valeur est :
1.  Immuable.
2.  Propre à une seule classe.
3.  Ne changera jamais sans que le code ne change aussi.

Utilisez une **Constante PHP**.

```php
class InvoiceGenerator
{
    // Bonne pratique : C'est une règle métier interne, pas besoin de config externe
    private const MAX_ITEMS_PER_PAGE = 50;
}
```

---

## Bonnes Pratiques (Résumé)

1.  **Secrets** : Toujours dans des variables d'environnement (réelles ou Vault). Jamais en dur, jamais dans `parameters` en clair.
2.  **Performance** : En production, lancez `composer dump-env prod` pour compiler le `.env` en PHP. Cela évite le parsing coûteux à chaque requête.
3.  **Type** : Utilisez les processeurs (`%env(int:...)%`) pour typer vos injections.
4.  **Autowiring** : Injectez vos params via l'attribut `#[Autowire]`.
    ```php
    public function __construct(
        #[Autowire('%app.admin_email%')] private string $adminEmail,
        #[Autowire(env: 'STRIPE_KEY')] private string $stripeKey
    ) {}
    ```

## Ressources
*   [Symfony Docs - Configuration](https://symfony.com/doc/current/configuration.html)
*   [Symfony Docs - Secrets](https://symfony.com/doc/current/configuration/secrets.html)
