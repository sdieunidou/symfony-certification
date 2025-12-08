# Création et Organisation des Templates

## Concept clé
L'organisation des fichiers templates suit des conventions strictes pour garantir la maintenabilité et la compatibilité avec l'écosystème Symfony.

## 1. Emplacement (`templates/`)
Par défaut, tous les templates de l'application résident dans le dossier `templates/` à la racine du projet.
*   Le chemin est relatif à ce dossier.
*   `$this->render('blog/index.html.twig')` cherche dans `templates/blog/index.html.twig`.

On peut configurer d'autres chemins via `twig.paths`.

## 2. Nommage
Convention : `nom_du_fichier.format.moteur`
*   **nom_du_fichier** : `snake_case` recommandé (ex: `blog_post.html.twig`).
*   **format** : Le format de sortie (`html`, `xml`, `json`, `txt`, `css`).
*   **moteur** : `twig` (ou `php` mais rare).

Exemples :
*   `index.html.twig`
*   `sitemap.xml.twig`
*   `email_notification.txt.twig`

## 3. Structure Recommandée
```
templates/
├── base.html.twig          # Layout global
├── _partials/              # Fragments réutilisables (Header, Footer)
│   ├── header.html.twig
│   └── flash.html.twig
├── blog/                   # Par section fonctionnelle
│   ├── index.html.twig
│   └── show.html.twig
├── admin/                  # Section Admin
│   └── dashboard.html.twig
└── emails/                 # Templates d'emails
    └── welcome.html.twig
```

## 4. Variables de Template
Twig permet d'accéder aux variables PHP passées par le contrôleur.
L'accès est **unifié** via l'opérateur point `.`.

Quand vous écrivez `user.name` en Twig, il essaie dans l'ordre :
1.  `$user['name']` (Clé de tableau)
2.  `$user->name` (Propriété publique)
3.  `$user->name()` (Méthode)
4.  `$user->getName()` (Getter)
5.  `$user->isName()` (Isser)
6.  `$user->hasName()` (Hasser)
7.  `null` (si rien ne matche, sauf si `strict_variables` est activé).

Cela permet de refactoriser votre code PHP (passer d'un tableau à un objet DTO) sans changer une seule ligne de Twig.

## ⚠️ Points de vigilance (Certification)
*   **Conflit de nom** : Évitez d'avoir deux fichiers avec le même nom dans le même dossier (évident).
*   **Strict Variables** : En environnement `dev`, Twig lance une erreur si une variable n'existe pas. En `prod`, il affiche silencieusement `null` (vide). Utilisez le filtre `default` pour gérer les cas optionnels : `{{ user.bio|default('Pas de bio') }}`.

## Ressources
*   [Symfony Docs - Creating Templates](https://symfony.com/doc/current/templates.html#creating-templates)
