# Authentification (AuthN)

## Concept clé
L'authentification répond à la question : **"Qui êtes-vous ?"**.
C'est le processus de vérification de l'identité d'un utilisateur (Login, API Token, LDAP...).

## Application dans Symfony 7.0
L'authentification est gérée par le système d'**Authenticators** (introduit en Symfony 5.1, standard depuis 6.0).

Le flux :
1.  La requête entre dans le Firewall.
2.  Les **Authenticators** configurés (ex: `form_login`, `json_login`, `custom`) essaient d'extraire des identifiants de la requête.
3.  S'ils trouvent, ils créent un **Passport** (qui contient l'utilisateur et les badges de sécurité comme CSRF ou Password).
4.  Si le Passport est valide, un **Token** est créé et stocké dans le `TokenStorage` (et souvent en Session).

## Points de vigilance (Certification)
*   **TokenStorage** : Le service qui contient le token de l'utilisateur authentifié.
*   **Session** : Par défaut, l'authentification est "stateful" (token stocké en session). Pour une API, on utilise souvent "stateless" (pas de session, authentification à chaque requête).

## Ressources
*   [Symfony Docs - Authentication](https://symfony.com/doc/current/security.html#authentication-identifying-the-user)

