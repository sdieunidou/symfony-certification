# Flux de Formulaires Multi-étapes (Form Flows) - Symfony 7.4+

## Concept clé
Nouveauté majeure de Symfony 7.4, le composant **Form Flow** permet de gérer nativement des formulaires multi-étapes (wizards) sans dépendre de bundles tiers complexes.
Il permet de découper un gros formulaire en plusieurs petites étapes logiques, tout en conservant l'état et la validation.

## Fonctionnement
Le concept repose sur :
1.  **Steps** : Chaque étape est un formulaire indépendant.
2.  **Flow** : L'orchestrateur qui enchaîne les étapes.
3.  **Buttons** : Des types de boutons spéciaux pour naviguer (`Next`, `Previous`).

## Implémentation

### 1. Définir les Étapes (FormTypes)
Créez des formulaires classiques pour chaque étape.

```php
// src/Form/Step/RegisterStep1Type.php
class RegisterStep1Type extends AbstractType { ... }

// src/Form/Step/RegisterStep2Type.php
class RegisterStep2Type extends AbstractType { ... }
```

### 2. Configurer le Flow (Controller)
Dans votre contrôleur, utilisez le `FormFlow` builder.

```php
use Symfony\Component\Form\FormFlowInterface;

public function register(Request $request, FormFlowInterface $flow): Response
{
    // Définition du flux
    $flow->addStep('account', RegisterStep1Type::class);
    $flow->addStep('profile', RegisterStep2Type::class);
    $flow->addStep('confirmation', RegisterStep3Type::class);

    // Gestion de la requête
    $flow->handleRequest($request);

    if ($flow->isFinished()) {
        // Tout est valide ! On récupère les données agrégées.
        $data = $flow->getData();
        // ... save ...
        return $this->redirectToRoute('success');
    }

    // Rendu de l'étape courante
    return $this->render('registration/index.html.twig', [
        'form' => $flow->getCurrentStepForm()->createView(),
    ]);
}
```

## Types de Boutons de Flux
Pour naviguer, utilisez les nouveaux types de boutons dans vos FormTypes d'étapes :

*   **`NextFlowType`** : Valide l'étape courante et passe à la suivante.
*   **`PreviousFlowType`** : Revient en arrière (sans valider forcément).
*   **`FinishFlowType`** : Valide la dernière étape et termine le flux.

Exemple :
```php
// RegisterStep1Type
$builder->add('next', NextFlowType::class, ['label' => 'Suivant']);

// RegisterStep2Type
$builder->add('prev', PreviousFlowType::class, ['label' => 'Précédent']);
$builder->add('finish', FinishFlowType::class, ['label' => 'Terminer']);
```

## Validation
Chaque étape est validée indépendamment.
Symfony utilise automatiquement le **nom de l'étape** comme **Groupe de Validation**.
*   Étape 'account' -> Valide le groupe 'account'.
*   Assurez-vous que vos contraintes dans l'entité utilisent ces groupes (ou utilisez `validation_groups` dans le FormType).

## 🧠 Concepts Clés
1.  **Stockage** : Par défaut, les données intermédiaires sont stockées en session (ou autre storage configuré).
2.  **Indépendance** : Chaque étape est isolée. On ne valide pas tout le formulaire à chaque étape, juste l'étape courante.

## ⚠️ Points de vigilance (Certification)
*   **Nouveauté** : C'est très récent (7.4). Assurez-vous de bien connaître les noms des classes (`FormFlowInterface`, `NextFlowType`).
*   **Data Class** : Généralement, on utilise un DTO global pour stocker les données de toutes les étapes, passé au `createFlow($dto)`.

## Ressources
*   [Symfony Blog - Multi-step forms](https://symfony.com/blog/new-in-symfony-7-4-multi-step-forms)
