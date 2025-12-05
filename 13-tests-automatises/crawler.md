# Objet Crawler (DomCrawler)

## Concept clé
Le `Crawler` permet de naviguer dans le DOM HTML/XML retourné par une réponse, de filtrer les éléments et d'extraire des données.

## Filtrage (Sélection)

```php
$crawler = $client->request('GET', '/blog');

// CSS Selector (nécessite css-selector component)
$title = $crawler->filter('h1.title');

// XPath
$title = $crawler->filterXPath('//h1[@class="title"]');

// Traversée
$crawler->filter('div')->eq(0);
$crawler->filter('div')->first();
$crawler->filter('div')->children();
```

## Extraction de Données

```php
$text = $crawler->filter('h1')->text();
$html = $crawler->filter('body')->html();
$src  = $crawler->filter('img')->attr('src');

// Boucler sur des résultats
$titles = $crawler->filter('h2')->each(function ($node, $i) {
    return $node->text();
});
```

## Interaction : Liens
Pour cliquer, il faut d'abord obtenir un objet `Link`.

```php
// Via le texte du lien (ou alt d'une image)
$link = $crawler->selectLink('Se connecter')->link();

// Accès aux infos
$uri = $link->getUri();

// Clic
$client->click($link);
```

*Raccourci client : `$client->clickLink('Se connecter');`*

## Interaction : Formulaires
Pour soumettre, il faut obtenir un objet `Form` via un bouton (submit).

```php
// Sélectionner le bouton par son texte, id ou value
$buttonCrawlerNode = $crawler->selectButton('Envoyer');
$form = $buttonCrawlerNode->form();

// Remplir les champs
// Notation tableau (nom du champ HTML)
$form['my_form[name]'] = 'Fabien';
$form['my_form[subject]'] = 'Symfony rocks!';

// Cocher une case
$form['my_form[terms]']->tick();

// Sélectionner une option (select/radio)
$form['my_form[country]']->select('France');

// Uploader un fichier
$form['my_form[photo]']->upload('/path/to/photo.jpg');
// Upload multiple
$form['my_form[gallery][0]']->upload('/path/1.jpg');

// Soumettre
$client->submit($form);
```

*Raccourci client : `$client->submitForm('Envoyer', ['my_form[name]' => 'Fabien']);`*

## Récupérer les valeurs
Vous pouvez inspecter ce que contient le formulaire avant envoi :

```php
// Valeurs brutes
$values = $form->getValues();

// Valeurs formatées PHP (tableaux associatifs)
$phpValues = $form->getPhpValues();
$files = $form->getFiles();
```

## ⚠️ Points de vigilance (Certification)
*   **Sélection** : On sélectionne toujours le **bouton** de soumission (`selectButton`), jamais la balise `<form>` directement.
*   **Scope** : Si vous utilisez `$crawler->filter('#my-form')->selectButton(...)`, la recherche du bouton est limitée à la partie filtrée.
