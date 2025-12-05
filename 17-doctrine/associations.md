# Associations (Relations)

## Types de Relations

### 1. ManyToOne (n..1)
La plus courante. "Plusieurs Produits appartiennent à une Catégorie".
C'est toujours le côté **Many** qui porte la clé étrangère (`category_id`).

```php
// Product.php (Owning Side)
#[ORM\ManyToOne(inversedBy: 'products')]
#[ORM\JoinColumn(nullable: false)]
private ?Category $category = null;
```

### 2. OneToMany (1..n)
L'inverse de ManyToOne. "Une Catégorie a plusieurs Produits".
C'est le côté **Inverse**. Il ne modifie pas la structure de la table `category`, il ne fait que "lire" l'autre côté.

```php
// Category.php (Inverse Side)
#[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
private Collection $products;

public function __construct() {
    $this->products = new ArrayCollection();
}
```

### 3. ManyToMany (n..n)
"Un étudiant a plusieurs cours, un cours a plusieurs étudiants".
Nécessite une **table de jointure** (Join Table), gérée automatiquement par Doctrine.

```php
#[ORM\ManyToMany(targetEntity: Course::class, inversedBy: 'students')]
private Collection $courses;
```

### 4. OneToOne (1..1)
"Un User a un Profile".

## Propriétaire vs Inverse (Owning vs Inverse Side)
C'est LE concept le plus important et le plus piégeux.

*   **Owning Side (Propriétaire)** : C'est l'entité qui contient la colonne de clé étrangère (Foreign Key). C'est **SEULEMENT** en modifiant ce côté que Doctrine persistera le changement en base.
*   **Inverse Side** : C'est l'autre côté (celui qui a `mappedBy`). Modifier la collection de ce côté ne change **RIEN** en base de données si le côté propriétaire n'est pas mis à jour.

**Exemple de piège classique :**
```php
$category->getProducts()->add($product);
$em->flush(); // NE FAIT RIEN en base car Product (le propriétaire) n'a pas changé !
```

**La bonne pratique : Méthodes `add/remove` synchronisées**
```php
// Category.php
public function addProduct(Product $product): static
{
    if (!$this->products->contains($product)) {
        $this->products->add($product);
        $product->setCategory($this); // <--- IMPORTANT : Met à jour le propriétaire
    }
    return $this;
}
```

## Cascade
Permet de propager des opérations d'une entité mère vers ses enfants.

*   `persist` : Si je persiste la Catégorie, persiste aussi ses nouveaux Produits.
*   `remove` : Si je supprime la Catégorie, supprime aussi ses Produits.
*   `orphanRemoval=true` : (Spécifique OneToMany) Si j'enlève un Produit de la collection `$category->getProducts()->removeElement($p)`, Doctrine supprimera le Produit de la base (DELETE).

```php
#[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
private Collection $products;
```

## Chargement (Fetch Mode)
*   **LAZY** (Défaut) : La relation n'est chargée que si on y accède (`$product->getCategory()->getName()`).
*   **EAGER** : La relation est chargée immédiatement via une jointure SQL. À utiliser avec parcimonie pour éviter de charger toute la base.

## Ressources
*   [Doctrine Docs - Association Mapping](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/association-mapping.html)
