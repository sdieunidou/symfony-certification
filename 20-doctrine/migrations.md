# Migrations

## Concept
Les migrations permettent de versionner le schéma de base de données, tout comme Git versionne le code.
Elles permettent de déployer les changements de structure (CREATE TABLE, ALTER TABLE) de manière reproductible sur tous les environnements (Dev, Staging, Prod).

## Workflow Standard

1.  **Modifier les entités** : Vous ajoutez une propriété `private string $phone;` dans `User.php`.
2.  **Générer la migration** : Doctrine compare vos Entités (état désiré) avec la Base de données actuelle (état actuel).
    ```bash
    php bin/console make:migration
    ```
    Cela crée un fichier `migrations/Version2023....php` contenant le SQL nécessaire.
3.  **Vérifier** : Toujours relire le fichier généré ! Parfois Doctrine renomme une table en la supprimant et en la recréant (perte de données), il faut ajuster le SQL manuellement.
4.  **Exécuter** :
    ```bash
    php bin/console doctrine:migrations:migrate
    ```

## Commandes Clés
*   `doctrine:migrations:diff` (alias de make:migration) : Génère la diff automatique.
*   `doctrine:migrations:execute --up X` : Exécute une version spécifique.
*   `doctrine:migrations:status` : Voir où on en est.

## Bonnes Pratiques (Certification)
*   **Ne jamais modifier une migration déjà jouée en production**. Créez-en une nouvelle.
*   **Déploiement** : La commande `migrate` doit être jouée lors du déploiement, généralement après la mise à jour du code mais avant le clear cache final.
*   **Données** : On peut mettre des requêtes `UPDATE` ou `INSERT` dans une migration (Data Migration), mais attention aux performances sur les grosses tables.

## Ressources
*   [Symfony Docs - Migrations](https://symfony.com/doc/current/doctrine.html#migrations-creating-the-database-tables-schema)
