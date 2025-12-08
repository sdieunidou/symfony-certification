# Formulaires : Fonctionnement Interne

## Concept clé
Le composant Form convertit des données (Requête HTTP) en objets PHP (Model Data) via un processus complexe de transformation et de validation.

## Architecture et Classes Clés

### 1. FormFactory
L'usine qui crée les formulaires. C'est le point d'entrée (`$this->createForm(...)`).

### 2. FormBuilder
L'objet utilisé pour configurer un formulaire.
*   On lui ajoute des champs (`add()`), des Event Listeners, et des Data Transformers.
*   La méthode `getForm()` "gèle" le builder et retourne l'instance finale `Form`.

### 3. Form (`FormInterface`)
L'instance du formulaire à l'exécution. C'est une structure en arbre (Composite Pattern) : un formulaire contient des enfants (champs), qui sont eux-mêmes des formulaires.

### 4. DataMapper
Responsable de lire/écrire les données dans l'objet sous-jacent.
*   Par défaut (`PropertyPathMapper`), il utilise les getters/setters ou propriétés publiques de l'entité.

### 5. DataTransformers
Ils convertissent la donnée entre trois formats :
1.  **Model Data** : La donnée dans votre objet (ex: objet `DateTime`).
2.  **Norm Data** : Format normalisé (ex: string `"2023-01-01"`).
3.  **View Data** : Format affiché dans le HTML (ex: string `"01/01/2023"` ou tableau pour un champ date éclaté).

## Le Flux de Soumission (`handleRequest`)

1.  **Submission** : Le formulaire vérifie si la requête contient des données pour lui (généralement via le nom du formulaire).
2.  **Pre-Submit (Event)** : Possibilité de modifier les données brutes de la requête.
3.  **Transformation (View -> Norm -> Model)** : Les Data Transformers inversés sont appelés.
4.  **Submit (Event)** : Les données sont converties mais pas encore mapées dans l'objet.
5.  **Mapping** : Le `DataMapper` écrit les données dans l'objet (`$user->setName(...)`).
6.  **Post-Submit (Event)** : L'objet est hydraté.
7.  **Validation** : Le formulaire appelle le composant Validator sur l'objet (et sur les contraintes ajoutées au formulaire).

## 🧠 Concepts Clés
1.  **Unidirectionnel** : Par défaut, le mapping se fait dans les deux sens (Objet -> Form -> Objet).
2.  **Synchronisation** : `handleRequest` ne fait rien si la requête n'est pas une soumission (ex: méthode GET pour un form POST).
3.  **FormRegistry** : C'est le service qui connaît tous les `FormType` disponibles.

## ⚠️ Points de vigilance (Certification)
*   **Dynamic Fields** : Pour modifier un formulaire dynamiquement (ex: champs dépendants), il faut utiliser les **Form Events** (`PRE_SET_DATA`, `PRE_SUBMIT`), pas le constructeur du Type, car la structure est figée après le build.
*   **Validation** : La validation du formulaire déclenche la validation de l'objet sous-jacent (via le groupe `Default` ou `validation_groups`).

## Ressources
*   [Symfony Docs - Form Events](https://symfony.com/doc/current/form/events.html)
*   [Data Transformers](https://symfony.com/doc/current/form/data_transformers.html)
