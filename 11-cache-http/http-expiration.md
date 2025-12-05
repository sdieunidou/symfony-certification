# Modèle d'Expiration

## Concept clé
Le modèle d'expiration est la forme la plus simple et la plus efficace de cache HTTP.
Le serveur déclare : **"Cette ressource est fraîche pendant X secondes"**.
Tant que le délai n'est pas écoulé, le client (ou proxy) utilise sa copie locale **sans même contacter le serveur**.

## En-têtes HTTP

### 1. `max-age` (Cache-Control)
Définit la durée de vie en secondes pour **tous** les caches (privés et partagés).
```
Cache-Control: max-age=3600
```

### 2. `s-maxage` (Cache-Control)
Définit la durée de vie uniquement pour les caches **Shared** (Partagés : CDN, Varnish).
Le "s" signifie "Shared".
*   Si présent, les proxies l'utilisent et ignorent `max-age`.
*   Les navigateurs (privés) l'ignorent et utilisent `max-age`.

Cela permet de cacher une page 1h sur le CDN (`s-maxage=3600`) mais seulement 5min dans le navigateur (`max-age=300`) pour permettre des mises à jour plus rapides si besoin.

### 3. `Expires`
Date d'expiration absolue (Legacy HTTP 1.0).
`Expires: Thu, 01 Dec 1994 16:00:00 GMT`.
Si `Cache-Control: max-age` est présent, `Expires` est ignoré par les clients modernes. Symfony calcule souvent `Expires` automatiquement pour la compatibilité.

## Application dans Symfony 7.0

```php
$response->setMaxAge(60);       // max-age=60
$response->setSharedMaxAge(3600); // s-maxage=3600, public
```

## 🧠 Concepts Clés
1.  **Efficacité maximale** : 0 requête réseau.
2.  **Inconvénient** : Invalidation difficile. Une fois que le navigateur a le fichier pour 1h, vous ne pouvez pas lui dire de l'effacer avant 1h (sauf si l'utilisateur vide son cache). C'est pourquoi on utilise le **Versioning** pour les assets (changer l'URL invalide le cache).

## ⚠️ Points de vigilance (Certification)
*   **Calcul de l'âge** : L'âge est calculé par rapport à la date de génération (`Date` header), pas la date de réception.
*   **Heure** : Tout repose sur une synchronisation d'horloge correcte (NTP), bien que `max-age` (delta en secondes) soit plus robuste que `Expires` (date absolue) face aux décalages horaires.

## Ressources
*   [Symfony Docs - Expiration](https://symfony.com/doc/current/http_cache/expiration.html)
