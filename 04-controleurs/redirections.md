# Redirections HTTP

## Concept clé
La redirection est une réponse HTTP (Code 3xx + Header `Location`) qui demande au navigateur d'aller voir ailleurs.

## Méthodes Helper (`AbstractController`)

### 1. `redirectToRoute` (Interne)
La plus utilisée. Redirige vers une route Symfony par son nom.

```php
// Défaut : 302 Found (Temporaire)
return $this->redirectToRoute('blog_show', ['slug' => 'my-post']);

// 301 Moved Permanently (Définitif - Cacheable par le navigateur)
return $this->redirectToRoute('home', [], Response::HTTP_MOVED_PERMANENTLY);

// 307 Temporary Redirect (Conserve la méthode POST)
return $this->redirectToRoute('api_endpoint', [], Response::HTTP_TEMPORARY_REDIRECT);
```

### 2. `redirect` (Externe)
Redirige vers une URL absolue.

```php
return $this->redirect('https://google.com');
```

## Codes de Redirection (Nuances)

| Code | Nom | Sens | Cache ? | Méthode POST conservée ? |
| :--- | :--- | :--- | :--- | :--- |
| **301** | Moved Permanently | Définitif (SEO) | Oui | Non (devient GET) |
| **302** | Found | Temporaire (Standard) | Non | Non (devient GET) |
| **307** | Temporary Redirect | Temporaire (Strict) | Non | **Oui** (POST reste POST) |
| **308** | Permanent Redirect | Définitif (Strict) | Oui | **Oui** (POST reste POST) |

## Redirection et Query Params
Par défaut, `redirectToRoute` ne conserve **pas** les paramètres de requête (`?foo=bar`) de la requête actuelle.
Si vous voulez les transmettre, vous devez les injecter manuellement :

```php
public function index(Request $request): Response
{
    return $this->redirectToRoute('other_route', [
        'filter' => $request->query->get('filter') // Passage manuel
    ]);
}
```
*(Note : Il existe une option `keepRequestMethod` sur certaines méthodes internes, mais pas sur `redirectToRoute` standard).*

## 🧠 Concepts Clés
1.  **PRG Pattern** (Post-Redirect-Get) : Après la soumission réussie d'un formulaire (POST), il faut **toujours** rediriger (302/303) vers une page de confirmation ou de liste (GET). Cela empêche l'utilisateur de re-soumettre le formulaire en rafraîchissant la page (F5).
2.  **RedirectResponse** : Les helpers retournent une instance de `Symfony\Component\HttpFoundation\RedirectResponse`.

## ⚠️ Points de vigilance (Certification)
*   **Boucles** : Attention à ne pas rediriger vers la route courante sans condition, sinon : `ERR_TOO_MANY_REDIRECTS`.
*   **URL Generator** : `redirectToRoute` utilise `generateUrl()` en interne. Si la route n'existe pas ou s'il manque des paramètres obligatoires, une exception `RouteNotFoundException` ou `MissingMandatoryParametersException` est lancée.

## Ressources
*   [Symfony Docs - Redirections](https://symfony.com/doc/current/controller.html#redirecting)
