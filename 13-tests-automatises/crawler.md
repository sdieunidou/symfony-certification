# Objet Crawler

## Concept clé
Le `Crawler` est un objet permettant de naviguer et d'extraire des informations du DOM HTML (ou XML) retourné par une réponse. C'est le "jQuery" de PHPUnit.

## Application dans Symfony 7.0

```php
$crawler = $client->request('GET', '/blog');

// Filtrage CSS ou XPath
$title = $crawler->filter('h1.post-title')->text();
$link = $crawler->filter('a:contains("Lire la suite")')->link();

// Navigation
$crawler = $client->click($link);

// Formulaires
$form = $crawler->selectButton('Valider')->form();
$form['name'] = 'Symfony';
$client->submit($form);
```

## Points de vigilance (Certification)
*   **Exceptions** : `filter()` ne lance pas d'exception si rien n'est trouvé (retourne un crawler vide), mais `text()` lancera une exception si le nœud est vide. Il faut vérifier `count() > 0` ou utiliser `filter(...)->first()`.
*   **Composant** : Le Crawler est un composant autonome (`symfony/dom-crawler`).

## Ressources
*   [Symfony Docs - Crawler](https://symfony.com/doc/current/components/dom_crawler.html)

