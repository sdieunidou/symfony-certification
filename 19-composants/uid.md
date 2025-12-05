# Composant UID

## Concept clé
Le composant **UID** fournit des utilitaires pour travailler avec des identifiants uniques (UUIDs et ULIDs).
Il facilite la génération, la conversion et l'inspection de ces identifiants, souvent utilisés comme clés primaires en base de données pour éviter les auto-incréments prédictibles.

## Installation
```bash
composer require symfony/uid
```

## UUID (Universally Unique Identifier)
Identifiant de 128-bits (32 hexadécimaux). Format : `xxxxxxxx-xxxx-Mxxx-Nxxx-xxxxxxxxxxxx`.

### Versions supportées
*   **v1** (Time-based) : Basé sur le temps et l'adresse MAC. Déconseillé (problèmes de vie privée et performance DB).
*   **v3** (Name-based MD5) : Déterministe (Même nom + namespace = Même UUID).
*   **v4** (Random) : **Le plus courant**. Totalement aléatoire.
*   **v5** (Name-based SHA1) : Comme v3 mais plus sécurisé (SHA1).
*   **v6** (Reordered Time) : Compatible v1 mais triable lexicographiquement (bon pour l'indexation DB).
*   **v7** (Unix Epoch) : **Recommandé**. Basé sur le Timestamp (millisecondes). Triable et entropy élevée.
*   **v8** (Custom) : Format libre expérimental.

### Utilisation
```php
use Symfony\Component\Uid\Uuid;

$uuid4 = Uuid::v4(); // Instance de UuidV4
$uuid7 = Uuid::v7(); // Instance de UuidV7

// Conversion
$string = $uuid7->toRfc4122(); // "017f22e2-79b0-7099-98e0-..."
$binary = $uuid7->toBinary(); // Stockage optimisé en DB (BINARY(16))
```

## ULID (Universally Unique Lexicographically Sortable Identifier)
Alternative aux UUIDs. 128-bits également, mais représenté en **Base32** (26 caractères).
*   **Triable** : Les premiers bits sont un timestamp.
*   **Lisible** : URL-safe, pas de tirets, insensible à la casse.
*   **Monotone** : Garantit l'ordre même généré à la même milliseconde.

### Utilisation
```php
use Symfony\Component\Uid\Ulid;

$ulid = new Ulid();
// Output: 01E439TP9XKZ57G662FP73CNKP

$param = $ulid->toRfc4122(); // Conversion en format UUID possible !
```

## Stockage en Base de Données (Doctrine)
Depuis Symfony 5.2 / DoctrineBundle 2.3, les types `uuid` et `ulid` sont gérés nativement.

```php
// Entity
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Id]
#[ORM\Column(type: 'uuid', unique: true)]
#[ORM\GeneratedValue(strategy: 'CUSTOM')]
#[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
private ?Uuid $id = null;
```

*   **PostgreSQL** : Utilise le type natif `UUID`.
*   **MySQL/MariaDB** : Stocke en `BINARY(16)` (très performant) et convertit automatiquement en objet PHP.

## Commandes Console
Pour générer ou inspecter des IDs depuis le CLI.

```bash
# Générer
php bin/console uuid:generate --count=5
php bin/console ulid:generate

# Inspecter (Version, Timestamp inclus...)
php bin/console uuid:inspect a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11
php bin/console ulid:inspect 01F8MEZ0...
```

## ⚠️ Points de vigilance (Certification)
1.  **Performance d'indexation** : Les UUID v4 (aléatoires) fragmentent les index de base de données (Page Splitting) lors des insertions massives. **Préférez UUID v7 ou ULID** qui sont séquentiels (Time-ordered).
2.  **Espace disque** : Stocker un UUID en `VARCHAR(36)` prend 36 octets (ou plus selon encoding). En `BINARY(16)`, c'est 16 octets. Symfony gère ça nativement avec le type Doctrine `uuid`.

## Ressources
*   [Symfony Docs - UID](https://symfony.com/doc/current/components/uid.html)
*   [RFC 9562 (UUID v7)](https://www.rfc-editor.org/rfc/rfc9562)

