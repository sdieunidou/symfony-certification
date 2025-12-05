# Concurrence avec Doctrine

## Problème
Deux utilisateurs éditent la même entité en même temps. Qui gagne ? Comment éviter l'écrasement de données ?

## 1. Transactions et Autocommit
Par défaut, Doctrine désactive l'autocommit lors du `flush()`. Il ouvre une transaction, envoie les requêtes, et commit.
Cependant, entre le moment où vous *lisez* une donnée et le moment où vous *écrivez*, il s'écoule du temps.

## 2. Pessimistic Locking (Verrouillage Pessimiste)
On verrouille la ligne en base de données **au moment de la lecture**. Personne d'autre ne peut la lire/modifier tant que la transaction n'est pas finie.
*SQL* : `SELECT ... FOR UPDATE`.

```php
$em->beginTransaction();
try {
    // PESSIMISTIC_WRITE bloque les autres lectures/écritures
    $account = $em->find(Account::class, $id, LockMode::PESSIMISTIC_WRITE);
    
    $account->setBalance($account->getBalance() + 100);
    $em->flush();
    $em->commit();
} catch (\Exception $e) {
    $em->rollback();
}
```
*Usage* : Systèmes bancaires, billetterie (éviter la double vente). Impact fort sur les performances (attente).

## 3. Optimistic Locking (Verrouillage Optimiste)
On ne verrouille rien. On espère qu'il n'y aura pas de conflit.
Au moment d'écrire, on vérifie si la donnée a changé depuis notre lecture.
On utilise une colonne de version (`@Version`).

```php
#[ORM\Entity]
class Product
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Version] // Magie Doctrine
    private int $version;
}
```

Si deux utilisateurs modifient la version 1 :
1.  User A lit v1.
2.  User B lit v1.
3.  User A sauvegarde -> v2.
4.  User B essaie de sauvegarder v1 -> Doctrine voit que la version en base est v2 -> **OptimisticLockException**.

*Usage* : Formulaires d'édition, Wikis. L'utilisateur doit recharger la page et refaire sa modif.

## 4. Retries (Réessais)
En cas d'échec (Deadlock ou OptimisticLockException), on peut retenter l'opération automatiquement.
C'est complexe à faire dans un contrôleur (car il faut réinitialiser l'EntityManager fermé).
C'est beaucoup plus simple avec **Messenger**.

