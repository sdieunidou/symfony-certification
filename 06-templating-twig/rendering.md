# Méthodes de Rendu (Rendering)

## Concept clé
Le rendu d'un template consiste à transformer un fichier Twig et des données PHP en une chaîne de caractères (généralement du HTML) renvoyée dans une `Response`.

## 1. Dans un Contrôleur (`AbstractController`)

### A. Rendu Standard (`render`)
Crée une `Response` contenant le HTML. C'est la méthode la plus utilisée.
```php
public function index(): Response
{
    return $this->render('blog/index.html.twig', [
        'posts' => $posts,
    ]);
}
```

### B. Rendu Vue (`renderView`)
Retourne une `string` (le HTML brut) sans créer de `Response`.
Utile pour générer un corps d'email, un PDF ou un JSON.
```php
$html = $this->renderView('emails/welcome.html.twig', ['user' => $user]);
// $mailer->send(...)
```

### C. Attribut `#[Template]` (Symfony 6.2+)
Permet de déclarer le template via un attribut et de ne retourner que les données (array).
```php
use Symfony\Bridge\Twig\Attribute\Template;

#[Template('blog/index.html.twig')]
public function index(): array
{
    return ['posts' => $posts]; // Automatiquement passé à render()
}
```
*   **Option `block` (Symfony 7.2)** : `#[Template('base.html.twig', block: 'content')]`.

### D. Rendu de Bloc (`renderBlock`, `renderBlockView`)
Permet de ne rendre qu'un seul bloc d'un template. Utile pour les fragments AJAX (Turbo Streams) ou l'héritage complexe.
```php
return $this->renderBlock('blog/index.html.twig', 'product_list', $data);
```

## 2. Dans un Service
Injectez `Twig\Environment`.
```php
use Twig\Environment;

class Mailer
{
    public function __construct(private Environment $twig) {}

    public function send()
    {
        $body = $this->twig->render('email.html.twig', [...]);
    }
}
```

## 3. Directement depuis une Route (`TemplateController`)
Pour les pages statiques (CGU, À propos) qui n'ont pas besoin de logique PHP.
Pas besoin de créer une classe Contrôleur !

```yaml
# config/routes.yaml
about:
    path: /about
    controller: Symfony\Bundle\FrameworkBundle\Controller\TemplateController
    defaults:
        template: 'static/about.html.twig'
        statusCode: 200
        maxAge: 86400 # Cache
        context: # Variables statiques
            team_name: 'La Team'
```

## 4. Vérifier l'existence d'un template
```php
if ($twig->getLoader()->exists('theme/dark.html.twig')) {
    // ...
}
```

## 🧠 Concepts Clés
1.  **Response** : `render()` retourne un objet `Response` complet (avec headers, status 200). `renderView()` retourne une chaîne.
2.  **Auto-découverte** : Avec `#[Template]`, si on ne donne pas de nom de fichier, Symfony tente de deviner `ControllerName/method_name.html.twig`.

## Ressources
*   [Symfony Docs - Rendering Templates](https://symfony.com/doc/current/templates.html#rendering-templates)
