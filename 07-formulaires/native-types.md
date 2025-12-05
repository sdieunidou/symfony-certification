# Types de Formulaires Natifs

## Concept clé
Connaître la liste des types fournis par défaut pour ne pas réinventer la roue.

## Application dans Symfony 7.0
Quelques types importants à connaître :

### Champs Texte
*   `TextType`, `TextareaType`
*   `EmailType`, `IntegerType`, `MoneyType`, `NumberType`, `PasswordType`
*   `PercentType`, `SearchType`, `UrlType`
*   `TelType`, `ColorType`

### Champs Choix
*   `ChoiceType` (select, radios, checkboxes)
*   `EnumType` (pour les Enum PHP 8.1)
*   `EntityType` (choix d'entités Doctrine)
*   `CountryType`, `LanguageType`, `LocaleType`, `TimezoneType`, `CurrencyType`

### Champs Date
*   `DateType`, `DateTimeType`, `TimeType`
*   `BirthdayType`, `WeekType`

### Champs Spéciaux
*   `CheckboxType` (booléen unique)
*   `FileType`
*   `HiddenType`
*   `CollectionType` (liste dynamique d'items)
*   `RepeatedType` (deux champs pour confirmation mot de passe)
*   `ButtonType`, `SubmitType`, `ResetType`

## Points de vigilance (Certification)
*   **DateType** : Par défaut, il rend 3 select (jour, mois, année). Pour avoir un champ input HTML5, utiliser `'widget' => 'single_text'`.
*   **CollectionType** : Le type le plus complexe. Permet d'ajouter/supprimer des éléments via Javascript (`allow_add`, `allow_delete`).

## Ressources
*   [Symfony Docs - Form Types Reference](https://symfony.com/doc/current/reference/forms/types.html)

