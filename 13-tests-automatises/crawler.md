# Objet Crawler (DomCrawler)

## Concept clé
Le `Crawler` est une librairie puissante pour naviguer dans le DOM HTML/XML retourné par une réponse.
Il permet de "scraper" la page pour vérifier son contenu ou extraire des éléments (liens, formulaires).

## Filtrage (Sélection)
Méthodes inspirées de jQuery.

```php
$crawler = $client->request('GET', '/blog');

// CSS Selector (nécessite le paquet css-selector)
$title = $crawler->filter('h1.title');

// XPath (Natif)
$title = $crawler->filterXPath('//h1[@class="title"]');

// Filtrage avancé
$crawler->filter('div')->eq(0); // Premier div
$crawler->filter('div')->first();
$crawler->filter('div')->last();
$crawler->filter('div')->siblings();
$crawler->filter('div')->children();
```

## Extraction de Données
Une fois le nœud trouvé :

```php
$text = $crawler->filter('h1')->text(); // Contenu texte nettoyé
$html = $crawler->filter('body')->html(); // Contenu HTML
$attr = $crawler->filter('img')->attr('src'); // Attribut
$texts = $crawler->filter('li')->each(fn ($node) => $node->text()); // Tableau de textes
```

## Interaction (Liens et Formulaires)
Le Crawler est le seul moyen d'obtenir les objets spéciaux `Link` et `Form` pour le client.

```php
// Trouver un lien par son texte
$link = $crawler->selectLink('Se connecter')->link();
$client->click($link);

// Trouver un bouton par son texte (submit)
$form = $crawler->selectButton('Envoyer')->form();
// On peut pré-remplir des valeurs ici
$form['name'] = 'Fabien';
$client->submit($form);
```

## 🧠 Concepts Clés
1.  **Exception** : Si un filtre ne trouve rien, il retourne un Crawler vide. Mais si vous appelez `text()` ou `attr()` sur un Crawler vide, une exception est lancée.
2.  **Contexte** : Le Crawler peut être initialisé avec du HTML brut ou une URL. Dans les tests fonctionnels, il est initialisé avec la `Response`.

## ⚠️ Points de vigilance (Certification)
*   **Bouton** : `selectButton` trouve un bouton `<button>` ou un `<input type="submit">` par son texte (value) ou son `id` ou son `alt` (pour les images).

## Ressources
*   [Symfony Docs - DomCrawler](https://symfony.com/doc/current/components/dom_crawler.html)
