# Réponse HTTP (Response)

## Concept clé
L'objet Réponse est le but ultime de toute application web : envoyer des données au client.
Structure :
1.  **Status Line** : `HTTP/1.1 200 OK`
2.  **Headers** : Métadonnées (`Content-Type: application/json`, `Set-Cookie: ...`)
3.  **Body** : Contenu payload (HTML, JSON, Binaire).

## Application dans Symfony 7.0
Symfony oblige le contrôleur à retourner un objet qui hérite de `Symfony\Component\HttpFoundation\Response`.

### Les Types de Réponses

1.  **`Response`** : Standard. Contenu string chargé en mémoire.
2.  **`JsonResponse`** : Spécifique API.
    *   Encode auto (`json_encode`).
    *   Header `Content-Type: application/json`.
3.  **`BinaryFileResponse`** : Pour servir des fichiers statiques (images, PDF) efficacement.
    *   Gère les `Range` (reprise de téléchargement).
    *   Gère `X-Sendfile` (délègue l'envoi réel à Nginx/Apache pour perf maximale).
4.  **`StreamedResponse`** : Pour les contenus générés à la volée (gros CSV, Logs temps réel).
    *   Maintient la connexion ouverte et envoie par paquets (chunks).
    *   Évite la saturation mémoire (OOM) sur les gros exports.
5.  **`RedirectResponse`** : Raccourci pour une 301/302 + Header Location.

## Exemple de Code Expert

```php
public function export(): StreamedResponse
{
    $response = new StreamedResponse(function () {
        $handle = fopen('php://output', 'w+');
        // Génération ligne par ligne : Faible empreinte mémoire
        for ($i = 0; $i < 100000; $i++) {
            fputcsv($handle, ["Row $i", rand()]);
            flush(); // Force l'envoi au client
        }
        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

    return $response;
}
```

## Sécurité via les Headers
La réponse est le lieu pour renforcer la sécurité côté client.
*   **CSP (Content-Security-Policy)** : Restreint les sources de scripts/styles.
*   **X-Content-Type-Options: nosniff** : Empêche le navigateur de deviner le type MIME.
*   **X-Frame-Options: DENY** : Empêche l'inclusion en iFrame (Clickjacking).

Dans Symfony, on configure souvent ces headers globalement via un EventListener (`response` event) ou `NelmioSecurityBundle`.

## HTTP/103 Early Hints
Nouveauté supportée par Symfony : Permet d'envoyer des headers "Link" (preload) au client **pendant** que le serveur calcule encore la réponse finale (traitement DB). Le navigateur commence à télécharger le CSS/JS avant même d'avoir reçu le HTML.

```php
$response->sendHeaders(103); // Envoie immédiat des headers
// ... calcul lourd ...
return $response;
```

## 🧠 Concepts Clés
1.  **Objet Mutable** : L'objet `Response` est mutable. Les listeners du Kernel peuvent le modifier avant l'envoi (ajouter des cookies, compresser le body, injecter la Toolbar).
2.  **`prepare()`** : Méthode cruciale appelée automatiquement avant l'envoi. Elle fixe le Charset par défaut, calcule le Content-Length si absent, et nettoie les headers invalides selon la norme HTTP.
3.  **`send()`** : Envoie physiquement les headers HTTP (via `header()`) et le contenu (via `echo`). Une fois fait, on ne peut plus rien modifier.

## ⚠️ Points de vigilance (Certification)
*   **Mémoire** : Ne jamais mettre le contenu d'un fichier de 1Go dans une `Response` standard (`setContent(file_get_contents(...))`). Crash assuré. Utilisez `BinaryFileResponse` ou `StreamedResponse`.
*   **Callback** : `StreamedResponse` utilise un callback. Attention : ce callback est exécuté au moment du `send()`, donc après que le Kernel a fini son travail. Les services injectés doivent être encore valides (attention aux connexions DB fermées si gestion manuelle).

## Ressources
*   [Symfony Docs - Response](https://symfony.com/doc/current/components/http_foundation.html#response)
*   [MDN - HTTP Headers](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers)
