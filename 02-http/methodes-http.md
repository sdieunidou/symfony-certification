# Méthodes HTTP (Verbes)

## Concept clé
Les méthodes HTTP définissent la **sémantique** de l'action demandée sur une ressource.
Le respect de ces sémantiques est crucial pour l'architecture REST, le cache et la sécurité.

## Matrice des Propriétés

| Méthode | Sémantique | Safe (Lecture seule) | Idempotent (Répétable sans effet cumulé) | Body (Corps) | Cacheable |
| :--- | :--- | :---: | :---: | :---: | :---: |
| **GET** | Récupérer | ✅ OUI | ✅ OUI | Non | ✅ OUI |
| **HEAD** | Récupérer (Headers seuls) | ✅ OUI | ✅ OUI | Non | ✅ OUI |
| **POST** | Traiter / Créer (sous-ressource) | ❌ NON | ❌ NON | Oui | ⚠️ Parfois |
| **PUT** | Remplacer (Complet) | ❌ NON | ✅ OUI | Oui | ❌ Non |
| **PATCH** | Modifier (Partiel) | ❌ NON | ❌ NON (théoriquement) | Oui | ❌ Non |
| **DELETE** | Supprimer | ❌ NON | ✅ OUI | Non | ❌ Non |
| **OPTIONS**| Capacités (CORS) | ✅ OUI | ✅ OUI | Non | ❌ Non |

## Définitions Avancées

### Safe (Sûr)
Une méthode est **Safe** si elle ne modifie pas l'état du serveur (Lecture seule).
*   Un crawler (Google Bot) peut appeler des méthodes Safe sans risque.
*   Il ne doit jamais y avoir d'action destructrice sur une requête GET (ex: `/delete?id=1` est une faille de sécurité grave, CSRF triviale).

### Idempotent
Une méthode est **Idempotente** si faire la requête **N** fois a le même état final que de la faire **1** fois.
*   `DELETE /user/1` : La 1ère fois supprime (204/200). La 2ème fois, l'user n'existe plus (404), mais l'état du serveur est le même (user supprimé). C'est idempotent.
*   `POST /user` : Crée un user à chaque appel. 10 appels = 10 users. **NON** idempotent.
*   Utilité : Si le client a un timeout (réseau coupé), il peut relancer une requête idempotente sans risque de doublon.

## Application dans Symfony 7.0

### 1. Restriction de Route (Attributes)
C'est la manière standard de sécuriser les contrôleurs.

```php
#[Route('/api/posts/{id}', methods: ['GET'])]
public function show(int $id) { ... }

#[Route('/api/posts/{id}', methods: ['DELETE'])]
public function delete(int $id) { ... }
```

### 2. Simulation des Méthodes (`_method`)
Les formulaires HTML ne supportent nativement que `GET` et `POST`.
Pour utiliser `PUT` ou `DELETE` depuis un formulaire HTML classique, Symfony utilise un trick standard :
1.  Le formulaire est envoyé en `POST`.
2.  Il contient un champ caché `<input type="hidden" name="_method" value="DELETE">`.
3.  Le framework lit ce paramètre et modifie l'objet `Request` pour qu'il apparaisse comme une requête `DELETE` (`$request->getMethod()` renvoie `DELETE`).

*Note : Cette fonctionnalité doit être activée explicitement via `http_method_override` dans `framework.yaml` ou via la méthode `Request::enableHttpMethodParameterOverride()` (bien que Symfony Flex le configure souvent par défaut).*

### 3. PATCH vs PUT
*   **PUT** : Remplace **toute** la ressource. Si vous envoyez `{ "nom": "A" }` pour un objet qui avait `{ "nom": "B", "age": 10 }`, l'âge est perdu (devient null).
*   **PATCH** : Modifie uniquement les champs envoyés. `{ "nom": "A" }` ne touche pas à l'âge.

## 🧠 Concepts Clés
1.  **HEAD** : Souvent utilisé pour vérifier l'existence d'un lien ou sa date de modif sans télécharger le fichier (Performance). Symfony gère HEAD automatiquement si une route GET existe (il exécute le contrôleur mais coupe le contenu de la réponse).
2.  **OPTIONS** : Fondamental pour les SPAs (React/Vue) hébergées sur un autre domaine. Le navigateur envoie une requête "Preflight" `OPTIONS` avant de faire le vrai `POST`/`PUT` pour vérifier les permissions CORS (`Access-Control-Allow-Methods`).

## ⚠️ Points de vigilance (Certification)
*   **Matching de Route** : Si vous définissez deux routes avec le même chemin `/test` mais des méthodes différentes (`GET` et `POST`), Symfony choisira la bonne méthode. Si aucune ne matche (ex: `PUT`), il renvoie une **405 Method Not Allowed** (et liste les méthodes permises dans le header `Allow`), et non une 404.
*   **Sécurité** : Restreindre les méthodes réduit la surface d'attaque. Une route de suppression ne doit JAMAIS répondre à GET.

## Ressources
*   [MDN - Méthodes HTTP](https://developer.mozilla.org/fr/docs/Web/HTTP/Methods)
*   [RFC 7231 - Semantics and Content](https://tools.ietf.org/html/rfc7231)
