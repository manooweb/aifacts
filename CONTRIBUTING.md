# Proposer un AI Fact

Merci de vouloir enrichir la collection ! 🤖

## Ce qu'on cherche

Un bon AI Fact, c'est :

- **une accroche** qui reprend le discours « l'IA sait tout faire » : *Claude a remplacé une équipe de 10 développeurs.*
- **une chute** qui le retourne : *Il en a fallu 12 pour relire son code.*

Quelques règles :

- **Court** : chaque partie tient en une phrase.
- **En français**, avec de vraies majuscules et la ponctuation qui va bien.
- **De l'humour de dev, pas de la méchanceté** : on se moque du battage, pas des gens. Aucune personne réelle nommée, rien d'insultant ni de discriminatoire.
- **Original** : pas de copie d'un fact existant, ni d'une blague trouvée ailleurs.
- Les facts peuvent viser n'importe quelle IA (Claude, ChatGPT, Copilot, Gemini…).

## Option 1 : sans Git

Ouvre une issue avec le modèle **[Nouveau fact](../../issues/new?template=nouveau-fact.yml)**. Il suffit de remplir deux champs.

## Option 2 : par pull request

Ajoute ton fact **à la fin** de [`facts/facts.json`](facts/facts.json), sur une seule ligne, **sans `id`** (le numéro est attribué à la publication) :

```json
{"setup": "Ton accroche.", "punchline": "Ta chute.", "status": "propose", "author": "ton-pseudo-github"}
```

- `status` vaut toujours `"propose"`.
- `author` est facultatif. Il sert à te créditer si ton fact est publié.
- Un fact par pull request, c'est plus simple à relire.

## Et ensuite ?

Les propositions sont relues à la main. Les meilleures sont publiées un vendredi sur LinkedIn et sur [aifacts.fr](https://aifacts.fr), avec ton nom.

En proposant un fact, tu acceptes qu'il soit publié sous licence [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/deed.fr).
