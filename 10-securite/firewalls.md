# Firewalls (Pare-feu)

## Concept clé
Un Firewall définit une zone de sécurité de votre application (un ensemble d'URLs).
Il gère l'authentification (Login) et le contexte de sécurité pour cette zone.

## Application dans Symfony 7.0
Chaque firewall a :
*   Un `pattern` (Regex d'URL).
*   Un `provider` (Source des utilisateurs).
*   Des `authenticators` (Méthodes de login : `form_login`, `json_login`, `http_basic`, `custom_authenticator`).
*   Un `entry_point` (Où rediriger si non connecté : page de login).

### Context
Les firewalls sont isolés. Si vous vous connectez sur le firewall `main`, vous n'êtes **pas** connecté sur le firewall `admin` (sauf si configuré explicitement avec `context` partagé).

### Stateless
Pour les APIs, on configure souvent `stateless: true`. Symfony ne créera pas de cookie de session. L'authentification doit être fournie à chaque requête (ex: Bearer Token).

## Points de vigilance (Certification)
*   **Security False** : `security: false` désactive complètement le moteur de sécurité pour ce pattern. Les listeners firewall ne s'exécutent pas. Utile pour les assets ou le profiler (gain de perf).
*   **Access Control** : Le firewall ne décide pas (tout seul) qui a le droit d'entrer (ça c'est `access_control` ou les voters), il décide juste "Est-ce que je connais cet utilisateur ?".

## Ressources
*   [Symfony Docs - Firewalls](https://symfony.com/doc/current/security.html#firewalls)

