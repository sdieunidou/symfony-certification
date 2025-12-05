# Types de Formulaires Natifs

## Concept clé
Symfony fournit une bibliothèque exhaustive de types de champs (`Field Types`) prêts à l'emploi.
Il est essentiel de connaître le bon type pour le bon usage afin de profiter de la validation, transformation et du rendu natifs.

## Classification

### 1. Champs Texte
*   `TextType` : Input simple.
*   `TextareaType` : Zone de texte.
*   `EmailType`, `UrlType`, `TelType` : Input HTML5 spécifiques.
*   `PasswordType` : Masqué (toujours vide à l'affichage).
*   `HiddenType` : Input caché.

### 2. Champs Nombres
*   `IntegerType` : Entier.
*   `NumberType` : Flottant (gère les séparateurs décimaux selon la locale).
*   `MoneyType` : Spécial devises (gère la précision, le symbole).
*   `PercentType` : Pourcentage (multiplie par 100 à l'affichage).

### 3. Champs Date & Temps
*   `DateType` : Date seule.
    *   Option clé : `'widget' => 'single_text'` (Input HTML5 `<input type="date">`).
*   `DateTimeType` : Date + Heure.
*   `TimeType` : Heure seule.
*   `BirthdayType` : Comme DateType, mais avec des années par défaut adaptées.
*   `WeekType` : Input HTML5 week.

### 4. Champs Choix
*   `ChoiceType` : Le couteau suisse (Select, Radio, Checkboxes).
    *   `expanded: true, multiple: true` => Checkboxes.
    *   `expanded: true, multiple: false` => Radios.
    *   `expanded: false` => Select (`multiple` pour multi-select).
*   `EnumType` (Symfony 5.4+) : Mappe directement sur une PHP Enum.
*   `EntityType` (Doctrine Bridge) : Sélection d'entités depuis la DB.
*   `CountryType`, `LanguageType`, `LocaleType`, `CurrencyType`, `TimezoneType`.

### 5. Champs Structurels
*   `CollectionType` : Permet de gérer une liste d'éléments (ex: liste de Tags pour un Article). Nécessite souvent du JS pour ajouter/supprimer des lignes (Prototype).
*   `RepeatedType` : Affiche deux fois le même champ (ex: Mot de passe + Confirmation) et vérifie qu'ils sont identiques.

### 6. Champs Actions
*   `SubmitType`, `ButtonType`, `ResetType`.
    *   *Best Practice* : Ne mettez pas les boutons dans la classe FormType (pour la réutilisabilité), ajoutez-les dans le template Twig.

### 7. Champs Workflow (Symfony 7.4+)
Nouveaux types pour gérer les **Form Flows** (Multi-étapes).
*   `NextFlowType` : Passe à l'étape suivante (submit + validation).
*   `PreviousFlowType` : Revient à l'étape précédente.
*   `FinishFlowType` : Termine le flux.
*   `ResetFlowType` : Réinitialise le flux.

## 🧠 Concepts Clés
1.  **Transformation** : Chaque type vient avec ses DataTransformers. `IntegerType` transforme "12" (string) en `12` (int).
2.  **Options** : Tous les types héritent des options de `FormType` (`label`, `required`, `attr`, `data`, `disabled`, `mapped`, `constraints`).

## ⚠️ Points de vigilance (Certification)
*   **CheckboxType** : Représente un booléen. Si non coché, retourne `false`. Attention : `value` dans les options définit la valeur envoyée *si coché*, pas l'état coché/décoché (c'est l'option `data` qui fait ça).
*   **ChoiceType** : L'option `choices` attend un tableau `[Label => Valeur]`. C'est l'inverse de l'ancienne convention HTML.

## Ressources
*   [Symfony Docs - Form Types Reference](https://symfony.com/doc/current/reference/forms/types.html)
