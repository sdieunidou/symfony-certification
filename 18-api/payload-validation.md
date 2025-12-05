# Validation des Payloads API

## Différence avec les Formulaires
Dans une application Symfony classique, le composant `Form` gère à la fois :
1.  Le mapping des données (Request -> Objet).
2.  La validation.
3.  Le rendu des erreurs (HTML).

En API, on évite souvent le composant `Form` (trop lourd, orienté HTML). On préfère :
1.  **Mapping** : Serializer ou `MapRequestPayload`.
2.  **Validation** : Composant Validator directement sur l'objet.
3.  **Erreurs** : Retour JSON structuré (RFC 7807 Problem Details ou format custom).

## Validation Manuelle (Méthode classique)

Si vous n'utilisez pas `#[MapRequestPayload]`, vous devez valider manuellement.

```php
public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
{
    // 1. Désérialisation
    $dto = $serializer->deserialize($request->getContent(), UserDto::class, 'json');

    // 2. Validation
    $errors = $validator->validate($dto);

    if (count($errors) > 0) {
        // 3. Construction de la réponse d'erreur
        $data = ['violations' => []];
        foreach ($errors as $violation) {
            $data['violations'][] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage()
            ];
        }
        return $this->json($data, 422);
    }
    
    // ...
}
```

## Validation Automatique (Symfony 6.3+)

L'attribut `#[MapRequestPayload]` effectue la validation automatiquement. En cas d'échec, il lance une exception.
Cependant, pour contrôler le format de sortie, vous devrez peut-être écouter l'événement `kernel.exception` ou laisser le `ErrorHandler` par défaut (qui retourne un format JSON standard en dev/prod si l'entête `Accept: application/json` est présent).

## Contraintes de Validation utiles en API

*   `#[Assert\NotBlank]` : Champ obligatoire.
*   `#[Assert\Type]` : Vérifie le type de donnée (souvent géré par le typage PHP + Serializer, mais utile pour les cas flous).
*   `#[Assert\Choice]` : Pour les énumérations (ex: status).
*   `#[Assert\Valid]` : **Crucial**. Permet de valider les sous-objets ("Cascade validation"). Si votre DTO contient une adresse, mettez `#[Valid]` sur la propriété `$address` pour que les contraintes de l'objet Adresse soient aussi vérifiées.

## Problem Details (RFC 7807)
Symfony supporte nativement ce standard pour les retours d'erreur API.
Si vous lancez une exception, et que le client demande du JSON, Symfony peut générer ce format :

```json
{
    "type": "https://tools.ietf.org/html/rfc2616#section-10",
    "title": "An error occurred",
    "status": 422,
    "detail": "email: This value is not a valid email address."
}
```

## 🧠 Concepts Clés
1.  **Fail Fast** : Valider le plus tôt possible (au niveau du DTO) avant de toucher à la base de données ou à la logique métier complexe.
2.  **400 vs 422** :
    *   **400 (Bad Request)** : Le JSON est mal formé (virgule manquante, syntaxe invalide). Le sérialiseur plante avant même d'avoir un objet.
    *   **422 (Unprocessable Entity)** : Le JSON est syntaxiquement valide, mais les données ne respectent pas les règles métier (email invalide).

## Ressources
*   [Symfony Docs - Validation](https://symfony.com/doc/current/validation.html)
