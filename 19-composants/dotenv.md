# Composant Dotenv

## Concept clé
Le composant **Dotenv** parse les fichiers `.env` pour définir des variables d'environnement accessibles via `$_SERVER` ou `$_ENV`.
C'est le pilier de la configuration "12-Factor App" dans Symfony.

## Hiérarchie des fichiers
Symfony charge les fichiers dans cet ordre (le dernier écrase le précédent) :
1.  `.env` : Valeurs par défaut (commité dans Git).
2.  `.env.local` : Surcharges pour la machine locale (NON commité, ignoré par git).
3.  `.env.{env}` : Config spécifique à un environnement (ex: `.env.test`).
4.  `.env.{env}.local` : Surcharge locale pour un environnement.

## Utilisation

```php
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// Accès
$dbUrl = $_ENV['DATABASE_URL'];
```

Dans une application Symfony, ce code est déjà présent dans `public/index.php` ou `bin/console`.

## Fonctionnement Interne

### Architecture
*   **Dotenv** : La classe principale.
*   **Parser** : Analyse le format `KEY=VALUE`.

### Le Flux
1.  **Load** : Lit le fichier `.env`.
2.  **Parse** : Extrait les clés/valeurs.
3.  **Populate** : Remplit `$_ENV` et `$_SERVER`.
4.  **Override** : Par défaut, ne touche pas aux variables qui existent déjà (les "vraies" variables d'environnement gagnent toujours).

## 🧠 Concepts Clés
1.  **Variables réelles** : Si une vraie variable d'environnement système existe (ex: définie dans Docker ou Apache), Dotenv **ne l'écrase pas** par défaut. La prod a toujours raison.
2.  **Référencement** : On peut référencer une variable dans une autre : `app_url=$Scheme://$Host`.

## Ressources
*   [Symfony Docs - Dotenv](https://symfony.com/doc/current/components/dotenv.html)
