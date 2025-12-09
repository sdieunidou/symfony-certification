# Composant Symfony AI

## Concept clé
Symfony AI est un écosystème complet pour intégrer l'Intelligence Artificielle dans les applications PHP. Il ne se limite pas à un simple client API, mais fournit une abstraction robuste pour gérer les modèles, les agents, la mémoire conversationnelle et les données vectorielles.

## Architecture Complète
L'écosystème est modulaire :
1.  **Platform Component** : Interface unifiée bas niveau pour parler aux LLMs (OpenAI, Gemini, Mistral, etc.).
2.  **Agent Component** : Framework haut niveau pour créer des agents autonomes capables d'utiliser des outils.
3.  **Chat Component** : Gestion de l'état des conversations (historique, persistance).
4.  **Store Component** : Abstraction pour le stockage vectoriel (RAG).
5.  **AI Bundle** : Intégration Symfony (Config, Injection de dépendances).
6.  **MCP Bundle** : Intégration du *Model Context Protocol* (Standard ouvert pour connecter les modèles aux données).

## Installation
```bash
composer require symfony/ai-bundle
```

---

## 1. Platform Component (`symfony/ai-platform`)
C'est la couche bas niveau qui abstrait les fournisseurs d'IA.
*   **Rôle** : Envoi de prompts, gestion des embeddings, transcription audio, génération d'images.
*   **Providers Supportés** : OpenAI, Anthropic, Google Gemini, Azure, AWS Bedrock, Mistral, Ollama, Replicate, etc.
*   **Modèles** : Supporte les modèles de texte (GPT-4), d'image (Dall-E), d'audio (Whisper) et d'embeddings.

**Utilisation directe (sans Agent) :**
```php
use Symfony\AI\Platform\PlatformFactory;

$platform = PlatformFactory::create($_ENV['OPENAI_API_KEY']);
$result = $platform->invoke('gpt-4o', 'Quelle est la capitale de la France ?');
echo $result->getContent();
```

**Fonctionnalités Avancées :**
*   **Caching** : Mise en cache des réponses pour économiser les coûts.
*   **Streaming** : Réception des tokens en temps réel.
*   **Parallélisme** : Exécution simultanée de plusieurs requêtes.

---

## 2. Agent Component (`symfony/ai-agent`)
C'est le cerveau de l'application. Un agent est un LLM doté de "capacités" (Outils).

### Tool Calling (Appel d'outils)
L'agent peut décider d'appeler une fonction PHP pour récupérer des données dynamiques (Météo, Base de données) avant de répondre.

```php
use Symfony\AI\Agent\Attribute\Tool;

class WeatherService
{
    #[Tool(description: "Donne la température actuelle pour une ville donnée")]
    public function getTemperature(string $city): int
    {
        return match($city) {
            'Paris' => 15,
            'Lyon' => 18,
            default => 20
        };
    }
}
```
L'agent détectera automatiquement cet outil via l'attribut `#[Tool]`.

### Workflows & Mémoire
L'agent gère le cycle de vie :
1.  Réception du message utilisateur.
2.  Analyse : A-t-il besoin d'un outil ?
3.  Si oui -> Exécution de l'outil PHP -> Injection du résultat dans le contexte -> Nouvel appel au LLM.
4.  Réponse finale.

---

## 3. Chat Component (`symfony/ai-chat`)
Gère la persistance et l'état des conversations.
*   **Conversation History** : Stocke les échanges passés pour que l'IA ait le contexte ("De quoi on parlait juste avant ?").
*   **Storage** : Supporte Doctrine, Redis, ou Filesystem pour sauvegarder les chats.

---

## 4. Store Component (`symfony/ai-store`)
Gère le stockage vectoriel pour le **RAG (Retrieval Augmented Generation)**.
Le RAG permet à l'IA de répondre sur vos données privées (PDFs, Documentation interne) sans re-training.

*   **Vector Stores Supportés** : ChromaDB, Pinecone, Weaviate, MongoDB Atlas, Doctrine (via `pgvector` ou autre).
*   **Embeddings** : Transforme vos textes en vecteurs numériques via le Platform Component pour la recherche sémantique.

**Flux RAG Typique :**
1.  Utilisateur pose une question.
2.  Application convertit la question en vecteur.
3.  Store cherche les documents les plus proches sémantiquement.
4.  Agent reçoit la question + les documents trouvés en contexte.
5.  Agent répond en utilisant ces informations.

---

## 5. MCP Bundle (Model Context Protocol)
Intègre le standard MCP qui permet de standardiser la connexion entre les modèles d'IA et les sources de données (Google Drive, Slack, Git, etc.).
*   Permet à votre agent Symfony d'agir comme un **Serveur MCP** (exposer vos données aux IA externes).
*   Ou comme un **Client MCP** (utiliser des serveurs MCP existants comme outils).

---

## Configuration (`ai.yaml`)

```yaml
ai:
    # Configuration des plateformes (Clés API)
    platform:
        openai:
            api_key: '%env(OPENAI_API_KEY)%'
            
    # Configuration des Agents
    agent:
        default:
            model: 'gpt-4o-mini'
            temperature: 0.7
            # Outils activés pour cet agent
            tools: 
                - 'App\Service\WeatherService'
                
    # Configuration du Store (Vector DB)
    store:
        default:
            provider: 'chromadb'
            host: '%env(CHROMADB_URL)%'
```

## Fonctionnalités Transverses
*   **Structured Output** : Force le modèle à retourner du JSON strict ou un objet PHP, vital pour l'intégration API.
*   **Testing** : `InMemoryPlatform` et `MockAgent` pour tester sans appels API réels.
*   **Profiling** : Intégration dans le Web Profiler Symfony pour voir les prompts, les coûts (tokens), et les outils appelés.

## Ressources
*   [Platform Documentation](https://symfony.com/doc/current/ai/components/platform.html)
*   [Agent Documentation](https://symfony.com/doc/current/ai/components/agent.html)
*   [Chat Documentation](https://symfony.com/doc/current/ai/components/chat.html)
*   [Store Documentation](https://symfony.com/doc/current/ai/components/store.html)
*   [AI Bundle](https://symfony.com/doc/current/ai/bundles/ai-bundle.html)
