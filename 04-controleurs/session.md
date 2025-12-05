# Session (Usage & Configuration)

## Concept clé
La session permet de persister des données utilisateur d'une page à l'autre.
Dans Symfony, la session est gérée par le composant `HttpFoundation` et offre une couche orientée objet au-dessus de `$_SESSION`.

## Accès (Injection)
Depuis Symfony 6, la manière recommandée est d'injecter `RequestStack`.

```php
use Symfony\Component\HttpFoundation\RequestStack;

class CartController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack
    ) {}

    public function index(): Response
    {
        $session = $this->requestStack->getSession();
        
        // API Fluide
        $cart = $session->get('cart', []);
        $session->set('cart', ['id' => 123]);
        
        // Supprimer
        $session->remove('cart');
        
        // Tout vider
        $session->clear(); 
        
        return $this->render('...');
    }
}
```
*On peut aussi faire `$request->getSession()` si on a injecté `Request`.*

## Session Bags (Sacs de données)
La session est organisée en "Bags" pour éviter la pollution de namespace :
1.  **AttributeBag** : Stockage général (`get`, `set`).
2.  **FlashBag** : Messages temporaires (voir fiche Flash Messages).
    *   `$session->getFlashBag()->add('success', 'Bravo')`
    *   `peek()`, `peekAll()` : Lire sans supprimer.
3.  **MetadataBag** : Informations techniques sur la session.
    *   `getCreated()` : Timestamp création.
    *   `getLastUsed()` : Timestamp dernière activité.
    *   `getLifetime()` : Durée de vie du cookie.

## Configuration (`framework.yaml`)
C'est ici qu'on définit **où** et **comment** les sessions sont stockées.

```yaml
framework:
    session:
        enabled: true
        # ID du service de stockage (null = fichiers PHP natifs par défaut)
        handler_id: null 
        
        # Sécurité des cookies
        cookie_secure: auto
        cookie_samesite: lax
        
        # Chemin de stockage (si handler natif)
        save_path: '%kernel.project_dir%/var/sessions/%kernel.environment%'
        
        # Garbage Collection (Probabilité 1%)
        gc_probability: 1
```

## Stockage en Base de Données (Handlers)
Pour une application multi-serveurs, le stockage fichier ne suffit pas. Symfony supporte nativement Redis, PDO (MySQL/PostgreSQL) et MongoDB.

### Redis
```yaml
# config/services.yaml
services:
    Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler:
        arguments:
            - '@Redis' # Service Redis configuré
            - { 'ttl': 3600 }

# config/packages/framework.yaml
framework:
    session:
        handler_id: Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler
```

### Base de données (PDO)
Symfony fournit `PdoSessionHandler`.
Il faut créer la table `sessions` (commande : `createTable()` ou migration).

```yaml
# config/services.yaml
services:
    Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler:
        arguments:
            - '%env(DATABASE_URL)%'
            - { db_table: 'sessions', db_id_col: 'sess_id' }

# config/packages/framework.yaml
framework:
    session:
        handler_id: Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler
```

## Sécurité & Expiration
1.  **Idle Timeout** : Vous pouvez vérifier manuellement l'inactivité.
    ```php
    if (time() - $session->getMetadataBag()->getLastUsed() > $maxIdleTime) {
        $session->invalidate(); // Détruit et régénère l'ID
    }
    ```
2.  **Chiffrement** : Il est possible de chiffrer les données de session via un Proxy (`SessionHandlerProxy`) ou un Marshaller (`SodiumMarshaller`).

## Sticky Locale
La locale (`_locale`) est stockée dans la requête, mais pas automatiquement persistée.
Pour la rendre "sticky" (persistante), on stocke souvent `_locale` en session via un `EventSubscriber` sur `kernel.request`.

## 🧠 Concepts Clés
1.  **Lazy Start** : La session ne démarre (`session_start()`) que si vous lisez ou écrivez dedans.
2.  **Sérialisation** : Les données sont sérialisées. Évitez de stocker des objets complexes (Entités Doctrine) -> Stockez les IDs.
3.  **Migration** : `MigratingSessionHandler` permet de changer de stockage (Fichier -> Redis) sans déconnecter les utilisateurs actifs (Double écriture).

## ⚠️ Points de vigilance (Certification)
*   **Service `session`** : Déprécié en injection directe. Utilisez `RequestStack`.
*   **Headers** : Les sessions envoient des headers (Cookies, Cache-Control: private). Une page utilisant la session est difficilement cachable publiquement.

## Ressources
*   [Symfony Docs - Sessions](https://symfony.com/doc/current/session.html)
