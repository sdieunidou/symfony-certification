# Documentation du Format de Quiz JSON

Ce document décrit la structure standard des fichiers JSON utilisés pour générer les quiz de révision. Chaque fichier de cours `.md` peut être accompagné d'un fichier `.md.json` correspondant.

## Convention de Nommage
*   **Fichier de cours** : `chemin/vers/sujet.md`
*   **Fichier de quiz** : `chemin/vers/sujet.md.json` (doit être dans le même dossier)

## Structure JSON

Le fichier JSON doit contenir un objet racine avec les clés suivantes :

| Clé | Type | Description |
| :--- | :--- | :--- |
| `source_file` | `string` | Le chemin relatif vers le fichier de cours associé (ex: `09-injection-dependances/autowiring.md`). |
| `title` | `string` | Le titre du quiz, généralement le même que le chapitre. |
| `questions` | `array` | Une liste d'objets définissant les questions. |

### Objet Question

Chaque objet dans le tableau `questions` doit avoir la structure suivante :

| Clé | Type | Description |
| :--- | :--- | :--- |
| `id` | `integer` | Identifiant unique de la question dans le fichier (incrémental). |
| `question` | `string` | L'intitulé de la question. |
| `type` | `string` | Le type de question. Valeurs acceptées : `"single_choice"` (radio), `"multiple_choice"` (checkbox). |
| `options` | `array<string>` | La liste des réponses possibles proposées à l'utilisateur. |
| `correct_answers` | `array<string>` | La liste des réponses correctes (doit correspondre exactement aux chaînes dans `options`). |
| `explanation` | `string` | Un texte explicatif affiché après la réponse (pourquoi c'est juste/faux). |

## Exemple Complet

```json
{
  "source_file": "09-injection-dependances/autowiring.md",
  "title": "Autowiring",
  "questions": [
    {
      "id": 1,
      "question": "Quelle est la couleur du cheval blanc d'Henri IV ?",
      "type": "single_choice",
      "options": [
        "Noir",
        "Blanc",
        "Gris"
      ],
      "correct_answers": [
        "Blanc"
      ],
      "explanation": "C'est une question piège classique, mais la réponse est dans la question."
    },
    {
      "id": 2,
      "question": "Quelles sont les méthodes valides pour injecter un service dans un contrôleur ?",
      "type": "multiple_choice",
      "options": [
        "Injection dans le constructeur",
        "Injection dans la méthode d'action (autowiring)",
        "Utilisation de $this->get() (si AbstractController)",
        "Injection dans une propriété privée sans attribut"
      ],
      "correct_answers": [
        "Injection dans le constructeur",
        "Injection dans la méthode d'action (autowiring)"
      ],
      "explanation": "$this->get() ne fonctionne que pour les services publics (rare), et l'injection propriété nécessite #[Required] ou #[Autowire]."
    }
  ]
}
```

## Règles de Validation
1.  Le JSON doit être valide (pas de virgules traînantes).
2.  `correct_answers` ne doit contenir que des valeurs présentes dans `options`.
3.  Pour `single_choice`, `correct_answers` ne doit contenir qu'un seul élément.
4.  L'encodage doit être UTF-8.
