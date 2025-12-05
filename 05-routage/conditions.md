# Correspondance de Requête Conditionnelle (Conditions)

## Concept clé
Parfois, les critères standards (URL, Méthode, Hôte) ne suffisent pas. Vous avez besoin de matcher une route selon une logique métier complexe (ex: Header spécifique, User-Agent, IP, Expression arbitraire).
Symfony intègre le composant **ExpressionLanguage** directement dans le routeur.

## Application dans Symfony 7.0
L'option `condition` permet d'écrire une expression logique qui doit retourner `true` pour que la route matche.

```php
use Symfony\Component\Routing\Attribute\Route;

class MobileController extends AbstractController
{
    // Matche SEULEMENT si le User-Agent contient "iPhone"
    #[Route(
        '/contact', 
        name: 'contact_mobile', 
        condition: "request.headers.get('User-Agent') matches '/iPhone/i'"
    )]
    public function contact(): Response
    {
        return $this->render('contact/mobile.html.twig');
    }
}
```

## Variables Disponibles
Dans l'expression, vous avez accès à deux variables objets :

1.  **`request`** (`Symfony\Component\HttpFoundation\Request`) : La requête HTTP complète.
    *   `request.headers.get('Referer')`
    *   `request.cookies.has('beta_access')`
    *   `request.getMethod()`
2.  **`context`** (`Symfony\Component\Routing\RequestContext`) : Le contexte de routage (subset de la requête utilisé par le routeur).
    *   `context.getMethod()`
    *   `context.getHost()`

## Cas d'usage Avancés
*   **Feature Flipping** : Router vers un nouveau contrôleur si un paramètre de requête est présent.
    *   `condition: "request.query.has('new_design')"`
*   **Maintenance** : Exclure une route selon une variable d'env (complexe à faire en pur routing, mieux vaut un Listener, mais possible via expression si paramètre injecté).

## 🧠 Concepts Clés
1.  **Ordre d'évaluation** : La condition est évaluée **après** le matching de l'URL, de la méthode et du host. C'est le dernier filtre.
2.  **ExpressionLanguage** : C'est le même langage que dans les ACLs de sécurité ou la validation. Syntaxe proche de Twig.

## ⚠️ Points de vigilance (Certification)
*   **Performance** : Contrairement aux regex d'URL qui sont compilées (rapides), les conditions sont évaluées en PHP au runtime. Abuser des conditions sur des routes très fréquentées peut avoir un impact (minime mais existant).
*   **Dumper** : Les conditions ne peuvent pas être dumpées en règles Apache/Nginx pures. Elles nécessitent que PHP soit exécuté.

## Ressources
*   [Symfony Docs - Route Conditions](https://symfony.com/doc/current/routing.html#matching-expressions)
*   [Expression Language Syntax](https://symfony.com/doc/current/reference/formats/expression_language.html)
