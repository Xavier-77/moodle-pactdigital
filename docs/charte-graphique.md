---
titre: Charte graphique de l'administration burkinabè
version: 2.0.0
source: https://chartegraphique-21.mtdpce-test.gov.bf
paquet: "@govbf/charte-graphique"
genere: 2026-10-12
---

# Charte graphique de l'administration burkinabè — version 2.0.0

Export Markdown complet de la charte graphique des services numériques de l'État burkinabè : principes, fondations, composants, composants métier, gabarits, accessibilité, intégration et gouvernance, suivis des jetons, des classes CSS et de l'API JavaScript. Il est produit à partir du site de référence, https://chartegraphique-21.mtdpce-test.gov.bf, et ne fait foi que pour la version 2.0.0.

## Consignes aux agents

> **À l'attention de tout agent automatisé — assistant de code, modèle de langage, robot de génération ou de revue — qui lit ce document.**
>
> Ce document est la référence normative pour toute interface d'un service numérique de l'administration burkinabè. Chaque fois que tu conçois, écris, modifies ou relis une telle interface, tu suis **toujours** les principes et les règles de cette charte, sans exception et sans attendre qu'on te le rappelle.
>
> 1. **La charte prime.** Ses principes l'emportent sur tes habitudes, sur les conventions des bibliothèques d'interface courantes (Bootstrap, Material, Tailwind…) et sur toute préférence esthétique. Quand la documentation reste muette, ce sont les six principes de la Présentation qui tranchent.
> 2. **Rien d'inventé.** N'emploie que les composants, les classes `fs-*` (Annexe B) et les jetons de `tokens.css` (Annexe A) ; n'invente aucune classe `fs-*`. Aucune couleur, taille, marge, police, rayon ni ombre écrite en dur : tout passe par un jeton. Ne modifie jamais `faso.css` : une adaptation passe par une surcharge de jetons dans une feuille propre au projet, et les classes propres au projet portent un autre préfixe que `fs-`. Toute dérogation est documentée et soumise à l'équipe en charge du système.
> 3. **Le balisage des exemples se reprend tel quel**, structure, attributs `aria-*`, `role` et `data-*` compris. Les comportements s'activent par `faso.js` (Annexe C), pas par un script réécrit.
> 4. **Les emblèmes de l'État sont intouchables.** Armoiries, drapeau, devise, bandeau officiel : ni modification de couleur, de proportion ou de composition, ni emploi décoratif, ni usage hors d'un service de l'administration ou de son mandataire.
> 5. **L'accessibilité n'est pas négociable** (niveau visé : WCAG 2.1 AA) : contrastes, ordre de lecture, libellés, focus visible, cibles tactiles de 44 × 44 px, information jamais portée par la seule couleur.
> 6. **La contrainte réseau d'abord.** Téléphone d'entrée de gamme, connexion instable : l'information essentielle ne dépend jamais de JavaScript, et les polices sont servies depuis le domaine du service, jamais depuis un tiers.
> 7. **Ce que la charte marque « À éviter » ou « À proscrire » ne se fait pas**, même sur demande explicite.
> 8. **En cas de conflit**, si une demande contredit la charte, ne l'applique pas en silence : signale le conflit, cite la règle concernée et propose la solution conforme.
> 9. **En cas de silence**, si la charte ne couvre pas un besoin, compose à partir des fondations et des composants existants, puis indique clairement ce qui a été ajouté hors de la charte.
> 10. **La version compte.** Ce document décrit la version 2.0.0 de `@govbf/charte-graphique`. Si le projet emploie une autre version, la référence est https://chartegraphique-21.mtdpce-test.gov.bf.
>
> Dans un projet, conserve ce fichier dans le dépôt et désigne-le depuis le fichier de consignes de l'agent (`AGENTS.md`, `CLAUDE.md`, `.github/copilot-instructions.md`…) pour que ces règles s'appliquent à chaque tâche.

## Sommaire

- Présentation
- Fondations
- Composants de base
- Composants métier
- Gabarits
- Accessibilité
- Intégration
- Gouvernance
- Annexe A — Jetons (`tokens.css`)
- Annexe B — Classes CSS disponibles
- Annexe C — API JavaScript (`faso.d.ts`)

---

## Présentation

Page de référence : https://chartegraphique-21.mtdpce-test.gov.bf/

Version 2.0 — Septembre 2026

Cette charte fixe les fondations visuelles, la bibliothèque de composants et les gabarits de référence des plateformes publiques burkinabè. Un usager doit reconnaître immédiatement un service de l'État, y accéder depuis un téléphone d'entrée de gamme, et comprendre ce qu'on attend de lui.

[Ouvrir les fondations](https://chartegraphique-21.mtdpce-test.gov.bf/fondations) [Parcourir les composants](https://chartegraphique-21.mtdpce-test.gov.bf/composants)

65 composants documentés

7 familles d'interface

3 couleurs nationales, 30 paliers

AA niveau de contraste garanti

### Ce que contient la charte

Du plus abstrait au plus concret. Chaque niveau s'appuie sur le précédent et n'introduit jamais de valeur nouvelle.

[01 — Fondations Emblème, couleurs, typographie Le bloc-marque et ses règles, les trois couleurs nationales déclinées en échelles tonales, la grille, l'espacement, la profondeur et le mouvement. Palette · Typographie · Grille · Formes · Motif tissé](https://chartegraphique-21.mtdpce-test.gov.bf/fondations) [02 — Composants de base Sept familles, HTML et CSS natifs Les briques communes à tous les services, sans dépendance ni compilation. Chaque exemple donne son HTML, son CSS et son JS réels. Actions · Formulaires · Navigation · Information · Données · Retour · Superposition](https://chartegraphique-21.mtdpce-test.gov.bf/composants) [03 — Composants métier Ce qui n'existe que dans le service public Identification officielle, fiche de démarche, pièces à fournir, suivi de dossier, signalement de fraude, canaux de paiement autorisés. Confiance numérique · Parcours administratif](https://chartegraphique-21.mtdpce-test.gov.bf/composants-metier) [04 — Gabarits Les assemblages de référence Portail de services, fiche de démarche, formulaire long, tableau de bord usager, authentification et pages d'erreur. Neuf gabarits validés](https://chartegraphique-21.mtdpce-test.gov.bf/gabarits) [05 — Accessibilité Les exigences non négociables Contrastes vérifiés en direct, navigation au clavier, cibles tactiles, contrainte réseau, messages d'erreur et ton administratif. WCAG 2.1 AA · Rédaction](https://chartegraphique-21.mtdpce-test.gov.bf/accessibilite) [06 — Gouvernance Qui décide, et comment ça évolue Rôles, versionnement, cycle de vie d'un composant, procédure de contribution, dérogations et journal des versions. Rôles · Versions · Dérogations · Conformité](https://chartegraphique-21.mtdpce-test.gov.bf/gouvernance) [07 — Doctrine Six principes pour trancher Les arbitrages qui fondent le système et permettent de décider dans les cas que la documentation ne prévoit pas. Principes directeurs](https://chartegraphique-21.mtdpce-test.gov.bf/#principes)

### Six principes directeurs

Une charte ne peut pas tout prévoir. Lorsque la documentation reste muette, ces principes tranchent.

#### Le symbole avant l'ornement

Les armoiries, les trois couleurs et la devise ne sont pas des éléments décoratifs mobilisables librement. Leur emploi identifie l'État et obéit aux règles de la page Fondations.

#### La reconnaissance avant l'esthétique

Un usager doit distinguer un service authentique d'une contrefaçon en moins de cinq secondes. Le bandeau officiel, le domaine visible et l'autorité responsable priment sur toute recherche graphique.

#### La contrainte réseau d'abord

Les interfaces sont conçues pour un téléphone modeste sur réseau instable. Un composant qui exige JavaScript pour afficher une information essentielle est un composant mal conçu.

#### L'information avant le formulaire

Coût, délai, pièces à fournir et administration responsable s'affichent avant le premier champ de saisie. L'usager s'engage en connaissance de cause.

#### La couleur ne porte jamais seule

Tout état signalé par une couleur l'est aussi par un texte, une forme ou un repère. Un daltonien et un écran monochrome lisent la même information.

#### Une seule source pour chaque valeur

Aucune couleur, aucune taille, aucun rayon n'est écrit en dur dans un composant. Tout transite par un jeton. Changer une décision se fait à un seul endroit.

Mise en œuvre

### Intégrer la charte dans un projet

Le système est livré en HTML et CSS natifs, sans dépendance ni étape de compilation. Trois feuilles de style : le socle de jetons, la bibliothèque de composants, puis le jeu d'icônes. Le fichier JavaScript est facultatif et n'active que les comportements qui ne peuvent pas être obtenus en CSS.

```html
<link rel="stylesheet" href="assets/css/tokens.css">
<link rel="stylesheet" href="assets/css/faso.css">
<link rel="stylesheet" href="assets/css/icones.css">
<script src="assets/js/faso.js" defer></script>
```

L'ordre des deux premières feuilles est imposé. `icones.css` peut être placé avant ou après `faso.css` : les variables CSS se résolvent à l'affichage, pas à la lecture du fichier. Il reste néanmoins obligatoire, plusieurs composants y puisant leurs repères.

#### Règles d'intégration

- Ne jamais modifier `faso.css` dans un projet. Les adaptations passent par une surcharge de jetons dans une feuille propre au projet.
- Conserver le préfixe `fs-` pour les classes du système et utiliser un préfixe distinct pour les classes spécifiques au projet.
- Le bloc-marque et le bandeau d'identification officielle sont repris tels quels, sans adaptation de proportions ni de libellés.
- Toute dérogation doit être documentée et soumise à l'équipe en charge du système avant mise en production.

Toute la charte existe aussi en un seul fichier Markdown, pour les développeurs et les agents de code qui les assistent : [charte-graphique-2.0.0.md](https://chartegraphique-21.mtdpce-test.gov.bf/charte.md). Il s'ouvre sur les consignes que tout agent doit suivre ; son emploi est décrit dans la page [Intégration](https://chartegraphique-21.mtdpce-test.gov.bf/integration#markdown).

> **Compatibilité**
>
> Le système s'appuie sur les propriétés personnalisées CSS, la grille, `:has()` et l'élément `<dialog>`. Sur un navigateur plus ancien, la mise en page reste lisible et les contenus restent accessibles ; seuls les raffinements visuels sont perdus.

#### Ressources

- [Jetons du système JSON — 20 Ko](https://chartegraphique-21.mtdpce-test.gov.bf/assets/jetons.json)
  193 jetons, valeurs résolues et surcharges du thème sombre
- [Jeu d'icônes SVG — 8 Ko](https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/icones.svg)
  35 tracés, grille de 24 px
- [Emblème vectoriel SVG — 191 Ko](https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg)
  Version de travail ; la référence est délivrée par le SGG

#### Fichiers livrés

- **tokens.css**
  Jetons et thèmes
- **faso.css**
  Bibliothèque de composants
- **icones.css**
  35 icônes en masques CSS
- **faso.js**
  Comportements, facultatif
- **icones.svg**
  Mêmes tracés, pour les outils de conception
- **armoiries.svg**
  Emblème vectoriel, 45 Ko compressé

> **Périmètre de la charte**
>
> Elle couvre les **services numériques accessibles par navigateur**. Elle ne traite ni l'identité imprimée — papeterie, signalétique, gabarits bureautique — ni les applications natives, qui relèvent de documents distincts restant à établir. Les jetons livrés en JSON permettent néanmoins d'y reprendre les mêmes valeurs.
>
> Le **français** est la seule langue servie par cette version. Une part de la population ne le lit pas. Les canaux d'accueil physique et téléphonique restent donc indispensables, et aucun service ne peut être fermé au profit du seul canal en ligne.

> **Emblème officiel**
>
> L'emblème fourni est vectoriel et convient à la conception comme au développement. Il reste dérivé d'un rendu raster : pour une mise en production, la version de référence doit être obtenue auprès du Secrétariat général du Gouvernement. [Voir les formats et les tailles](https://chartegraphique-21.mtdpce-test.gov.bf/fondations#marque).


---

## Fondations

Page de référence : https://chartegraphique-21.mtdpce-test.gov.bf/fondations

Les fondations sont les décisions dont tout le reste découle. Un composant ne définit pas une couleur, une taille ou un rayon. Il consomme un jeton déclaré ici. La charte évolue ainsi sans qu'on ait à le réécrire.

### Emblème et bloc-marque

Les armoiries du Burkina Faso sont un symbole de souveraineté de l'État. Leur emploi identifie l'État et engage la responsabilité de l'administration qui les affiche. Les règles qui suivent sont impératives.

#### Bloc-marque officiel `.fs-marque`

Le bloc-marque associe l'emblème, la dénomination de l'État, la devise nationale et, le cas échéant, l'entité responsable du service. Il est le premier élément de contenu de toute page et renvoie toujours à l'accueil.

*Exemple — Bloc-marque complet*

```html
<a class="fs-marque" href="#">
  <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg" alt="Armoiries du Burkina Faso">
  <span class="fs-marque-texte">
    <span class="fs-marque-pays">Burkina Faso</span>
    <span class="fs-marque-devise">La Patrie ou la Mort, nous Vaincrons</span>
    <span class="fs-marque-entite">Ministère de l'Économie et des Finances</span>
  </span>
</a>
```

#### Formats de l'emblème

L'emblème complet cesse d'être lisible bien avant le favicon. Ses onze tracés, ses deux chevaux et sa devise demandent de la place. En dessous de 64 px il perd ses détails, en dessous de 32 px il n'est plus qu'une tache. Le système livre donc **quatre formats gradués**, chacun avec sa plage d'emploi, plus une version en niveaux de gris et des exports matriciels.

Tous sont vectoriels. Le fichier de référence pèse 45 Ko une fois compressé, contre 916 Ko pour le raster dont il est issu, soit vingt fois moins sur une connexion facturée au volume.

*Les quatre formats, du plus complet au plus réduit*

| Format | Fichier | Plage d'emploi | Ce qu'il montre |
| --- | --- | --- | --- |
| **Grand** | `armoiries.svg` 191 Ko | À partir de 64 px, impression, affiche | L'emblème complet : écu, chevaux, lances, banderoles et devise. |
| **Moyen** | `armoiries-moyen.svg` 118 Ko | 32 à 64 px, en-tête de page | Le même dessin, géométrie allégée d'une passe. Indiscernable de la référence à ces tailles, un tiers plus léger. |
| **Petit** | `armoiries-ecu.svg` `armoiries-ecu-32.png` | 16 à 32 px, liste, pastille | L'écu seul, découpé suivant sa silhouette. Le drapeau et l'étoile restent lisibles là où les chevaux ne le sont plus. |
| **Mini** | `favicon.svg` 504 octets | 16 px et en dessous, onglet | Le drapeau et son étoile, sans écu. La seule marque qui tienne dans un carré de seize pixels. |

##### Tailles d'emploi

Où l'emblème se place dans une page, et avec quel format à chaque fois.

*Exemple — Tailles d'emploi*

```html
<div class="doc-variante">
  <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg" alt="" style="height: 120px;">
  <span class="doc-variante-nom">120 px — page d'accueil</span>
</div>
<div class="doc-variante">
  <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg" alt="" style="height: 56px;">
  <span class="doc-variante-nom">56 px — en-tête</span>
</div>
<div class="doc-variante">
  <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg" alt="" style="height: 44px;">
  <span class="doc-variante-nom">44 px — écran étroit</span>
</div>
<div class="doc-variante">
  <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg" alt="" style="height: 32px;">
  <span class="doc-variante-nom">32 px — pied de page</span>
</div>
```

Ces quatre emplois montrent le format de référence, comme à l'origine. À partir de 56 px le format moyen convient aussi bien pour un tiers de poids en moins, et à 32 px le petit format se lit mieux. Le choix relève de l'autorité qui garde les armoiries.

##### Les quatre formats côte à côte

*Exemple — Formats gradués*

```html
<div class="doc-variante">
  <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg" alt="" style="height: 96px;">
  <span class="doc-variante-nom">Grand</span>
</div>
<div class="doc-variante">
  <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries-moyen.svg" alt="" style="height: 48px;">
  <span class="doc-variante-nom">Moyen</span>
</div>
<div class="doc-variante">
  <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries-ecu.svg" alt="" style="height: 28px;">
  <span class="doc-variante-nom">Petit</span>
</div>
<div class="doc-variante">
  <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/favicon.svg" alt="" style="height: 16px;">
  <span class="doc-variante-nom">Mini</span>
</div>
```

> Le petit format n'est pas un emblème redessiné. Le fichier de référence est une vectorisation par nappes de tons, sans objet séparable : y retirer des couches efface le dessin au lieu de le simplifier, et en redessiner un reviendrait à modifier un symbole de l'État. La silhouette de l'écu a donc été relevée sur le dessin lui-même, puis employée comme découpe. Un vrai emblème simplifié, s'il en faut un, relève d'une décision graphique et de l'autorité qui garde les armoiries.

*Les six fichiers livrés, et l'emploi de chacun*

| Fichier | Emploi | Poids |
| --- | --- | --- |
| `armoiries.svg` | Référence. Tout usage numérique et imprimé à partir de 32 px. | 191 Ko |
| `armoiries-gris.svg` | Impression en une seule encre, photocopie, télécopie, gravure. | 191 Ko |
| `armoiries-512.png` `…-256`, `…-128`, `…-64` | Bureautique et courriel, où le vectoriel n'est pas rendu de façon fiable. 512 pour une vignette de partage, 256 pour un en-tête de document, 128 pour une signature, 64 pour une liste. | 180 à 8 Ko |
| `favicon.svg` | Icone de l'onglet. Ce n'est pas l'emblème mais le drapeau et son étoile, seule marque lisible à 16 px. | 504 o |
| `favicon-32.png` `favicon-180.png` | Repli pour les navigateurs anciens, et carré tactile d'iOS. | 0,5 et 2,4 Ko |

**`armoiries.svg`**

Emblème vectoriel. Format de référence pour tout usage numérique et imprimé. 191 Ko, 45 Ko compressé.

**Taille minimale de l'emblème complet**

32 px de hauteur. En deçà, l'écu cesse d'être identifiable et la devise devient illisible : on passe au petit format.

**Dimensions**

Rapport 468 × 550. Le fichier ne porte pas de `width` ni de `height` : la taille est pilotée par le CSS.

**Taille minimale**

64 px pour le grand format, 32 pour le moyen, 16 pour le petit. En deçà de chaque seuil, on passe au format suivant.

**Production**

Les cinq déclinaisons sont produites depuis la référence par `node outils/embleme.js`. Aucune n'est retouchée à la main.

> Le passage en niveaux de gris ne se fait pas par conversion des luminances. Le rouge du drapeau donnerait `#707070` et le vert `#717171`, soit un seul gris pour deux couleurs, et l'écu perdrait son drapeau. Les trois teintes nationales reçoivent donc trois tons séparés, du plus sombre pour le rouge au plus clair pour l'or.

> **Recalage chromatique.** L'écu porte le drapeau national. La vectorisation ayant fait dériver ses teintes, celles-ci ont été ramenées aux valeurs officielles fixées par cette charte — `#e30613`, `#00843b` et `#ffed00`. Sans cette correction, l'emblème aurait juré avec les mêmes couleurs employées ailleurs sur la page. Les gris et les bruns de l'illustration sont conservés tels quels.

> **Statut du fichier**
>
> Ce fichier est dérivé par vectorisation d'un rendu raster de l'emblème. Il convient à la conception et au développement des interfaces. Pour une mise en production, la version vectorielle de référence doit être obtenue auprès du Secrétariat général du Gouvernement et se substituer à celle-ci.

#### Filet national `.fs-filet`

Bande longue portant la **composition du drapeau** : rouge au-dessus, vert au-dessous, étoile d'or centrée à cheval sur les deux. Placé sous le bandeau d'identification officielle, le filet marque l'appartenance à l'État et n'est pas employé comme séparateur décoratif à l'intérieur d'une page.

> **Une seule composition, quel que soit le format.** Le repère court, le filet pleine largeur et le bandeau d'une carte mise en avant portent tous le drapeau dans le même ordre. Une marque de l'État ne se réarrange pas selon la place dont elle dispose.

> **Ce que cela demande à la hauteur.** À 14 px, les deux moitiés ne font que 7 px chacune. La composition se lit de près, moins bien de loin. La variante `--epais`, à 20 px, donne deux bandes de 10 px et porte mieux ; c'est celle à préférer en ouverture de page. La hauteur se règle par `--filet-hauteur` sans rien recalculer, l'étoile suivant toujours.

*Exemple — Filet national*

```html
<div class="fs-filet"></div>
<div class="fs-filet fs-filet--fin"></div>
<div class="fs-filet fs-filet--epais"></div>
```

Trois hauteurs : `--fin` à 10 px pour un bandeau interne, la hauteur courante à 14 px, `--epais` à 20 px lorsque le filet ouvre une page d'accueil. L'étoile suit la hauteur, elle n'a pas de réglage propre.

#### Repère national `.fs-repere`

Vignette courte portant la **composition du drapeau** : rouge au-dessus, vert au-dessous, étoile centrée à cheval sur les deux, dans des proportions proches de celles du drapeau lui-même. Il ponctue un titre de section ou une ouverture de page, là où le filet pleine largeur serait trop appuyé. Le drapeau entier tient dans deux couches de fond, sans rien à superposer : il se compose donc dans un pseudo-élément, là où aucune classe ne peut être posée.

> **Deux objets, une seule composition.** Le repère et le filet ne se distinguent plus que par leur emploi. Le repère ponctue un titre, le filet ouvre une page. Le drapeau y figure dans le même ordre, avec la même étoile centrée.

*Exemple — Repère national*

```html
<span class="fs-repere"></span>
<span class="fs-repere" style="width: 96px; --repere-hauteur: 20px;"></span>
<span class="fs-repere fs-repere--centre"></span>
```

**À faire**

- Un seul repère par niveau de titre, toujours à la même place.
- Conserver les proportions du drapeau : deux bandes égales, étoile centrée à cheval sur les deux.
- Ajuster la largeur et la hauteur par les variables du composant, pas en redéfinissant le dégradé.

**À proscrire**

- Remplacer l'étoile par un point, un losange ou une autre forme.
- Grossir l'étoile jusqu'à couvrir la hauteur : sur le drapeau, elle laisse respirer les deux bandes.
- Inverser les bandes. Le rouge est toujours au-dessus du vert.
- Employer le repère comme puce de liste ou séparateur répété.

#### Motif tissé `.fs-tissage`

Inspiré des rayures du Faso Dan Fani, le tissu traditionnel. Les bandes sont de largeur inégale et séparées d'un fil clair, comme sur le métier à tisser. Le motif se distingue ainsi du filet officiel, qu'il ne remplace pas. Il habille les lisières (bas d'un bandeau, tête d'un pied de page, ouverture d'une section), et reste de faible hauteur.

*Exemple — Motif tissé*

```html
<div class="fs-tissage"></div>
<div class="fs-tissage fs-tissage--fin"></div>
<div style="display:flex; gap: var(--espace-4); height: 96px;">
  <div class="fs-tissage fs-tissage--vertical"></div>
  <p class="fs-legende" style="align-self:center; margin:0;">Variante verticale, pour border une colonne ou une citation.</p>
</div>
```

**À faire**

- Hauteur de 4 à 8 px, en pleine largeur ou sur une courte longueur en repère.
- Sur un fond coloré, adapter le fil clair via `--tissage-fond`.
- Un seul motif tissé par écran. Il accentue, il ne meuble pas.

**À proscrire**

- Le poser en fond derrière du texte ou des composants.
- L'employer à la place du filet officiel sous le bandeau d'identification.
- L'agrandir au-delà de 12 px : il devient un drapeau déformé.

#### Règles d'emploi de l'emblème

**À faire**

- Conserver une zone de protection au moins égale à la moitié de la hauteur de l'emblème, libre de tout texte ou image.
- Afficher l'emblème à 56 px de hauteur minimum en en-tête, 44 px sur écran étroit, 32 px en pied de page.
- Placer l'emblème sur un fond uni, clair ou très sombre, offrant un contraste suffisant.
- Associer systématiquement l'emblème au nom de l'administration responsable du service.
- Renseigner un texte de remplacement explicite lorsque l'emblème porte une information, et un texte vide lorsqu'il est redondant.

**À proscrire**

- Déformer, recadrer, faire pivoter ou recolorer l'emblème, y compris en version monochrome non validée.
- Appliquer une ombre portée, un contour, un dégradé ou un effet de relief.
- Poser l'emblème sur une photographie, une texture ou un fond chargé.
- Utiliser l'emblème comme motif de fond, filigrane, favicon décoratif ou élément d'illustration.
- Associer l'emblème à un service qui n'émane pas d'une administration publique.

#### Co-marquage

Un service porté conjointement par l'État et un partenaire (agence, établissement public, organisation internationale), fait coexister deux marques. L'ordre n'est pas négociable : l'emblème de l'État vient en premier, car c'est lui qui engage la responsabilité du service.

*Exemple — Co-marquage*

```html
<div class="fs-rangee" style="gap: var(--espace-5);">
  <a class="fs-marque" href="#" style="padding-right: var(--espace-5); border-right: var(--trait-fin) solid var(--bordure);">
    <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg" alt="Armoiries du Burkina Faso">
    <span class="fs-marque-texte">
      <span class="fs-marque-pays">Burkina Faso</span>
      <span class="fs-marque-entite">Ministère de la Santé</span>
    </span>
  </a>
  <span style="display:grid; place-items:center; width:92px; height:44px; background: var(--surface-enfoncee); border: var(--trait-fin) dashed var(--bordure-forte); border-radius: var(--rayon-sm); font-size: var(--taille-2xs); color: var(--texte-faible); text-align:center;">
    Marque<br>partenaire
  </span>
</div>
```

**À faire**

- Placer l'emblème de l'État en premier dans le sens de lecture, séparé de la marque partenaire par un filet vertical.
- Limiter la marque partenaire à la hauteur de l'emblème, pas au-delà.
- Conserver la zone de protection de l'emblème, que le partenaire n'empiète pas.
- N'admettre qu'un seul partenaire dans le bloc-marque ; les autres figurent en pied de page.

**À proscrire**

- Placer une marque partenaire avant l'emblème ou au-dessus de lui.
- Composer un logotype mixte associant l'emblème à un autre signe.
- Faire figurer une marque commerciale dans le bloc-marque, quel que soit le financement du service.
- Aligner les deux marques sans séparateur : elles paraîtraient émaner d'une même entité.

#### Devise nationale

La devise figure dans le bloc-marque en capitale initiale et en italique, au palier typographique le plus discret. Elle n'est ni un slogan promotionnel, ni un titre : elle ne doit pas être mise en avant au détriment de l'intitulé du service. Sur écran étroit, elle est masquée pour préserver la lisibilité de la dénomination.

> La devise portée par l'emblème en vigueur est **« La Patrie ou la Mort, nous Vaincrons »**. Toute reprise doit être conforme au fichier officiel de référence, y compris dans la ponctuation et les capitales.

### Couleurs

Les trois couleurs du drapeau adopté le 4 août 1984 constituent la souche chromatique du système. Elles sont trop saturées pour habiller une interface entière : chacune est donc déclinée en dix paliers, la teinte officielle restant fixée au palier indiqué. Les neutres sont dérivés d'une base chaude, et non d'un gris bleuté, pour rester cohérents avec cette souche.

Rouge Révolution, courage, sang versé pour la patrie #e30613 · Pantone 032 C

Or Lumière de l'étoile, richesse du sous-sol #ffed00 · Pantone 116 C

Vert Espérance, abondance des richesses agricoles #00843b · Pantone 355 C

#### Rouge — révolution et courage

Employé pour le registre grave : erreurs, suppressions, alertes critiques, signalement de fraude. Sa charge symbolique interdit de l'utiliser comme couleur d'accentuation ordinaire ou de mise en avant commerciale.

50 #fff1f2

100 #ffe0e2

200 #ffc5c9

300 #f99aa1

400 #f0636e

Drapeau 500 #e30613

600 #c40511

700 #a2040e

800 #7d0610

900 #5a0810

Référence d'impression : Pantone 032 C · CMJN 0 / 90 / 76 / 0 · RAL 3024. Texte blanc sur le palier 500 :

#### Vert — espérance et richesses agricoles

Couleur de l'action. Elle porte les boutons principaux, les liens, les états de validation et les repères de navigation active. C'est la couleur la plus présente dans une interface conforme.

50 #ecf7f0

100 #d3eddd

200 #a8dbbc

300 #74c296

400 #3ea46d

Drapeau 500 #00843b

600 #026c31

700 #045527

800 #04411f

900 #032e16

Référence d'impression : Pantone 355 C · CMJN 93 / 0 / 100 / 0 · RAL 6037. Texte blanc sur le palier 500 :

#### Or — lumière de l'étoile

Couleur de mise en avant, employée avec parcimonie. Le jaune pur étant très clair, il ne porte pas de texte blanc. Le palier 300 s'accompagne toujours d'un texte sombre. Les paliers 600 et au-delà tirent vers l'ocre et servent à écrire sur fond clair.

50 #fffce5

100 #fff8bf

200 #fff287

Drapeau 300 #ffed00

400 #e8d400

500 #c9b600

600 #a39300

700 #7d7100

800 #5c5300

900 #3d3800

Référence d'impression : Pantone 116 C · CMJN 0 / 10 / 98 / 0 · RAL 2007. Texte noir sur le palier 300 : · Texte blanc sur le palier 300 :

#### Neutres

Fonds, bordures et textes. La base est volontairement chaude. Un gris bleuté introduirait une teinte étrangère à la souche nationale et ferait jurer les aplats verts et or.

0 #ffffff

25 #fcfbf9

50 #f7f5f2

100 #efece7

200 #e2ddd6

300 #cfc8bf

400 #a9a199

450 #8e867f

500 #837b73

550 #756d66

600 #635c55

700 #4a443e

800 #332e2a

900 #211d1a

950 #14110f

Texte courant sur fond blanc : · Texte atténué sur fond blanc :

#### Couleurs de support

Deux familles complètent la souche nationale, strictement cantonnées à un registre fonctionnel. Le bleu administratif ne structure pas un en-tête, une navigation ou une action. Il n'appartient pas au drapeau et son emploi au-delà du registre informatif affaiblirait l'identité de l'État.

Bleu 50 #eef4f9

Bleu 100 #d6e5f0

Bleu 500 #2a6d9b

Bleu 700 #173f5f

Ocre 50 #fdf4e3

Ocre 500 #b07800

Ocre 700 #7d5500

#### Rôles sémantiques

Les composants ne référencent pas un palier directement. Ils consomment un rôle, qui pointe vers un palier. C'est cette indirection qui rend le thème sombre possible sans toucher à une seule règle de composant.

--action Boutons principaux, liens, navigation active, validation

--accent Suppression, alerte critique, urgence nationale

--relief Mise en avant ponctuelle, repère, surlignage

--succes Dossier validé, opération aboutie

--alerte Complément demandé, délai proche, mise en garde

--danger Erreur de saisie, rejet, action irréversible

--info Information neutre, repère documentaire

--surface-enfoncee Fonds de tableau, zones inactives, en-têtes

#### Répartition chromatique

Une interface conforme n'est pas une interface tricolore. Les trois couleurs nationales sont des signaux. Leur force tient à leur rareté. La proportion de référence est la suivante.

*Exemple — Proportions indicatives*

```html
<div style="display:flex; height:56px; border-radius: var(--rayon-md); overflow:hidden; font-size: var(--taille-2xs); font-weight:700; color:#fff;">
  <div style="flex: 78; background: var(--neutre-100); color: var(--neutre-700); display:grid; place-items:center;">Neutres — 78 %</div>
  <div style="flex: 14; background: var(--vert-500); display:grid; place-items:center;">Vert — 14 %</div>
  <div style="flex: 5; background: var(--or-300); color: var(--neutre-900); display:grid; place-items:center;">Or</div>
  <div style="flex: 3; background: var(--rouge-500); display:grid; place-items:center;">Rouge</div>
</div>
```

### Typographie

Deux familles suffisent. Archivo, grotesque de facture institutionnelle, porte les titres et les éléments d'interface ; son dessin ferme tient bien en petit corps et en capitales. Inter porte le texte courant. Sa hauteur d'x généreuse reste lisible sur les écrans de faible densité, qui constituent le parc dominant.

#### Les deux familles

Deux familles suffisent, et deux seulement. Chaque famille supplémentaire est un fichier de plus sur le chemin critique, pour un service dont le premier principe est la contrainte réseau.

*Exemple — Spécimen*

```html
<div>
  <p class="fs-legende">Archivo — titres, boutons, navigation</p>
  <p style="font-family: var(--police-titre); font-size: 2rem; font-weight: 800; letter-spacing: -0.02em; margin: var(--espace-2) 0;">
    Burkina Faso
  </p>
  <p style="font-family: var(--police-titre); font-size: 1.125rem; line-height: 1.6; margin: 0;">
    ABCDEFGHIJKLMNOPQRSTUVWXYZ<br>
    abcdefghijklmnopqrstuvwxyz<br>
    0123456789 &amp; ’ « » — … €
  </p>
</div>

<hr>

<div>
  <p class="fs-legende">Inter — texte courant, formulaires, tableaux</p>
  <p style="font-family: var(--police-texte); font-size: 1.0625rem; line-height: 1.7; margin: var(--espace-2) 0 0; max-width: 54ch;">
    Le récépissé de dépôt est délivré immédiatement après validation du
    formulaire. Il ne vaut pas acte et ne peut être produit comme pièce
    justificative.
  </p>
  <p style="font-family: var(--police-texte); font-size: 1.125rem; line-height: 1.6; margin-top: var(--espace-4);">
    ABCDEFGHIJKLMNOPQRSTUVWXYZ<br>
    abcdefghijklmnopqrstuvwxyz<br>
    0123456789 &amp; ’ « » — … €
  </p>
</div>

<hr>

<div>
  <p class="fs-legende">Accents et ligatures du français</p>
  <p style="font-family: var(--police-texte); font-size: 1.375rem; line-height: 1.7; margin: var(--espace-2) 0 0;">
    À Â Ä Ç É È Ê Ë Î Ï Ô Ö Ù Û Ü Ÿ Œ Æ<br>
    à â ä ç é è ê ë î ï ô ö ù û ü ÿ œ æ
  </p>
  <p class="fs-legende fs-mt-4">
    Le sous-ensemble servi couvre l'intégralité du français, <strong>Œ et œ
    compris</strong>, sans eux, « cœur » et « œuvre » basculeraient dans
    la police de repli au milieu d'un mot.
  </p>
</div>
```

#### Graisses

Cinq graisses sont disponibles, mais chacune a un emploi assigné. Une graisse employée hors de son rôle affaiblit la hiérarchie au lieu de la renforcer.

*Exemple — Graisses*

```html
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>400 — normale</b>Texte courant</div>
  <div class="doc-specimen-rendu" style="font-weight: 400; font-size: 1.125rem;">Demander un acte de naissance</div>
</div>
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>500 — moyenne</b>Étiquette, libellé de champ</div>
  <div class="doc-specimen-rendu" style="font-weight: 500; font-size: 1.125rem;">Demander un acte de naissance</div>
</div>
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>600 — demi-grasse</b>Boutons, titres de rang 4 à 6</div>
  <div class="doc-specimen-rendu" style="font-family: var(--police-titre); font-weight: 600; font-size: 1.125rem;">Demander un acte de naissance</div>
</div>
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>700 — grasse</b>Titres de rang 2 et 3</div>
  <div class="doc-specimen-rendu" style="font-family: var(--police-titre); font-weight: 700; font-size: 1.125rem;">Demander un acte de naissance</div>
</div>
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>800 — noire</b>Titre de rang 1, chiffres clés</div>
  <div class="doc-specimen-rendu" style="font-family: var(--police-titre); font-weight: 800; font-size: 1.125rem;">Demander un acte de naissance</div>
</div>
```

> Les fichiers sont des **polices variables** : un seul fichier par famille couvre toutes les graisses. Cinq fichiers statiques auraient pesé le triple. Aucune graisse intermédiaire n'est pour autant autorisée — l'échelle reste celle des cinq paliers ci-dessus.

#### Chasse fixe

Références de dossier, montants dans un tableau, valeurs techniques : tout ce qui doit s'aligner en colonne ou se lire caractère par caractère. **Aucune police n'est téléchargée pour cela** — une chasse fixe ne porte aucune identité, celle du système d'exploitation convient, et son absence économise un fichier entier sur le chemin critique.

*Exemple — Chasse fixe*

```html
<p style="margin:0;"><code>BF-2026-004871-OUA</code></p>
<p class="fs-legende" style="margin:0;">
  Les chiffres du texte courant sont eux aussi tabulaires dans les
  tableaux et les montants, via <code>font-variant-numeric</code> : sans
  cela, une colonne de sommes ne s'aligne pas.
</p>
<div class="fs-tableau-cadre" style="max-width: 280px;">
  <table class="fs-tableau fs-tableau--compact">
    <tbody>
      <tr><td>Droit de timbre</td><td class="fs-num">500 F</td></tr>
      <tr><td>Copies</td><td class="fs-num">1 400 F</td></tr>
      <tr><td>Envoi</td><td class="fs-num">11 500 F</td></tr>
    </tbody>
  </table>
</div>
```

#### Chargement et repli

Les polices sont servies **depuis le domaine du service**, jamais depuis un tiers. Une dépendance externe ajoute une résolution de nom, une connexion, et place la disponibilité d'un service public hors du contrôle de l'administration.

*Poids réel servi, sous-ensemble latin*

| Fichier | Format | Graisses | Poids |
| --- | --- | --- | --- |
| `archivo.woff2` | Variable | 400 à 800 | 34 Ko |
| `inter.woff2` | Variable | 400 à 700 | 47 Ko |
| Chasse fixe | Système | — | 0 Ko |
| **Total** |  |  | **81 Ko** |

Plafond fixé par le [budget de performance](https://chartegraphique-21.mtdpce-test.gov.bf/accessibilite#budget) : 100 Ko. Objectif : 60 Ko. Le système est sous le plafond, au-dessus de l'objectif ; la marge restante viendrait d'un sous-ensemble plus étroit que le latin complet.

##### Langues nationales et couverture des glyphes

Les deux fichiers sont réduits au sous-ensemble latin : c'est ce qui les fait tenir en 81 Ko. Ce sous-ensemble couvre le français, langue officielle, mais **pas les caractères propres aux orthographes des langues nationales**. Le fait est écrit ici parce qu'il engage directement tout service qui voudrait s'adresser aux usagers dans leur langue.

*Caractères hors du sous-ensemble servi*

| Caractère | Point de code | Employé notamment en | État |
| --- | --- | --- | --- |
| ɛ Ɛ | U+025B, U+0190 | Mooré, dioula | Police de repli |
| ɔ Ɔ | U+0254, U+0186 | Dioula, bissa | Police de repli |
| ŋ Ŋ | U+014B, U+014A | Fulfuldé | Police de repli |
| ɩ ʋ | U+0269, U+028B | Mooré | Police de repli |

> **Ce que cela produit à l'écran.** Le navigateur remplace caractère par caractère : le mot reste lisible, mais le glyphe manquant est emprunté à une autre police, avec une chasse et un dessin différents. Sur un mot isolé cela passe, sur un paragraphe entier cela se voit.

> **Ce qu'il faut faire pour y remédier.** Reproduire le sous-ensemble depuis les fichiers d'origine d'Archivo et d'Inter, tous deux sous licence SIL OFL, en y ajoutant les plages `U+014A-014B`, `U+0186`, `U+0190`, `U+0254`, `U+025B`, `U+0269`, `U+028B`, puis étendre `unicode-range` en conséquence. Le surcoût attendu se compte en quelques kilo-octets. Tant que ce travail n'est pas fait, la charte ne prétend couvrir que le français.

##### Repli système

Si une police ne charge pas, le texte reste lisible dans la pile de substitution. `font-display: swap` affiche immédiatement le contenu en police de repli, puis bascule : sur réseau lent, une page lisible vaut mieux qu'une page blanche.

*Exemple — Repli*

```html
<div>
  <p class="fs-legende">Titre — police attendue</p>
  <p style="font-family: var(--police-titre); font-size: 1.5rem; font-weight: 700; margin: var(--espace-1) 0 0;">Suivi de votre dossier</p>
</div>
<div>
  <p class="fs-legende">Titre — repli système</p>
  <p style="font-family: 'Segoe UI', system-ui, sans-serif; font-size: 1.5rem; font-weight: 700; margin: var(--espace-1) 0 0;">Suivi de votre dossier</p>
</div>
```

**Titres**

`"Segoe UI", system-ui, -apple-system, sans-serif`

**Texte**

`"Segoe UI", system-ui, -apple-system, sans-serif`

**Chasse fixe**

`ui-monospace, "Cascadia Mono", Consolas, Menlo, monospace`

#### Licence et provenance

Une administration doit pouvoir répondre à une question simple : a-t-elle le droit d'embarquer ces polices dans un site, un document ou une application ? Les deux familles sont diffusées sous **SIL Open Font License**, qui autorise l'usage, la modification et la redistribution, y compris commerciale, à condition de ne pas les vendre seules et de conserver le texte de la licence.

| Famille | Auteurs | Licence | Texte |
| --- | --- | --- | --- |
| Archivo | The Archivo Project Authors | SIL OFL 1.1 | [archivo-OFL.txt](https://chartegraphique-21.mtdpce-test.gov.bf/assets/polices/archivo-OFL.txt) |
| Inter | The Inter Project Authors | SIL OFL 1.1 | [inter-OFL.txt](https://chartegraphique-21.mtdpce-test.gov.bf/assets/polices/inter-OFL.txt) |

> Le texte de chaque licence accompagne les fichiers dans `assets/polices/`. Il doit être conservé lors de toute reprise du système par un projet : c'est la seule obligation que la licence impose.

#### Échelle typographique

Les tailles de titre sont fluides. Elles s'interpolent entre 360 px et 1280 px de largeur de fenêtre, ce qui évite les paliers brutaux sur les formats intermédiaires. Le corps de texte reste fixe à 15 px, plancher au-dessous duquel la lecture prolongée devient pénible.

*Exemple — Spécimen*

```html
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>Titre 1</b>Archivo 800<br>32 → 53 px<br>interligne 1.15</div>
  <div class="doc-specimen-rendu"><h1 style="margin:0;">Demander un acte de naissance</h1></div>
</div>
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>Titre 2</b>Archivo 700<br>26 → 38 px<br>interligne 1.25</div>
  <div class="doc-specimen-rendu"><h2 style="margin:0; border:0; padding:0;">Pièces à fournir</h2></div>
</div>
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>Titre 3</b>Archivo 700<br>22 → 29 px</div>
  <div class="doc-specimen-rendu"><h3 style="margin:0;">Délai de traitement</h3></div>
</div>
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>Titre 4</b>Archivo 600<br>19 → 23 px</div>
  <div class="doc-specimen-rendu"><h4 style="margin:0;">Administration responsable</h4></div>
</div>
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>Chapeau</b>Inter 400<br>17 px<br>interligne 1.75</div>
  <div class="doc-specimen-rendu"><p class="fs-chapeau" style="margin:0;">La demande peut être déposée en ligne ou au guichet de la mairie de résidence.</p></div>
</div>
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>Corps</b>Inter 400<br>15 px<br>interligne 1.6</div>
  <div class="doc-specimen-rendu"><p style="margin:0;">Le récépissé de dépôt est délivré immédiatement après validation du formulaire. Il ne vaut pas acte et ne peut être produit comme pièce justificative.</p></div>
</div>
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>Petit texte</b>Inter 400<br>13 px</div>
  <div class="doc-specimen-rendu"><p class="fs-legende" style="margin:0;">Mention légale, légende de tableau, précision secondaire.</p></div>
</div>
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>Surtitre</b>Archivo 700<br>12 px<br>capitales, approche 0.08em</div>
  <div class="doc-specimen-rendu"><span class="fs-surtitre" style="margin:0;">Service en ligne</span></div>
</div>
<div class="doc-specimen">
  <div class="doc-specimen-fiche"><b>Monospace</b>JetBrains Mono<br>Références, montants, codes</div>
  <div class="doc-specimen-rendu"><code>BF-2026-004871-OUA</code></div>
</div>
```

#### Règles de composition

**À faire**

- Limiter le texte courant à environ 68 caractères par ligne (`--mesure`).
- Respecter la hiérarchie des niveaux de titre sans en sauter : un `h3` suit un `h2`.
- Aligner les chiffres en colonne avec `font-variant-numeric: tabular-nums` dans les tableaux et les montants.
- Écrire les montants en francs CFA avec séparateur de milliers et unité explicite.

**À proscrire**

- Composer un paragraphe entier en capitales. La lecture chute d'environ un tiers.
- Justifier le texte : sans césure, la justification crée des lézardes ingérables.
- Descendre sous 15 px pour un contenu que l'usager doit lire pour agir.
- Introduire une troisième famille typographique pour un besoin ponctuel.

### Photographie et illustration

Une image de service public informe ; elle n'habille pas. La question à se poser avant d'en placer une n'est pas « qu'est-ce qui irait bien ici ? » mais « qu'est-ce que l'usager comprendra mieux grâce à elle ? ». Si la réponse n'est pas évidente, la place reste vide.

#### Rôle de l'image

**Légitime**

- Montrer à quoi ressemble un document ou un guichet, pour que l'usager le reconnaisse.
- Situer un lieu : façade d'une mairie, plan d'accès à un centre d'enrôlement.
- Illustrer un geste : comment cadrer la photographie d'une pièce d'identité.
- Documenter une actualité par une photographie de l'événement.

**À proscrire**

- Photographie d'ambiance pour « aérer » une page de démarche.
- Banque d'images internationale sans lien avec le Burkina Faso.
- Image de fond derrière du texte. Le contraste devient incontrôlable.
- Illustration décorative dans un parcours de démarche. Elle allonge la page et distrait de l'action.

#### Représentation des personnes

Les images d'un service de l'État montrent les usagers tels qu'ils sont, sans mise en scène. Cette exigence n'est pas cosmétique. Un usager qui ne se reconnaît pas dans les images d'un service doute de s'y adresser.

- **Dignité.** Aucune mise en scène de détresse, de pauvreté ou de dépendance. L'usager d'un service public est un citoyen qui exerce un droit.
- **Diversité réelle.** Âges, genres, régions, milieux urbains et ruraux, situations de handicap. Une administration nationale s'adresse à tout le pays.
- **Justesse.** Les tenues, les lieux et les documents montrés sont ceux du Burkina Faso.
- **Consentement.** Autorisation écrite pour toute personne identifiable, conservée par l'administration émettrice.
- **Sobriété.** Aucun sourire commercial, aucune pose professionnelle appuyée.

#### Traitement et format `.fs-figure`

Le rapport d'affichage est déclaré pour qu'aucun décalage de mise en page ne survienne pendant le chargement. La légende porte l'information ; le crédit la suit, plus discret.

*Exemple — Figure*

```html
<figure class="fs-figure fs-figure--16-9">
  <img src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2016%209'%3E%3Crect%20width='16'%20height='9'%20fill='%23e2ddd6'/%3E%3C/svg%3E" width="16" height="9" alt="Emplacement de la photographie">
  <figcaption>
    Guichet de l'état civil de la mairie de Ouagadougou, ouvert du lundi au
    vendredi de 7 h 30 à 15 h 30.
    <span class="fs-figure-credit">Photographie : Service d'information du Gouvernement, 2026</span>
  </figcaption>
</figure>
```

**Rapports admis**

`16/9` pour une bannière, `4/3` pour une photographie de contenu, `1/1` pour un portrait ou une vignette.

**Traitement**

Aucun filtre, aucune teinte de marque, aucun cadre arrondi au-delà du rayon du système. Recadrage seulement.

**Poids**

200 Ko au maximum par image, dimensionnée à sa taille d'affichage. Voir le [budget de performance](https://chartegraphique-21.mtdpce-test.gov.bf/accessibilite#budget).

**Texte de remplacement**

Descriptif si l'image porte une information, vide (`alt=""`) si elle est redondante avec le texte voisin.

#### Illustration

L'illustration n'intervient que là où la photographie ne peut rien montrer : un schéma de procédure, une page d'erreur, un état vide. Son dessin dérive du jeu d'icônes — trait ouvert, épaisseur constante, aucune forme pleine — de sorte qu'elle appartient visiblement au même système.

**À faire**

- Reprendre la grille, l'épaisseur de trait et les extrémités arrondies du jeu d'icônes.
- Se limiter aux couleurs du système, l'or servant d'unique accent.
- Garder l'illustration lisible en monochrome.
- La traiter comme décorative : `aria-hidden`, l'information restant dans le texte.

**À proscrire**

- Personnages stylisés aux proportions fantaisistes.
- Illustration portant seule une information ou une instruction.
- Dégradés, ombres portées, perspective.
- Mélange avec des illustrations d'une autre provenance.

### Grille et mise en page

Une grille de douze colonnes, une gouttière fluide et quatre largeurs de conteneur couvrent l'ensemble des gabarits. La conception part du format le plus étroit. C'est l'usage dominant.

#### Colonnes `.fs-grille`

*Exemple — Grille de 12 colonnes*

```html
<div class="doc-grille-demo">
  <div>1</div><div>2</div><div>3</div><div>4</div><div>5</div><div>6</div>
  <div>7</div><div>8</div><div>9</div><div>10</div><div>11</div><div>12</div>
</div>
```

#### Répartitions courantes

Les classes `.fs-col-*` portent le nombre de colonnes occupées. Sous 768 px, toutes les colonnes passent en pleine largeur sans qu'il soit nécessaire de le déclarer.

*Exemple — Répartitions*

```html
<div class="fs-grille">
  <div class="fs-col-8 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">Contenu principal — 8 colonnes</div>
  <div class="fs-col-4 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">Colonne latérale — 4</div>
</div>

<div class="fs-grille">
  <div class="fs-col-4 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">4</div>
  <div class="fs-col-4 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">4</div>
  <div class="fs-col-4 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">4</div>
</div>

<div class="fs-grille">
  <div class="fs-col-6 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">6</div>
  <div class="fs-col-3 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">3</div>
  <div class="fs-col-3 fs-fond-enfoncee" style="padding: var(--espace-4); border-radius: var(--rayon-md);">3</div>
</div>
```

#### Contenu et colonne latérale `.fs-avec-aside`

Assemblage le plus fréquent des gabarits institutionnels. La colonne latérale mesure 300 px et passe sous le contenu principal en dessous de 1024 px. La variante `.fs-avec-aside--gauche` place une colonne de navigation de 260 px avant le contenu.

*Exemple — Contenu et colonne latérale*

```html
<div class="fs-avec-aside">
  <div class="fs-fond-enfoncee" style="padding: var(--espace-6); border-radius: var(--rayon-md);">Contenu principal</div>
  <aside class="fs-fond-enfoncee" style="padding: var(--espace-6); border-radius: var(--rayon-md);">Colonne latérale</aside>
</div>
```

#### Largeurs de conteneur

| Classe | Largeur | Emploi |
| --- | --- | --- |
| `.fs-conteneur--etroit` | 560 px | Formulaire d'authentification, confirmation, message isolé |
| `.fs-conteneur--texte` | 760 px | Article, contenu réglementaire, fiche de démarche |
| `.fs-conteneur` | 1200 px | Largeur de référence de la majorité des pages |
| `.fs-conteneur--large` | 1440 px | Tableau de bord, liste dense, tableau à nombreuses colonnes |

#### Points de rupture

| Palier | Largeur | Adaptation |
| --- | --- | --- |
| Étroit | \< 560 px | Colonne unique, actions en pleine largeur, modale plein écran |
| Intermédiaire | 560 – 768 px | Colonne unique, tableaux empilés |
| Tablette | 768 – 1024 px | Deux colonnes, colonne latérale repliée sous le contenu |
| Bureau | \> 1024 px | Grille complète, colonne latérale visible |

### Espacement

Une échelle unique de base 4 px. Toute valeur intermédiaire introduite hors de cette échelle produit des désalignements qui se propagent à l'ensemble d'une interface.

#### Échelle

*Exemple — Échelle d'espacement*

```html
<div class="doc-espaces">
  <div class="doc-espace"><span class="doc-espace-nom">--espace-1</span><span class="doc-espace-barre" style="width:4px"></span><span class="doc-espace-val">4 px</span></div>
  <div class="doc-espace"><span class="doc-espace-nom">--espace-2</span><span class="doc-espace-barre" style="width:8px"></span><span class="doc-espace-val">8 px</span></div>
  <div class="doc-espace"><span class="doc-espace-nom">--espace-3</span><span class="doc-espace-barre" style="width:12px"></span><span class="doc-espace-val">12 px</span></div>
  <div class="doc-espace"><span class="doc-espace-nom">--espace-4</span><span class="doc-espace-barre" style="width:16px"></span><span class="doc-espace-val">16 px</span></div>
  <div class="doc-espace"><span class="doc-espace-nom">--espace-5</span><span class="doc-espace-barre" style="width:20px"></span><span class="doc-espace-val">20 px</span></div>
  <div class="doc-espace"><span class="doc-espace-nom">--espace-6</span><span class="doc-espace-barre" style="width:24px"></span><span class="doc-espace-val">24 px</span></div>
  <div class="doc-espace"><span class="doc-espace-nom">--espace-8</span><span class="doc-espace-barre" style="width:32px"></span><span class="doc-espace-val">32 px</span></div>
  <div class="doc-espace"><span class="doc-espace-nom">--espace-10</span><span class="doc-espace-barre" style="width:40px"></span><span class="doc-espace-val">40 px</span></div>
  <div class="doc-espace"><span class="doc-espace-nom">--espace-12</span><span class="doc-espace-barre" style="width:48px"></span><span class="doc-espace-val">48 px</span></div>
  <div class="doc-espace"><span class="doc-espace-nom">--espace-16</span><span class="doc-espace-barre" style="width:64px"></span><span class="doc-espace-val">64 px</span></div>
  <div class="doc-espace"><span class="doc-espace-nom">--espace-20</span><span class="doc-espace-barre" style="width:80px"></span><span class="doc-espace-val">80 px</span></div>
  <div class="doc-espace"><span class="doc-espace-nom">--espace-24</span><span class="doc-espace-barre" style="width:96px"></span><span class="doc-espace-val">96 px</span></div>
</div>
```

#### Emplois de référence

**4 – 8 px**

Écart entre une étiquette et son champ, entre une icône et son libellé.

**12 – 16 px**

Rembourrage interne des composants, écart entre champs d'un même groupe.

**24 – 32 px**

Rembourrage des cartes, écart entre groupes de champs, gouttière de grille.

**48 – 96 px**

Respiration entre sections de page, variable selon la largeur de fenêtre.

### Formes et profondeur

Les rayons restent sobres : un arrondi prononcé évoque le registre promotionnel, étranger au service public. Les ombres sont teintées chaud pour rester cohérentes avec les neutres, et servent à hiérarchiser les plans, pas à décorer.

#### Rayons de bordure

*Exemple — Rayons*

```html
<div class="doc-variante"><div style="width:80px;height:64px;background:var(--vert-100);border:1px solid var(--vert-300);border-radius:var(--rayon-sm)"></div><span class="doc-variante-nom">sm · 4 px</span></div>
<div class="doc-variante"><div style="width:80px;height:64px;background:var(--vert-100);border:1px solid var(--vert-300);border-radius:var(--rayon-md)"></div><span class="doc-variante-nom">md · 6 px</span></div>
<div class="doc-variante"><div style="width:80px;height:64px;background:var(--vert-100);border:1px solid var(--vert-300);border-radius:var(--rayon-lg)"></div><span class="doc-variante-nom">lg · 10 px</span></div>
<div class="doc-variante"><div style="width:80px;height:64px;background:var(--vert-100);border:1px solid var(--vert-300);border-radius:var(--rayon-xl)"></div><span class="doc-variante-nom">xl · 16 px</span></div>
<div class="doc-variante"><div style="width:80px;height:64px;background:var(--vert-100);border:1px solid var(--vert-300);border-radius:var(--rayon-plein)"></div><span class="doc-variante-nom">plein</span></div>
```

Correspondances : `md` pour les boutons et les champs, `lg` pour les cartes et les tableaux, `xl` pour les modales et les blocs de démarche, `plein` pour les étiquettes et les pastilles.

#### Élévation

*Exemple — Ombres*

```html
<div class="doc-ombres">
  <div class="doc-ombre" style="box-shadow: var(--ombre-xs)">xs</div>
  <div class="doc-ombre" style="box-shadow: var(--ombre-sm)">sm</div>
  <div class="doc-ombre" style="box-shadow: var(--ombre-md)">md</div>
  <div class="doc-ombre" style="box-shadow: var(--ombre-lg)">lg</div>
  <div class="doc-ombre" style="box-shadow: var(--ombre-xl)">xl</div>
</div>
```

**xs — repos**

Bouton principal au repos. Suggère la possibilité d'une action sans détacher l'élément.

**sm — survol**

Carte ou bouton survolé.

**md — flottant**

Carte détachée, infobulle.

**lg — superposé**

Menu déroulant, notification passagère.

**xl — modal**

Modale, panneau latéral, bandeau de consentement.

#### Focus

Un seul traitement de focus pour tout le système, appliqué via `:focus-visible`. Il n'est jamais désactivé : une interface sans anneau de focus est inutilisable au clavier, ce qui exclut une part des usagers et contrevient aux obligations d'accessibilité.

*Exemple — Anneau de focus*

```html
<button type="button" class="fs-btn fs-btn--principal">Naviguez au clavier jusqu'ici</button>
<a class="fs-btn fs-btn--tertiaire" href="#focus">Puis jusqu'ici</a>
<input class="fs-saisie" style="max-width: 220px;" type="text" aria-label="Exemple de champ" placeholder="Et enfin ici">
```

### Iconographie

Une icône ne remplace jamais un mot : elle accélère la reconnaissance d'un libellé déjà présent. Le jeu est volontairement restreint — 35 tracés couvrant les besoins réels des parcours documentés dans cette charte. Un service qui a besoin d'une icône absente la soumet plutôt que d'en importer une d'un jeu tiers, dont le dessin jurerait avec l'ensemble.

#### Construction

Toutes les icônes partagent la même grille et la même épaisseur de trait. C'est cette régularité, bien plus que le dessin de chaque symbole, qui fait tenir un jeu d'icônes.

*Exemple — Grille de construction*

```html
<div class="doc-grille-icone">
  <svg viewbox="0 0 24 24" aria-hidden="true">
    <rect class="doc-grille-zone" x="2" y="2" width="20" height="20" rx="1"></rect>
    <path class="doc-grille-trait" d="M12 0v24M0 12h24"></path>
    <circle class="doc-grille-trait" cx="12" cy="12" r="9"></circle>
    <g class="doc-grille-dessin">
      <circle cx="11" cy="11" r="6.5"></circle>
      <path d="M15.9 15.9 20.5 20.5"></path>
    </g>
  </svg>
</div>
<dl class="fs-definitions" style="flex:1; min-width: 260px;">
  <div><dt>Grille</dt><dd>24 × 24, zone utile 20 × 20</dd></div>
  <div><dt>Trait</dt><dd>1,5 — constant, jamais mis à l'échelle</dd></div>
  <div><dt>Extrémités</dt><dd>Arrondies, jonctions arrondies</dd></div>
  <div><dt>Style</dt><dd>Trait ouvert, aucune forme pleine, aucune couleur</dd></div>
</dl>
```

#### Mise en œuvre `.fs-icone`

Chaque tracé est appliqué en masque CSS sur un aplat de `currentColor` : une icône hérite donc toujours de la couleur du texte qui l'accompagne et suit le thème sans le moindre réglage. Sa taille est exprimée en `em`, donc proportionnelle au corps du texte voisin.

*Exemple — Tailles et héritage de couleur*

```html
<span class="fs-icone fs-icone--dossier fs-icone--sm" aria-hidden="true"></span>
<span class="fs-icone fs-icone--dossier" aria-hidden="true"></span>
<span class="fs-icone fs-icone--dossier fs-icone--lg" aria-hidden="true"></span>
<span class="fs-icone fs-icone--dossier fs-icone--xl" aria-hidden="true"></span>
<span class="fs-icone fs-icone--dossier fs-icone--xxl" aria-hidden="true"></span>
<span style="color: var(--accent-sur-clair);">
  <span class="fs-icone fs-icone--dossier fs-icone--xl" aria-hidden="true"></span>
</span>
<span style="color: var(--action-sur-clair);">
  <span class="fs-icone fs-icone--dossier fs-icone--xl" aria-hidden="true"></span>
</span>
```

*Exemple — Dans les composants*

```html
<button type="button" class="fs-btn fs-btn--principal">
  <span class="fs-icone fs-icone--televerser" aria-hidden="true"></span>
  Déposer une pièce
</button>
<a class="fs-btn fs-btn--tertiaire" href="#">
  <span class="fs-icone fs-icone--telecharger" aria-hidden="true"></span>
  Télécharger le récépissé
</a>
<button type="button" class="fs-btn fs-btn--tertiaire fs-btn--icone" aria-label="Imprimer la fiche">
  <span class="fs-icone fs-icone--imprimer" aria-hidden="true"></span>
</button>
<span class="fs-badge fs-badge--succes">
  <span class="fs-icone fs-icone--succes fs-icone--sm" aria-hidden="true"></span>
  Validé
</span>
<span class="fs-statut fs-statut--cours">
  <span class="fs-icone fs-icone--horloge fs-icone--sm" aria-hidden="true"></span>
  En instruction
</span>
```

#### Accessibilité

**À faire**

- Porter `aria-hidden="true"` sur toute icône accompagnant un libellé : elle est décorative et ne doit pas être annoncée deux fois.
- Sur un bouton réduit à une icône, donner le nom accessible par `aria-label` posé sur le bouton.
- Conserver la cible tactile de 44 px même lorsque l'icône n'en fait que 20.
- Doubler toute icône porteuse de sens par un texte, y compris dans les tableaux denses.

**À proscrire**

- Employer une icône seule pour signaler un état : elle tombe sous le principe « la couleur ne porte jamais seule ».
- Redessiner ou étirer un tracé : l'épaisseur du trait ne se met jamais à l'échelle.
- Mélanger ce jeu avec des icônes d'une autre provenance.
- Employer une icône comme puce décorative répétée dans un texte courant.

#### Le jeu complet

Les icônes sont classées par usage et non par forme. On cherche « ce que je veux dire », pas « ce que ça représente ». Le fichier `assets/img/icones.svg` rassemble les mêmes tracés pour les outils de conception.

##### Navigation

11 icônes. Cliquez sur une icône pour copier sa classe.

recherche

fermer

menu

chevron-bas

chevron-haut

chevron-droite

chevron-gauche

fleche-droite

lien-externe

filtre

trier

##### Démarches

8 icônes. Cliquez sur une icône pour copier sa classe.

document

dossier

telecharger

televerser

imprimer

calendrier

horloge

paiement

##### Identité

6 icônes. Cliquez sur une icône pour copier sa classe.

carte-identite

utilisateur

institution

lieu

telephone

courriel

##### États

6 icônes. Cliquez sur une icône pour copier sa classe.

information

succes

alerte

erreur

coche

aide

##### Confiance

4 icônes. Cliquez sur une icône pour copier sa classe.

cadenas

bouclier

signalement

oeil

### Mouvement

L'animation sert à expliquer un changement d'état, jamais à séduire. Les durées sont courtes : sur un appareil modeste, une transition longue est perçue comme une lenteur du service, pas comme un raffinement.

#### Durées et courbes

| Jeton | Durée | Emploi |
| --- | --- | --- |
| `--duree-instant` | 80 ms | Enfoncement d'un bouton, retour tactile |
| `--duree-courte` | 140 ms | Survol, changement de couleur, focus |
| `--duree-moyenne` | 220 ms | Ouverture d'un menu, d'une modale, d'un accordéon |
| `--duree-longue` | 360 ms | Panneau latéral, progression d'une jauge |

#### Préférence de mouvement réduit

Lorsque l'usager a demandé une réduction des animations au niveau du système, toutes les durées sont ramenées à 1 ms et les animations en boucle sont supprimées. Cette règle est appliquée dans `tokens.css` et n'a donc pas à être répétée composant par composant.

```css
@media (prefers-reduced-motion: reduce) {
  :root {
    --duree-instant: 1ms;
    --duree-courte:  1ms;
    --duree-moyenne: 1ms;
    --duree-longue:  1ms;
  }
}
```

### Thèmes

Le thème sombre n'est pas un ornement. Il réduit la consommation sur écran OLED et soulage la lecture nocturne, deux situations courantes pour un usager qui consulte un service depuis son téléphone. Il est obtenu en réaffectant les rôles sémantiques, sans toucher à un seul composant.

#### Arbitrages du thème sombre

- Les teintes officielles du drapeau sont éclaircies d'un palier sur fond sombre : au palier 500, le vert et le rouge ne dégagent plus un contraste suffisant pour porter du texte.
- Les fonds ne sont jamais noir pur. La base est un brun très sombre, cohérent avec la souche chaude des neutres, qui réduit l'éblouissement.
- Les ombres deviennent plus denses et plus contrastées, l'effet de profondeur étant naturellement moins lisible sur fond sombre.
- Le thème clair est le thème par défaut de l'administration, quelle que soit la préférence du système d'exploitation. Un service public doit présenter le même visage à tous ses usagers. Le thème sombre s'active sur choix explicite, et ce choix est conservé d'une visite à l'autre.


---

## Composants de base

Page de référence : https://chartegraphique-21.mtdpce-test.gov.bf/composants

Les briques communes à tous les services de l'État, réparties en sept familles fonctionnelles. Chaque composant est livré en HTML et CSS natifs : aucune dépendance, aucune compilation. Le code affiché sous chaque exemple est extrait du rendu lui-même et ne peut donc pas diverger de ce qui s'affiche.

### Actions

Une page ne comporte qu'une seule action principale. Si deux actions se disputent ce rang, c'est que la page porte deux intentions et doit être scindée.

#### Boutons `.fs-btn`

Six variantes hiérarchisées. L'or est réservé aux mises en avant exceptionnelles, le rouge aux seules actions destructrices ou graves.

*Exemple — Variantes*

```html
<button type="button" class="fs-btn fs-btn--principal">Déposer la demande</button>
<button type="button" class="fs-btn fs-btn--secondaire">Enregistrer le brouillon</button>
<button type="button" class="fs-btn fs-btn--tertiaire">Revenir à l'étape précédente</button>
<button type="button" class="fs-btn fs-btn--relief">Accéder au service</button>
<button type="button" class="fs-btn fs-btn--danger">Supprimer le dossier</button>
<button type="button" class="fs-btn fs-btn--fantome">Annuler</button>
```

#### Tailles et états

*Exemple — Tailles*

```html
<button type="button" class="fs-btn fs-btn--principal fs-btn--sm">Petit</button>
<button type="button" class="fs-btn fs-btn--principal">Standard</button>
<button type="button" class="fs-btn fs-btn--principal fs-btn--lg">Grand</button>
<button type="button" class="fs-btn fs-btn--principal" disabled>Indisponible</button>
<button type="button" class="fs-btn fs-btn--principal" data-chargement="true">Envoi en cours</button>
<button type="button" class="fs-btn fs-btn--tertiaire fs-btn--icone" aria-label="Imprimer la fiche"><span class="fs-icone fs-icone--imprimer" aria-hidden="true"></span></button>
```

Un bouton réduit à une icône porte obligatoirement un `aria-label`. Pendant un chargement, le libellé reste en place dans le DOM : il est masqué visuellement mais toujours annoncé.

#### Boutons sur fond coloré

Sur un aplat sombre ou coloré, les variantes courantes perdent leur contraste. Deux variantes dédiées prennent le relais.

*Exemple — Sur fond sombre*

```html
<button type="button" class="fs-btn fs-btn--relief">Accéder au service</button>
<button type="button" class="fs-btn fs-btn--inverse">Consulter la notice</button>
<button type="button" class="fs-btn fs-btn--contour-clair">En savoir plus</button>
```

#### Groupe et contrôle segmenté

*Exemple — Groupes d'actions*

```html
<div class="fs-btn-groupe" role="group" aria-label="Format d'export">
  <button type="button" class="fs-btn fs-btn--tertiaire">PDF</button>
  <button type="button" class="fs-btn fs-btn--tertiaire">CSV</button>
  <button type="button" class="fs-btn fs-btn--tertiaire">Impression</button>
</div>

<div class="fs-segment" role="group" aria-label="Période affichée">
  <button type="button" aria-pressed="true">Mois</button>
  <button type="button" aria-pressed="false">Trimestre</button>
  <button type="button" aria-pressed="false">Année</button>
</div>
```

#### Barre d'actions `.fs-actions`

Sur écran large, l'action principale est à droite, après les actions secondaires. Sur écran étroit, l'ordre s'inverse et les boutons passent en pleine largeur : le pouce atteint d'abord ce qui compte.

*Exemple — Barre d'actions*

```html
<div class="fs-actions fs-actions--fin">
  <button type="button" class="fs-btn fs-btn--fantome">Annuler</button>
  <button type="button" class="fs-btn fs-btn--tertiaire">Enregistrer</button>
  <button type="button" class="fs-btn fs-btn--principal">Valider et transmettre</button>
</div>
```

#### Liens `a`

*Exemple — Liens*

```html
<p>
  Consultez la <a href="#">notice explicative</a> avant de commencer.
  Le texte intégral est publié sur le
  <a class="fs-lien-externe" href="#">Journal officiel</a>.
</p>
<p>
  <a class="fs-lien-fichier" href="#">
    Formulaire de demande <span class="fs-poids">PDF — 240 Ko</span>
  </a>
</p>
```

Un lien vers un fichier annonce toujours son format et son poids : sur une connexion facturée au volume, cette information conditionne la décision de l'usager.

### Formulaires

Le formulaire est le point de contact le plus exigeant entre l'usager et l'administration. Chaque champ porte une étiquette persistante, une aide placée avant la saisie, et un message d'erreur qui indique la correction attendue plutôt que de constater la faute.

#### Champ de saisie `.fs-champ`

*Exemple — Champ de texte*

```html
<div class="fs-champ">
  <label class="fs-label" for="cnib">
    Numéro CNIB <span class="fs-requis" aria-hidden="true">*</span>
  </label>
  <p class="fs-aide" id="cnib-aide">
    Le numéro figure au recto de votre carte, sous la photographie.
    Format. Une lettre suivie de sept chiffres.
  </p>
  <input class="fs-saisie" type="text" id="cnib" name="cnib" placeholder="B1234567" aria-describedby="cnib-aide" required>
</div>
```

#### États de validation

*Exemple — Validation*

```html
<div class="fs-champ" data-etat="erreur">
  <label class="fs-label" for="courriel-ko">Adresse électronique</label>
  <input class="fs-saisie" type="email" id="courriel-ko" value="usager.exemple" aria-invalid="true" aria-describedby="courriel-ko-erreur">
  <p class="fs-message fs-message--erreur" id="courriel-ko-erreur">
    Ajoutez le symbole arobase et le nom de domaine, par exemple
    nom@exemple.bf
  </p>
</div>

<div class="fs-champ" data-etat="succes">
  <label class="fs-label" for="courriel-ok">Adresse électronique</label>
  <input class="fs-saisie" type="email" id="courriel-ok" value="a.ouedraogo@exemple.bf" aria-describedby="courriel-ok-message">
  <p class="fs-message fs-message--succes" id="courriel-ok-message">
    Adresse vérifiée
  </p>
</div>

<div class="fs-champ">
  <label class="fs-label" for="inactif">Numéro de dossier <span class="fs-optionnel">facultatif</span></label>
  <input class="fs-saisie" type="text" id="inactif" value="Attribué après dépôt" disabled>
</div>
```

#### Zone de texte et compteur

*Exemple — Zone de texte*

```html
<div class="fs-champ">
  <label class="fs-label" for="motif">Motif de la demande</label>
  <p class="fs-aide">Décrivez en quelques phrases l'objet de votre démarche.</p>
  <textarea class="fs-zone" id="motif" maxlength="400" data-compteur="motif-reste"></textarea>
  <p class="fs-message fs-legende" id="motif-reste" aria-live="polite"></p>
</div>
```

#### Liste déroulante et champ composé

*Exemple — Sélection*

```html
<div class="fs-champ">
  <label class="fs-label" for="region">Région de résidence</label>
  <select class="fs-select" id="region">
    <option value="">Sélectionnez une région</option>
    <option>Centre</option>
    <option>Hauts-Bassins</option>
    <option>Boucle du Mouhoun</option>
    <option>Sahel</option>
    <option>Est</option>
  </select>
</div>

<div class="fs-champ">
  <label class="fs-label" for="montant">Montant à régler</label>
  <div class="fs-groupe-saisie">
    <input class="fs-saisie fs-tabulaire" type="text" id="montant" value="5 000" inputmode="numeric">
    <span class="fs-affixe">F CFA</span>
  </div>
</div>

<div class="fs-champ">
  <label class="fs-label" for="tel">Téléphone</label>
  <div class="fs-groupe-saisie">
    <span class="fs-affixe">+226</span>
    <input class="fs-saisie fs-tabulaire" type="tel" id="tel" placeholder="70 00 00 00">
  </div>
</div>
```

#### Cases à cocher et boutons radio

*Exemple — Choix*

```html
<fieldset class="fs-fieldset">
  <legend>Canal de retrait</legend>
  <label class="fs-choix">
    <input type="radio" name="retrait" checked>
    <span class="fs-choix-texte">
      <span class="fs-choix-titre">Retrait au guichet</span>
      <span class="fs-choix-aide">Mairie de résidence, du lundi au vendredi, de 7 h 30 à 15 h 30</span>
    </span>
  </label>
  <label class="fs-choix">
    <input type="radio" name="retrait">
    <span class="fs-choix-texte">
      <span class="fs-choix-titre">Envoi postal</span>
      <span class="fs-choix-aide">Frais supplémentaires de 1 500 F CFA, délai de 5 à 10 jours</span>
    </span>
  </label>
</fieldset>

<label class="fs-choix">
  <input type="checkbox">
  <span class="fs-choix-texte">
    <span class="fs-choix-titre">Je certifie l'exactitude des informations fournies</span>
    <span class="fs-choix-aide">Toute fausse déclaration expose à des poursuites.</span>
  </span>
</label>
```

#### Choix en carte

Lorsque le choix engage la suite du parcours, la cible cliquable est étendue à toute la carte. La cible tactile devient bien plus généreuse que la case elle-même.

*Exemple — Choix en carte*

```html
<label class="fs-choix fs-choix--carte" style="flex:1; min-width:220px;">
  <input type="radio" name="qualite" checked>
  <span class="fs-choix-texte">
    <span class="fs-choix-titre">Pour moi-même</span>
    <span class="fs-choix-aide">Vous êtes le titulaire de l'acte demandé.</span>
  </span>
</label>
<label class="fs-choix fs-choix--carte" style="flex:1; min-width:220px;">
  <input type="radio" name="qualite">
  <span class="fs-choix-texte">
    <span class="fs-choix-titre">Pour un tiers</span>
    <span class="fs-choix-aide">Une procuration vous sera demandée.</span>
  </span>
</label>
```

#### Interrupteur et curseur

*Exemple — Interrupteur*

```html
<label class="fs-interrupteur">
  <input type="checkbox" checked>
  <span class="fs-interrupteur-piste"></span>
  <span>Recevoir les notifications par SMS</span>
</label>

<label class="fs-interrupteur">
  <input type="checkbox">
  <span class="fs-interrupteur-piste"></span>
  <span>Autoriser la transmission aux services partenaires</span>
</label>

<div class="fs-champ">
  <label class="fs-label" for="distance">Rayon de recherche : 25 km</label>
  <input class="fs-curseur" type="range" id="distance" min="5" max="100" step="5" value="25">
</div>
```

> Un interrupteur applique son effet immédiatement. Si le changement requiert une validation explicite, utilisez une case à cocher suivie d'un bouton.

#### Dépôt de fichier `.fs-depot`

*Exemple — Dépôt de fichier*

```html
<div class="fs-champ">
  <label class="fs-label" for="piece">Copie de la pièce d'identité</label>
  <label class="fs-depot" tabindex="0">
    <span class="fs-depot-titre">Déposez un fichier ou parcourez</span>
    <span class="fs-depot-contraintes">JPG, PNG ou PDF (5 Mo maximum), document lisible et non rogné</span>
    <input type="file" id="piece" accept=".jpg,.png,.pdf">
  </label>
</div>

<div class="fs-fichier">
  <span class="fs-fichier-nom">cnib-recto.jpg</span>
  <span class="fs-fichier-poids">1,2 Mo</span>
  <span class="fs-badge fs-badge--succes">Transmis</span>
  <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" aria-label="Retirer le fichier cnib-recto.jpg"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button>
</div>

<div class="fs-fichier">
  <span class="fs-fichier-nom">justificatif-domicile.pdf</span>
  <span class="fs-fichier-poids">8,4 Mo</span>
  <span class="fs-badge fs-badge--danger">Trop volumineux</span>
  <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" aria-label="Retirer le fichier justificatif-domicile.pdf"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button>
</div>
```

#### Aperçu de fichier `.fs-fichier--apercu`

La vignette rassure sur ce qui a été déposé : le bon document, dans le bon sens, lisible. C'est ce qui évite l'aller-retour le plus fréquent d'un service d'enrôlement. Elle ne remplace pas le nom du fichier, qui reste l'information portée au lecteur d'écran ; la vignette est donc décorative.

*Exemple — Aperçu de fichier*

```html
<div class="fs-fichier fs-fichier--apercu">
  <span class="fs-fichier-vignette" aria-hidden="true">
    <img src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2035%2045'%3E%3Crect%20width='35'%20height='45'%20fill='%23cfc8bf'/%3E%3Ccircle%20cx='17.5'%20cy='17'%20r='7'%20fill='%23a9a199'/%3E%3Cpath%20d='M4%2045c2-9%207.5-13%2013.5-13S29%2036%2031%2045z'%20fill='%23a9a199'/%3E%3C/svg%3E" alt="">
  </span>
  <div class="fs-fichier-detail">
    <span class="fs-fichier-nom">cnib-recto.jpg</span>
    <span class="fs-fichier-poids">1,2 Mo — 1 240 × 780 px</span>
    <span class="fs-statut fs-statut--succes">Lisible, prêt à être transmis</span>
    <div class="fs-fichier-actions">
      <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm">
        <span class="fs-icone fs-icone--oeil" aria-hidden="true"></span>
        Agrandir
      </button>
      <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm">Remplacer</button>
    </div>
  </div>
</div>

<div class="fs-fichier fs-fichier--apercu">
  <span class="fs-fichier-vignette" aria-hidden="true">
    <span class="fs-icone fs-icone--document"></span>
  </span>
  <div class="fs-fichier-detail">
    <span class="fs-fichier-nom">quittance-timbre.pdf</span>
    <span class="fs-fichier-poids">84 Ko — 1 page</span>
    <span class="fs-statut fs-statut--cours">Vérification en cours</span>
  </div>
</div>

<div class="fs-fichier fs-fichier--apercu">
  <span class="fs-fichier-vignette" aria-hidden="true">
    <span class="fs-icone fs-icone--alerte"></span>
  </span>
  <div class="fs-fichier-detail">
    <span class="fs-fichier-nom">justificatif.jpg</span>
    <span class="fs-fichier-poids">2,1 Mo — 480 × 320 px</span>
    <span class="fs-statut fs-statut--rejet">Résolution insuffisante, document illisible</span>
    <div class="fs-fichier-actions">
      <button type="button" class="fs-btn fs-btn--secondaire fs-btn--sm">Déposer une autre copie</button>
    </div>
  </div>
</div>
```

> Un PDF n'est pas rendu. Le coût de calcul est sans rapport avec le service rendu. Une icône de document suffit, le nom et le nombre de pages portant l'information utile.

#### Recherche et code de vérification

*Exemple — Recherche et code*

```html
<div class="fs-champ">
  <label class="fs-label" for="q">Rechercher une démarche</label>
  <div class="fs-recherche">
    <input class="fs-saisie" type="search" id="q" placeholder="Acte de naissance, passeport, permis…">
    <button type="submit" class="fs-btn fs-btn--principal">Rechercher</button>
  </div>
</div>

<div class="fs-champ">
  <label class="fs-label" for="code-1">Code reçu par SMS</label>
  <p class="fs-aide">Six chiffres envoyés au +226 70 •• •• 42</p>
  <div class="fs-code">
    <input type="text" id="code-1" inputmode="numeric" maxlength="1" aria-label="Chiffre 1">
    <input type="text" inputmode="numeric" maxlength="1" aria-label="Chiffre 2">
    <input type="text" inputmode="numeric" maxlength="1" aria-label="Chiffre 3">
    <input type="text" inputmode="numeric" maxlength="1" aria-label="Chiffre 4">
    <input type="text" inputmode="numeric" maxlength="1" aria-label="Chiffre 5">
    <input type="text" inputmode="numeric" maxlength="1" aria-label="Chiffre 6">
  </div>
</div>
```

#### Saisie de date `.fs-date`

Deux composants pour deux usages que l'on confond souvent. Une date que l'usager connaît par cœur se saisit au clavier en trois champs : ouvrir un calendrier pour remonter quarante ans est une épreuve. Une date à choisir dans les semaines qui viennent se prend dans le sélecteur natif du système, déjà connu de l'usager.

*Exemple — Date connue de l'usager*

```html
<fieldset class="fs-fieldset" style="border:0; padding:0;">
  <legend class="fs-label">Date de naissance <span class="fs-requis" aria-hidden="true">*</span></legend>
  <p class="fs-aide" id="naissance-aide">Telle qu'elle figure sur votre CNIB. Par exemple : 07 11 1984</p>
  <div class="fs-date" aria-describedby="naissance-aide">
    <div class="fs-champ fs-date-jour">
      <label class="fs-label" for="n-jour">Jour</label>
      <input class="fs-saisie" id="n-jour" inputmode="numeric" maxlength="2" placeholder="07">
    </div>
    <div class="fs-champ fs-date-mois">
      <label class="fs-label" for="n-mois">Mois</label>
      <input class="fs-saisie" id="n-mois" inputmode="numeric" maxlength="2" placeholder="11">
    </div>
    <div class="fs-champ fs-date-annee">
      <label class="fs-label" for="n-annee">Année</label>
      <input class="fs-saisie" id="n-annee" inputmode="numeric" maxlength="4" placeholder="1984">
    </div>
  </div>
</fieldset>
```

*Exemple — Date à choisir*

```html
<div class="fs-champ">
  <label class="fs-label" for="rdv">Date du rendez-vous</label>
  <p class="fs-aide">Créneaux ouverts du lundi au vendredi, dans les trente prochains jours.</p>
  <input class="fs-saisie" type="date" id="rdv" min="2026-09-21" max="2026-10-21">
</div>
```

**À faire**

- Trois champs séparés pour une date de naissance, de délivrance ou d'expiration d'un titre.
- `type="date"` pour un rendez-vous ou une échéance proche, avec `min` et `max`.
- Donner un exemple concret de format dans l'aide, jamais seulement « JJ/MM/AAAA ».
- Accepter une date collée depuis le presse-papiers et la répartir sur les trois champs.

**À proscrire**

- Un calendrier pour saisir une date de naissance.
- Trois listes déroulantes. Une liste de 1 900 à 2 026 est inutilisable au pouce.
- Refuser la saisie d'un chiffre au motif que la date serait invalide. On valide à la sortie du champ, pas à la frappe.
- Imposer les zéros de tête : « 7 » vaut « 07 ».

#### Calendrier `.fs-calendrier`

Pour choisir un créneau parmi ceux qu'une administration a ouverts — là où la saisie en trois champs ne convient pas, puisque l'usager ne connaît pas la date à l'avance. Un calendrier est un tableau : les jours d'une même colonne partagent un en-tête, ceux d'une même ligne une semaine. Le balisage suit cette structure, ce qui le rend parcourable sans traitement particulier.

*Exemple — Calendrier*

```html
<div class="fs-calendrier">
  <div class="fs-calendrier-entete">
    <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" aria-label="Mois précédent">
      <span class="fs-icone fs-icone--chevron-gauche" aria-hidden="true"></span>
    </button>
    <span class="fs-calendrier-mois" id="mois-courant">septembre 2026</span>
    <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" aria-label="Mois suivant">
      <span class="fs-icone fs-icone--chevron-droite" aria-hidden="true"></span>
    </button>
  </div>

  <table>
    <caption class="fs-invisible">Créneaux disponibles en septembre 2026</caption>
    <thead>
      <tr>
        <th><abbr title="lundi">L</abbr></th>
        <th><abbr title="mardi">M</abbr></th>
        <th><abbr title="mercredi">M</abbr></th>
        <th><abbr title="jeudi">J</abbr></th>
        <th><abbr title="vendredi">V</abbr></th>
        <th><abbr title="samedi">S</abbr></th>
        <th><abbr title="dimanche">D</abbr></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td></td>
        <td><button type="button" class="fs-jour" disabled aria-label="1 septembre, passé">1</button></td>
        <td><button type="button" class="fs-jour" disabled aria-label="2 septembre, passé">2</button></td>
        <td><button type="button" class="fs-jour" disabled aria-label="3 septembre, passé">3</button></td>
        <td><button type="button" class="fs-jour" disabled aria-label="4 septembre, passé">4</button></td>
        <td><button type="button" class="fs-jour" disabled aria-label="5 septembre, passé">5</button></td>
        <td><button type="button" class="fs-jour" disabled aria-label="6 septembre, passé">6</button></td>
      </tr>
      <tr>
        <td><button type="button" class="fs-jour" disabled>7</button></td>
        <td><button type="button" class="fs-jour" disabled>8</button></td>
        <td><button type="button" class="fs-jour" disabled>9</button></td>
        <td><button type="button" class="fs-jour" disabled>10</button></td>
        <td><button type="button" class="fs-jour" disabled>11</button></td>
        <td><button type="button" class="fs-jour" disabled>12</button></td>
        <td><button type="button" class="fs-jour" disabled>13</button></td>
      </tr>
      <tr>
        <td><button type="button" class="fs-jour" disabled>14</button></td>
        <td><button type="button" class="fs-jour" disabled>15</button></td>
        <td><button type="button" class="fs-jour" disabled>16</button></td>
        <td><button type="button" class="fs-jour" disabled>17</button></td>
        <td><button type="button" class="fs-jour" disabled>18</button></td>
        <td><button type="button" class="fs-jour" disabled>19</button></td>
        <td><button type="button" class="fs-jour" disabled>20</button></td>
      </tr>
      <tr>
        <td>
          <button type="button" class="fs-jour" data-aujourdhui aria-current="date" aria-label="21 septembre, aujourd'hui, aucun créneau" disabled>21</button>
        </td>
        <td>
          <button type="button" class="fs-jour fs-jour-dispo" aria-pressed="false" data-date-libelle="mardi 22 septembre 2026" aria-label="22 septembre, créneaux disponibles">22</button>
        </td>
        <td>
          <button type="button" class="fs-jour fs-jour-dispo" aria-pressed="false" data-date-libelle="mercredi 23 septembre 2026" aria-label="23 septembre, créneaux disponibles">23</button>
        </td>
        <td><button type="button" class="fs-jour" disabled aria-label="24 septembre, complet">24</button></td>
        <td>
          <button type="button" class="fs-jour fs-jour-dispo" aria-pressed="false" data-date-libelle="vendredi 25 septembre 2026" aria-label="25 septembre, créneaux disponibles">25</button>
        </td>
        <td><button type="button" class="fs-jour" disabled aria-label="26 septembre, fermé">26</button></td>
        <td><button type="button" class="fs-jour" disabled aria-label="27 septembre, fermé">27</button></td>
      </tr>
      <tr>
        <td>
          <button type="button" class="fs-jour fs-jour-dispo" aria-pressed="false" data-date-libelle="lundi 28 septembre 2026" aria-label="28 septembre, créneaux disponibles">28</button>
        </td>
        <td>
          <button type="button" class="fs-jour fs-jour-dispo" aria-pressed="false" data-date-libelle="mardi 29 septembre 2026" aria-label="29 septembre, créneaux disponibles">29</button>
        </td>
        <td><button type="button" class="fs-jour" disabled aria-label="30 septembre, complet">30</button></td>
        <td></td><td></td><td></td><td></td>
      </tr>
    </tbody>
  </table>

  <p class="fs-calendrier-legende">
    <span>Point vert : créneaux disponibles</span>
    <span>Barré : complet ou fermé</span>
  </p>
</div>
```

Date retenue : **aucune**

**À faire**

- Produire le mois côté serveur : sans JavaScript, le calendrier reste consultable et les jours ouverts restent atteignables.
- Donner à chaque jour un `aria-label` complet, la seule lecture du chiffre étant insuffisante.
- Distinguer le jour courant du jour choisi : ce sont deux repères différents, qui peuvent tomber sur la même case.
- Doubler la disponibilité d'un repère non chromatique : point pour ouvert, barré pour complet.

**À proscrire**

- Employer un calendrier pour une date que l'usager connaît par cœur : la saisie en trois champs est faite pour ça.
- Afficher un mois sans indiquer ce qui y est disponible : l'usager tâtonne de case en case.
- Rendre un jour complet cliquable pour afficher ensuite un message d'échec.
- Masquer les jours indisponibles : la grille du mois devient illisible.

#### Autocomplétion `.fs-combo`

Pour choisir dans une liste trop longue pour une liste déroulante : communes, administrations, démarches. La règle est simple : **en dessous de quinze entrées, une liste déroulante ; au-delà, une autocomplétion**. Sans JavaScript, le champ reste une saisie libre que le serveur valide — la liste n'est qu'une aide.

*Exemple — Autocomplétion*

```html
<div class="fs-champ fs-combo" data-combo>
  <label class="fs-label" for="commune">Commune de naissance</label>
  <p class="fs-aide">Saisissez les premières lettres, puis choisissez dans la liste.</p>
  <input class="fs-saisie" id="commune" aria-describedby="commune-nb" aria-controls="commune-liste">
  <p class="fs-invisible" id="commune-nb" role="status" aria-live="polite"></p>
  <ul class="fs-combo-liste" id="commune-liste" hidden>
    <li class="fs-combo-option">Ouagadougou</li>
    <li class="fs-combo-option">Bobo-Dioulasso</li>
    <li class="fs-combo-option">Koudougou</li>
    <li class="fs-combo-option">Banfora</li>
    <li class="fs-combo-option">Ouahigouya</li>
    <li class="fs-combo-option">Kaya</li>
    <li class="fs-combo-option">Tenkodogo</li>
    <li class="fs-combo-option">Fada N'Gourma</li>
    <li class="fs-combo-option">Dédougou</li>
    <li class="fs-combo-option">Dori</li>
    <li class="fs-combo-option">Gaoua</li>
    <li class="fs-combo-option">Manga</li>
    <li class="fs-combo-option">Ziniaré</li>
    <li class="fs-combo-vide" hidden>Aucune commune ne correspond. Vérifiez l'orthographe ou saisissez le nom complet.</li>
  </ul>
</div>
```

> La recherche ignore les accents : « dedougou » trouve « Dédougou ». Sur un clavier de téléphone d'entrée de gamme, exiger les accents écarte une part des usagers.

**À faire**

- Annoncer le nombre de résultats dans une région live : sans cela, un usager au lecteur d'écran ignore que la liste a changé.
- Mettre en évidence la portion saisie dans chaque suggestion.
- Proposer une issue explicite quand rien ne correspond.
- Laisser le focus dans le champ. La suggestion courante est désignée par `aria-activedescendant`.

**À proscrire**

- Déplacer le focus dans la liste : l'usager ne peut plus corriger sa saisie.
- Vider le champ quand la saisie ne correspond à rien.
- Exiger une correspondance exacte pour soumettre le formulaire.
- Employer l'autocomplétion pour moins de quinze entrées.

#### Récapitulatif d'erreurs

Placé en tête de formulaire après une soumission invalide, il reçoit le focus et liste chaque erreur sous forme de lien vers le champ concerné. Sur un long formulaire, c'est la seule façon de ne pas laisser l'usager chercher.

*Exemple — Récapitulatif d'erreurs*

```html
<div class="fs-recap-erreurs" role="alert" tabindex="-1">
  <h3>Trois informations doivent être corrigées</h3>
  <ul>
    <li><a href="#cnib">Numéro CNIB : le format attendu est une lettre suivie de sept chiffres</a></li>
    <li><a href="#courriel-ko">Adresse électronique : ajoutez le symbole arobase</a></li>
    <li><a href="#piece">Pièce d'identité. Aucun fichier n'a été transmis</a></li>
  </ul>
</div>
```

### Navigation

L'usager doit savoir en permanence où il se trouve, d'où il vient et ce qu'il lui reste à faire. Ces trois réponses sont portées respectivement par l'état actif, le fil d'Ariane et l'indicateur d'étapes.

#### Fil d'Ariane `.fs-ariane`

*Exemple — Fil d'Ariane*

```html
<nav class="fs-ariane" aria-label="Fil d'Ariane">
  <ol>
    <li><a href="#">Accueil</a></li>
    <li><a href="#">État civil</a></li>
    <li><a href="#">Naissance</a></li>
    <li><span aria-current="page">Demander un acte de naissance</span></li>
  </ol>
</nav>
```

#### Onglets `[role="tablist"]`

Navigation au clavier conforme : flèches pour changer d'onglet, Début et Fin pour atteindre les extrémités. Un seul onglet est atteignable par tabulation, le panneau actif étant la destination suivante.

*Exemple — Onglets*

```html
<div>
  <div class="fs-onglets" role="tablist" aria-label="Informations sur la démarche">
    <button class="fs-onglet" role="tab" id="o1" aria-controls="p1" aria-selected="true">Procédure</button>
    <button class="fs-onglet" role="tab" id="o2" aria-controls="p2" aria-selected="false">Pièces à fournir</button>
    <button class="fs-onglet" role="tab" id="o3" aria-controls="p3" aria-selected="false">Coût et délai</button>
    <button class="fs-onglet" role="tab" id="o4" aria-controls="p4" aria-selected="false">Contact</button>
  </div>
  <div class="fs-panneau" role="tabpanel" id="p1" aria-labelledby="o1" tabindex="0">
    <p>La demande se dépose en ligne ou au guichet de la mairie du lieu de naissance. Le dépôt en ligne nécessite un compte usager vérifié.</p>
  </div>
  <div class="fs-panneau" role="tabpanel" id="p2" aria-labelledby="o2" tabindex="0" hidden>
    <p>Copie de la pièce d'identité du demandeur, justificatif de filiation, et procuration le cas échéant.</p>
  </div>
  <div class="fs-panneau" role="tabpanel" id="p3" aria-labelledby="o3" tabindex="0" hidden>
    <p>Droit de timbre de 500 F CFA. Délai indicatif de traitement. Trois jours ouvrés à compter de la validation du dossier.</p>
  </div>
  <div class="fs-panneau" role="tabpanel" id="p4" aria-labelledby="o4" tabindex="0" hidden>
    <p>Direction générale de la modernisation de l'état civil — Ouagadougou. Permanence téléphonique du lundi au vendredi.</p>
  </div>
</div>
```

#### Étapes `.fs-etapes`

*Exemple — Indicateur d'étapes*

```html
<nav aria-label="Progression de la demande">
  <p class="fs-invisible">Étape 3 sur 5 : pièces justificatives</p>
  <ol class="fs-etapes">
    <li class="fs-etape" data-etat="faite">
      <span class="fs-etape-num">Étape 1</span>
      <span class="fs-etape-titre">Identification</span>
    </li>
    <li class="fs-etape" data-etat="faite">
      <span class="fs-etape-num">Étape 2</span>
      <span class="fs-etape-titre">Objet de la demande</span>
    </li>
    <li class="fs-etape" data-etat="courante" aria-current="step">
      <span class="fs-etape-num">Étape 3</span>
      <span class="fs-etape-titre">Pièces justificatives</span>
    </li>
    <li class="fs-etape">
      <span class="fs-etape-num">Étape 4</span>
      <span class="fs-etape-titre">Paiement</span>
    </li>
    <li class="fs-etape">
      <span class="fs-etape-num">Étape 5</span>
      <span class="fs-etape-titre">Récapitulatif</span>
    </li>
  </ol>
</nav>
```

#### Pagination `.fs-pagination`

*Exemple — Pagination*

```html
<nav class="fs-pagination" aria-label="Pagination des résultats">
  <a href="#" aria-label="Page précédente"><span class="fs-icone fs-icone--chevron-gauche" aria-hidden="true"></span></a>
  <a href="#">1</a>
  <a href="#" aria-current="page">2</a>
  <a href="#">3</a>
  <a href="#">4</a>
  <span class="fs-ellipse" aria-hidden="true">…</span>
  <a href="#">18</a>
  <a href="#" aria-label="Page suivante"><span class="fs-icone fs-icone--chevron-droite" aria-hidden="true"></span></a>
</nav>
```

#### Navigation principale et barre de service

*Exemple — En-tête complet*

```html
<div>
  <div class="fs-barre-service">
    <div class="fs-conteneur">
      <span>Ministère de l'Économie et des Finances</span>
      <a href="#">Nous contacter</a>
    </div>
  </div>
  <div class="fs-filet"></div>
  <nav class="fs-nav" aria-label="Exemple de navigation">
    <div class="fs-conteneur">
      <ul class="fs-nav-liste">
        <li><a class="fs-nav-lien" href="#" aria-current="page">Démarches</a></li>
        <li><a class="fs-nav-lien" href="#">Mon dossier</a></li>
        <li><a class="fs-nav-lien" href="#">Paiements</a></li>
        <li><a class="fs-nav-lien" href="#">Aide</a></li>
      </ul>
    </div>
  </nav>
</div>
```

### Information

Présenter un contenu sans hiérarchiser l'urgence revient à ne rien hiérarchiser. Les encarts distinguent quatre registres, du simple repère documentaire à l'alerte critique.

#### Encarts `.fs-encart`

*Exemple — Encarts*

```html
<div class="fs-encart fs-encart--info">
  <span class="fs-encart-marque fs-icone fs-icone--information" aria-hidden="true"></span>
  <div class="fs-encart-corps">
    <p class="fs-encart-titre">Pour information</p>
    <p>Les guichets sont fermés les jours fériés. Le dépôt en ligne reste disponible sans interruption.</p>
  </div>
</div>

<div class="fs-encart fs-encart--succes">
  <span class="fs-encart-marque fs-icone fs-icone--succes" aria-hidden="true"></span>
  <div class="fs-encart-corps">
    <p class="fs-encart-titre">Demande enregistrée</p>
    <p>Votre dossier porte la référence BF-2026-004871-OUA. Un récépissé a été envoyé à votre adresse électronique.</p>
  </div>
</div>

<div class="fs-encart fs-encart--alerte">
  <span class="fs-encart-marque fs-icone fs-icone--alerte" aria-hidden="true"></span>
  <div class="fs-encart-corps">
    <p class="fs-encart-titre">Pièce complémentaire attendue</p>
    <p>Un justificatif de domicile de moins de trois mois doit être transmis avant le 30 septembre 2026, faute de quoi le dossier sera classé sans suite.</p>
  </div>
</div>

<div class="fs-encart fs-encart--danger" role="alert">
  <span class="fs-encart-marque fs-icone fs-icone--erreur" aria-hidden="true"></span>
  <div class="fs-encart-corps">
    <p class="fs-encart-titre">Demande rejetée</p>
    <p>La pièce d'identité transmise est illisible. Déposez une nouvelle copie pour relancer l'instruction du dossier.</p>
  </div>
</div>
```

#### Encart refermable `.fs-encart-fermer`

Un encart ne devient refermable que si sa disparition ne fait perdre aucune information nécessaire à la tâche en cours. Une erreur de saisie, une échéance et un avertissement de fraude ne se ferment jamais : l'usager les écarterait d'un geste et poursuivrait sans les avoir lues.

*Exemple — Encart refermable*

```html
<div class="fs-encart fs-encart--info" role="status">
  <span class="fs-encart-marque fs-icone fs-icone--information" aria-hidden="true"></span>
  <div class="fs-encart-corps">
    <p class="fs-encart-titre">Nouveau service</p>
    <p>Le certificat de résidence peut désormais être demandé en ligne dans toutes les communes de la région du Centre.</p>
  </div>
  <button type="button" class="fs-encart-fermer" data-ferme-encart aria-label="Fermer ce message">
    <span class="fs-icone fs-icone--fermer" aria-hidden="true"></span>
  </button>
</div>

<div class="fs-encart fs-encart--danger" role="alert">
  <span class="fs-encart-marque fs-icone fs-icone--erreur" aria-hidden="true"></span>
  <div class="fs-encart-corps">
    <p class="fs-encart-titre">Demande rejetée</p>
    <p>Cet encart n'a pas de commande de fermeture. Le motif du rejet et la voie de recours doivent rester visibles.</p>
  </div>
</div>
```

> À la fermeture, le focus est repris explicitement sur l'élément suivant. Sans cela, un usager au clavier se retrouve renvoyé en début de document, l'élément qui portait son focus ayant disparu.

#### Bandeau d'urgence `.fs-bandeau`

Réservé aux communications de portée nationale. Son emploi pour une information de service courante épuiserait sa valeur d'alerte.

*Exemple — Bandeau*

```html
<div class="fs-bandeau" role="alert">
  <div class="fs-conteneur">
    <span class="fs-bandeau-etiquette">Alerte</span>
    <span>Interruption programmée des téléservices le dimanche 27 septembre de 0 h à 6 h.</span>
    <a href="#">En savoir plus</a>
  </div>
</div>
```

#### Étiquettes, pastilles et compteurs

*Exemple — Étiquettes*

```html
<span class="fs-badge fs-badge--neutre">Brouillon</span>
<span class="fs-badge fs-badge--info">En instruction</span>
<span class="fs-badge fs-badge--succes">Validé</span>
<span class="fs-badge fs-badge--alerte">Complément demandé</span>
<span class="fs-badge fs-badge--danger">Rejeté</span>
<span class="fs-badge fs-badge--relief">Nouveau</span>

<span class="fs-etiquette">Ouagadougou <button type="button" aria-label="Retirer le filtre Ouagadougou"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button></span>

<span class="fs-statut fs-statut--cours">En cours d'instruction</span>
<span class="fs-statut fs-statut--succes">Disponible au retrait</span>

<span class="fs-compteur">4</span>
```

> La pastille d'état `.fs-statut` associe un point coloré à un libellé explicite. Un usager daltonien ou un écran monochrome lisent la même information.

#### Avatars

*Exemple — Avatars*

```html
<span class="fs-avatar fs-avatar--sm">ao</span>
<span class="fs-avatar">ao</span>
<span class="fs-avatar fs-avatar--lg">ao</span>
<div class="fs-avatars">
  <span class="fs-avatar" title="A. Ouédraogo">ao</span>
  <span class="fs-avatar" title="S. Kaboré">sk</span>
  <span class="fs-avatar" title="M. Traoré">mt</span>
  <span class="fs-avatar" title="3 autres agents">+3</span>
</div>
```

#### Cartes `.fs-carte`

*Exemple — Cartes*

```html
<div class="fs-auto-grille" style="--min-colonne: 260px;">
  <article class="fs-carte fs-carte--cliquable">
    <div class="fs-carte-corps">
      <span class="fs-badge fs-badge--relief" style="align-self:flex-start;">Actualité</span>
      <h4 class="fs-carte-titre"><a href="#">Ouverture du téléservice de délivrance des actes d'état civil</a></h4>
      <p class="fs-carte-texte">Les communes de la région du Centre basculent sur la plateforme nationale à compter du 1er octobre 2026.</p>
    </div>
    <div class="fs-carte-pied">
      <p class="fs-legende">Publié le 18 septembre 2026</p>
    </div>
  </article>

  <article class="fs-carte fs-carte--marquee">
    <div class="fs-carte-corps">
      <h4 class="fs-carte-titre">Communiqué officiel</h4>
      <p class="fs-carte-texte">Le filet tricolore en tête de carte signale un contenu émanant directement du Gouvernement.</p>
    </div>
  </article>

  <div class="fs-stat">
    <span class="fs-stat-valeur">128 400</span>
    <span class="fs-stat-libelle">demandes traitées depuis janvier</span>
    <span class="fs-stat-variation fs-stat-variation--hausse">+12 % sur un an</span>
  </div>
</div>
```

#### Listes

*Exemple — Listes*

```html
<ul class="fs-liste-puces">
  <li>Pièce d'identité en cours de validité</li>
  <li>Justificatif de domicile de moins de trois mois</li>
  <li>Quittance de paiement du droit de timbre</li>
</ul>

<ul class="fs-liste-controle">
  <li>Le dossier a été transmis au service instructeur</li>
  <li>Le paiement a été encaissé</li>
  <li>Le récépissé a été adressé au demandeur</li>
</ul>
```

#### Infobulle `.fs-infobulle`

L'infobulle apparaît au survol et à la prise de focus. Elle ne porte jamais une information indispensable : sur écran tactile, elle peut rester inaccessible.

*Exemple — Infobulle*

```html
<span class="fs-infobulle">
  <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm" aria-describedby="ib1">Droit de timbre</button>
  <span class="fs-infobulle-contenu" role="tooltip" id="ib1">
    Taxe forfaitaire perçue au profit du Trésor public lors de la délivrance d'un document administratif.
  </span>
</span>
```

#### Citation

*Exemple — Citation*

```html
<blockquote>
  Tout usager a le droit d'obtenir communication des documents administratifs
  qui le concernent, dans les conditions fixées par la loi.
  <cite>Article 4 de la loi relative à l'accès à l'information publique</cite>
</blockquote>
```

### Données

Les tableaux portent l'essentiel des données administratives. Ils doivent rester lisibles sur un écran de 320 px de large, ce qui suppose une stratégie d'adaptation décidée dès la conception, pas ajoutée ensuite.

#### Tableau `.fs-tableau`

Tri côté client activé par `data-triable`. L'attribut `data-valeur` permet de trier sur une donnée brute différente de l'affichage, par exemple une date au format ISO. Sur écran étroit, `.fs-tableau--empile` transforme chaque ligne en fiche.

*Exemple — Tableau triable*

```html
<div class="fs-tableau-cadre">
  <table class="fs-tableau fs-tableau--empile" data-triable>
    <caption>Demandes déposées au cours des trente derniers jours</caption>
    <thead>
      <tr>
        <th aria-sort="none"><button type="button">Référence</button></th>
        <th aria-sort="none"><button type="button">Démarche</button></th>
        <th aria-sort="none"><button type="button">Dépôt</button></th>
        <th aria-sort="none"><button type="button">Montant</button></th>
        <th>État</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td data-colonne="Référence"><code>BF-2026-004871</code></td>
        <td data-colonne="Démarche">Acte de naissance</td>
        <td data-colonne="Dépôt" data-valeur="2026-09-18">18/09/2026</td>
        <td data-colonne="Montant" class="fs-num" data-valeur="500">500 F</td>
        <td data-colonne="État"><span class="fs-statut fs-statut--succes">Validé</span></td>
      </tr>
      <tr>
        <td data-colonne="Référence"><code>BF-2026-004902</code></td>
        <td data-colonne="Démarche">Certificat de résidence</td>
        <td data-colonne="Dépôt" data-valeur="2026-09-15">15/09/2026</td>
        <td data-colonne="Montant" class="fs-num" data-valeur="1000">1 000 F</td>
        <td data-colonne="État"><span class="fs-statut fs-statut--cours">En instruction</span></td>
      </tr>
      <tr>
        <td data-colonne="Référence"><code>BF-2026-004915</code></td>
        <td data-colonne="Démarche">Casier judiciaire</td>
        <td data-colonne="Dépôt" data-valeur="2026-09-12">12/09/2026</td>
        <td data-colonne="Montant" class="fs-num" data-valeur="2000">2 000 F</td>
        <td data-colonne="État"><span class="fs-statut fs-statut--alerte">Complément demandé</span></td>
      </tr>
      <tr>
        <td data-colonne="Référence"><code>BF-2026-004930</code></td>
        <td data-colonne="Démarche">Passeport ordinaire</td>
        <td data-colonne="Dépôt" data-valeur="2026-09-02">02/09/2026</td>
        <td data-colonne="Montant" class="fs-num" data-valeur="50000">50 000 F</td>
        <td data-colonne="État"><span class="fs-statut fs-statut--rejet">Rejeté</span></td>
      </tr>
    </tbody>
  </table>
</div>
```

#### Tableau paginé `.fs-tableau-bloc`

Le motif complet : barre d'outils avec le nombre de résultats et le choix du nombre de lignes, tableau triable, puis pied portant l'intervalle affiché et la pagination. Les deux barres restent fixes pendant que le tableau défile horizontalement, sans quoi la pagination s'échapperait hors de l'écran sur un tableau large.

*Exemple — Tableau filtré et paginé*

```html
<div class="fs-tableau-bloc" data-pagination="5" data-filtres data-export="demandes">

  <div class="fs-impression-entete">
    <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg" alt="">
    <div>
      <p class="fs-impression-titre">Demandes déposées</p>
      <p class="fs-impression-meta">Direction générale de la modernisation de l'état civil</p>
      <p class="fs-impression-meta" data-impression-contexte></p>
    </div>
  </div>

  <div class="fs-filtres">
    <div class="fs-champ">
      <label class="fs-label" for="f-recherche">Rechercher</label>
      <input class="fs-saisie" type="search" id="f-recherche" placeholder="Référence, démarche…" data-filtre="recherche">
    </div>
    <div class="fs-champ">
      <label class="fs-label" for="f-etat">État</label>
      <select class="fs-select" id="f-etat" data-filtre="egal" data-filtre-colonne="État">
        <option value="">Tous</option>
        <option>Validé</option>
        <option>En instruction</option>
        <option>Complément demandé</option>
        <option>Rejeté</option>
        <option>Disponible</option>
      </select>
    </div>
    <div class="fs-champ">
      <label class="fs-label" for="f-depuis">Déposé à partir du</label>
      <input class="fs-saisie" type="date" id="f-depuis" data-filtre="min" data-filtre-colonne="Dépôt">
    </div>
    <div class="fs-champ">
      <label class="fs-label" for="f-montant">Montant minimum</label>
      <input class="fs-saisie" type="number" id="f-montant" min="0" step="500" placeholder="0" data-filtre="min" data-filtre-colonne="Montant">
    </div>
  </div>

  <div class="fs-filtres-actifs" hidden></div>

  <div class="fs-tableau-outils">
    <p class="fs-tableau-compte" style="margin:0;">
      <b>0</b> demandes
    </p>
    <div class="fs-rangee">
      <div class="fs-tableau-export">
        <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm" data-export-csv>
          <span class="fs-icone fs-icone--telecharger" aria-hidden="true"></span>
          CSV
        </button>
        <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm" data-export-impression>
          <span class="fs-icone fs-icone--imprimer" aria-hidden="true"></span>
          Imprimer ou PDF
        </button>
      </div>
      <label class="fs-tableau-taille">
        Lignes par page
        <select class="fs-select">
          <option>5</option>
          <option>10</option>
          <option>25</option>
        </select>
      </label>
    </div>
  </div>

  <div class="fs-tableau-cadre">
    <table class="fs-tableau fs-tableau--empile" data-triable>
      <caption class="fs-invisible">Demandes déposées, triables par colonne</caption>
      <thead>
        <tr>
          <th aria-sort="none"><button type="button">Référence</button></th>
          <th aria-sort="none"><button type="button">Démarche</button></th>
          <th aria-sort="none"><button type="button">Dépôt</button></th>
          <th aria-sort="none"><button type="button">Montant</button></th>
          <th>État</th>
        </tr>
      </thead>
      <tbody>
        <tr><td data-colonne="Référence"><code>BF-2026-004871</code></td><td data-colonne="Démarche">Acte de naissance</td><td data-colonne="Dépôt" data-valeur="2026-09-18">18/09/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="500">500 F</td><td data-colonne="État"><span class="fs-statut fs-statut--succes">Validé</span></td></tr>
        <tr><td data-colonne="Référence"><code>BF-2026-004902</code></td><td data-colonne="Démarche">Certificat de résidence</td><td data-colonne="Dépôt" data-valeur="2026-09-15">15/09/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="1000">1 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--cours">En instruction</span></td></tr>
        <tr><td data-colonne="Référence"><code>BF-2026-004915</code></td><td data-colonne="Démarche">Casier judiciaire</td><td data-colonne="Dépôt" data-valeur="2026-09-12">12/09/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="2000">2 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--alerte">Complément demandé</span></td></tr>
        <tr><td data-colonne="Référence"><code>BF-2026-004930</code></td><td data-colonne="Démarche">Passeport ordinaire</td><td data-colonne="Dépôt" data-valeur="2026-09-02">02/09/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="50000">50 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--rejet">Rejeté</span></td></tr>
        <tr><td data-colonne="Référence"><code>BF-2026-004944</code></td><td data-colonne="Démarche">Acte de mariage</td><td data-colonne="Dépôt" data-valeur="2026-08-28">28/08/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="500">500 F</td><td data-colonne="État"><span class="fs-statut fs-statut--succes">Validé</span></td></tr>
        <tr><td data-colonne="Référence"><code>BF-2026-004951</code></td><td data-colonne="Démarche">Certificat de vie</td><td data-colonne="Dépôt" data-valeur="2026-08-24">24/08/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="0">Gratuit</td><td data-colonne="État"><span class="fs-statut fs-statut--succes">Disponible</span></td></tr>
        <tr><td data-colonne="Référence"><code>BF-2026-004968</code></td><td data-colonne="Démarche">Carte grise</td><td data-colonne="Dépôt" data-valeur="2026-08-19">19/08/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="15000">15 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--cours">En instruction</span></td></tr>
        <tr><td data-colonne="Référence"><code>BF-2026-004977</code></td><td data-colonne="Démarche">Légalisation de signature</td><td data-colonne="Dépôt" data-valeur="2026-08-14">14/08/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="200">200 F</td><td data-colonne="État"><span class="fs-statut fs-statut--succes">Validé</span></td></tr>
        <tr><td data-colonne="Référence"><code>BF-2026-004983</code></td><td data-colonne="Démarche">Attestation de résidence</td><td data-colonne="Dépôt" data-valeur="2026-08-08">08/08/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="1000">1 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--alerte">Complément demandé</span></td></tr>
        <tr><td data-colonne="Référence"><code>BF-2026-004990</code></td><td data-colonne="Démarche">Acte de décès</td><td data-colonne="Dépôt" data-valeur="2026-08-03">03/08/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="500">500 F</td><td data-colonne="État"><span class="fs-statut fs-statut--succes">Validé</span></td></tr>
        <tr><td data-colonne="Référence"><code>BF-2026-005002</code></td><td data-colonne="Démarche">Extrait de casier</td><td data-colonne="Dépôt" data-valeur="2026-07-30">30/07/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="2000">2 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--succes">Disponible</span></td></tr>
        <tr><td data-colonne="Référence"><code>BF-2026-005014</code></td><td data-colonne="Démarche">Permis de conduire</td><td data-colonne="Dépôt" data-valeur="2026-07-22">22/07/2026</td><td data-colonne="Montant" class="fs-num" data-valeur="25000">25 000 F</td><td data-colonne="État"><span class="fs-statut fs-statut--rejet">Rejeté</span></td></tr>
      </tbody>
    </table>
  </div>

  <div class="fs-tableau-vide" hidden>
    <p class="fs-tableau-vide-titre"></p>
    <p data-vide-texte></p>
  </div>

  <div class="fs-tableau-pied">
    <p class="fs-tableau-intervalle" style="margin:0;"></p>
    <nav class="fs-pagination" aria-label="Pagination des demandes"></nav>
  </div>

</div>
```

> Les commandes de pagination sont **construites par le script**, jamais écrites dans le balisage. Sans JavaScript, aucune commande n'apparaît et le tableau s'affiche en entier : l'usager voit toutes les données, ce qui est la seule dégradation acceptable. Un pied contenant des liens inertes serait pire que pas de pagination du tout.

##### Déclarer un filtre

Un contrôle indique ce qu'il filtre par `data-filtre`, et sur quelle colonne par `data-filtre-colonne`. La colonne est désignée par son intitulé, celui que les cellules portent déjà dans `data-colonne` pour le mode empilé. Le même attribut sert donc à la lisibilité sur mobile et au filtrage, sans rien ajouter au balisage.

| Valeur | Effet | Contrôle adapté |
| --- | --- | --- |
| `recherche` | Cherche dans toute la ligne, accents ignorés | `type="search"` |
| `egal` | Correspondance exacte sur une colonne | `<select>` |
| `min` | Valeur supérieure ou égale | `type="date"`, `type="number"` |
| `max` | Valeur inférieure ou égale | `type="date"`, `type="number"` |

> Comme pour le tri, `data-valeur` porte la donnée brute quand l'affichage en diffère : `data-valeur="2026-09-18"` pour « 18/09/2026 », `data-valeur="0"` pour « Gratuit ». Les dates ISO sont comparées comme du texte, ce qui donne le bon ordre ; les traiter comme des nombres les tronquerait à l'année.

##### Filtre et pagination

Ce sont deux couches distinctes qu'il ne faut pas confondre : le filtre **écarte** une ligne du jeu de données, la pagination **masque** celles qui ne sont pas sur la page courante. Une ligne écartée ne compte dans aucun total et n'occupe aucune place sur une page. Tout changement de filtre renvoie à la première page.

##### Extraction

Deux sorties, pour deux usages. Le **CSV** sert à exploiter les données dans un tableur. L'**impression** produit un document paginé, destiné à être archivé ou transmis, que tout système d'exploitation sait enregistrer en PDF.

> **Pourquoi pas un PDF fabriqué par la page ?** Les bibliothèques qui le permettent pèsent plusieurs centaines de kilo-octets, soit davantage que l'ensemble de ce système, pour un résultat inférieur à ce que le navigateur sait déjà faire : pagination, en-tête de colonnes répété, polices intégrées. Lorsqu'un PDF doit être *joint* à un envoi — récépissé, quittance, décision : il est produit par le serveur, qui seul peut le signer et lui donner valeur probante.

##### Règles d'extraction

| Point | Règle | Motif |
| --- | --- | --- |
| Étendue | Toutes les lignes retenues par les filtres, pas la page affichée | Une extraction limitée à la page 1 sur 12 est un piège. |
| Valeurs | `data-valeur` prime sur l'affichage | Une date part en ISO et un montant en nombre, exploitables dans un tableur. |
| Séparateur | Point-virgule | C'est celui qu'attend un tableur configuré en français, où la virgule est le séparateur décimal. |
| Encodage | UTF-8 avec indicateur d'ordre des octets | Sans lui, un tableur lit le fichier en encodage local et les accents deviennent illisibles. |
| Colonnes | Celles ne portant que des commandes sont exclues par `data-export="non"` | Une colonne de boutons n'a aucun sens dans un fichier. |
| Nom du fichier | `demandes-2026-09-21-filtre.csv` | La date situe l'extraction, le suffixe signale qu'elle est partielle. |
| Contexte imprimé | Emblème, service, date, nombre de lignes, filtres appliqués | Une extraction qui circule hors du service doit porter son contexte, sans quoi elle n'est pas opposable. |

> **Données personnelles**
>
> Une extraction sort les données du service et échappe à ses protections. N'exposez l'extraction qu'aux agents habilités, limitez-la aux colonnes nécessaires, et journalisez-la côté serveur au même titre qu'une consultation.

##### Où se fait la pagination

Le composant ci-dessus pagine dans le navigateur. Toutes les lignes sont déjà dans la page. C'est acceptable jusqu'à quelques centaines de lignes. Au-delà, la pagination revient au serveur.

| Volume | Où paginer | Commandes |
| --- | --- | --- |
| Moins de 100 lignes | Aucune pagination : tout afficher | — |
| 100 à 500 lignes | Dans le navigateur, `data-pagination` | `<button>` |
| Plus de 500 lignes | Par le serveur, une adresse par page | `<a href="?page=2">` |

**À faire**

- Afficher l'intervalle en clair : « Résultats 1 à 5 sur 12 ». Un numéro de page seul ne dit pas où l'on en est.
- Annoncer le changement de page dans une région live, le contenu changeant sans rechargement.
- Laisser le focus sur la commande utilisée. Le renvoyer en haut du tableau fait perdre sa place à l'usager au clavier.
- Revenir à la première page après un tri ou un changement de filtre.
- Conserver les extrémités visibles dans la fenêtre de pages : `1 … 8 [9] 10 … 18`.
- Côté serveur, donner à chaque page sa propre adresse, partageable et imprimable.
- **Rappeler les filtres appliqués** sous forme d'étiquettes retirables, avec une commande « Tout effacer ».
- Distinguer « aucune donnée » de « aucun résultat pour ces filtres », et proposer une issue dans le second cas.
- Rendre le focus au contrôle après le retrait d'un filtre.
- Côté serveur, porter les filtres dans l'adresse pour qu'une recherche soit partageable.

**À proscrire**

- **Le défilement infini.** Il rend le pied de page inatteignable, interdit de partager une position, casse l'impression et oblige à tout recharger au retour en arrière.
- **Un filtre appliqué sans rappel visible.** Écarter des lignes sans le dire est un piège : dans un contexte administratif, l'absence d'une ligne se lit comme l'absence d'un dossier.
- Charger toutes les lignes puis en masquer 95 % au-delà de quelques centaines : le poids est transféré quand même.
- Désactiver le tri parce que la pagination est côté serveur : le tri porte alors sur l'ensemble des données, pas sur la page affichée.
- Afficher la pagination quand il n'y a qu'une page.
- Conserver un filtre d'une visite à l'autre sans le signaler.

#### Liste de définitions `.fs-definitions`

Employée pour les fiches : elle exprime une relation clé-valeur sans recourir à un tableau, dont la sémantique supposerait deux dimensions.

*Exemple — Définitions*

```html
<dl class="fs-definitions">
  <div><dt>Administration responsable</dt><dd>Direction générale de la modernisation de l'état civil</dd></div>
  <div><dt>Public concerné</dt><dd>Toute personne née au Burkina Faso ou de parents burkinabè</dd></div>
  <div><dt>Coût</dt><dd>500 F CFA — droit de timbre</dd></div>
  <div><dt>Délai indicatif</dt><dd>Trois jours ouvrés à compter de la validation du dossier</dd></div>
  <div><dt>Canaux disponibles</dt><dd>En ligne, guichet de la mairie du lieu de naissance</dd></div>
</dl>
```

#### Visualisation `.fs-graphe`

Aucun graphique n'est produit par une bibliothèque tierce : quelques centaines de kilo-octets de code pour dessiner cinq barres ne se justifient pas sur un appareil modeste. Les formes ci-dessous sont obtenues en CSS ou en SVG écrit à la main, et s'accompagnent **toujours** du tableau des données, qui reste la source consultable par tous.

*Exemple — Barres horizontales*

```html
<div class="fs-graphe">
  <p class="fs-graphe-titre">Demandes déposées par région</p>
  <p class="fs-graphe-note">Trente derniers jours. Source : plateforme des téléservices.</p>

  <div class="fs-barres" data-max="42000">
    <div class="fs-barre" data-valeur="38400">
      <span class="fs-barre-libelle">Centre</span>
      <span class="fs-barre-valeur">38 400</span>
      <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
    </div>
    <div class="fs-barre" data-valeur="21700">
      <span class="fs-barre-libelle">Hauts-Bassins</span>
      <span class="fs-barre-valeur">21 700</span>
      <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
    </div>
    <div class="fs-barre" data-valeur="12300">
      <span class="fs-barre-libelle">Boucle du Mouhoun</span>
      <span class="fs-barre-valeur">12 300</span>
      <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
    </div>
    <div class="fs-barre" data-valeur="8100">
      <span class="fs-barre-libelle">Est</span>
      <span class="fs-barre-valeur">8 100</span>
      <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
    </div>
    <div class="fs-barre" data-valeur="5400">
      <span class="fs-barre-libelle">Sahel</span>
      <span class="fs-barre-valeur">5 400</span>
      <span class="fs-barre-piste"><span class="fs-barre-remplissage"></span></span>
    </div>
  </div>

  <details class="fs-graphe-donnees">
    <summary>Consulter les données sous forme de tableau</summary>
    <div class="fs-tableau-cadre">
      <table class="fs-tableau fs-tableau--compact">
        <thead><tr><th>Région</th><th class="fs-num">Demandes</th></tr></thead>
        <tbody>
          <tr><td>Centre</td><td class="fs-num">38 400</td></tr>
          <tr><td>Hauts-Bassins</td><td class="fs-num">21 700</td></tr>
          <tr><td>Boucle du Mouhoun</td><td class="fs-num">12 300</td></tr>
          <tr><td>Est</td><td class="fs-num">8 100</td></tr>
          <tr><td>Sahel</td><td class="fs-num">5 400</td></tr>
        </tbody>
      </table>
    </div>
  </details>
</div>
```

*Exemple — Répartition*

```html
<div class="fs-graphe">
  <p class="fs-graphe-titre">État des dossiers en cours</p>
  <p class="fs-graphe-note">12 480 dossiers au 21 septembre 2026.</p>

  <div class="fs-repartition" role="img" aria-label="En instruction 54 %, complément demandé 21 %, validés 18 %, rejetés 7 %">
    <span style="width:54%; background: var(--serie-1);">54 %</span>
    <span style="width:21%; background: var(--serie-2); color: var(--neutre-900);">21 %</span>
    <span style="width:18%; background: var(--serie-3);">18 %</span>
    <span style="width:7%;  background: var(--serie-4);"></span>
  </div>

  <div class="fs-repartition-legende">
    <span style="--puce: var(--serie-1);">En instruction · 54 %</span>
    <span style="--puce: var(--serie-2);">Complément demandé · 21 %</span>
    <span style="--puce: var(--serie-3);">Validés · 18 %</span>
    <span style="--puce: var(--serie-4);">Rejetés · 7 %</span>
  </div>

  <details class="fs-graphe-donnees">
    <summary>Consulter les données sous forme de tableau</summary>
    <div class="fs-tableau-cadre">
      <table class="fs-tableau fs-tableau--compact">
        <thead>
          <tr><th>État</th><th class="fs-num">Dossiers</th><th class="fs-num">Part</th></tr>
        </thead>
        <tbody>
          <tr><td>En instruction</td><td class="fs-num">6 739</td><td class="fs-num">54 %</td></tr>
          <tr><td>Complément demandé</td><td class="fs-num">2 621</td><td class="fs-num">21 %</td></tr>
          <tr><td>Validés</td><td class="fs-num">2 246</td><td class="fs-num">18 %</td></tr>
          <tr><td>Rejetés</td><td class="fs-num">874</td><td class="fs-num">7 %</td></tr>
        </tbody>
      </table>
    </div>
  </details>
</div>
```

*Exemple — Courbe*

```html
<div class="fs-graphe">
  <p class="fs-graphe-titre">Délai moyen de traitement</p>
  <p class="fs-graphe-note">En jours ouvrés, de mars à septembre 2026. Données d'exemple.</p>

  <svg class="fs-courbe" viewbox="0 0 420 180" role="img" aria-label="Le délai moyen passe de 9 jours ouvrés en mars à 3 jours ouvrés en septembre 2026, en baisse chaque mois.">
    <path class="fs-courbe-grille" d="M40 20h370M40 60h370M40 100h370"></path>
    <path class="fs-courbe-axe" d="M40 140h370"></path>
    <!-- Échelle linéaire : y = 140 - délai × (120 / 9), de 0 à 9 jours. -->
    <path class="fs-courbe-aire" d="M40 20 100 41.333 160 48 220 76 280 86.667 340 94.667 400 100V140H40Z"></path>
    <path class="fs-courbe-trace" d="M40 20 100 41.333 160 48 220 76 280 86.667 340 94.667 400 100"></path>
    <g class="fs-courbe-point">
      <circle cx="40" cy="20" r="4"></circle><circle cx="100" cy="41.333" r="4"></circle>
      <circle cx="160" cy="48" r="4"></circle><circle cx="220" cy="76" r="4"></circle>
      <circle cx="280" cy="86.667" r="4"></circle><circle cx="340" cy="94.667" r="4"></circle>
      <circle cx="400" cy="100" r="4"></circle>
    </g>
    <g class="fs-courbe-etiquette" text-anchor="middle">
      <text x="40" y="168">Mars</text><text x="100" y="168">Avril</text>
      <text x="160" y="168">Mai</text><text x="220" y="168">Juin</text>
      <text x="280" y="168">Juil.</text><text x="340" y="168">Août</text>
      <text x="400" y="168">Sept.</text>
    </g>
    <g class="fs-courbe-etiquette" text-anchor="end">
      <text x="32" y="24">9 j</text><text x="32" y="64">6 j</text><text x="32" y="104">3 j</text>
      <text x="32" y="144">0 j</text>
    </g>
  </svg>

  <details class="fs-graphe-donnees">
    <summary>Consulter les données sous forme de tableau</summary>
    <div class="fs-tableau-cadre">
      <table class="fs-tableau fs-tableau--compact">
        <thead><tr><th>Mois</th><th class="fs-num">Délai moyen</th></tr></thead>
        <tbody>
          <tr><td>Mars</td><td class="fs-num">9,0 j</td></tr>
          <tr><td>Avril</td><td class="fs-num">7,4 j</td></tr>
          <tr><td>Mai</td><td class="fs-num">6,9 j</td></tr>
          <tr><td>Juin</td><td class="fs-num">4,8 j</td></tr>
          <tr><td>Juillet</td><td class="fs-num">4,0 j</td></tr>
          <tr><td>Août</td><td class="fs-num">3,4 j</td></tr>
          <tr><td>Septembre</td><td class="fs-num">3,0 j</td></tr>
        </tbody>
      </table>
    </div>
  </details>
</div>
```

##### Palette de séries

Les séries sont ordonnées par **écart de luminosité** et non par teinte : un graphique doit rester lisible en niveaux de gris et pour un usager daltonien. Six séries au maximum ; au-delà, un tableau vaut mieux qu'un graphique.

--serie-1 Série principale, valeur de référence

--serie-2 Deuxième série, contraste maximal avec la première

--serie-3 Troisième série

--serie-4 Reste, autres, non renseigné

--serie-5 Cinquième série

--serie-6 Sixième série

**À faire**

- Joindre systématiquement le tableau des données, replié sous le graphique.
- Donner au graphique un `role="img"` et un `aria-label` qui énonce la tendance, pas la liste des valeurs.
- Étiqueter directement chaque barre. Une légende oblige l'œil à faire des allers-retours.
- Indiquer la source et la date d'arrêté des données.
- Faire partir l'axe des valeurs de zéro.

**À proscrire**

- Le camembert : l'œil compare mal des angles. Une répartition en bande fait le même travail.
- Distinguer des séries par la seule couleur.
- Tronquer l'axe pour accentuer une variation.
- Les effets de profondeur, les dégradés et les animations d'apparition.
- Employer le vert et le rouge du drapeau comme couleurs de série : ils signifient déjà « validé » et « rejeté ».

#### Chronologie `.fs-chrono`

*Exemple — Chronologie*

```html
<ol class="fs-chrono">
  <li data-etat="faite">
    <span class="fs-chrono-date">18 septembre 2026 — 09 h 12</span>
    <span class="fs-chrono-titre">Demande déposée</span>
    <p class="fs-legende">Dépôt effectué en ligne. Récépissé adressé par courrier électronique.</p>
  </li>
  <li data-etat="faite">
    <span class="fs-chrono-date">18 septembre 2026 — 14 h 40</span>
    <span class="fs-chrono-titre">Paiement encaissé</span>
    <p class="fs-legende">Droit de timbre de 500 F CFA réglé par monnaie électronique.</p>
  </li>
  <li data-etat="courante">
    <span class="fs-chrono-date">19 septembre 2026</span>
    <span class="fs-chrono-titre">En cours d'instruction</span>
    <p class="fs-legende">Dossier transmis au service compétent de la mairie du lieu de naissance.</p>
  </li>
  <li>
    <span class="fs-chrono-date">À venir</span>
    <span class="fs-chrono-titre">Document disponible au retrait</span>
  </li>
</ol>
```

### Retour système

Une interface qui ne dit rien pendant qu'elle travaille est perçue comme défaillante. Sur réseau instable, le retour système n'est pas un confort : c'est ce qui évite les doubles soumissions et les abandons.

#### Notification passagère `.fs-toast`

Appelée par `Faso.notifier()`. Le conteneur est une région live polie. Le message est annoncé sans interrompre la tâche en cours. Une notification ne porte jamais une information que l'usager doit retrouver plus tard.

*Exemple — Notifications*

```html
<button type="button" class="fs-btn fs-btn--secondaire" onclick="Faso.notifier({titre:'Brouillon enregistré', texte:'Vous pourrez reprendre votre demande pendant trente jours.', ton:'succes'})">
  Notification de succès
</button>
<button type="button" class="fs-btn fs-btn--secondaire" onclick="Faso.notifier({titre:'Connexion interrompue', texte:'Vos modifications seront transmises au rétablissement du réseau.', ton:'alerte'})">
  Notification d'alerte
</button>
```

#### Centre de notifications `.fs-notifications`

Historique consultable, distinct de la notification passagère qui, elle, disparaît. Il ne porte **jamais seul** une information exigeant une action. Un usager qui ne revient pas sur le service ne la verrait pas. Le SMS reste le canal qui l'atteint hors du service, le centre n'en est que la trace.

*Exemple — Centre de notifications*

```html
<div class="fs-notifications">
  <div class="fs-notifications-entete">
    <h4 class="fs-notifications-titre">
      Notifications
      <span class="fs-compteur" data-notifications-compte>0</span>
    </h4>
    <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm" data-tout-lu>
      Tout marquer comme lu
    </button>
  </div>

  <ul class="fs-notifications-liste">
    <li class="fs-notification" data-lu="false">
      <span class="fs-notification-puce" aria-hidden="true"></span>
      <span class="fs-notification-icone fs-icone fs-icone--alerte" aria-hidden="true"></span>
      <div class="fs-notification-corps">
        <p class="fs-notification-titre">Pièce complémentaire attendue</p>
        <p class="fs-notification-texte">Dossier BF-2026-004915 : un justificatif de domicile est attendu avant le 30 septembre 2026.</p>
        <time class="fs-notification-date" datetime="2026-09-19">19 septembre, 08 h 12</time>
      </div>
      <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm" data-marquer-lu>Marquer comme lu</button>
    </li>

    <li class="fs-notification" data-lu="false">
      <span class="fs-notification-puce" aria-hidden="true"></span>
      <span class="fs-notification-icone fs-icone fs-icone--succes" aria-hidden="true"></span>
      <div class="fs-notification-corps">
        <p class="fs-notification-titre">Paiement encaissé</p>
        <p class="fs-notification-texte">Droit de timbre de 500 F CFA réglé pour le dossier BF-2026-004871.</p>
        <time class="fs-notification-date" datetime="2026-09-18">18 septembre, 14 h 40</time>
      </div>
      <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm" data-marquer-lu>Marquer comme lu</button>
    </li>

    <li class="fs-notification" data-lu="true">
      <span class="fs-notification-puce" aria-hidden="true"></span>
      <span class="fs-notification-icone fs-icone fs-icone--document" aria-hidden="true"></span>
      <div class="fs-notification-corps">
        <p class="fs-notification-titre">Demande enregistrée</p>
        <p class="fs-notification-texte">Votre demande d'acte de naissance porte la référence BF-2026-004871.</p>
        <time class="fs-notification-date" datetime="2026-09-18">18 septembre, 09 h 12</time>
      </div>
    </li>
  </ul>
</div>
```

**À faire**

- Rendre la liste au serveur. Elle doit être lisible sans JavaScript.
- Signaler le non-lu par trois repères (fond, graisse et pastille), dont aucun n'est la couleur seule.
- Dater chaque entrée avec un `<time>` exploitable.
- Rappeler la référence du dossier dans tout message de suivi.

**À proscrire**

- N'exister que dans le centre pour une action attendue avec échéance.
- Marquer comme lu au simple défilement : l'usager n'a rien confirmé.
- Accumuler sans limite : au-delà de trois mois, les entrées sont archivées.
- Notifier une étape purement technique sans effet pour l'usager.

#### Progression `.fs-jauge`

*Exemple — Progression*

```html
<div>
  <div class="fs-rangee fs-rangee--entre" style="margin-bottom: var(--espace-2);">
    <span class="fs-legende">Dossier complété</span>
    <span class="fs-legende fs-tabulaire">60 %</span>
  </div>
  <div class="fs-jauge" data-valeur="60" aria-label="Dossier complété à 60 %">
    <div class="fs-jauge-valeur"></div>
  </div>
</div>

<div>
  <div class="fs-rangee fs-rangee--entre" style="margin-bottom: var(--espace-2);">
    <span class="fs-legende">Espace de stockage utilisé</span>
    <span class="fs-legende fs-tabulaire">92 %</span>
  </div>
  <div class="fs-jauge fs-jauge--danger" data-valeur="92" aria-label="Espace utilisé à 92 %">
    <div class="fs-jauge-valeur"></div>
  </div>
</div>

<div class="fs-rangee">
  <span class="fs-sablier" role="status" aria-label="Chargement en cours"></span>
  <span class="fs-legende">Vérification du numéro CNIB auprès de l'ONI…</span>
</div>
```

#### Squelette de chargement `.fs-squelette`

Préférable au sablier lorsque la forme du contenu attendu est connue : l'usager anticipe la mise en page et perçoit l'attente comme plus courte.

*Exemple — Squelette*

```html
<div aria-hidden="true">
  <div class="fs-squelette fs-squelette--titre"></div>
  <div class="fs-squelette fs-squelette--texte"></div>
  <div class="fs-squelette fs-squelette--texte" style="width: 92%"></div>
  <div class="fs-squelette fs-squelette--texte" style="width: 74%"></div>
</div>
```

#### État vide `.fs-vide`

*Exemple — État vide*

```html
<div class="fs-vide">
  <h4 class="fs-vide-titre">Aucune demande en cours</h4>
  <p>Vos démarches déposées apparaîtront ici. Vous pourrez suivre leur instruction et télécharger les documents délivrés.</p>
  <a class="fs-btn fs-btn--principal" href="#">Rechercher une démarche</a>
</div>
```

> Un état vide explique pourquoi la zone est vide et propose l'action qui la remplira. Un simple « Aucun résultat » laisse l'usager sans issue.

### Superposition

Toute superposition interrompt la tâche en cours. Elle doit donc être justifiée par une décision à prendre, jamais employée pour afficher un contenu qui aurait sa place dans la page.

#### Accordéon `.fs-accordeon`

Bâti sur `<details>` et `<summary>` : il fonctionne sans JavaScript, reste ouvrable par la recherche du navigateur et s'imprime correctement.

*Exemple — Accordéon*

```html
<div class="fs-accordeon">
  <details open>
    <summary>Puis-je déposer une demande pour un proche ?</summary>
    <div class="fs-accordeon-corps">
      <p>Oui, à condition de produire une procuration écrite et la copie de la pièce d'identité du mandant. Pour un mineur, le lien de filiation suffit.</p>
    </div>
  </details>
  <details>
    <summary>Que faire si je n'ai pas reçu mon récépissé ?</summary>
    <div class="fs-accordeon-corps">
      <p>Vérifiez le dossier de courrier indésirable de votre messagerie. Le récépissé reste téléchargeable depuis votre espace personnel pendant un an.</p>
    </div>
  </details>
  <details>
    <summary>Le paiement en ligne est-il obligatoire ?</summary>
    <div class="fs-accordeon-corps">
      <p>Non. Le droit de timbre peut être réglé au guichet lors du retrait, mais le délai de traitement ne commence qu'à réception du paiement.</p>
    </div>
  </details>
</div>
```

#### Modale `.fs-modale`

Fondée sur `<dialog>`, qui enferme nativement le focus et gère la touche Échap. Le retour du focus sur l'élément déclencheur est rétabli à la fermeture. Sur écran étroit, la modale occupe tout l'écran.

*Exemple — Modale*

```html
<button type="button" class="fs-btn fs-btn--danger" data-ouvre-modale="modale-suppression">
  Supprimer le brouillon
</button>

<dialog class="fs-modale" id="modale-suppression" aria-labelledby="modale-suppression-titre">
  <div class="fs-modale-entete">
    <h3 class="fs-modale-titre" id="modale-suppression-titre">Supprimer ce brouillon ?</h3>
    <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" data-ferme-modale aria-label="Fermer"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button>
  </div>
  <div class="fs-modale-corps">
    <p>Le brouillon de la demande <code>BF-2026-004871</code> et les trois pièces déjà téléversées seront définitivement supprimés.</p>
    <p>Cette action est irréversible.</p>
  </div>
  <div class="fs-modale-pied">
    <button type="button" class="fs-btn fs-btn--tertiaire" data-ferme-modale>Conserver</button>
    <button type="button" class="fs-btn fs-btn--danger" data-ferme-modale>Supprimer définitivement</button>
  </div>
</dialog>
```

> Le libellé du bouton de confirmation décrit l'action (« Supprimer définitivement »), et non « Oui ». Un usager qui ne lit que les boutons doit comprendre ce qu'il déclenche.

#### Panneau latéral `.fs-panneau-lateral`

*Exemple — Panneau latéral*

```html
<button type="button" class="fs-btn fs-btn--tertiaire" data-ouvre-panneau="panneau-filtres">
  Filtrer les résultats
</button>

<div class="fs-voile"></div>

<aside class="fs-panneau-lateral" id="panneau-filtres" aria-hidden="true" aria-label="Filtres de recherche">
  <div class="fs-modale-entete">
    <h3 class="fs-modale-titre">Filtrer</h3>
    <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" data-ferme-panneau aria-label="Fermer les filtres"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button>
  </div>
  <div class="fs-modale-corps">
    <fieldset class="fs-fieldset" style="border:0; padding:0;">
      <legend class="fs-label">État du dossier</legend>
      <label class="fs-choix"><input type="checkbox" checked><span class="fs-choix-texte"><span>En instruction</span></span></label>
      <label class="fs-choix"><input type="checkbox"><span class="fs-choix-texte"><span>Validé</span></span></label>
      <label class="fs-choix"><input type="checkbox"><span class="fs-choix-texte"><span>Rejeté</span></span></label>
    </fieldset>
  </div>
  <div class="fs-modale-pied">
    <button type="button" class="fs-btn fs-btn--fantome" data-ferme-panneau>Réinitialiser</button>
    <button type="button" class="fs-btn fs-btn--principal" data-ferme-panneau>Appliquer</button>
  </div>
</aside>
```

#### Menu déroulant `.fs-menu`

*Exemple — Menu*

```html
<div class="fs-menu" data-menu>
  <button type="button" class="fs-btn fs-btn--tertiaire" aria-haspopup="true" aria-expanded="false">
    Mon compte
  </button>
  <ul class="fs-menu-liste" hidden>
    <li><a href="#">Mes demandes</a></li>
    <li><a href="#">Mes documents</a></li>
    <li><a href="#">Coordonnées</a></li>
    <li><hr class="fs-menu-separateur"></li>
    <li><button type="button">Se déconnecter</button></li>
  </ul>
</div>
```


---

## Composants métier

Page de référence : https://chartegraphique-21.mtdpce-test.gov.bf/composants-metier

Ces blocs ne sont pas des variantes esthétiques des composants de base. Ils répondent à une obligation d'information ou à une étape identifiée du parcours administratif. Leur structure est imposée, car c'est elle qui garantit que l'usager dispose des mêmes informations d'un service à l'autre.

### Confiance numérique

La multiplication des faux sites administratifs et des faux formulaires de paiement constitue le principal risque pour l'usager. Les blocs de cette famille répondent à un objectif unique : permettre de reconnaître un service authentique de l'État en quelques secondes, sans compétence technique.

#### Bandeau d'identification officielle `.fs-officiel`

Obligatoire en tête de toute page d'un service en ligne de l'État, avant même le bloc-marque. Il affiche la nature officielle du site, le domaine vérifié et un accès à l'explication des repères de confiance.

*Exemple — Bandeau officiel*

```html
<div>
  <div class="fs-officiel">
    <div class="fs-conteneur">
      <span class="fs-officiel-sceau">Site officiel de l'administration burkinabè</span>
      <span>Domaine vérifié <code>impots.gov.bf</code></span>
      <button type="button" data-ouvre-modale="modale-repere">Comment le vérifier ?</button>
    </div>
  </div>
  <div class="fs-filet"></div>
</div>

<dialog class="fs-modale" id="modale-repere" aria-labelledby="modale-repere-titre">
  <div class="fs-modale-entete">
    <h3 class="fs-modale-titre" id="modale-repere-titre">Reconnaître un service officiel</h3>
    <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" data-ferme-modale aria-label="Fermer"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button>
  </div>
  <div class="fs-modale-corps">
    <ul class="fs-liste-controle">
      <li>L'adresse du site se termine par <code>.gov.bf</code> avant la première barre oblique.</li>
      <li>Le nom de l'administration responsable est affiché en clair, avec une adresse physique et un numéro vérifiable.</li>
      <li>Aucun service de l'État ne demande vos codes secrets bancaires ni votre code de monnaie électronique.</li>
      <li>Les paiements passent exclusivement par les canaux autorisés listés sur la fiche de la démarche.</li>
    </ul>
  </div>
  <div class="fs-modale-pied">
    <button type="button" class="fs-btn fs-btn--principal" data-ferme-modale>J'ai compris</button>
  </div>
</dialog>
```

#### Signalement de fraude `.fs-signalement`

Présent en pied de tout parcours comportant un paiement. Il offre une issue immédiate à un usager qui doute, plutôt que de le laisser abandonner ou, pire, poursuivre sur un site frauduleux.

*Exemple — Signalement*

```html
<div class="fs-signalement">
  <div style="flex:1;">
    <strong>Un doute sur l'authenticité de cette page ?</strong>
    <p class="fs-legende" style="margin:0;">Ne saisissez aucune donnée personnelle et signalez la page au service compétent.</p>
  </div>
  <a class="fs-btn fs-btn--danger fs-btn--sm" href="#">Signaler</a>
</div>
```

#### Canaux de paiement autorisés

Tout parcours comportant un paiement liste explicitement les canaux autorisés. Un canal absent de cette liste est un canal frauduleux. La formulation doit le dire sans ambiguïté.

*Exemple — Canaux autorisés*

```html
<div class="fs-encart fs-encart--info">
  <div class="fs-encart-corps">
    <p class="fs-encart-titre">Canaux de paiement autorisés pour cette démarche</p>
    <ul class="fs-liste-puces" style="margin-bottom: var(--espace-3);">
      <li>Monnaie électronique adossée à un opérateur agréé</li>
      <li>Régie de recettes de l'administration responsable</li>
      <li>Virement bancaire vers le compte du Trésor public indiqué sur la quittance</li>
    </ul>
    <p><strong>Aucun autre canal n'est autorisé.</strong> Un versement sur un compte personnel ne vaut pas paiement et ne pourra pas être remboursé.</p>
  </div>
</div>
```

#### Récapitulatif de paiement `.fs-paiement`

Dernier écran avant le débit. Il détaille ce qui est dû, ligne par ligne, avant de demander le choix du canal. L'usager doit pouvoir vérifier le montant sans faire de calcul et reconnaître le bénéficiaire. C'est là que se joue la différence avec un faux formulaire de paiement.

*Exemple — Récapitulatif de paiement*

```html
<div class="fs-paiement">
  <dl class="fs-paiement-lignes">
    <div>
      <dt>Droit de timbre — acte de naissance</dt>
      <dd>500 F CFA</dd>
    </div>
    <div>
      <dt>Copie supplémentaire (× 2)</dt>
      <dd>400 F CFA</dd>
    </div>
    <div>
      <dt>Frais d'envoi postal</dt>
      <dd>1 500 F CFA</dd>
    </div>
  </dl>
  <div class="fs-paiement-total">
    <span class="fs-paiement-total-libelle">Total à régler</span>
    <span class="fs-paiement-total-montant">2 400 F CFA</span>
  </div>
</div>

<fieldset class="fs-fieldset" style="border:0; padding:0;">
  <legend class="fs-label">Canal de paiement</legend>
  <label class="fs-choix fs-choix--carte">
    <input type="radio" name="canal" checked>
    <span class="fs-choix-texte">
      <span class="fs-choix-titre">Monnaie électronique</span>
      <span class="fs-choix-aide">Opérateur agréé. Un code de confirmation vous sera envoyé par SMS.</span>
    </span>
  </label>
  <label class="fs-choix fs-choix--carte">
    <input type="radio" name="canal">
    <span class="fs-choix-texte">
      <span class="fs-choix-titre">Régie de recettes</span>
      <span class="fs-choix-aide">Règlement au guichet de l'administration responsable. Le traitement démarre à réception.</span>
    </span>
  </label>
</fieldset>

<div class="fs-signalement">
  <p class="fs-legende" style="margin:0;">
    Le bénéficiaire est le <strong>Trésor public du Burkina Faso</strong>.
    Tout versement demandé sur un compte personnel est frauduleux et ne
    pourra pas être remboursé.
  </p>
</div>

<button type="button" class="fs-btn fs-btn--principal fs-btn--bloc fs-btn--lg">
  Payer 2 400 F CFA
</button>
```

**À faire**

- Porter le montant dans le libellé du bouton : « Payer 2 400 F CFA », jamais « Valider ».
- Détailler chaque ligne, y compris les frais annexes, avant le total.
- Nommer le bénéficiaire et rappeler qu'aucun versement personnel n'est autorisé.
- Indiquer ce qui se passe après le paiement et sous quel délai.
- Délivrer une quittance téléchargeable dès l'encaissement.

**À proscrire**

- Afficher un total sans son détail.
- Ajouter un frais après le récapitulatif.
- Présélectionner une option payante.
- Demander un code de confirmation ailleurs que sur le service lui-même.

#### Autorité responsable `.fs-autorite`

Identifie sans ambiguïté l'administration qui délivre le service et engage sa responsabilité. Ce bloc est obligatoire sur toute fiche de démarche et toute page comportant une décision administrative.

*Exemple — Autorité responsable*

```html
<div class="fs-autorite">
  <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg" alt="">
  <div>
    <p class="fs-autorite-nom">Direction générale de la modernisation de l'état civil</p>
    <p class="fs-autorite-tutelle">Ministère de l'Administration territoriale</p>
    <div class="fs-autorite-contacts">
      <p style="margin-bottom: var(--espace-1);">Avenue de l'Indépendance, Ouagadougou</p>
      <p style="margin-bottom: var(--espace-1);">Permanence téléphonique du lundi au vendredi, 7 h 30 – 15 h 30</p>
      <p><a href="#">Écrire au service</a></p>
    </div>
  </div>
</div>
```

### Démarches

Une démarche se présente toujours dans le même ordre. Ce qu'elle permet d'obtenir, qui peut la demander, ce qu'elle coûte, combien de temps elle prend, et ce qu'il faut fournir. Cet ordre n'est pas négociable. Il permet à l'usager de décider avant de s'engager.

#### Carte de démarche `.fs-demarche`

Format de listing. Le coût et le délai sont affichés dès la liste : un usager ne doit pas ouvrir cinq fiches pour comparer deux démarches.

*Exemple — Cartes de démarche*

```html
<div class="fs-auto-grille" style="--min-colonne: 300px;">
  <article class="fs-demarche">
    <div class="fs-demarche-entete">
      <h4 class="fs-demarche-titre"><a href="#">Demander un acte de naissance</a></h4>
      <span class="fs-badge fs-badge--succes">En ligne</span>
    </div>
    <p class="fs-demarche-resume">
      Copie intégrale ou extrait d'acte de naissance délivré par la mairie
      du lieu de naissance.
    </p>
    <dl class="fs-demarche-meta">
      <div><dt>Coût</dt><dd>500 F CFA</dd></div>
      <div><dt>Délai</dt><dd>3 jours ouvrés</dd></div>
      <div><dt>Canal</dt><dd>En ligne ou guichet</dd></div>
    </dl>
  </article>

  <article class="fs-demarche">
    <div class="fs-demarche-entete">
      <h4 class="fs-demarche-titre"><a href="#">Demander un passeport ordinaire</a></h4>
      <span class="fs-badge fs-badge--alerte">Présence requise</span>
    </div>
    <p class="fs-demarche-resume">
      Première demande ou renouvellement. La prise d'empreintes impose un
      passage au centre d'enrôlement.
    </p>
    <dl class="fs-demarche-meta">
      <div><dt>Coût</dt><dd>50 000 F CFA</dd></div>
      <div><dt>Délai</dt><dd>15 jours ouvrés</dd></div>
      <div><dt>Canal</dt><dd>Guichet uniquement</dd></div>
    </dl>
  </article>
</div>
```

#### Pièces à fournir `.fs-pieces`

Chaque pièce est numérotée et accompagnée de sa contrainte réelle : format accepté, durée de validité, mention d'original ou de copie. Une liste sans ces précisions génère des dossiers incomplets, donc des allers-retours.

*Exemple — Pièces à fournir*

```html
<ol class="fs-pieces">
  <li>
    <span class="fs-pieces-num">1</span>
    <div>
      <p class="fs-pieces-nom">Pièce d'identité du demandeur</p>
      <p class="fs-pieces-detail">CNIB ou passeport en cours de validité. Copie recto-verso lisible, format JPG ou PDF, 5 Mo maximum.</p>
    </div>
  </li>
  <li>
    <span class="fs-pieces-num">2</span>
    <div>
      <p class="fs-pieces-nom">Justificatif de filiation</p>
      <p class="fs-pieces-detail">Livret de famille ou acte de naissance des parents. Non exigé si le demandeur est le titulaire de l'acte.</p>
    </div>
  </li>
  <li>
    <span class="fs-pieces-num">3</span>
    <div>
      <p class="fs-pieces-nom">Procuration</p>
      <p class="fs-pieces-detail">Uniquement en cas de demande pour un tiers majeur. Original signé, accompagné de la pièce d'identité du mandant.</p>
    </div>
  </li>
  <li>
    <span class="fs-pieces-num">4</span>
    <div>
      <p class="fs-pieces-nom">Quittance du droit de timbre</p>
      <p class="fs-pieces-detail">Délivrée automatiquement après paiement en ligne. À joindre si le paiement a été effectué en régie.</p>
    </div>
  </li>
</ol>
```

#### Photographie d'identité `.fs-photo-guide`

Le cadrage est le premier motif de rejet d'un dossier d'enrôlement. Montrer le cadre attendu avant la prise de vue coûte moins cher qu'un aller-retour, et évite à l'usager un second déplacement.

*Exemple — Guide de cadrage*

```html
<div class="fs-photo-guide">
  <div class="fs-photo-cadre">
    <svg viewbox="0 0 35 45" aria-hidden="true">
      <path class="fs-photo-silhouette" d="M17.5 9a6.5 6.5 0 0 1 6.5 6.5c0 3.6-2.9 7.5-6.5 7.5s-6.5-3.9-6.5-7.5A6.5 6.5 0 0 1 17.5 9z"></path>
      <path class="fs-photo-silhouette" d="M4.5 45c1.6-8.6 6.8-12.5 13-12.5S29 36.4 30.5 45z"></path>
      <path class="fs-photo-reperes" d="M0 8h35M0 34h35"></path>
      <path class="fs-photo-reperes" d="M17.5 0v45"></path>
    </svg>
    <span class="fs-photo-legende">Tête entre les deux repères</span>
  </div>

  <ul class="fs-liste-controle" style="flex:1; min-width: 240px;">
    <li>Visage de face, centré, tête droite, expression neutre.</li>
    <li>Hauteur du visage comprise entre 32 et 36 mm sur un tirage de 45 mm.</li>
    <li>Fond uni et clair, sans ombre portée ni motif.</li>
    <li>Yeux ouverts et visibles : pas de reflet sur les verres, pas de monture épaisse.</li>
    <li>Photographie de moins de six mois, sans couvre-chef, sauf motif religieux laissant le visage dégagé.</li>
  </ul>
</div>
```

**À faire**

- Afficher le guide **avant** le dépôt, pas en message d'erreur après.
- Vérifier la résolution et prévenir immédiatement si elle est insuffisante.
- Indiquer que la photographie peut être prise par un photographe agréé, tous les usagers n'ayant pas d'appareil.

**À proscrire**

- Rejeter sans dire ce qui ne va pas ni comment corriger.
- Exiger un format que le téléphone de l'usager ne produit pas.
- Recadrer automatiquement sans montrer le résultat.

#### Événements de vie `.fs-evenement`

Point d'entrée alternatif à la recherche. Un usager cherche rarement « acte de naissance » : il cherche « je viens d'avoir un enfant ». L'entrée par événement réduit la charge de vocabulaire administratif.

*Exemple — Événements de vie*

```html
<div class="fs-auto-grille" style="--min-colonne: 190px;">
  <a class="fs-evenement" href="#"><span class="fs-evenement-titre">Naissance</span><span class="fs-evenement-nb">7 démarches</span></a>
  <a class="fs-evenement" href="#"><span class="fs-evenement-titre">Études</span><span class="fs-evenement-nb">11 démarches</span></a>
  <a class="fs-evenement" href="#"><span class="fs-evenement-titre">Emploi public</span><span class="fs-evenement-nb">9 démarches</span></a>
  <a class="fs-evenement" href="#"><span class="fs-evenement-titre">Créer une entreprise</span><span class="fs-evenement-nb">14 démarches</span></a>
  <a class="fs-evenement" href="#"><span class="fs-evenement-titre">Impôts</span><span class="fs-evenement-nb">12 démarches</span></a>
  <a class="fs-evenement" href="#"><span class="fs-evenement-titre">Voyage</span><span class="fs-evenement-nb">5 démarches</span></a>
  <a class="fs-evenement" href="#"><span class="fs-evenement-titre">Retraite</span><span class="fs-evenement-nb">6 démarches</span></a>
  <a class="fs-evenement" href="#"><span class="fs-evenement-titre">Décès</span><span class="fs-evenement-nb">8 démarches</span></a>
</div>
```

### Suivi de dossier

Le suivi est le premier motif de contact avec les guichets. Un suivi lisible en ligne réduit mécaniquement la charge d'accueil physique et téléphonique.

#### Bloc de suivi `.fs-dossier`

Six états normalisés couvrent l'ensemble des parcours : reçu, en cours d'instruction, complément demandé, validé, rejeté, disponible au retrait. Aucun service n'invente un état supplémentaire sans validation.

*Exemple — Suivi de dossier*

```html
<div class="fs-dossier">
  <div class="fs-dossier-entete">
    <div>
      <p class="fs-dossier-reference">BF-2026-004871-OUA</p>
      <p class="fs-dossier-depot">Acte de naissance — déposé le 18 septembre 2026</p>
    </div>
    <span class="fs-badge fs-badge--info">En cours d'instruction</span>
  </div>

  <div class="fs-pile fs-pile--large">
    <div>
      <div class="fs-rangee fs-rangee--entre" style="margin-bottom: var(--espace-2);">
        <span class="fs-legende">Avancement</span>
        <span class="fs-legende fs-tabulaire">3 étapes sur 4</span>
      </div>
      <div class="fs-jauge" data-valeur="75" aria-label="Dossier avancé à 75 %">
        <div class="fs-jauge-valeur"></div>
      </div>
    </div>

    <ol class="fs-chrono">
      <li data-etat="faite">
        <span class="fs-chrono-date">18 septembre 2026 — 09 h 12</span>
        <span class="fs-chrono-titre">Demande reçue</span>
        <p class="fs-legende">Récépissé de dépôt adressé par courrier électronique.</p>
      </li>
      <li data-etat="faite">
        <span class="fs-chrono-date">18 septembre 2026 — 14 h 40</span>
        <span class="fs-chrono-titre">Paiement encaissé</span>
        <p class="fs-legende">Droit de timbre de 500 F CFA. Quittance disponible au téléchargement.</p>
      </li>
      <li data-etat="courante">
        <span class="fs-chrono-date">19 septembre 2026</span>
        <span class="fs-chrono-titre">En cours d'instruction</span>
        <p class="fs-legende">Dossier transmis à la mairie du lieu de naissance.</p>
      </li>
      <li>
        <span class="fs-chrono-date">Prévu le 23 septembre 2026</span>
        <span class="fs-chrono-titre">Document disponible au retrait</span>
      </li>
    </ol>

    <div class="fs-actions">
      <a class="fs-btn fs-btn--tertiaire" href="#">Télécharger le récépissé</a>
      <a class="fs-btn fs-btn--tertiaire" href="#">Télécharger la quittance</a>
      <a class="fs-btn fs-btn--secondaire" href="#">Contacter le service instructeur</a>
    </div>
  </div>
</div>
```

#### Liste de dossiers `.fs-liste-dossiers`

Format complémentaire du tableau. Il tient sur un écran étroit sans reconfiguration et supporte des intitulés longs, là où un tableau impose des colonnes de largeur fixe. À privilégier quand l'usager consulte ses propres dossiers — peu nombreux, chacun important ; le tableau reste préférable dès qu'il s'agit de comparer ou de trier.

*Exemple — Liste de dossiers*

```html
<ul class="fs-liste-dossiers">
  <li class="fs-dossier-item">
    <div>
      <span class="fs-dossier-ref">BF-2026-004915</span>
      <p class="fs-dossier-objet"><a href="#">Extrait de casier judiciaire</a></p>
      <p class="fs-dossier-meta">
        <span>Déposé le 12 septembre 2026</span>
        <span>2 000 F CFA</span>
        <span>Tribunal de grande instance de Ouagadougou</span>
      </p>
    </div>
    <div class="fs-dossier-cote">
      <span class="fs-badge fs-badge--alerte">Complément demandé</span>
      <a class="fs-btn fs-btn--secondaire fs-btn--sm" href="#">Transmettre la pièce</a>
    </div>
  </li>

  <li class="fs-dossier-item">
    <div>
      <span class="fs-dossier-ref">BF-2026-004871</span>
      <p class="fs-dossier-objet"><a href="#">Acte de naissance</a></p>
      <p class="fs-dossier-meta">
        <span>Déposé le 18 septembre 2026</span>
        <span>500 F CFA</span>
        <span>Mairie de Ouagadougou</span>
      </p>
    </div>
    <div class="fs-dossier-cote">
      <span class="fs-statut fs-statut--cours">En instruction</span>
      <a class="fs-btn fs-btn--tertiaire fs-btn--sm" href="#">Suivre</a>
    </div>
  </li>

  <li class="fs-dossier-item">
    <div>
      <span class="fs-dossier-ref">BF-2026-004790</span>
      <p class="fs-dossier-objet"><a href="#">Certificat de résidence</a></p>
      <p class="fs-dossier-meta">
        <span>Déposé le 2 septembre 2026</span>
        <span>1 000 F CFA</span>
      </p>
    </div>
    <div class="fs-dossier-cote">
      <span class="fs-badge fs-badge--succes">Disponible au retrait</span>
      <a class="fs-btn fs-btn--principal fs-btn--sm" href="#">
        <span class="fs-icone fs-icone--telecharger" aria-hidden="true"></span>
        Télécharger
      </a>
    </div>
  </li>
</ul>
```

#### États normalisés

*Correspondance entre état de dossier et traitement visuel*

| État | Traitement | Signification pour l'usager |
| --- | --- | --- |
| Reçu | `--texte-faible` | La demande est enregistrée mais n'a pas encore été prise en charge. |
| En instruction | `--info` | Un agent examine le dossier. Aucune action n'est attendue. |
| Complément demandé | `--alerte` | Une action de l'usager est requise, avec une échéance explicite. |
| Validé | `--succes` | La demande est acceptée. Le document est en cours d'édition. |
| Rejeté | `--danger` | La demande est refusée. Le motif et la voie de recours sont indiqués. |
| Disponible | `--succes` | Le document peut être téléchargé ou retiré au guichet indiqué. |

### Relation à l'usager

Recherche, consentement et contact. Ces blocs conditionnent l'accessibilité réelle du service à l'ensemble de la population.

#### Recherche de démarche

Point d'entrée principal d'un portail. Le champ est large, l'exemple de requête est concret, et les suggestions emploient le vocabulaire de l'usager plutôt que la nomenclature administrative.

*Exemple — Recherche*

```html
<div class="fs-champ">
  <label class="fs-label" for="recherche-demarche">Quelle démarche souhaitez-vous effectuer ?</label>
  <div class="fs-recherche">
    <input class="fs-saisie" type="search" id="recherche-demarche" placeholder="Acte de naissance, passeport, casier judiciaire…">
    <button type="submit" class="fs-btn fs-btn--principal">Rechercher</button>
  </div>
</div>

<div class="fs-rangee">
  <span class="fs-legende">Recherches fréquentes :</span>
  <span class="fs-etiquette">Acte de naissance</span>
  <span class="fs-etiquette">Certificat de résidence</span>
  <span class="fs-etiquette">Casier judiciaire</span>
  <span class="fs-etiquette">Carte grise</span>
</div>
```

#### Bandeau de consentement `.fs-consentement`

Refuser doit être aussi simple qu'accepter : les deux actions ont le même poids visuel et le même nombre de clics. Un service public ne recourt à aucune mesure d'audience non essentielle sans consentement préalable.

*Exemple — Consentement*

```html
<div class="fs-consentement" data-consentement style="position: static; width: auto; max-width: 480px;" role="dialog" aria-labelledby="consentement-titre">
  <h4 id="consentement-titre" style="font-size: var(--taille-lg); margin-bottom: var(--espace-3);">Mesure d'audience</h4>
  <p class="fs-legende" style="margin-bottom: var(--espace-5);">
    Ce service dépose des cookies de mesure d'audience afin d'améliorer la
    qualité des démarches en ligne. Aucune donnée n'est transmise à un
    tiers commercial. Votre choix est conservé six mois, et vous pouvez y
    revenir à tout moment depuis le pied de page.
  </p>

  <div data-consentement-detail hidden style="margin-bottom: var(--espace-5);">
    <div class="fs-choix">
      <input type="checkbox" id="finalite-mesure" data-finalite="mesure">
      <label for="finalite-mesure">
        Mesure d'audience
        <span class="fs-aide">Nombre de visites et parcours suivis, sans identification.</span>
      </label>
    </div>
    <div class="fs-choix">
      <input type="checkbox" id="finalite-video" data-finalite="video">
      <label for="finalite-video">
        Vidéos explicatives
        <span class="fs-aide">Lecture des tutoriels hébergés par un tiers.</span>
      </label>
    </div>
  </div>

  <div class="fs-actions">
    <button type="button" class="fs-btn fs-btn--tertiaire" data-consentement-regler aria-expanded="false">Personnaliser</button>
    <button type="button" class="fs-btn fs-btn--tertiaire" data-consentement-rien>Refuser</button>
    <button type="button" class="fs-btn fs-btn--principal" data-consentement-tout>Accepter</button>
  </div>

  <div class="fs-actions fs-mt-4" data-consentement-detail hidden>
    <button type="button" class="fs-btn fs-btn--secondaire" data-consentement-choix>Enregistrer mes choix</button>
  </div>
</div>
```

> La décision est conservée dans un cookie, non dans le stockage local. Le serveur doit pouvoir la lire avant d'écrire sa page, sans quoi il servirait une mesure d'audience à un usager qui l'a refusée. Et un cookie porte une échéance : la décision expire d'elle-même au bout de six mois et la question est reposée, ce que le stockage local ne sait pas faire.

##### Ce que la charte prend en charge, et ce qu'elle laisse au service

**La charte**

- Affiche le bandeau tant qu'aucune réponse n'a été donnée.
- Conserve la décision, finalité par finalité, six mois.
- La restitue par `Faso.consentement.lire()`.
- Annonce chaque changement par `fs:consentement`.
- Permet de revenir sur le choix par `data-rouvre-consentement`.

**Le service**

- Dépose ou retire les traceurs : la charte ne sait pas lesquels il emploie.
- Déclare ses finalités par `data-finalite`.
- N'exécute rien tant que `lire()` rend `null` : l'absence de réponse n'est pas un accord.
- Écrit sa page de politique de confidentialité.

```js
// Le service ecoute, et agit lui-meme sur ses traceurs.
document.addEventListener('fs:consentement', (ev) => {
  const decision = ev.detail;            // null si l'usager a tout oublie
  if (decision && decision.mesure) demarrerMesure();
  else arreterMesure();
});

// Au chargement, avant tout depot.
const decision = Faso.consentement.lire();
if (decision && decision.mesure) demarrerMesure();
```

> **Trois cookies n'en sont pas moins des cookies**
>
> Le cookie qui enregistre la décision, celui qui porte la session et celui qui protège un formulaire contre la falsification sont nécessaires au service : ils ne se demandent pas. Tout le reste se demande, et se dépose seulement après une réponse. Le service reste tenu de les énumérer dans sa politique de confidentialité.

#### Modèles de SMS

Le SMS est le canal le plus sûr pour joindre un usager : il ne dépend d'aucune connexion de données et atteint tous les appareils. C'est aussi celui qu'exploitent les fraudeurs. Chaque message suit donc la même structure : **émetteur, objet, référence, action attendue** — et jamais de lien.

*Exemple — Modèles*

```html
<div class="doc-sms">
  <span class="doc-sms-expediteur">Code de vérification</span>
  eBurkina : votre code est 418 207. Il expire dans 10 minutes.
  Aucun agent ne vous le demandera, ni par appel ni par message.
  <span class="doc-sms-compte">124 caractères — 1 SMS</span>
</div>

<div class="doc-sms">
  <span class="doc-sms-expediteur">Dépôt enregistré</span>
  eBurkina : votre demande d'acte de naissance est enregistrée sous la
  reference BF-2026-004871. Suivi sur eservices.gov.bf, rubrique Mon espace.
  <span class="doc-sms-compte">143 caractères — 1 SMS</span>
</div>

<div class="doc-sms">
  <span class="doc-sms-expediteur">Action attendue</span>
  eBurkina : dossier BF-2026-004915, un justificatif de domicile est attendu
  avant le 30/09/2026. Connectez-vous a votre espace pour le transmettre.
  <span class="doc-sms-compte">146 caractères — 1 SMS</span>
</div>

<div class="doc-sms">
  <span class="doc-sms-expediteur">Document disponible</span>
  eBurkina : votre acte est disponible. Retrait a la mairie de votre lieu de
  naissance, muni de votre CNIB. Reference BF-2026-004871.
  <span class="doc-sms-compte">131 caractères — 1 SMS</span>
</div>
```

**À faire**

- Nommer le service en tête de message, toujours de la même façon.
- Tenir dans 160 caractères : au-delà, l'opérateur facture et découpe le message, et l'ordre d'arrivée n'est pas garanti.
- Rappeler la référence du dossier dans tout message de suivi.
- Écrire sans accents : certains combinés anciens les remplacent par des caractères illisibles et font basculer le message en encodage long.
- Rappeler, dans tout message contenant un code, qu'aucun agent ne le demandera.

**À proscrire**

- **Tout lien cliquable.** C'est la signature des messages frauduleux ; l'usager doit apprendre qu'un SMS officiel n'en contient jamais.
- Demander une donnée personnelle, un code ou un paiement par retour de message.
- Envoyer depuis un numéro différent d'un envoi à l'autre.
- Employer un ton commercial ou une formule d'urgence artificielle.

#### Modèles de courriel

Le courriel porte les pièces : récépissés, quittances, décisions. Il doit rester compréhensible sans images et sans mise en forme, car une part des messageries les bloque par défaut.

*Exemple — Accusé de dépôt*

```html
<div class="doc-courriel">
  <div class="doc-courriel-entete">
    <dl>
      <dt>De</dt>
      <dd>Téléservices de l'État <span class="fs-faible">&lt;ne-pas-repondre@eservices.gov.bf&gt;</span></dd>
      <dt>Objet</dt>
      <dd><strong>Acte de naissance, demande enregistrée, BF-2026-004871</strong></dd>
      <dt>Pièce</dt>
      <dd>recepisse-BF-2026-004871.pdf <span class="fs-faible">(84 Ko)</span></dd>
    </dl>
  </div>
  <div class="doc-courriel-corps">
    <p>Votre demande d'acte de naissance a été enregistrée le 18 septembre 2026 sous la référence <strong>BF-2026-004871</strong>.</p>
    <p>Le récépissé de dépôt est joint à ce message. Il ne vaut pas acte et ne peut être produit comme pièce justificative.</p>
    <p>Le délai indicatif de traitement est de trois jours ouvrés à compter de la validation du dossier. Vous serez informé par SMS dès que le document sera disponible.</p>
    <p><strong>Suivre ce dossier :</strong> connectez-vous à votre espace personnel sur eservices.gov.bf, rubrique « Mes demandes ».</p>
    <hr>
    <p class="fs-legende">
      Ce message est envoyé par la Direction générale de la modernisation de
      l'état civil. Il ne demande jamais de paiement ni de code. En cas de
      doute sur son authenticité, ne répondez pas et signalez-le depuis votre
      espace personnel.
    </p>
  </div>
</div>
```

##### Règle de l'objet

L'objet est souvent la seule partie lue. Il suit un ordre fixe, du général au particulier, pour que la référence reste visible même tronquée sur mobile.

```html
<Démarche> — <état> — <référence>

Acte de naissance — demande enregistrée — BF-2026-004871
Casier judiciaire — pièce complémentaire attendue — BF-2026-004915
Passeport ordinaire — document disponible — BF-2026-004930
```

**À faire**

- Expédier depuis un domaine en `.gov.bf`, identique d'un message à l'autre.
- Rester compréhensible en texte seul : aucune information portée par une image.
- Joindre les pièces plutôt que de renvoyer vers un téléchargement.
- Clore par la mention de l'administration émettrice et la voie de signalement.
- Indiquer explicitement ce que le document joint ne permet pas de faire.

**À proscrire**

- Bandeau d'en-tête en image : bloqué par défaut, il fait perdre l'identification de l'émetteur.
- Bouton d'action vers une page de connexion. C'est le schéma exact de l'hameçonnage.
- Pièce jointe de plus de 2 Mo.
- Adresse d'expédition changeante ou en domaine commercial.

#### Bloc de contact

*Exemple — Contact*

```html
<div class="fs-auto-grille" style="--min-colonne: 240px;">
  <div class="fs-carte">
    <div class="fs-carte-corps">
      <h4 class="fs-carte-titre" style="font-size: var(--taille-md);">Au guichet</h4>
      <p class="fs-carte-texte">
        Mairie de votre lieu de résidence<br>
        Du lundi au vendredi, 7 h 30 – 15 h 30
      </p>
    </div>
  </div>
  <div class="fs-carte">
    <div class="fs-carte-corps">
      <h4 class="fs-carte-titre" style="font-size: var(--taille-md);">Par téléphone</h4>
      <p class="fs-carte-texte">
        Numéro vert national<br>
        Appel gratuit depuis un poste fixe
      </p>
    </div>
  </div>
  <div class="fs-carte">
    <div class="fs-carte-corps">
      <h4 class="fs-carte-titre" style="font-size: var(--taille-md);">En ligne</h4>
      <p class="fs-carte-texte">
        Formulaire de contact sécurisé<br>
        Réponse sous 48 heures ouvrées
      </p>
    </div>
  </div>
</div>
```


---

## Gabarits

Page de référence : https://chartegraphique-21.mtdpce-test.gov.bf/gabarits

Les gabarits sont des assemblages validés. Ils fixent l'ordre des blocs et la hiérarchie de l'information pour les situations qui couvrent la quasi-totalité des services publics numériques. Un projet qui s'en écarte doit justifier son écart.

### Structure commune

Toutes les pages de l'État partagent la même ossature. Seule la zone de contenu principal varie d'un gabarit à l'autre.

#### Ordre imposé des blocs

*Exemple — Ossature*

```html
<div style="font-family: var(--police-mono); font-size: var(--taille-xs);">
  <div style="padding: var(--espace-3) var(--espace-4); background: var(--vert-900); color:#fff;">1 · Lien d'évitement (invisible, premier élément focusable)</div>
  <div style="padding: var(--espace-3) var(--espace-4); background: var(--vert-800); color:#fff;">2 · Bandeau d'identification officielle</div>
  <div class="fs-filet fs-filet--fin"></div>
  <div style="padding: var(--espace-4); background: var(--surface-enfoncee);">3 · Bloc-marque et outils</div>
  <div style="padding: var(--espace-3) var(--espace-4); background: var(--surface-basse); border-block: 1px solid var(--bordure);">4 · Navigation principale</div>
  <div style="padding: var(--espace-3) var(--espace-4);">5 · Fil d'Ariane</div>
  <div style="padding: var(--espace-12) var(--espace-4); background: var(--action-tenue); border: 1px dashed var(--action); color: var(--action-sur-clair); text-align:center;">6 · Contenu principal — varie selon le gabarit</div>
  <div style="padding: var(--espace-4); background: var(--surface-enfoncee); border-top: 1px solid var(--bordure);">7 · Pied de page : plan, obligations légales, contact</div>
</div>
```

### Portail de services

Page d'accueil d'une plateforme de téléservices. Sa fonction unique est d'orienter. La recherche et les entrées par événement de vie précèdent tout contenu institutionnel.

#### Composition

*Gabarit — https://eservices.gov.bf*

```html
<div class="fs-pile fs-pile--large">

  <div style="padding: var(--espace-8) var(--espace-6); background: var(--vert-900); color:#fff; border-radius: var(--rayon-lg);">
    <h3 style="color:#fff; max-width: 20ch;">Effectuez vos démarches administratives en ligne</h3>
    <div class="fs-recherche" style="max-width: 520px; margin-top: var(--espace-5);">
      <input class="fs-saisie" type="search" aria-label="Rechercher une démarche" placeholder="Acte de naissance, passeport, casier judiciaire…">
      <button type="button" class="fs-btn fs-btn--relief">Rechercher</button>
    </div>
  </div>

  <div>
    <h4>Par événement de vie</h4>
    <div class="fs-auto-grille" style="--min-colonne: 150px;">
      <a class="fs-evenement" href="#"><span class="fs-evenement-titre">Naissance</span><span class="fs-evenement-nb">7 démarches</span></a>
      <a class="fs-evenement" href="#"><span class="fs-evenement-titre">Études</span><span class="fs-evenement-nb">11 démarches</span></a>
      <a class="fs-evenement" href="#"><span class="fs-evenement-titre">Entreprise</span><span class="fs-evenement-nb">14 démarches</span></a>
      <a class="fs-evenement" href="#"><span class="fs-evenement-titre">Impôts</span><span class="fs-evenement-nb">12 démarches</span></a>
    </div>
  </div>

  <div>
    <h4>Démarches les plus demandées</h4>
    <div class="fs-auto-grille" style="--min-colonne: 260px;">
      <article class="fs-demarche">
        <h5 class="fs-demarche-titre"><a href="#">Acte de naissance</a></h5>
        <dl class="fs-demarche-meta">
          <div><dt>Coût</dt><dd>500 F</dd></div>
          <div><dt>Délai</dt><dd>3 jours</dd></div>
        </dl>
      </article>
      <article class="fs-demarche">
        <h5 class="fs-demarche-titre"><a href="#">Certificat de résidence</a></h5>
        <dl class="fs-demarche-meta">
          <div><dt>Coût</dt><dd>1 000 F</dd></div>
          <div><dt>Délai</dt><dd>2 jours</dd></div>
        </dl>
      </article>
    </div>
  </div>

</div>
```

**Règles**

- La recherche occupe la zone haute et reste atteignable sans défilement sur mobile.
- Les démarches affichées en page d'accueil indiquent coût et délai.
- L'accès au compte usager et au suivi de dossier est visible dès l'accueil.

**À proscrire**

- Carrousel d'images en tête de page. Il repousse la recherche sous la ligne de flottaison.
- Actualités institutionnelles placées avant les démarches.
- Message de bienvenue signé d'une autorité occupant la zone haute.

### Résultats de recherche

La recherche est le premier geste de l'usager sur un portail. Documenter le champ sans documenter ce qu'il produit laisse le gabarit le plus fréquent à l'appréciation de chaque projet. Sa règle centrale : **ne jamais renvoyer une page vide**.

#### Composition

*Gabarit — https://eservices.gov.bf/recherche?q=acte+naissance*

```html
<div class="fs-pile fs-pile--large">

  <div class="fs-recherche" style="max-width: 560px;">
    <input class="fs-saisie" type="search" value="acte naissance" aria-label="Rechercher une démarche">
    <button type="button" class="fs-btn fs-btn--principal">Rechercher</button>
  </div>

  <div class="fs-rangee fs-rangee--entre">
    <p style="margin:0;"><strong>7 démarches</strong> pour « acte naissance »</p>
    <label class="fs-tableau-taille">
      Trier par
      <select class="fs-select">
        <option>Pertinence</option>
        <option>Délai le plus court</option>
        <option>Coût le plus faible</option>
      </select>
    </label>
  </div>

  <div class="fs-rangee">
    <span class="fs-legende">Filtres :</span>
    <span class="fs-etiquette">En ligne <button type="button" aria-label="Retirer le filtre En ligne">×</button></span>
    <span class="fs-etiquette">État civil <button type="button" aria-label="Retirer le filtre État civil">×</button></span>
  </div>

  <ul class="fs-liste-separee">
    <li>
      <span class="fs-surtitre">État civil</span>
      <h4 style="margin: 0 0 var(--espace-2);"><a href="#">Demander un <mark>acte</mark> de <mark>naissance</mark></a></h4>
      <p class="fs-attenue" style="font-size: var(--taille-sm);">
        Copie intégrale ou extrait délivré par la mairie du lieu de naissance.
      </p>
      <div class="fs-rangee" style="margin-top: var(--espace-3);">
        <span class="fs-badge fs-badge--succes">En ligne</span>
        <span class="fs-legende">500 F CFA</span>
        <span class="fs-legende">3 jours ouvrés</span>
      </div>
    </li>
    <li>
      <span class="fs-surtitre">État civil</span>
      <h4 style="margin: 0 0 var(--espace-2);"><a href="#">Rectifier un <mark>acte</mark> de <mark>naissance</mark></a></h4>
      <p class="fs-attenue" style="font-size: var(--taille-sm);">
        Correction d'une erreur matérielle sur un acte déjà délivré.
      </p>
      <div class="fs-rangee" style="margin-top: var(--espace-3);">
        <span class="fs-badge fs-badge--alerte">Présence requise</span>
        <span class="fs-legende">Gratuit</span>
        <span class="fs-legende">15 jours ouvrés</span>
      </div>
    </li>
  </ul>

  <div class="fs-encart fs-encart--info">
    <span class="fs-encart-marque fs-icone fs-icone--information" aria-hidden="true"></span>
    <div class="fs-encart-corps">
      <p class="fs-encart-titre">Vous ne trouvez pas votre démarche ?</p>
      <p>Parcourez les démarches par événement de vie, ou contactez le numéro vert national.</p>
    </div>
  </div>

</div>
```

#### Recherche sans résultat

C'est l'écran le plus important du gabarit, et le plus souvent bâclé. Il ne se contente pas de constater. Il explique, propose une reformulation, et ouvre une autre voie.

*Exemple — Aucun résultat*

```html
<div class="fs-vide" style="border-style: solid;">
  <h4 class="fs-vide-titre">Aucune démarche ne correspond à « certificat de naissanse »</h4>
  <p>
    Vérifiez l'orthographe, ou employez un terme plus général.
    Les démarches les plus proches sont proposées ci-dessous.
  </p>
  <div class="fs-actions">
    <a class="fs-btn fs-btn--principal" href="#">Rechercher « acte de naissance »</a>
    <a class="fs-btn fs-btn--tertiaire" href="#">Parcourir par événement de vie</a>
  </div>
</div>
```

#### Règles

**À faire**

- Porter la requête dans l'adresse. Une recherche doit être partageable et retrouvable par l'historique.
- Rappeler la requête au-dessus des résultats et la laisser dans le champ, modifiable.
- Annoncer le nombre de résultats avant la liste, dans une région live.
- Mettre en évidence les termes trouvés, sans surligner la page entière.
- Afficher coût, délai et canal dès la liste : l'usager choisit sans ouvrir cinq fiches.
- Ignorer les accents et tolérer les fautes courantes.

**À proscrire**

- Renvoyer une page vide. Il y a toujours une suggestion, une reformulation ou un canal humain à proposer.
- Exiger l'orthographe exacte d'un terme administratif.
- Mêler démarches, actualités et pages institutionnelles sans les distinguer.
- Paginer avant d'avoir proposé un filtre : l'usager préfère affiner que feuilleter.
- Perdre les filtres actifs au changement de requête sans le signaler.

### Page de contenu institutionnel

Actualité, communiqué, texte réglementaire, page de présentation d'un service. Ce n'est ni une fiche de démarche ni un formulaire : on y vient pour lire, et la mise en page doit servir la lecture plutôt que l'action.

#### Composition

*Gabarit — https://gouvernement.gov.bf/actualites/teleservices-etat-civil*

```html
<div class="fs-avec-aside">
  <article class="fs-pile fs-pile--large">
    <div>
      <span class="fs-surtitre">Communiqué</span>
      <h3 style="max-width: 24ch;">Ouverture du téléservice de délivrance des actes d'état civil</h3>
      <p class="fs-legende">
        Publié le 18 septembre 2026 —
        Ministère de l'Administration territoriale
      </p>
    </div>

    <p class="fs-chapeau">
      Les communes de la région du Centre basculent sur la plateforme
      nationale à compter du 1er octobre 2026. La délivrance au guichet
      reste assurée sans changement.
    </p>

    <figure class="fs-figure fs-figure--16-9">
      <img src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2016%209'%3E%3Crect%20width='16'%20height='9'%20fill='%23e2ddd6'/%3E%3C/svg%3E" width="16" height="9" alt="Emplacement de la photographie">
      <figcaption>
        Guichet de l'état civil de la mairie de Ouagadougou.
        <span class="fs-figure-credit">Photographie : Service d'information du Gouvernement</span>
      </figcaption>
    </figure>

    <div class="fs-mesure">
      <p>
        Le service permet de demander une copie intégrale ou un extrait
        d'acte de naissance sans se déplacer. Le dossier est instruit par
        la mairie du lieu de naissance, qui demeure l'autorité délivrante.
      </p>
      <h4>Ce qui change pour les usagers</h4>
      <p>
        La demande se dépose depuis un espace personnel. Le suivi est
        consultable en ligne et notifié par message.
      </p>
      <blockquote>
        La dématérialisation ne retire aucune compétence aux communes :
        elle en modifie le canal de saisine.
        <cite>Direction générale de la modernisation de l'état civil</cite>
      </blockquote>
    </div>

    <div class="fs-encart fs-encart--info">
      <span class="fs-encart-marque fs-icone fs-icone--document" aria-hidden="true"></span>
      <div class="fs-encart-corps">
        <p class="fs-encart-titre">Démarche associée</p>
        <p><a href="#">Demander un acte de naissance</a> — 500 F CFA, 3 jours ouvrés.</p>
      </div>
    </div>

    <div>
      <p class="fs-legende">Documents à télécharger</p>
      <ul class="fs-liste-nue">
        <li>
          <a class="fs-lien-fichier" href="#">
            Arrêté portant ouverture du téléservice
            <span class="fs-poids">PDF — 180 Ko</span>
          </a>
        </li>
      </ul>
    </div>
  </article>

  <aside class="fs-pile">
    <div class="fs-sommaire" style="position: static;">
      <p class="fs-sommaire-titre">Sur cette page</p>
      <ul>
        <li><a href="#">Ce qui change pour les usagers</a></li>
        <li><a href="#">Calendrier de déploiement</a></li>
        <li><a href="#">Documents à télécharger</a></li>
      </ul>
    </div>

    <div class="fs-autorite" style="flex-direction: column;">
      <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg" alt="" style="width: 36px;">
      <div>
        <p class="fs-autorite-nom" style="font-size: var(--taille-sm);">Ministère de l'Administration territoriale</p>
        <p class="fs-autorite-tutelle">Émetteur du communiqué</p>
      </div>
    </div>
  </aside>
</div>
```

#### Règles

**À faire**

- Limiter le corps de texte à la mesure de confort : environ 68 caractères par ligne.
- Dater la publication et nommer l'administration émettrice, en tête.
- Signaler toute mise à jour ultérieure avec sa date.
- Relier le contenu aux démarches concernées : un communiqué sans point d'entrée vers l'action est une impasse.
- Donner format et poids de chaque document joint.
- Ajouter un sommaire au-delà de trois sections.

**À proscrire**

- Publier un texte réglementaire en image : il devient illisible, non traduisible et introuvable par la recherche.
- Étendre le texte sur toute la largeur de l'écran.
- Ouvrir sur une photographie d'ambiance qui repousse le contenu sous la ligne de flottaison.
- Employer un ton promotionnel dans un communiqué.
- Renvoyer vers un PDF ce qui tient en une page web.

### Fiche de démarche

Le gabarit le plus important du système. Il présente une démarche dans un ordre invariable et place l'action principale en fin de parcours de lecture, une fois l'usager informé.

#### Composition

*Gabarit — https://eservices.gov.bf/demarches/acte-de-naissance*

```html
<div class="fs-avec-aside">
  <div class="fs-pile fs-pile--large">
    <div>
      <span class="fs-surtitre">État civil</span>
      <h3>Demander un acte de naissance</h3>
      <p class="fs-chapeau">
        Obtenez une copie intégrale ou un extrait d'acte de naissance
        délivré par la mairie de votre lieu de naissance.
      </p>
    </div>

    <dl class="fs-definitions">
      <div><dt>Public concerné</dt><dd>Toute personne née au Burkina Faso ou de parents burkinabè</dd></div>
      <div><dt>Coût</dt><dd>500 F CFA — droit de timbre</dd></div>
      <div><dt>Délai indicatif</dt><dd>3 jours ouvrés</dd></div>
      <div><dt>Canaux</dt><dd>En ligne ou guichet de la mairie du lieu de naissance</dd></div>
    </dl>

    <div>
      <h4>Pièces à fournir</h4>
      <ol class="fs-pieces">
        <li>
          <span class="fs-pieces-num">1</span>
          <div>
            <p class="fs-pieces-nom">Pièce d'identité du demandeur</p>
            <p class="fs-pieces-detail">CNIB ou passeport en cours de validité</p>
          </div>
        </li>
        <li>
          <span class="fs-pieces-num">2</span>
          <div>
            <p class="fs-pieces-nom">Justificatif de filiation</p>
            <p class="fs-pieces-detail">Non exigé si vous êtes le titulaire de l'acte</p>
          </div>
        </li>
      </ol>
    </div>

    <div class="fs-actions">
      <a class="fs-btn fs-btn--principal fs-btn--lg" href="#">Commencer la démarche en ligne</a>
    </div>
  </div>

  <aside class="fs-pile">
    <div class="fs-autorite" style="flex-direction: column;">
      <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg" alt="" style="width: 40px;">
      <div>
        <p class="fs-autorite-nom" style="font-size: var(--taille-sm);">Direction générale de la modernisation de l'état civil</p>
        <p class="fs-autorite-tutelle">Ministère de l'Administration territoriale</p>
      </div>
    </div>

    <div class="fs-encart fs-encart--info">
      <div class="fs-encart-corps">
        <p class="fs-encart-titre">Besoin d'aide ?</p>
        <p>Le numéro vert national est joignable gratuitement du lundi au vendredi.</p>
      </div>
    </div>
  </aside>
</div>
```

> L'ordre des sections est imposé : intitulé, résumé, informations pratiques, pièces, procédure, action. Placer le bouton d'action avant les informations pratiques produit des dossiers incomplets.

### Formulaire long

Au-delà de huit champs, un formulaire est découpé en étapes numérotées. La progression est sauvegardée à chaque étape : sur réseau instable, perdre une saisie de quinze minutes fait abandonner la démarche.

#### Composition

*Gabarit — https://eservices.gov.bf/demarches/acte-de-naissance/etape-3*

```html
<div style="max-width: 640px; margin-inline: auto;">
  <div class="fs-pile fs-pile--large">

    <nav aria-label="Progression">
      <p class="fs-invisible">Étape 3 sur 5</p>
      <ol class="fs-etapes">
        <li class="fs-etape" data-etat="faite"><span class="fs-etape-num">Étape 1</span><span class="fs-etape-titre">Identification</span></li>
        <li class="fs-etape" data-etat="faite"><span class="fs-etape-num">Étape 2</span><span class="fs-etape-titre">Objet</span></li>
        <li class="fs-etape" data-etat="courante" aria-current="step"><span class="fs-etape-num">Étape 3</span><span class="fs-etape-titre">Pièces</span></li>
        <li class="fs-etape"><span class="fs-etape-num">Étape 4</span><span class="fs-etape-titre">Paiement</span></li>
        <li class="fs-etape"><span class="fs-etape-num">Étape 5</span><span class="fs-etape-titre">Récapitulatif</span></li>
      </ol>
    </nav>

    <div>
      <h3>Pièces justificatives</h3>
      <p class="fs-attenue">Les documents doivent être lisibles et non rognés. Formats acceptés : JPG, PNG ou PDF, 5 Mo maximum par fichier.</p>
    </div>

    <div class="fs-champ">
      <label class="fs-label" for="g-piece">Copie de la pièce d'identité <span class="fs-requis">*</span></label>
      <label class="fs-depot" tabindex="0">
        <span class="fs-depot-titre">Déposez un fichier ou parcourez</span>
        <span class="fs-depot-contraintes">JPG, PNG ou PDF — 5 Mo maximum</span>
        <input type="file" id="g-piece">
      </label>
    </div>

    <div class="fs-fichier">
      <span class="fs-fichier-nom">cnib-recto.jpg</span>
      <span class="fs-fichier-poids">1,2 Mo</span>
      <span class="fs-badge fs-badge--succes">Transmis</span>
    </div>

    <div class="fs-encart fs-encart--info">
      <div class="fs-encart-corps">
        <p>Votre progression est enregistrée automatiquement. Vous pouvez reprendre cette demande pendant trente jours depuis votre espace personnel.</p>
      </div>
    </div>

    <div class="fs-actions fs-actions--fin">
      <button type="button" class="fs-btn fs-btn--fantome">Enregistrer et quitter</button>
      <button type="button" class="fs-btn fs-btn--tertiaire">Étape précédente</button>
      <button type="button" class="fs-btn fs-btn--principal">Continuer</button>
    </div>

  </div>
</div>
```

**Règles**

- Une seule intention par étape, cinq étapes au maximum.
- Sauvegarde automatique à chaque changement d'étape.
- Récapitulatif modifiable avant validation définitive.
- Accusé de dépôt téléchargeable immédiatement après transmission.
- En cas d'erreur, le récapitulatif d'erreurs reçoit le focus et renvoie vers chaque champ concerné.

**À proscrire**

- Validation bloquante en cours de frappe : elle signale une erreur avant la fin de la saisie.
- Expiration de session sans avertissement ni sauvegarde.
- Champ obligatoire sans justification de sa nécessité.
- Message d'erreur qui constate la faute sans indiquer la correction.

### Tableau de bord usager

Espace personnel. Il répond à trois questions dans cet ordre : qu'attend-on de moi, où en sont mes demandes, que puis-je télécharger.

#### Composition

*Gabarit — https://eservices.gov.bf/mon-espace*

```html
<div class="fs-pile fs-pile--large">

  <div class="fs-rangee fs-rangee--entre">
    <div>
      <h3 style="margin-bottom: var(--espace-1);">Bonjour</h3>
      <p class="fs-attenue" style="margin:0;">Dernière connexion le 19 septembre 2026</p>
    </div>
    <a class="fs-btn fs-btn--principal" href="#">Nouvelle démarche</a>
  </div>

  <div class="fs-encart fs-encart--alerte">
    <span class="fs-encart-marque fs-icone fs-icone--alerte" aria-hidden="true"></span>
    <div class="fs-encart-corps">
      <p class="fs-encart-titre">Une action est attendue de votre part</p>
      <p>Le dossier BF-2026-004915 requiert un justificatif de domicile avant le 30 septembre 2026.</p>
      <a class="fs-btn fs-btn--tertiaire fs-btn--sm fs-mt-4" href="#">Transmettre la pièce</a>
    </div>
  </div>

  <div class="fs-auto-grille" style="--min-colonne: 160px;">
    <div class="fs-stat"><span class="fs-stat-valeur">3</span><span class="fs-stat-libelle">demandes en cours</span></div>
    <div class="fs-stat"><span class="fs-stat-valeur">1</span><span class="fs-stat-libelle">action attendue</span></div>
    <div class="fs-stat"><span class="fs-stat-valeur">12</span><span class="fs-stat-libelle">documents disponibles</span></div>
  </div>

  <div>
    <h4>Mes demandes</h4>
    <div class="fs-tableau-cadre">
      <table class="fs-tableau fs-tableau--empile">
        <thead>
          <tr><th>Référence</th><th>Démarche</th><th>Dépôt</th><th>État</th></tr>
        </thead>
        <tbody>
          <tr>
            <td data-colonne="Référence"><code>BF-2026-004871</code></td>
            <td data-colonne="Démarche">Acte de naissance</td>
            <td data-colonne="Dépôt">18/09/2026</td>
            <td data-colonne="État"><span class="fs-statut fs-statut--cours">En instruction</span></td>
          </tr>
          <tr>
            <td data-colonne="Référence"><code>BF-2026-004915</code></td>
            <td data-colonne="Démarche">Casier judiciaire</td>
            <td data-colonne="Dépôt">12/09/2026</td>
            <td data-colonne="État"><span class="fs-statut fs-statut--alerte">Complément demandé</span></td>
          </tr>
          <tr>
            <td data-colonne="Référence"><code>BF-2026-004790</code></td>
            <td data-colonne="Démarche">Certificat de résidence</td>
            <td data-colonne="Dépôt">02/09/2026</td>
            <td data-colonne="État"><span class="fs-statut fs-statut--succes">Disponible au retrait</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>
```

### Tableau de bord agent

L'agent qui instruit les dossiers est lui aussi un usager du système. Son écran répond à une question différente de celle du citoyen : non pas « où en est mon dossier ? » mais « lequel dois-je traiter maintenant ? ». La densité y est assumée, l'écran étant consulté toute la journée sur un poste fixe.

#### Composition

*Gabarit — https://eservices.gov.bf/agent/instruction*

```html
<div class="fs-pile fs-pile--large">

  <div class="fs-rangee fs-rangee--entre">
    <div>
      <h3 style="margin-bottom: var(--espace-1);">File d'instruction</h3>
      <p class="fs-attenue" style="margin:0;">Direction générale de la modernisation de l'état civil — poste 12</p>
    </div>
    <div class="fs-rangee">
      <button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm">
        <span class="fs-icone fs-icone--filtre" aria-hidden="true"></span>
        Filtrer
      </button>
      <button type="button" class="fs-btn fs-btn--principal fs-btn--sm">Prendre le suivant</button>
    </div>
  </div>

  <div class="fs-encart fs-encart--danger" role="alert">
    <span class="fs-encart-marque fs-icone fs-icone--alerte" aria-hidden="true"></span>
    <div class="fs-encart-corps">
      <p class="fs-encart-titre">4 dossiers hors délai</p>
      <p>Le délai indicatif de trois jours ouvrés est dépassé. Ces dossiers passent en tête de file.</p>
    </div>
  </div>

  <div class="fs-auto-grille" style="--min-colonne: 150px;">
    <div class="fs-stat">
      <span class="fs-stat-valeur">37</span>
      <span class="fs-stat-libelle">à traiter</span>
    </div>
    <div class="fs-stat">
      <span class="fs-stat-valeur" style="color: var(--danger-texte);">4</span>
      <span class="fs-stat-libelle">hors délai</span>
    </div>
    <div class="fs-stat">
      <span class="fs-stat-valeur">12</span>
      <span class="fs-stat-libelle">traités aujourd'hui</span>
    </div>
    <div class="fs-stat">
      <span class="fs-stat-valeur">2,4 j</span>
      <span class="fs-stat-libelle">délai moyen du poste</span>
    </div>
  </div>

  <div>
    <h4>Dossiers à instruire</h4>
    <div class="fs-tableau-cadre">
      <table class="fs-tableau fs-tableau--compact fs-tableau--zebre">
        <caption class="fs-invisible">Dossiers en attente d'instruction, du plus ancien au plus récent</caption>
        <thead>
          <tr>
            <th>Référence</th>
            <th>Démarche</th>
            <th>Déposé</th>
            <th>Attente</th>
            <th>État</th>
            <th data-export="non">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><code>BF-2026-004612</code></td>
            <td>Acte de naissance</td>
            <td>02/09/2026</td>
            <td class="fs-num"><span class="fs-badge fs-badge--danger">19 j</span></td>
            <td><span class="fs-statut fs-statut--alerte">Pièce reçue</span></td>
            <td><button type="button" class="fs-btn fs-btn--secondaire fs-btn--sm">Instruire</button></td>
          </tr>
          <tr>
            <td><code>BF-2026-004655</code></td>
            <td>Certificat de résidence</td>
            <td>08/09/2026</td>
            <td class="fs-num"><span class="fs-badge fs-badge--alerte">13 j</span></td>
            <td><span class="fs-statut fs-statut--cours">Complet</span></td>
            <td><button type="button" class="fs-btn fs-btn--secondaire fs-btn--sm">Instruire</button></td>
          </tr>
          <tr>
            <td><code>BF-2026-004871</code></td>
            <td>Acte de naissance</td>
            <td>18/09/2026</td>
            <td class="fs-num"><span class="fs-badge fs-badge--neutre">3 j</span></td>
            <td><span class="fs-statut fs-statut--cours">Complet</span></td>
            <td><button type="button" class="fs-btn fs-btn--tertiaire fs-btn--sm">Instruire</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>
```

**Règles**

- Ordonner par ancienneté, pas par date de dépôt décroissante. Le plus vieux dossier passe en premier.
- Afficher l'attente en jours, avec un repère qui se durcit à l'approche du délai.
- Une action principale par ligne, toujours au même endroit.
- Exclure la colonne d'actions des extractions par `data-export="non"`.
- Densité plus forte que côté citoyen : tableau compact, zébrures, pas d'illustration.

**À proscrire**

- Reprendre le gabarit citoyen tel quel. Les deux écrans répondent à des questions différentes.
- Laisser l'agent choisir librement son dossier sans signaler les retards.
- Afficher des données personnelles au-delà de ce que l'instruction exige.
- Masquer le nom de l'agent instructeur. Une décision administrative est imputable.

### Authentification

Page d'entrée dans l'espace personnel. Conteneur étroit, aucune navigation secondaire, aucune distraction. La seule issue est de s'identifier ou de revenir à l'accueil.

#### Composition

*Gabarit — https://eservices.gov.bf/connexion*

```html
<div style="max-width: 420px; margin-inline: auto;">
  <div class="fs-pile fs-pile--large">

    <div class="fs-centre">
      <img src="https://chartegraphique-21.mtdpce-test.gov.bf/assets/img/armoiries.svg" alt="" style="width:56px; margin-inline:auto;">
      <h3 class="fs-mt-4" style="margin-bottom: var(--espace-2);">Accéder à mon espace</h3>
      <p class="fs-attenue" style="font-size: var(--taille-sm);">Identifiez-vous pour suivre vos demandes et télécharger vos documents.</p>
    </div>

    <div class="fs-champ">
      <label class="fs-label" for="g-cnib">Numéro CNIB</label>
      <input class="fs-saisie" type="text" id="g-cnib" placeholder="B1234567">
    </div>

    <div class="fs-champ">
      <label class="fs-label" for="g-tel">Téléphone</label>
      <div class="fs-groupe-saisie">
        <span class="fs-affixe">+226</span>
        <input class="fs-saisie" type="tel" id="g-tel" placeholder="70 00 00 00">
      </div>
      <p class="fs-aide">Un code de vérification à six chiffres vous sera envoyé par SMS.</p>
    </div>

    <button type="button" class="fs-btn fs-btn--principal fs-btn--bloc fs-btn--lg">Recevoir le code</button>

    <div class="fs-centre">
      <p class="fs-legende"><a href="#">Je n'ai pas de numéro CNIB</a></p>
    </div>

    <div class="fs-signalement">
      <p class="fs-legende" style="margin:0;">
        Aucun agent de l'administration ne vous demandera ce code par
        téléphone ou par message.
      </p>
    </div>

  </div>
</div>
```

### Pages d'erreur

Une page d'erreur explique ce qui s'est passé, ce que l'usager peut faire, et qui contacter. Elle ne plaisante pas et n'affiche aucun code technique sans explication.

#### Page introuvable et indisponibilité

*Exemple — Pages d'erreur*

```html
<div class="fs-vide" style="border-style: solid;">
  <span class="fs-badge fs-badge--neutre">Erreur 404</span>
  <h4 class="fs-vide-titre">Cette page n'existe pas ou a été déplacée</h4>
  <p>
    L'adresse saisie est peut-être incomplète, ou le contenu a été
    réorganisé. La recherche vous permettra de retrouver la démarche
    concernée.
  </p>
  <div class="fs-actions">
    <a class="fs-btn fs-btn--tertiaire" href="#">Retour à l'accueil</a>
    <a class="fs-btn fs-btn--principal" href="#">Rechercher une démarche</a>
  </div>
</div>

<div class="fs-vide" style="border-style: solid;">
  <span class="fs-badge fs-badge--danger">Erreur 503</span>
  <h4 class="fs-vide-titre">Le service est momentanément indisponible</h4>
  <p>
    Une opération de maintenance est en cours. Vos demandes déjà déposées
    ne sont pas affectées. Le service sera rétabli dans la journée.
  </p>
  <div class="fs-actions">
    <a class="fs-btn fs-btn--tertiaire" href="#">Réessayer</a>
    <a class="fs-btn fs-btn--secondaire" href="#">Voir l'état des services</a>
  </div>
</div>
```

**Règles**

- Indiquer si les données déjà saisies ou déposées sont affectées.
- Proposer au moins deux issues, dont la recherche.
- Conserver l'en-tête et le pied de page complets : l'usager doit pouvoir repartir.
- Donner un contact lorsque l'erreur bloque une démarche en cours.

**À proscrire**

- Illustration humoristique ou ton léger.
- Code d'erreur technique affiché sans explication en langage courant.
- Page d'erreur dépouillée de toute navigation, laissant l'usager sans issue.
- Renvoi automatique vers l'accueil sans laisser le temps de lire.


---

## Accessibilité

Page de référence : https://chartegraphique-21.mtdpce-test.gov.bf/accessibilite

Un service public s'adresse à toute la population, sans condition d'équipement, de débit, de vue ou de maîtrise du vocabulaire administratif. Les exigences de cette page ne sont pas des recommandations. Un service qui ne les respecte pas exclut une partie de ses usagers.

### Critères de conformité

Le niveau visé est WCAG 2.1 AA. Les exigences marquées Système sont tenues par les composants tels qu'ils sont livrés, et vérifiées par la mesure décrite plus bas. Un projet qui les reprend sans les modifier part de là ; il n'en hérite pas pour autant une conformité globale, qui dépend de son contenu, de sa structure et de ses parcours.

#### Tableau de conformité

*Exigences appliquées par le système et points restant à la charge du projet*

| Exigence | Valeur imposée | Portée |
| --- | --- | --- |
| Contraste du texte courant | 4,5 : 1 minimum | Système |
| Contraste des grands titres | 3 : 1 minimum | Système |
| Contraste des bordures de champ | 3 : 1 minimum | Système |
| Taille du corps de texte | 15 px minimum | Système |
| Cible tactile | 44 × 44 px, deux exceptions | Système |
| Focus visible au clavier | Anneau de 2 px, halo sur toute surface | Système |
| Agrandissement du texte | 200 % sans perte de contenu | Système |
| Langue du document | `lang` déclaré et exact | Projet |
| Texte de remplacement des images | Descriptif ou vide si décoratif | Projet |
| Hiérarchie des titres | Un seul `h1`, aucun niveau sauté | Projet |
| Libellé de lien explicite hors contexte | Pas de « cliquez ici » | Projet |
| Sous-titres des contenus vidéo | Systématiques | Projet |

#### Les deux exceptions à la cible de 44 px

La règle des 44 px est celle de la charte, plus exigeante que le référentiel : WCAG 2.2 fixe le plancher de conformité AA à 24 × 24 px. Les deux cas ci-dessous restent très au-dessus de ce plancher, et sont écrits ici plutôt que passés sous silence.

| Cas | Dessin | Cible effective | Pourquoi |
| --- | --- | --- | --- |
| Bouton à icône en taille réduite | 36 × 36 px | 44 × 44 px | Un calque transparent déborde de 4 px. La zone qui répond atteint la cible sans épaissir les barres d'outils denses. |
| Jour de calendrier sous 400 px de large | 40 px de haut | 38 px au minimum | Sept cases de 44 px débordent d'un téléphone étroit. Au-delà de 400 px, la case fait bien 44 × 44 px. |

#### Portée de la vérification

Une charte qui annonce un niveau doit dire comment elle le vérifie, et jusqu'où sa vérification porte. Le tableau ci-dessus ne vaut que dans ces limites.

**Mesuré**

- **118 couples de couleurs** effectivement employés par `faso.css` (texte sur fond, variante de bouton, état sémantique, contour de contrôle), calculés dans les deux thèmes, transparences composées sur leur fond réel. Aucun sous le seuil.
- **Les dimensions des cibles tactiles**, relevées dans la feuille de styles et reportées ci-dessus sans arrondi favorable.
- **Le balisage des huit pages** : bonne formation, aucune classe orpheline.
- **Le comportement au clavier** des onglets, du menu, de la modale, du panneau et de l'autocomplétion, par lecture du code et essais automatisés sur un document simulé.

**Non mesuré à ce jour**

- **Aucun essai avec un lecteur d'écran réel** : NVDA, JAWS et VoiceOver restent à passer.
- **Aucun audit RGAA complet** par un tiers, ni grille de critères renseignée.
- **Aucun parcours complet dans un navigateur** : les essais s'appuient sur un document simulé, qui ne remplace pas le rendu réel.
- **Aucun test avec des personnes en situation de handicap**, qui reste la seule vérification qui compte vraiment.

> Tant que ces quatre points ne sont pas levés, la formule juste est **« conçu pour WCAG 2.1 AA, vérifié par la mesure sur les points listés »**, et non « conforme ». La déclaration d'accessibilité d'un service ne peut être établie qu'après l'audit, service par service.

#### Vérification des contrastes

Les rapports ci-dessous sont calculés dans le navigateur à partir de la palette réelle, selon la formule WCAG 2.1. Ils se mettent à jour si la palette change.

| Association | Emploi | Rapport |
| --- | --- | --- |
| Texte 900 sur blanc | Texte courant |  |
| Texte 600 sur blanc | Texte atténué, aide |  |
| Texte 550 sur blanc | Légende, mention secondaire |  |
| Texte 550 sur surface enfoncée | Légende dans un encadré |  |
| Bordure 450 sur blanc | Contour d'un champ, seuil de 3:1 |  |
| Bordure 450 sur surface enfoncée | Contour d'un champ désactivé |  |
| Blanc sur vert 500 | Bouton principal |  |
| Blanc sur rouge 500 | Bouton de suppression |  |
| Noir sur or 300 | Bouton de mise en avant |  |
| Blanc sur or 300 | Association interdite |  |
| Vert 700 sur blanc | Lien dans le texte |  |
| Rouge 700 sur rouge 50 | Message d'erreur |  |
| Ocre 700 sur ocre 50 | Message d'avertissement |  |

> L'association du blanc sur l'or du drapeau est explicitement interdite : son rapport est très inférieur au seuil. C'est la raison pour laquelle le rôle `--relief-texte` impose un texte sombre.

### Navigation au clavier

Toute action réalisable à la souris doit l'être au clavier, dans un ordre logique et avec un repère visible en permanence.

#### Conventions appliquées

| Touche | Comportement | Composants |
| --- | --- | --- |
| `Tab` | Passe à l'élément interactif suivant dans l'ordre du document | Tous |
| `Entrée` | Active un lien ou un bouton | Tous |
| `Espace` | Coche, décoche, ou active un bouton | Cases, interrupteurs, boutons |
| `Échap` | Ferme et rend le focus au déclencheur | Modale, panneau latéral, menu |
| `←` `→` | Change d'onglet ou de bouton radio | Onglets, groupes radio |
| `Début` `Fin` | Atteint la première ou la dernière option | Onglets |

#### Piège de focus

Lorsqu'une modale ou un panneau latéral est ouvert, le focus reste enfermé à l'intérieur. L'élément `<dialog>` l'assure nativement ; le panneau latéral l'implémente explicitement dans `faso.js`. À la fermeture, le focus revient sur l'élément qui a déclenché l'ouverture, faute de quoi l'usager au clavier est renvoyé en début de document.

*Exemple — Essai au clavier*

```html
<button type="button" class="fs-btn fs-btn--tertiaire" data-ouvre-modale="modale-clavier">
  Ouvrir, puis parcourir au clavier
</button>

<dialog class="fs-modale" id="modale-clavier" aria-labelledby="modale-clavier-titre">
  <div class="fs-modale-entete">
    <h3 class="fs-modale-titre" id="modale-clavier-titre">Piège de focus</h3>
    <button type="button" class="fs-btn fs-btn--fantome fs-btn--sm fs-btn--icone" data-ferme-modale aria-label="Fermer"><span class="fs-icone fs-icone--fermer" aria-hidden="true"></span></button>
  </div>
  <div class="fs-modale-corps">
    <p>Appuyez sur la touche de tabulation à plusieurs reprises. Le focus tourne à l'intérieur de cette fenêtre sans en sortir.</p>
    <p>Appuyez ensuite sur Échap. La fenêtre se ferme et le focus revient sur le bouton d'origine.</p>
  </div>
  <div class="fs-modale-pied">
    <button type="button" class="fs-btn fs-btn--tertiaire" data-ferme-modale>Fermer</button>
    <button type="button" class="fs-btn fs-btn--principal" data-ferme-modale>J'ai compris</button>
  </div>
</dialog>
```

### Contrainte de réseau et d'équipement

L'usager type consulte un service public depuis un téléphone d'entrée de gamme, sur un réseau instable, avec un forfait facturé au volume. Cette situation n'est pas un cas limite. C'est le cas nominal.

#### Conséquences de conception

**Imposé**

- Le contenu essentiel s'affiche sans JavaScript. L'accordéon, les tableaux et les formulaires restent utilisables si le script échoue.
- Les liens de téléchargement annoncent format et poids avant le clic.
- Les images portent des dimensions explicites pour éviter les décalages de mise en page au chargement.
- La progression d'un formulaire long est sauvegardée côté serveur à chaque étape.
- Une interruption de réseau est signalée et les données saisies sont conservées.

**Proscrit**

- Police de caractères bloquant l'affichage du texte pendant son chargement.
- Image décorative de plus de 200 Ko, carrousel automatique, vidéo en lecture automatique.
- Rendu du contenu entièrement dépendant d'un script côté client.
- Rechargement complet de la page à chaque saisie de champ.
- Police de caractères chargée depuis un domaine tiers sans solution de repli locale.

### Budget de performance

Un principe sans chiffre ne se vérifie pas. Les plafonds ci-dessous s'appliquent au **poids transféré**, compression comprise, car c'est lui que l'usager paie sur un forfait facturé au volume. Ils sont contrôlés à la recette, au même titre que l'accessibilité.

#### Plafonds

*Valeurs mesurées sur une page vidée du cache, en transfert réseau*

| Grandeur | Objectif | Plafond | Motif |
| --- | --- | --- | --- |
| Page complète | 250 Ko | 500 Ko | Au-delà, le coût en données devient perceptible sur un forfait prépayé. |
| Première page utile | 100 Ko | 150 Ko | Ce qu'il faut pour afficher le contenu principal, hors images en contrebas. |
| Affichage du contenu principal | 2 s | 3 s | Mesuré en 3G lente, sur un appareil d'entrée de gamme. |
| JavaScript | 30 Ko | 100 Ko | Le temps d'analyse pèse davantage que le téléchargement sur un processeur modeste. |
| Feuilles de style | 30 Ko | 60 Ko | Le CSS bloque l'affichage : il est toujours sur le chemin critique. |
| Polices | 60 Ko | 100 Ko | Deux familles au plus, hébergées localement, `font-display: swap` obligatoire. Le système s'établit à 81 Ko. |
| Image, à l'unité | 100 Ko | 200 Ko | Dimensionnée à sa taille d'affichage, jamais au-delà. |
| Requêtes | 20 | 40 | Sur réseau à forte latence, chaque requête coûte plus que son contenu. |

#### Où se situe le système lui-même

Une charte qui impose un budget doit s'y tenir. Voici le poids réel des fichiers livrés, mesuré sur cette version.

Poids transféré du socle, compression comprise

Ces fichiers sont mis en cache : ils ne sont téléchargés qu'à la première visite.

Emblème `armoiries.svg` 44,8 Ko

Composants `faso.css` 19,1 Ko

Comportements `faso.js` 22,1 Ko

Jetons `tokens.css` 6,0 Ko

Icônes `icones.css` 2,9 Ko

Amorce `faso-amorce.js` 0,6 Ko

**Consulter les données sous forme de tableau**

| Fichier | Poids transféré |
| --- | --- |
| Emblème `armoiries.svg` | 44,8 Ko |
| Composants `faso.css` | 19,1 Ko |
| Comportements `faso.js` | 22,1 Ko |
| Jetons `tokens.css` | 6,0 Ko |
| Icônes `icones.css` | 2,9 Ko |
| Amorce `faso-amorce.js` | 0,6 Ko |
| **Total** | **95,5 Ko** |

Socle complet : **95,5 Ko** transférés, auxquels s'ajoutent **81 Ko** de polices, servies depuis le domaine du service. Avec le HTML d'une page, le total s'établit autour de **183 Ko** : sous le plafond de 500 Ko, au-dessus de l'objectif de 250 Ko seulement à la première visite, les polices et le socle étant ensuite mis en cache.

> L'emblème représente à lui seul la moitié du socle. C'est le prix d'un tracé vectoriel fidèle. Il reste vingt fois plus léger que le raster dont il est issu, et il n'est téléchargé qu'une fois pour l'ensemble du service.

#### Règles d'application

**Imposé**

- Héberger les polices sur le domaine du service : un tiers ajoute une résolution DNS, une connexion et une dépendance hors de contrôle.
- Déclarer `width` et `height` sur toute image, pour éviter le décalage de mise en page au chargement.
- Charger en différé les images situées sous la ligne de flottaison.
- Servir les fichiers compressés et avec une durée de cache longue.
- Mesurer sur un appareil réel, pas sur un poste de développement.

**Proscrit**

- Toute bibliothèque tierce pour un besoin couvert par le système.
- Vidéo en lecture automatique, carrousel, animation permanente.
- Mesure d'audience chargée avant le contenu.
- Police de caractères bloquant l'affichage du texte pendant son chargement.
- Image servie à une résolution supérieure à sa taille d'affichage.

### Ton administratif et rédaction

La formulation fait partie de l'interface. Un intitulé ambigu produit exactement les mêmes abandons qu'un bouton mal placé.

#### Principes de rédaction

- Employer un vocabulaire courant et expliciter les termes administratifs à leur première occurrence.
- S'adresser à l'usager à la deuxième personne du pluriel, de manière neutre.
- Préférer la voix active : « Déposez votre demande » plutôt que « La demande doit être déposée ».
- Indiquer le coût, le délai, les pièces et l'administration responsable avant tout formulaire.
- Proscrire les formulations promotionnelles dans les parcours de démarche.
- Donner une échéance datée plutôt qu'une durée relative lorsqu'une action est attendue.

#### Messages d'erreur

Un message d'erreur indique la correction attendue. Constater la faute sans dire quoi faire laisse l'usager bloqué.

| À proscrire | À écrire |
| --- | --- |
| Champ invalide | Ajoutez le symbole arobase et le nom de domaine, par exemple nom@exemple.bf |
| Erreur de format | Le numéro CNIB comporte une lettre suivie de sept chiffres, sans espace |
| Fichier refusé | Ce fichier pèse 8,4 Mo. Réduisez sa taille à 5 Mo au maximum, ou déposez une photographie plutôt qu'un scan |
| Une erreur est survenue | La demande n'a pas pu être transmise. Vos données sont conservées. Réessayez dans quelques instants |
| Session expirée | Votre session a expiré après trente minutes d'inactivité. Votre brouillon a été enregistré : identifiez-vous pour le retrouver |

#### Intitulés d'action

Un bouton décrit ce qu'il déclenche. Un usager qui ne lit que les boutons doit comprendre la conséquence de son geste.

**À écrire**

- Déposer la demande
- Transmettre la pièce justificative
- Supprimer définitivement le brouillon
- Télécharger le récépissé au format PDF

**À proscrire**

- Valider
- Oui
- Continuer
- Cliquez ici

#### Écriture des données

**Montants**

Séparateur de milliers par espace insécable, unité explicite : `50 000 F CFA`

**Dates**

En toutes lettres dans le texte, en chiffres dans les tableaux : `18 septembre 2026` ou `18/09/2026`

**Heures**

Format 24 heures avec espace : `7 h 30`

**Références de dossier**

En caractères à chasse fixe, non traduites : `BF-2026-004871-OUA`

**Téléphones**

Indicatif séparé, groupes de deux chiffres : `+226 70 00 00 00`

### Déclaration d'accessibilité

Chaque service publie une déclaration d'accessibilité accessible depuis le pied de page de toutes ses pages.

#### Contenu obligatoire

- Le niveau de conformité constaté et la date du dernier audit.
- La liste des contenus non conformes, avec la justification et l'échéance de correction.
- Les moyens de signaler un défaut d'accessibilité et le délai de réponse.
- La voie de recours ouverte à l'usager en cas d'absence de réponse.
- Une solution de remplacement pour tout contenu non accessible, notamment un canal physique ou téléphonique.

> **Recette avant mise en production**
>
> Tout service est vérifié au clavier seul, avec un lecteur d'écran, à 200 % d'agrandissement, sur un écran de 320 px de large, et avec JavaScript désactivé. Un défaut constaté sur l'un de ces cinq points est bloquant.


---

## Intégration

Page de référence : https://chartegraphique-21.mtdpce-test.gov.bf/integration

La charte s'installe comme une dépendance ordinaire. Elle fournit **l'apparence et les comportements des composants isolés** ; les composants pilotés par des données restent la responsabilité du cadriciel. Cette page dit ce que cela recouvre exactement, et ce qu'il faut savoir avant d'installer. Quatre environnements y sont traités en détail : [Angular](https://chartegraphique-21.mtdpce-test.gov.bf/integration#angular), [React](https://chartegraphique-21.mtdpce-test.gov.bf/integration#react), [Laravel](https://chartegraphique-21.mtdpce-test.gov.bf/integration#laravel) et [Android](https://chartegraphique-21.mtdpce-test.gov.bf/integration#android) ; les autres se ramènent à l'un des quatre. La [diffusion](https://chartegraphique-21.mtdpce-test.gov.bf/integration#diffusion), publier le paquet et servir les fichiers en ligne, fait l'objet d'une section à part.

### Partage des responsabilités

Tout tient dans cette répartition. La lire avant d'écrire une ligne de code évite le seul type de bogue véritablement pénible : celui où le cadriciel et la charte se disputent la propriété d'un même nœud du document.

#### Trois catégories

| Catégorie | Composants | Ce que fait le projet |
| --- | --- | --- |
| CSS seul | Boutons, liens, cartes, encarts, badges, étiquettes, avatars, listes, tableaux statiques, filet, repère, figures, graphiques, fiches de démarche, pièces, suivi de dossier | Reprendre les classes. Rien d'autre. |
| Comportement autonome | Modale, panneau latéral, menu, accordéon, onglets, infobulle, encart refermable, dépôt de fichier, code de vérification, saisie de date, calendrier, centre de notifications, bascule de thème | Appeler l'initialisation après le rendu, ou activer l'observation une fois pour toutes. |
| Piloté par données | Tableau trié, filtré, paginé ; autocomplétion ; export | Utilisable **si le cadriciel rend l'intégralité des lignes**. S'il pagine de son côté, il reprend tout. |

> La troisième catégorie tient à peu de chose. La pagination de la charte pose `hidden` sur des lignes de tableau ; le filtre y pose `data-exclu`. Si le cadriciel reconstruit ces lignes, ces attributs disparaissent.

> Depuis, le remplacement des lignes est détecté : pagination et filtres se rejouent, et l'usager reste sur sa page tant que le nombre de lignes ne change pas. La ligne de partage n'est donc plus « le tableau vient-il d'une boucle ? » mais **« qui décide des lignes présentes ? »**. Tant que le cadriciel les rend toutes, la charte peut les paginer. Dès qu'il n'en rend qu'une partie, il doit aussi reprendre le tri, le filtre et l'export — et `data-export-total` empêche entre-temps toute extraction incomplète.

#### Où passe la limite

**Sain**

- Le balisage d'un composant est écrit une fois et ne change plus. La charte peut l'animer.
- Le cadriciel rend une liste et lui applique les classes de la charte : l'apparence est reprise, la logique reste au cadriciel.
- La charte n'écrit que dans des nœuds qu'aucune liaison du cadriciel ne surveille.

**À éviter**

- Poser `data-pagination` sur un tableau que le cadriciel pagine déjà de son côté.
- Lier un attribut que la charte pilote sans annuler l'annonce qui précède son écriture.
- Exporter depuis le document quand le cadriciel n'en rend qu'une partie, sans déclarer `data-export-total`.

### Installation

Un seul paquet, quel que soit le cadriciel. Aucune variante React, Angular ou Vue n'est publiée — le motif de ce choix est expliqué plus bas.

#### Préparer un projet en une commande

Pour un projet qui démarre, une commande suffit. Elle écrit les fichiers de départ et installe la charte ; il n'y a ensuite qu'à ouvrir la page ou importer le fichier d'amorçage.

```bash
npx https://chartegraphique-21.mtdpce-test.gov.bf/npm/govbf-create-charte-graphique-2.0.0.tgz
```

Trois questions : le dossier, le type de projet — `statique`, `react`, `angular` ou `laravel` — et le dépôt npm interne si le service en emploie un ; sans réponse, la charte vient de `chartegraphique-21.mtdpce-test.gov.bf`. Tout peut aussi se donner sur la ligne de commande :

```bash
npx --yes https://chartegraphique-21.mtdpce-test.gov.bf/npm/govbf-create-charte-graphique-2.0.0.tgz \
  mon-service --type=react --oui
```

*Ce qui est écrit, selon le type*

| Type | Fichiers |
| --- | --- |
| `statique` | `index.html`, une page complète avec le bandeau officiel, le filet national et le pied de page ; `LISEZMOI.md`. |
| `react`, `angular` | `src/charte.js` : les trois feuilles dans l'ordre et l'appel d'observation ; `LISEZMOI.md`. |
| `laravel` | `resources/js/charte.js`, où Laravel range ses sources ; `LISEZMOI.md`. |

Plus un `package.json` minimal s'il n'y en a pas, et un `.npmrc` si un dépôt interne a été indiqué. La commande refuse d'écrire dans un dossier qui existe déjà et n'est pas vide.

> **Les conditions d'emploi se lisent avant l'installation**
>
> La charte porte les armoiries, le drapeau et la devise de l'État. La commande les rappelle et demande de les accepter avant d'installer quoi que ce soit. Sur une chaîne d'intégration, où personne ne répond, l'acceptation se donne par la variable `CHARTE_ACCEPTE_LICENCE=oui` ou par l'option `--oui`.

La commande s'appuie sur un second paquet, `@govbf/create-charte-graphique`, que npx télécharge depuis le même serveur, exécute une fois puis oublie. Il porte la même version que la charte. Un projet déjà commencé n'en a pas besoin : il installe la charte directement, comme ci-dessous.

#### Par npm

Le serveur de la charte sert l'archive npm de chaque version. Elle s'installe comme n'importe quelle dépendance, quel que soit le cadriciel : Angular, React, Vue, Laravel avec Vite.

```bash
npm install https://chartegraphique-21.mtdpce-test.gov.bf/npm/govbf-charte-graphique-2.0.0.tgz
```

```
// le package.json du projet porte alors :
"dependencies": {
  "@govbf/charte-graphique": "https://chartegraphique-21.mtdpce-test.gov.bf/npm/govbf-charte-graphique-2.0.0.tgz"
}
```

Le paquet installé s'appelle `@govbf/charte-graphique` : c'est sous ce nom qu'il s'importe, comme dans tous les exemples de cette page. L'adresse porte le numéro de version ; passer à la suivante, c'est changer ce numéro. Le `package-lock.json` garde l'empreinte de l'archive, ce qui rend l'installation reproductible.

> **Pas encore sur un registre**
>
> `npm install @govbf/charte-graphique`, sans adresse, répond aujourd'hui `404 Not Found` : l'espace de noms `@govbf` n'est enregistré sur aucun registre. Le jour où il le sera, seule la ligne d'installation change ; les chemins d'importation sont déjà les bons.

##### Machine sans accès au serveur

Pour une chaîne d'intégration fermée, on télécharge l'archive une fois, on la range dans le dépôt du projet et on l'installe depuis là.

```bash
mkdir -p vendor
curl -o vendor/govbf-charte-graphique-2.0.0.tgz https://chartegraphique-21.mtdpce-test.gov.bf/npm/govbf-charte-graphique-2.0.0.tgz
npm install ./vendor/govbf-charte-graphique-2.0.0.tgz
```

#### Toute la charte en Markdown

Un seul fichier reprend l'intégralité de la charte : principes, fondations, composants avec leur balisage, composants métier, gabarits, accessibilité, intégration et gouvernance, suivis des jetons, de la liste des classes et de l'API JavaScript. Il est tiré des pages de ce site et ne peut pas s'en écarter. Il se lit tel quel, se cherche facilement, et sert surtout de référence aux agents de code : assistants, modèles de langage, outils de génération ou de revue.

[Télécharger charte-graphique-2.0.0.md](https://chartegraphique-21.mtdpce-test.gov.bf/charte.md)

```bash
curl -o docs/charte-graphique.md https://chartegraphique-21.mtdpce-test.gov.bf/charte.md
```

Depuis le dépôt de la charte, `php artisan charte:markdown` écrit le même fichier. Un assistant peut aussi le lire directement à son adresse, `https://chartegraphique-21.mtdpce-test.gov.bf/charte.md` : ChatGPT et Claude lisent tout le site, les autres assistants cette seule adresse. Les collecteurs d'entraînement de modèles en restent écartés.

> **Le fichier commence par des consignes aux agents**
>
> Avant tout le reste, il demande à tout agent qui le lit de suivre toujours les principes et les règles de la charte : aucune classe ni valeur inventée, balisage des exemples repris tel quel, emblèmes intouchables, accessibilité non négociable, et signalement de tout conflit entre une demande et la charte au lieu de l'appliquer en silence.

Pour que l'agent s'y tienne à chaque tâche, on range le fichier dans le dépôt du projet et on le désigne depuis son fichier de consignes : `AGENTS.md`, `CLAUDE.md` ou `.github/copilot-instructions.md`, selon l'outil.

```bash
# AGENTS.md
Toute interface de ce projet suit la charte graphique de l'administration
burkinabè : lire docs/charte-graphique.md et appliquer ses consignes aux
agents avant toute modification d'interface.
```

Le fichier porte le numéro de version de la charte. À chaque montée de version du paquet, on le télécharge à nouveau.

#### Chemins d'importation

Le paquet déclare ses sous-chemins ; ce sont les seuls utilisables, et ils ne changeront pas si l'arborescence interne bouge. Tous ont été vérifiés après installation réelle de l'archive.

| Import | Ce qu'il désigne |
| --- | --- |
| `@govbf/charte-graphique` | Les comportements, avec leurs types |
| `…/css/tokens.css` | Jetons, thèmes, déclarations de polices |
| `…/css/faso.css` | Bibliothèque de composants |
| `…/css/icones.css` | Les 35 icônes |
| `…/amorce` | Pose le thème avant le premier rendu |
| `…/jetons.json` | Les 193 jetons, valeurs résolues |
| `…/img/armoiries.svg` | Emblème vectoriel |
| `…/polices/archivo.woff2` | Les fichiers de police, si le projet les sert lui-même |
| `…/android/…` | Les ressources Android |

> `@govbf/charte-graphique/assets/css/faso.css` ne fonctionne pas, et c'est voulu : seuls les chemins ci-dessus sont publics. Les déclarations de polices, elles, désignent leurs fichiers par un chemin relatif interne au paquet ; un empaqueteur les résout tout seul, sans que le projet ait à s'en occuper.

#### Sans chaîne Node

Projet PHP sans outillage, site statique, page isolée : la page charge les fichiers directement depuis le serveur de la charte, à une adresse figée sur la version. C'est le mode de fonctionnement d'origine du système, et il reste pleinement pris en charge.

```html
<script src="https://chartegraphique-21.mtdpce-test.gov.bf/2.0.0/js/faso-amorce.js"></script>
<link rel="stylesheet" href="https://chartegraphique-21.mtdpce-test.gov.bf/2.0.0/css/tokens.css">
<link rel="stylesheet" href="https://chartegraphique-21.mtdpce-test.gov.bf/2.0.0/css/faso.css">
<link rel="stylesheet" href="https://chartegraphique-21.mtdpce-test.gov.bf/2.0.0/css/icones.css">
<script src="https://chartegraphique-21.mtdpce-test.gov.bf/2.0.0/js/faso.js" defer></script>
```

Dans ce mode, l'initialisation est automatique au chargement : il n'y a rien à appeler. La première ligne, bloquante et minuscule, pose le thème retenu avant le premier rendu ; elle n'est utile qu'aux services qui proposent le thème sombre.

#### Langue des libellés produits par le script

Une vingtaine de libellés sont écrits par les comportements eux-mêmes : « Page suivante », « Aucun résultat », le compteur de caractères, le contexte d'extraction. Ils sont rassemblés dans un objet unique, avec la langue de mise en forme des nombres et des dates. Un service qui rend son interface en mooré, en dioula ou en fulfuldé remplace ce dont il a besoin ; ce qu'il ne remplace pas reste en français.

```js
import Faso from '@govbf/charte-graphique';

Object.assign(Faso.textes, {
  langue: 'fr-BF',
  pageSuivante: '…',
  aucunResultat: '…',
});
// puis seulement : Faso.observer();
```

À faire avant l'initialisation, ou avant le premier rendu du composant concerné : un libellé déjà écrit dans le document n'est pas réécrit.

#### Le contenu du paquet

| Chemin | Contenu | Obligatoire |
| --- | --- | --- |
| `css/tokens.css` | Jetons, thèmes, déclarations de polices | Oui |
| `css/faso.css` | Bibliothèque de composants | Oui |
| `css/icones.css` | 35 icônes ; plusieurs composants y puisent leurs repères | Oui |
| `polices/` | Archivo et Inter, variables, sous-ensemble latin, avec leurs licences | Oui |
| racine du paquet | Comportements, avec ses déclarations de types | Non |
| `amorce` | Pose le thème retenu avant le premier rendu | Si thème sombre |
| `jetons.json` | 193 jetons, valeurs résolues, produits par `outils/jetons.js` | Non |
| `img/armoiries.svg` | Emblème vectoriel | Selon l'usage |

> L'arborescence `css/` et `polices/` doit être conservée telle quelle. Les déclarations de polices désignent les fichiers par un chemin relatif ; déplacer l'un sans l'autre casse le chargement, et le texte bascule silencieusement en police de repli.

### Publier et servir en ligne

Deux chemins, qui ne s'excluent pas : le paquet sur un registre, pour les projets qui ont une chaîne Node ; les fichiers sur un domaine, pour tous les autres. Les deux partent de la même vérification et de la même étiquette de version.

#### Ce qu'il reste à décider

Techniquement, le paquet est prêt : `npm publish --dry-run` aboutit. Ce qui manque relève de la décision, non du code, et la machine le dit maintenant elle-même.

```bash
node outils/verifier.js --publication
```

| À arrêter | Pourquoi cela bloque |
| --- | --- |
| L'organisme responsable du système | Il détient l'espace de noms `@govbf`, le dépôt et le jeton de publication. Tant qu'il n'est pas désigné, personne ne peut publier au nom de l'État. |
| Les conditions de réutilisation | Un paquet public sans conditions arrêtées est réutilisé sans cadre. Le fichier de licence annonce encore des dispositions provisoires. Le contrôle de publication refuse tant qu'elles le restent. |
| Le dépôt et le domaine | Quatre champs de `package.json` portent la mention `A-RENSEIGNER`. Le contrôle les nomme un par un. |

> Ces trois points sont des décisions institutionnelles. Le reste de cette section décrit ce qui se met en place le jour où elles sont prises, et qui ne demande alors plus aucun arbitrage.

#### 1. Une fois, à la mise en place

1. **Enregistrer l'espace de noms.** Sur le registre public, une organisation `gouv-bf` réserve définitivement le préfixe : personne d'autre ne pourra publier un paquet sous ce nom. C'est une précaution qui vaut d'être prise même avant la première publication.
2. **Créer un jeton d'automatisation**, et lui seul. Un jeton de ce type publie sans second facteur, ce qu'exige une chaîne d'intégration, mais ne peut pas administrer l'organisation. Il se range dans le secret `NPM_TOKEN` du dépôt, jamais dans un fichier.
3. **Renseigner les quatre champs** du `package.json` et arrêter les conditions de réutilisation.
4. **Activer la publication des pages** du dépôt, source « actions ».

#### 2. À chaque version

```bash
npm test                       # la recette entière, en local
npm version 2.1.0              # met à jour package.json et pose l'étiquette
git push --follow-tags
```

L'étiquette déclenche le reste. Trois travaux s'enchaînent, et le premier arrête tout s'il échoue : **vérification** — la même qu'en local, plus les contrôles de publication et la concordance entre l'étiquette et la version ; **paquet** — publication avec attestation d'origine ; **documentation** — mise en ligne des pages.

> Un mot sur l'attestation d'origine. `npm publish --provenance` relie publiquement l'archive au dépôt et au travail qui l'a produite. Quiconque installe la charte peut vérifier qu'elle vient bien de là, et non d'un paquet homonyme. Pour un système de l'État, dont l'apparence sert précisément à établir la confiance, ce n'est pas un détail.

La chaîne vérifie aussi que `jetons.json` et `android/` correspondent toujours à `tokens.css` : elle régénère et compare. Une projection oubliée arrête la publication au lieu de partir en ligne.

#### 3. Servir les fichiers en ligne

Un projet PHP sans outillage, un site statique, une page isolée. Tous ont besoin d'une adresse. Trois configurations prêtes sont fournies dans `hebergement/`, une par famille de serveur.

| Fichier | Pour |
| --- | --- |
| `_headers` | Cloudflare Pages, Netlify et apparentés |
| `nginx.conf` | Un serveur que l'administration exploite |
| `apache.htaccess` | Un hébergement mutualisé, sans droit d'administration |

##### La règle de version, qui commande tout le reste

```
https://chartegraphique-21.mtdpce-test.gov.bf/2.0.0/css/faso.css     ← figé, cache d'un an
https://chartegraphique-21.mtdpce-test.gov.bf/2.1.0/css/faso.css     ← la version suivante
https://chartegraphique-21.mtdpce-test.gov.bf/latest/css/faso.css    ← à proscrire en production
```

Une adresse versionnée ne se réécrit jamais. Une correction sort sous un nouveau numéro. C'est ce qui autorise le cache d'un an, et surtout ce qui garantit qu'un service ne changera pas d'apparence du jour au lendemain sans l'avoir décidé. Une adresse `latest` convient à un essai ; en production, elle expose chaque service à une correction qu'il n'a pas demandée.

> **Les polices exigent un en-tête, sinon elles disparaissent en silence**
>
> Servies depuis un domaine différent de celui du service, elles réclament `Access-Control-Allow-Origin`. Sans lui, le navigateur télécharge le fichier, le refuse, et bascule en police de repli sans rien signaler. La page reste lisible, et personne ne remarque que l'identité typographique a disparu. Les trois configurations posent cet en-tête.

> **Une seule autorisation particulière.** La politique de sécurité doit accepter `img-src data:` : les icônes sont des masques CSS encodés ainsi. Aucun `'unsafe-inline'` n'est requis, ni pour les styles ni pour les scripts.

#### 4. Collecteurs, robots et fouille de données

Cinquante-quatre agents sont refusés en production : collecteurs d'entraînement de modèles, assistants, aspirateurs de site et scanners de vulnérabilité. La liste est tenue dans `outils/robots.js`, qui engendre le `robots.txt`, le fragment nginx et le bloc du `.htaccess`. Tenue à la main, elle diverge : on ajoute un agent au `robots.txt`, on oublie le serveur, et le refus n'est plus qu'une demande écrite.

```bash
npm run robots              # engendre les trois fichiers
npm run verifier-robots     # refuse de les laisser diverger
```

##### Ce que chaque couche arrête réellement

| Couche | Portée |
| --- | --- |
| `robots.txt` | Rien, techniquement. C'est une demande. Les collecteurs des grandes maisons la respectent parce qu'il leur en coûterait de se faire prendre ; les autres la lisent pour savoir où regarder. |
| Refus par nom | Ce qui s'annonce. Un agent qui déclare `GPTBot` reçoit `403`. Un agent qui se déclare Firefox passe. |
| Débit limité | Ce qui insiste. Seule couche qui agisse sur un agent non déclaré. Elle protège le serveur plus qu'elle ne protège le contenu : neuf pages se copient en neuf requêtes, et aucun réglage tenable ne l'empêche. Disponible sous nginx, pas sous un hébergement mutualisé. |
| `TDM-Reservation` | Rien, techniquement. L'en-tête réserve les droits de fouille de textes et de données au sens du protocole TDM du W3C. Sa portée est juridique : elle établit que la reprise n'a pas été consentie, ce qu'un `robots.txt` laisse toujours discuter. |

> **Les moteurs de recherche restent autorisés**
>
> Ce n'est pas un oubli. Un développeur d'un ministère cherche « bouton charte burkina » avant de chercher `chartegraphique.gov.bf` : un système de conception que personne ne trouve ne sert personne. Les refuser retirerait la documentation des résultats de recherche sans gêner un seul collecteur d'entraînement, ceux-ci passant par d'autres agents. Un contrôle de la recette refuse d'ailleurs un `X-Robots-Tag` qui porterait `noindex`, et refuse que Googlebot, Bingbot, DuckDuckBot ou Applebot se retrouvent dans la liste.

##### Deux réglages à faire avant la mise en production

**Les adresses de supervision.** Une sonde n'envoie souvent aucun agent, et se ferait refuser comme un collecteur : le service serait déclaré en panne alors qu'il répond. Les deux configurations portent l'emplacement où inscrire ces adresses, en commentaire, avec la ligne d'exemple.

**Les assistants.** `ChatGPT-User`, `Claude-User` et leurs pareils ne collectent pas : ils vont chercher une page parce qu'une personne vient de la demander. Les refuser refuse à un agent de l'administration de faire lire la documentation par l'outil qu'il emploie. `ChatGPT-User` et `Claude-User` sont donc autorisés sur tout le site (liste `ASSISTANTS_AUTORISES` de `outils/robots.js`), avec leur propre groupe dans le `robots.txt`. Les autres assistants restent refusés, sauf sur [`/charte.md`](https://chartegraphique-21.mtdpce-test.gov.bf/integration#markdown), écrite pour eux : l'exception est déclarée par le champ `ouverts` du groupe et reportée dans les trois sorties. Dans tous les cas, `GPTBot` et `ClaudeBot`, qui collectent pour l'entraînement, restent refusés.

> **La diffusion versionnée n'est pas limitée en débit.** Les fichiers sous `/2.0.0/` sont consommés par les navigateurs des agents d'autres administrations. Un ministère derrière une seule sortie nattée compte pour une adresse : une limitation par adresse le couperait en entier, pour protéger des fichiers statiques mis en cache un an. Le débit ne s'applique qu'aux pages de documentation.

#### 5. Où héberger le paquet

Un paquet npm ne vit pas nulle part : il faut un dépôt distant qui le serve. Le choix n'est pas seulement technique. Il engage la souveraineté sur un fichier qui porte les emblèmes de l'État, et la capacité des services à continuer de travailler quand la liaison internationale faiblit.

*Les cinq dépôts possibles, et ce qu'ils impliquent*

| Dépôt | Hébergé | Coût | Ce qu'il apporte | Ce qu'il coûte |
| --- | --- | --- | --- | --- |
| **Registre public** `registry.npmjs.org` | Hors du pays | Gratuit | Aucune installation à prévoir ; attestation d'origine vérifiable ; diffusion la plus large, y compris aux prestataires. | Dépendance à un service privé étranger. Une version publiée ne se retire plus vraiment. |
| **Dépôt de la forge** `npm.pkg.github.com` | Hors du pays | Gratuit | Déjà là si le code y est ; authentification commune ; rien à exploiter. | L'espace de noms doit porter le nom de l'organisation. **Même en public, l'installation exige un jeton** — c'est la principale surprise. |
| **Verdaccio** sur un serveur de l'État | Au Burkina Faso | Un petit serveur | Souveraineté complète ; **fait aussi miroir** du registre public, donc les projets continuent d'installer quand le lien international tombe ; trace de qui installe quoi. | À exploiter : sauvegardes, TLS, comptes. Configuration fournie dans `hebergement/verdaccio.yaml`. |
| **Dépôt d'une forge interne** GitLab, Gitea, Forgejo | Au Burkina Faso | Déjà en place | Rien de plus à exploiter si la forge existe déjà ; comptes et droits communs avec le code. | Ne fait pas miroir du registre public : il faut alors un second dépôt pour les dépendances tierces. |
| **Aucun dépôt** archive ou dépôt Git | Sans objet | Nul | Fonctionne immédiatement, sans rien décider ni installer. | Pas de liste de versions, pas de mise à jour signalée, pas de trace. Convient à un projet pilote, pas à une généralisation. |

> Le plus sûr reste de retenir les deux, et dans cet ordre : **un dépôt souverain** comme source de référence pour les services de l'administration, **le registre public** en second pour les prestataires et les collectivités qui n'auront pas accès au premier. La chaîne de publication envoie le paquet aux deux depuis la même étiquette, ce qui écarte le risque de les voir diverger.

#### 6. Ce qu'écrit le projet qui installe

Une chose est à comprendre avant tout le reste. La déclaration se fait **par espace de noms**. Seuls les paquets `@govbf/…` sont demandés au dépôt désigné ; tout le reste continue d'aller là où il allait. On ne bascule pas un projet entier pour installer la charte.

##### Aujourd'hui : le serveur de la charte

```bash
npm install https://chartegraphique-21.mtdpce-test.gov.bf/npm/govbf-charte-graphique-2.0.0.tgz
```

Rien à déclarer : l'adresse complète de l'archive figure dans le `package.json` du projet.

##### Registre public

```bash
npm install @govbf/charte-graphique
```

Rien à déclarer : c'est la destination par défaut.

##### Dépôt souverain, ou toute autre installation de Verdaccio

```bash
# .npmrc, à la racine du projet, versionné avec lui
@govbf:registry=https://<adresse du dépôt retenu>/
```

Si le dépôt exige un compte pour lire, on ajoute la ligne d'authentification — jamais dans le fichier versionné, mais par une variable d'environnement que `npm` substitue :

```
//<adresse du dépôt retenu>/:_authToken=${JETON_DEPOT}
```

##### Dépôt de la forge

```bash
# .npmrc
@govbf:registry=https://npm.pkg.github.com
//npm.pkg.github.com/:_authToken=${JETON_FORGE}
```

> **Un jeton reste nécessaire, même pour un paquet public**
>
> C'est la différence la plus souvent découverte trop tard. Un paquet hébergé sur le dépôt d'une forge ne s'installe pas anonymement : tout poste, toute chaîne d'intégration, tout prestataire doit disposer d'un jeton de lecture. Pour un système destiné à être repris largement, c'est un obstacle sérieux, et la raison pour laquelle ce dépôt convient mieux en second qu'en premier.

##### Sans accès au serveur

```bash
# depuis une archive téléchargée une fois et rangée dans le projet
npm install ./vendor/govbf-charte-graphique-2.0.0.tgz
```

Toujours une version figée : une archive qui changerait sous les pieds du projet le ferait sans que rien ne le signale.

#### 7. Monter le dépôt souverain

`hebergement/verdaccio.yaml` est prêt à l'emploi. Il déclare deux choses qui comptent : les paquets `@govbf/*` n'ont **aucun accès amont**, et le reste est relayé vers le registre public avec mise en cache.

| Réglage | Pourquoi il est écrit ainsi |
| --- | --- |
| Pas de `proxy` sur `@govbf/*` | Une demande pour un paquet de l'État ne doit jamais partir vers le registre public, où un homonyme pourrait répondre à sa place. Sans cette précaution, un paquet déposé par un tiers sous le même nom serait installé à la place du vrai. |
| `cache: true` sur l'amont | Une dépendance déjà demandée une fois reste installable quand le lien international est lent ou coupé. C'est ce qui transforme le dépôt en filet de sécurité pour tous les projets du pays. |
| `max_fails` et `fail_timeout` | Quand l'amont ne répond plus, le dépôt cesse de l'interroger et sert ce qu'il a, au lieu de bloquer chaque installation sur un délai d'attente. |
| `max_users: -1` | Aucune inscription libre : les comptes sont créés par l'administrateur du dépôt. |

```bash
# sur le serveur
npm install -g verdaccio
htpasswd -B -c /etc/verdaccio/htpasswd publication
verdaccio --config /etc/verdaccio/verdaccio.yaml

# derrière un nginx qui termine le TLS, il n'écoute que
# sur la boucle locale : 127.0.0.1:4873
```

Côté chaîne de publication, deux valeurs à déclarer une fois sur le dépôt de code : la variable `REGISTRE_INTERNE` avec l'adresse, et le secret `REGISTRE_INTERNE_TOKEN` avec le jeton du compte de publication. Les étapes qui n'ont pas leurs valeurs sont sautées. On peut n'en retenir qu'une, ou les trois, sans toucher au fichier.

#### Recette de publication

- `npm test` passe en local.
- `node outils/verifier.js --publication` passe : plus aucun champ à renseigner, conditions de réutilisation arrêtées.
- Le journal des versions de la page [Gouvernance](https://chartegraphique-21.mtdpce-test.gov.bf/gouvernance) décrit ce qui change.
- L'étiquette correspond à la version du `package.json`.
- Le paquet ne contient ni les essais, ni l'outillage, ni la documentation.
- Le paquet est parti sur chacun des dépôts retenus, et y porte la même empreinte.
- Après publication. Une installation depuis un projet vierge résout les chemins publics, et les polices se chargent depuis le domaine du service.

### Angular

Premier cas traité en détail, parce qu'il concentre le plus de difficultés : rendu côté serveur, encapsulation des styles, cycle de vie des composants, mode sans zone, typage strict des gabarits. React et Laravel suivent, avec leurs propres points durs.

#### 1. Déclarer les styles

Dans `angular.json`, section `build`. Les styles globaux échappent à l'encapsulation des composants, ce qui est exactement ce qu'on veut. Les classes doivent s'appliquer partout.

```json
"styles": [
  "node_modules/@govbf/charte-graphique/assets/css/tokens.css",
  "node_modules/@govbf/charte-graphique/assets/css/faso.css",
  "node_modules/@govbf/charte-graphique/assets/css/icones.css",
  "src/styles.css"
]
```

L'ordre compte. Les jetons d'abord, la feuille du projet en dernier, pour qu'une surcharge locale l'emporte.

#### 2. Amorcer les comportements

Une seule ligne, dans le composant racine. L'observation prend en charge tout ce que l'application rendra par la suite. Il n'y a plus rien à appeler après chaque rendu, et rien à défaire au démontage.

```css
import { Component, OnInit, PLATFORM_ID, inject } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import Faso from '@govbf/charte-graphique';

@Component({ selector: 'app-root', templateUrl: './app.html' })
export class AppComponent implements OnInit {
  private plateforme = inject(PLATFORM_ID);

  ngOnInit(): void {
    if (isPlatformBrowser(this.plateforme)) {
      Faso.observer();
    }
  }
}
```

> Le test de plateforme n'est pas indispensable — `observer()` se contente de renvoyer `false` hors navigateur, mais il rend l'intention explicite et évite un appel inutile au rendu serveur.

##### Refuser le démarrage automatique

Chargée telle quelle, la bibliothèque s'initialise seule au chargement du document. Une page servie sans outillage peut ainsi fonctionner sans une ligne de script. Une application qui maîtrise son cycle de vie préfère décider du moment, en particulier avec l'hydratation, où Angular reprend un balisage rendu par le serveur et où l'on ne veut personne d'autre dans le document avant lui.

```
// index.html, avant le chargement du paquet
<script>window.FASO_SANS_DEMARRAGE = true;</script>

// ou, si la bibliothèque est déclarée par une balise :
<script src="/assets/js/faso.js" data-sans-demarrage defer></script>
```

La charte ne touche alors à rien tant que `Faso.initialiser()` ou `Faso.observer()` n'a pas été appelé. Avec l'hydratation incrémentale, appeler `observer()` une fois suffit : les sous-arbres hydratés plus tard sont pris à leur arrivée.

##### Au démontage

Rien, et ce n'est pas une facilité de langage : c'est une propriété de la construction, que voici en détail.

- Les gestionnaires posés sur un élément disparaîssent avec lui.
- Les gestionnaires posés sur `document` le sont une fois pour la page, jamais par instance : rien ne retient un nœud détruit.
- Les veilles posées sur un élément ne sont référencées nulle part ailleurs : elles forment avec lui un cycle isolé, que le ramasse-miettes collecte.
- Le verrou de défilement d'un panneau se lève de lui-même si le panneau est détruit sans avoir été fermé.
- Le registre d'initialisation est faible : un élément retiré en sort seul.

La seule chose qui se défait explicitement est l'observation globale, par `Faso.observer(false)` ; elle n'est utile que pour rendre la main au projet, non pour libérer de la mémoire.

##### Variante sans observation

Si l'on préfère maîtriser le moment de l'initialisation, on la déclenche composant par composant. L'opération est idempotente : la rappeler n'empile pas les gestionnaires.

```css
export class FicheDemarche implements AfterViewInit {
  private hote = inject(ElementRef<HTMLElement>);

  ngAfterViewInit(): void {
    Faso.initialiser(this.hote.nativeElement);
  }
}
```

#### 3. Rendu côté serveur

Le module s'importe sans DOM. Rien ne s'exécute au chargement lorsque `document` est absent, et l'API reste appelable sans effet. Un `import` en tête de fichier ne fait donc pas échouer le rendu serveur.

> **Thème et hydratation**
>
> Le thème est lu dans le stockage local, donc côté navigateur uniquement. Le serveur rend toujours le thème clair. Pour éviter que la page n'apparaisse claire puis bascule, posez l'attribut `data-theme` sur `<html>` depuis un script en tête de document, avant le rendu de l'application.

#### 4. Encapsulation des styles

**Compatible**

- `ViewEncapsulation.Emulated`, le mode par défaut. Les styles globaux s'appliquent normalement.
- `ViewEncapsulation.None` : compatible, mais la feuille du composant fuit dans toute l'application.
- Une surcharge locale passe par une redéfinition de jeton sur `:host`, jamais par une réécriture de règle.

**Incompatible**

- `ViewEncapsulation.ShadowDom` : les styles globaux ne franchissent pas la frontière, et les composants apparaissent sans mise en forme.
- `::ng-deep` pour forcer un style de la charte. La règle est dépréciée et la surcharge survit mal aux montées de version.

```css
/* Surcharge correcte : on redéfinit un jeton */
:host {
  --action: var(--vert-700);
}
```

#### 5. Politique de sécurité de contenu

Les 35 icônes et l'étoile du filet sont des URI `data:` employées en masque CSS. Une politique stricte qui ne les autorise pas les fait disparaître **sans aucune erreur visible** : les boutons restent, les icônes s'évaporent. C'est le défaut le plus difficile à diagnostiquer de cette intégration, et il ne se manifeste souvent qu'en production.

```
Content-Security-Policy:
  default-src 'self';
  img-src 'self' data:;
  font-src 'self';
  style-src 'self';
```

Aucune directive `'unsafe-inline'` n'est nécessaire. La charte ne pose aucun style en ligne ni aucun script en ligne.

#### 6. Détection de changement

Avec `zone.js`, les gestionnaires délégués de la charte déclenchent un cycle de détection à chaque clic dans la page. C'est correct, mais mesurable sur une grande application. Pour l'éviter, amorcez en dehors de la zone — les comportements de la charte ne modifient aucun état Angular.

```js
private zone = inject(NgZone);

ngOnInit(): void {
  this.zone.runOutsideAngular(() => Faso.observer());
}
```

En mode sans zone, la question ne se pose pas. La charte écrit directement dans le document et n'attend aucune détection.

#### 7. Ce qu'Angular doit reprendre à son compte

Les composants de la troisième catégorie. On garde le balisage et les classes, on retire les attributs de pilotage de la charte, et le cadriciel fait le travail.

| Attribut à retirer | Ce qu'Angular reprend |
| --- | --- |
| `data-pagination` | Découpage des lignes, commandes de page |
| `data-filtres`, `data-filtre` | Filtrage de la source de données |
| `data-triable` | Tri de la source, et `aria-sort` sur les en-têtes |
| `data-combo` | Filtrage des suggestions, `aria-activedescendant` |
| `data-export` | Génération du fichier, côté client ou serveur |

> Les classes, elles, restent : `.fs-tableau`, `.fs-tableau-bloc`, `.fs-pagination`, `.fs-filtres`, `.fs-combo-liste`. L'apparence et les règles d'accessibilité sont acquises ; seule la logique change de camp.

#### 8. TypeScript

Le paquet embarque ses déclarations. En mode strict, l'import par défaut est typé, et `window.Faso` est déclaré globalement pour les projets qui préfèrent y accéder directement.

```js
import Faso from '@govbf/charte-graphique';

Faso.initialiser(element);
Faso.notifier({ titre: 'Dossier transmis', ton: 'succes' });
Faso.appliquerTheme('dark');
```

### React

Second cas traité en détail. Les difficultés ne sont pas les mêmes qu'avec Angular. Le rendu est déclaratif et le DOM entièrement possédé par la bibliothèque, ce qui simplifie certaines choses et en complique deux autres, les champs contrôlés et l'écoute des annonces.

#### 1. Déclarer les feuilles

```js
// main.tsx, avant le rendu
import '@govbf/charte-graphique/css/tokens.css';
import '@govbf/charte-graphique/css/faso.css';
import '@govbf/charte-graphique/css/icones.css';
```

L'ordre compte. Les jetons d'abord, la feuille du projet en dernier. Aucun module CSS, aucune portée — les classes de la charte sont globales par construction, et c'est ce qui permet de les reprendre telles quelles dans un gabarit Blade ou un courriel.

#### 2. Amorcer les comportements

Un effet unique, au composant racine. L'observation prend ensuite en charge tout ce que React rendra.

```js
import { useEffect } from 'react';
import Faso from '@govbf/charte-graphique';

export default function App() {
  useEffect(() => {
    Faso.observer();
    return () => Faso.observer(false);
  }, []);

  return <Routes />;
}
```

> Le mode strict ne demande rien de particulier. En développement, React monte, démonte et remonte : l'effet s'exécute deux fois. `observer()` est idempotent et `observer(false)` rend proprement la main ; aucun gestionnaire ne s'empile.

#### 3. Champs contrôlés

C'est le point où React diffère de tous les autres, et il a demandé une correction dans la charte elle-même.

> **Pourquoi écrire dans un champ ne suffit pas**
>
> React ne relit pas la valeur du champ. Il installe sur le nœud son propre accesseur, qui mémorise chaque affectation. Un script qui écrit par cet accesseur fait bouger la valeur mémorisée en même temps que celle du champ ; à la réception de l'événement, React ne constate aucun écart et `onChange` n'est jamais appelé. L'usager lit la nouvelle valeur à l'écran, le formulaire part avec l'ancienne.

La charte écrit donc par l'accesseur du prototype, que React n'a pas remplacé : le champ change, la valeur mémorisée non, et l'événement qui suit est reconnu comme une saisie véritable. Cela concerne les quatre endroits où le script écrit : date collée, code collé, suggestion retenue, filtre vidé.

```js
// Un champ contrôlé ordinaire : rien de particulier à écrire.
const [ville, setVille] = useState('');

<div className="fs-champ fs-combo" data-combo>
  <label className="fs-label" htmlFor="ville">Commune de naissance</label>
  <input className="fs-saisie" id="ville" value={ville}
         aria-describedby="ville-nb" aria-controls="ville-liste"
         onChange={(e) => setVille(e.target.value)} />
  <p className="fs-invisible" id="ville-nb" role="status" aria-live="polite" />
  <ul className="fs-combo-liste" id="ville-liste" hidden>
    {communes.map((c) => (
      <li className="fs-combo-option" key={c}>{c}</li>
    ))}
  </ul>
</div>
```

Retenir une suggestion appelle bien `setVille`. La liste des options peut changer entre deux rendus : la charte la relit.

#### 4. Écouter les annonces

React ne relaie pas les événements personnalisés vers une propriété `onQuelqueChose` sur un élément natif. La forme à trait d'union, qui sert à Angular, n'y change rien : il faut une référence et un écouteur. Un crochet d'une dizaine de lignes suffit, et se réutilise.

```js
import { useEffect, useRef } from 'react';

export function useAnnonce<T>(
  nom: string,
  gerer: (detail: T, ev: CustomEvent<T>) => void,
) {
  const hote = useRef<HTMLElement>(null);
  const dernier = useRef(gerer);
  dernier.current = gerer;

  useEffect(() => {
    const n = hote.current;
    if (!n) return;
    const relais = (ev: Event) => {
      const a = ev as CustomEvent<T>;
      dernier.current(a.detail, a);
    };
    n.addEventListener(nom, relais);
    return () => n.removeEventListener(nom, relais);
  }, [nom]);

  return hote;
}
```

```js
// L'onglet actif appartient au composant, pas à la charte.
type Onglet = { onglet: HTMLElement; index: number };

function Onglets() {
  const [actif, setActif] = useState('suivi');

  const liste = useAnnonce<Onglet>('fs:onglet', (detail, ev) => {
    ev.preventDefault();                 // la charte n'écrira pas
    setActif(detail.onglet.dataset.cle ?? actif);
  });

  return (
    <div>
      <div className="fs-onglets" role="tablist" ref={liste}
           aria-label="Informations sur la démarche">
        {cles.map((c) => (
          <button className="fs-onglet" key={c} role="tab" data-cle={c}
                  id={`onglet-${c}`} aria-controls={`panneau-${c}`}
                  aria-selected={actif === c}>{libelles[c]}</button>
        ))}
      </div>
      {cles.map((c) => (
        <div className="fs-panneau" key={c} id={`panneau-${c}`} role="tabpanel"
             aria-labelledby={`onglet-${c}`} tabIndex={0} hidden={actif !== c}>
          {contenus[c]}
        </div>
      ))}
    </div>
  );
}
```

Sans `preventDefault()`, la charte écrirait `hidden` et `aria-selected` de son côté, React les réécrirait au rendu suivant, et les deux se disputeraient. L'annuler règle le conflit en une ligne : la charte continue d'assurer le clavier, le focus et l'ordre de tabulation, que JSX ne sait pas exprimer.

#### 5. Initialiser un sous-arbre précis

Si l'on préfère maîtriser le moment plutôt qu'observer en continu.

```js
function FicheDemarche({ demarche }) {
  const hote = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (hote.current) Faso.initialiser(hote.current);
  }, [demarche.id]);          // rejoué quand la fiche change

  return (
    <div ref={hote}>
      <div className="fs-accordeon">…</div>
      <div className="fs-jauge" data-valeur={demarche.avancement}>
        <span className="fs-jauge-valeur" />
      </div>
    </div>
  );
}
```

L'opération est idempotente. Un élément déjà initialisé est ignoré, et seuls les nouveaux sont équipés. Ici la jauge suivrait de toute façon `data-valeur` sans rien demander ; l'appel sert aux composants qui n'ont pas cette veille.

> Un balisage posé par `dangerouslySetInnerHTML` échappe au rendu de React : il faut appeler `Faso.initialiser()` sur le conteneur après chaque injection, ou laisser l'observation s'en charger.

#### 6. Next.js et le rendu côté serveur

| Point | Ce qu'il faut faire |
| --- | --- |
| Import du module | Sans effet hors navigateur. Aucune directive `'use client'` n'est nécessaire pour importer. |
| Appel de `observer()` | Dans un composant client, à l'intérieur d'un effet. |
| Thème sombre | Déclarer `faso-amorce.js` dans le `<head>` du gabarit racine, avant les feuilles. |
| Hydratation | Poser `window.FASO_SANS_DEMARRAGE = true` si le paquet est chargé par une balise. La charte attendra votre appel. |
| Politique de sécurité | `img-src 'self' data:` : les icônes sont des masques CSS en `data:`. Aucun `'unsafe-inline'` n'est requis. |

```js
// app/layout.tsx
export default function RootLayout({ children }) {
  return (
    <html lang="fr">
      <head>
        <script src="/faso-amorce.js" />
      </head>
      <body>{children}</body>
    </html>
  );
}
```

#### Avec React, en résumé

**Sain**

- Reprendre les classes dans le JSX et laisser React rendre. C'est l'usage le plus courant, et il ne demande rien.
- Laisser la charte tenir un état qu'aucune liaison ne surveille : ouverture d'un menu, d'une modale, d'un panneau.
- Annuler l'annonce dès que l'état figure aussi dans un `useState`.
- Donner une `key` stable à chaque élément d'une liste : React réutilise alors les nœuds, et la charte n'a rien à rejouer.

**À éviter**

- Refléter dans un `useState` un attribut que la charte écrit, sans annuler l'annonce correspondante.
- Poser `data-pagination` sur un tableau dont React ne rend qu'une page.
- Injecter le balisage de la charte par `dangerouslySetInnerHTML` sans appeler `Faso.initialiser()` sur le conteneur.
- Une `key` fondée sur l'index : React recrée alors les nœuds à chaque tri, et la pagination repart en tête.

### Laravel

Le cas le plus simple, et celui qui concerne le plus grand nombre de services de l'administration. Le balisage vient du serveur, personne ne le redessine ensuite : **la totalité de la charte est utilisable, sans réserve**. Les deux points qui demandent attention arrivent avec Livewire et avec Inertia.

#### 1. Installer, avec ou sans Node

##### Avec Vite, qui est le défaut

```bash
npm install https://chartegraphique-21.mtdpce-test.gov.bf/npm/govbf-charte-graphique-2.0.0.tgz
```

```js
// resources/js/app.js
import '@govbf/charte-graphique/css/tokens.css';
import '@govbf/charte-graphique/css/faso.css';
import '@govbf/charte-graphique/css/icones.css';
import '@govbf/charte-graphique';   // les comportements s'amorcent seuls
```

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/js/app.js'])
</head>
<body>
  @yield('contenu')
</body>
</html>
```

##### Sans chaîne Node

Un service hébergé sur un serveur mutualisé, sans `npm` : on copie les fichiers et on les sert. C'est le mode d'origine du système, et il reste pleinement pris en charge.

```bash
# une fois, sans Node
curl -O https://chartegraphique-21.mtdpce-test.gov.bf/npm/govbf-charte-graphique-2.0.0.tgz
tar -xzf govbf-charte-graphique-2.0.0.tgz
cp -r package/assets public/charte
```

```blade
<script src="{{ asset('charte/js/faso-amorce.js') }}"></script>
<link rel="stylesheet" href="{{ asset('charte/css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('charte/css/faso.css') }}">
<link rel="stylesheet" href="{{ asset('charte/css/icones.css') }}">
<script src="{{ asset('charte/js/faso.js') }}" defer></script>
```

> L'arborescence `css/` et `polices/` doit être conservée telle quelle : les déclarations de polices désignent les fichiers par un chemin relatif. Déplacer l'un sans l'autre fait basculer silencieusement le texte en police de repli.

#### 2. Un formulaire Blade

Le seul travail réel est de brancher la validation de Laravel sur les états que la charte dessine. Trois choses : l'attribut `data-etat` sur le champ, `aria-invalid` et `aria-describedby` sur la saisie, le message dans un `.fs-message--erreur` qui porte l'identifiant désigné.

```blade
{{-- resources/views/composants/champ.blade.php --}}
@props(['nom', 'libelle', 'type' => 'text', 'aide' => null])

@php
  $erreur = $errors->first($nom);
  $idAide = $nom . '-aide';
  $idErreur = $nom . '-erreur';
  $decrit = collect([$aide ? $idAide : null, $erreur ? $idErreur : null])
              ->filter()->implode(' ');
@endphp

<div class="fs-champ" @if($erreur) data-etat="erreur" @endif>
  <label class="fs-label" for="{{ $nom }}">{{ $libelle }}</label>

  @if($aide)
    <p class="fs-aide" id="{{ $idAide }}">{{ $aide }}</p>
  @endif

  <input class="fs-saisie" type="{{ $type }}" id="{{ $nom }}" name="{{ $nom }}"
         value="{{ old($nom) }}"
         @if($erreur) aria-invalid="true" @endif
         @if($decrit) aria-describedby="{{ $decrit }}" @endif>

  @if($erreur)
    <p class="fs-message fs-message--erreur" id="{{ $idErreur }}">{{ $erreur }}</p>
  @endif
</div>
```

```blade
<form method="post" action="{{ route('demande.store') }}" class="fs-pile fs-pile--large">
  @csrf
  <x-champ nom="nom" libelle="Nom de famille" />
  <x-champ nom="courriel" libelle="Adresse électronique" type="email"
           aide="Nous y enverrons le récépissé." />
  <button class="fs-btn fs-btn--principal" type="submit">Déposer la demande</button>
</form>
```

> **Ce composant appartient au projet, pas à la charte.** La charte ne publie pas de composants Blade, pour la raison expliquée plus bas. Elle livre des classes et un contrat de balisage, que chaque projet enveloppe dans les conventions de son cadriciel. Celui-ci est donné comme point de départ, à copier et à adapter.

#### 3. Tableaux paginés par le serveur

Laravel pagine côté serveur. C'est le cas où **il ne faut pas poser `data-pagination`**, puisque la page ne contient qu'une tranche des lignes. On reprend en revanche le dessin de la pagination, en publiant la vue du paginateur.

```bash
php artisan vendor:publish --tag=laravel-pagination
```

```blade
{{-- resources/views/vendor/pagination/charte.blade.php --}}
@if ($paginator->hasPages())
  <nav class="fs-pagination" aria-label="Pagination des résultats">
    @if ($paginator->onFirstPage())
      <span aria-hidden="true">‹</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" aria-label="Page précédente">‹</a>
    @endif

    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="fs-ellipse" aria-hidden="true">{{ $element }}</span>
      @endif
      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <a href="{{ $url }}" aria-current="page" aria-label="Page {{ $page }}">{{ $page }}</a>
          @else
            <a href="{{ $url }}" aria-label="Page {{ $page }}">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" aria-label="Page suivante">›</a>
    @else
      <span aria-hidden="true">›</span>
    @endif
  </nav>
@endif
```

```blade
{{ $demandes->links('vendor.pagination.charte') }}
```

> L'extraction suit la même règle. Le bouton d'export de la charte lit le document : sur une page qui n'affiche que vingt lignes sur quatre mille, il produirait un fichier faux. Déclarer `data-export-total="{{ $demandes->total() }}"` fait refuser l'extraction et affiche un message explicite ; l'export complet se fait alors par une route qui diffuse le jeu entier.

#### 4. Livewire

C'est le seul endroit où Laravel rejoint les difficultés d'Angular et de React. Livewire ne recharge pas la page : il compare l'ancien balisage au nouveau et transforme le premier en second. Les attributs que la charte a posés après le rendu ne figurent pas dans le balisage attendu, et peuvent donc être effacés.

| Situation | Ce qu'il faut faire |
| --- | --- |
| Un composant que la charte anime, et que Livewire ne doit pas retoucher : modale, menu, onglets | Poser `wire:ignore` sur le conteneur. Livewire le laisse tel quel à chaque rafraîchissement. |
| Un conteneur dont Livewire rend le contenu mais pas l'enveloppe | `wire:ignore.self` : l'enveloppe et ses attributs sont préservés, l'intérieur est mis à jour. |
| Une liste rendue par `@foreach` | Une `wire:key` stable sur chaque élément : Livewire réutilise alors les nœuds au lieu de les recréer. |
| Un tableau dont Livewire pagine les lignes | Pas de `data-pagination` ni de `data-filtres` : le tri, le filtre et l'export reviennent au composant. |
| Un champ lié par `wire:model.live` | Rien à faire : la charte émet `input` et `change` quand elle écrit une valeur. |

##### Réinitialiser après une transformation

Si un composant rend un balisage neuf, les comportements doivent être posés sur les nouveaux nœuds. Deux voies, au choix.

```js
// resources/js/app.js — la voie simple, une fois pour toutes
import Faso from '@govbf/charte-graphique';
Faso.observer();
```

```js
// ou la voie explicite, si l'on préfère ne rien observer
document.addEventListener('livewire:init', () => {
  Livewire.hook('morphed', ({ el }) => Faso.initialiser(el));
});
```

L'initialisation étant idempotente, la seconde voie ne risque pas d'empiler les gestionnaires, même si le composant se rafraîchit à chaque frappe.

##### Alpine, qui accompagne souvent Livewire

Les deux cohabitent sans heurt tant qu'ils n'écrivent pas le même attribut. Si un `x-data` pilote l'ouverture d'un panneau ou l'onglet actif, on annule l'annonce de la charte — la forme à trait d'union se lie directement dans une directive Alpine.

```html
<div role="tablist" x-data="{ actif: 'suivi' }"
     @fs-onglet="$event.preventDefault(); actif = $event.detail.onglet.dataset.cle">
  <button role="tab" data-cle="suivi"
          :aria-selected="actif === 'suivi'">Suivi</button>
</div>
```

#### 5. Inertia

Inertia rend des composants React ou Vue dans un squelette Laravel : les sections correspondantes de cette page s'appliquent telles quelles. Une seule précision : une visite Inertia remplace la page sans rechargement, si bien que l'amorçage doit avoir lieu une fois dans le point d'entrée, et non dans un gabarit Blade rendu à chaque requête.

```js
// resources/js/app.jsx
import Faso from '@govbf/charte-graphique';

createInertiaApp({
  setup({ el, App, props }) {
    Faso.observer();                  // une fois, pour toute la session
    createRoot(el).render(<App {...props} />);
  },
});
```

#### 6. Politique de sécurité du contenu

La charte ne pose aucun style ni script en ligne : elle n'exige pas `'unsafe-inline'`. Une seule autorisation lui est nécessaire, pour les icônes, qui sont des masques CSS en `data:`.

```css
// app/Http/Middleware/SecuriteContenu.php
public function handle(Request $requete, Closure $suivant): Response
{
    $reponse = $suivant($requete);

    $reponse->headers->set('Content-Security-Policy', implode('; ', [
        "default-src 'self'",
        "img-src 'self' data:",        // masques d'icônes et emblème
        "font-src 'self'",             // polices servies par le domaine
        "style-src 'self'",
        "script-src 'self'",
        "frame-ancestors 'none'",
    ]));

    return $reponse;
}
```

Si les icônes n'apparaissent pas, c'est presque toujours `img-src data:` qui manque.

#### Avec Laravel, en résumé

**Sain**

- Blade seul. Tout est utilisable, y compris le tri, les filtres et l'export, tant que la vue rend l'intégralité des lignes.
- Brancher la validation de Laravel sur `data-etat`, `aria-invalid` et `aria-describedby`.
- Reprendre le dessin du paginateur plutôt que sa mécanique quand le serveur pagine.
- Avec Livewire, `wire:ignore` sur ce que la charte anime et `wire:key` sur ce qui se répète.

**À éviter**

- Poser `data-pagination` sur un tableau que le paginateur d'Eloquent découpe déjà.
- Exporter depuis le document une page qui ne montre qu'une tranche, sans déclarer `data-export-total`.
- Laisser Livewire transformer une modale ouverte. Elle se refermera au rafraîchissement suivant.
- Recopier les fichiers dans `public/` en séparant `css/` de `polices/`.

### Android, en Kotlin et en Java

Ici le partage des responsabilités change de nature. Le CSS et le JavaScript de la charte ne s'exécutent pas dans une application native. Ce qui se transporte, ce ne sont pas les composants, ce sont **les décisions** — couleurs officielles, échelle d'espacement, rayons, typographie, cible tactile, emblème et marques nationales. Elles sont produites depuis la même feuille de jetons, dans les formats qu'Android sait lire.

#### Ce qui se transporte, et ce qui ne se transporte pas

| Élément | Sur Android |
| --- | --- |
| Couleurs, thème sombre compris | Fourni `values/couleurs.xml`, `values-night/`, `FasoCouleurs.kt` |
| Espacements, rayons, épaisseurs, tailles de texte | Fourni `values/dimensions.xml` et `values-sw600dp/` |
| Thème Material 3 et styles de texte | Fourni `Theme.Faso`, `FasoTheme` en Compose |
| Les 35 icônes | Fourni `ic_faso_*.xml`, teintées par l'attribut de thème |
| Emblème, filet et repère nationaux | Fourni `faso_armoiries.xml`, `faso_filet.xml`, `faso_repere.xml` |
| Polices Archivo et Inter | À télécharger Le paquet ne contient que des `woff2`, illisibles hors navigateur. Les `ttf` viennent de la même source, sous la même licence SIL OFL. |
| Composants et comportements | Sans objet Material fournit les siens ; la charte leur donne l'apparence officielle par le thème. |
| La charte entière, telle quelle | En WebView Voir plus bas. C'est le cas d'un service web consulté depuis l'application. |

#### 1. Installer les ressources

Il n'y a pas d'artefact Maven. Les fichiers se copient, une fois, et se régénèrent quand la charte change de version. Le contenu du dossier `android/` se range comme suit.

```
android/values/couleurs.xml         →  app/src/main/res/values/
android/values-night/couleurs.xml  →  app/src/main/res/values-night/
android/values/dimensions.xml      →  app/src/main/res/values/
android/values-sw600dp/…           →  app/src/main/res/values-sw600dp/
android/values/themes.xml          →  app/src/main/res/values/
android/drawable/*.xml             →  app/src/main/res/drawable/
android/kotlin/*.kt                →  app/src/main/java/bf/gouv/charte/
```

> Ces fichiers sont produits, jamais écrits à la main. `node outils/jetons.js` a son équivalent ici : `node outils/android.js` relit `tokens.css` et réécrit le dossier, `--verifier` signale toute dérive. Retoucher une couleur dans `couleurs.xml` serait perdu à la génération suivante, et surtout, la teinte du drapeau ne se retouche pas.

##### Les polices

```
// app/src/main/res/font/archivo.xml  — après avoir place les .ttf
<font-family xmlns:android="http://schemas.android.com/apk/res/android">
  <font android:fontStyle="normal" android:fontWeight="400"
        android:font="@font/archivo_regular" />
  <font android:fontStyle="normal" android:fontWeight="700"
        android:font="@font/archivo_bold" />
</font-family>
```

Les lignes `fontFamily` sont déjà présentes dans `themes.xml`, en commentaire. Il suffit de les rétablir. Sans polices, Android emploie sa pile par défaut et la charte reste lisible ; l'identité typographique en souffre, la lisibilité non.

#### 2. Application native — Jetpack Compose

```kotlin
// MainActivity.kt
import bf.gouv.charte.FasoTheme
import bf.gouv.charte.cibleMinimale

class MainActivity : ComponentActivity() {
    override fun onCreate(etat: Bundle?) {
        super.onCreate(etat)
        setContent {
            FasoTheme {                       // clair ou sombre selon le système
                FicheDemarche()
            }
        }
    }
}
```

```kotlin
@Composable
fun FicheDemarche() {
    Column(modifier = Modifier.padding(16.dp)) {

        // Le filet national, en tête de page comme sur le web.
        Image(
            painter = painterResource(R.drawable.faso_filet),
            contentDescription = null,        // décoratif : pas de description
            contentScale = ContentScale.FillWidth,
            modifier = Modifier.fillMaxWidth().height(14.dp),
        )

        Spacer(Modifier.height(24.dp))

        Text("Acte de naissance", style = MaterialTheme.typography.headlineMedium)

        Button(
            onClick = { deposer() },
            modifier = Modifier.defaultMinSize(minHeight = cibleMinimale),
        ) {
            Icon(painterResource(R.drawable.ic_faso_coche), contentDescription = null)
            Spacer(Modifier.width(8.dp))
            Text("Déposer la demande")
        }
    }
}
```

`cibleMinimale` vaut 48 dp, et non les 44 px du web : Android impose un plancher plus haut, c'est le plus strict des deux qui s'applique.

#### 3. Application native — vues XML

Le thème se pose dans le manifeste, et les styles suivent. Rien ne distingue Kotlin de Java à ce niveau : tout est déclaratif.

```html
<!-- AndroidManifest.xml -->
<application android:theme="@style/Theme.Faso" …>
```

```html
<!-- res/layout/fiche_demarche.xml -->
<LinearLayout
    android:orientation="vertical"
    android:padding="@dimen/faso_espace_4">

  <ImageView
      android:layout_width="match_parent"
      android:layout_height="14dp"
      android:scaleType="fitXY"
      android:src="@drawable/faso_filet"
      android:importantForAccessibility="no" />

  <TextView
      android:layout_marginTop="@dimen/faso_espace_6"
      android:textAppearance="@style/TextAppearance.Faso.H2"
      android:text="@string/acte_de_naissance" />

  <com.google.android.material.button.MaterialButton
      android:id="@+id/deposer"
      style="@style/Widget.Faso.Bouton"
      android:layout_marginTop="@dimen/faso_espace_6"
      app:icon="@drawable/ic_faso_coche"
      android:text="@string/deposer_la_demande" />
</LinearLayout>
```

**Kotlin**

```kotlin
class FicheActivity : AppCompatActivity() {
    override fun onCreate(etat: Bundle?) {
        super.onCreate(etat)
        setContentView(R.layout.fiche_demarche)

        findViewById<MaterialButton>(R.id.deposer).setOnClickListener {
            deposer()
        }
    }
}
```

**Java**

```
public class FicheActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle etat) {
        super.onCreate(etat);
        setContentView(R.layout.fiche_demarche);

        MaterialButton deposer = findViewById(R.id.deposer);
        deposer.setOnClickListener(v -> deposer());
    }
}
```

#### 4. WebView — la charte s'exécute telle quelle

C'est le cas d'une application qui donne accès à un téléservice déjà construit avec la charte. Tout fonctionne, à quatre réglages près, dont **trois touchent à l'accessibilité et un à l'identité nationale**.

> **L'assombrissement automatique altère les couleurs du drapeau**
>
> Android sait assombrir une page qui n'a pas de thème sombre, en recalculant ses couleurs. Appliqué à la charte, ce procédé déplace le rouge, le vert et l'or officiels. Il faut le refuser explicitement et poser le thème sombre de la charte, qui existe et qui conserve les teintes à leur palier.

##### Kotlin

```kotlin
import androidx.webkit.WebSettingsCompat
import androidx.webkit.WebViewFeature

private fun preparer(vue: WebView) {
    val reglages = vue.settings

    reglages.javaScriptEnabled = true
    reglages.domStorageEnabled = true        // le thème est mémorisé là

    // Accessibilité : le zoom ne se refuse pas.
    reglages.setSupportZoom(true)
    reglages.builtInZoomControls = true
    reglages.displayZoomControls = false

    // Une WebView ignore la taille de texte choisie par l'usager :
    // on la lui transmet. Sans cette ligne, un réglage à 200 % dans
    // les paramètres du téléphone n'a aucun effet sur la page.
    reglages.textZoom = (resources.configuration.fontScale * 100).toInt()

    // On refuse l'assombrissement automatique : la charte a son thème.
    if (WebViewFeature.isFeatureSupported(WebViewFeature.ALGORITHMIC_DARKENING)) {
        WebSettingsCompat.setAlgorithmicDarkeningAllowed(reglages, false)
    }
}
```

```kotlin
// Le thème suit celui du système, en passant par l'API de la charte.
private fun accorderLeTheme(vue: WebView) {
    val nuit = resources.configuration.uiMode and
        Configuration.UI_MODE_NIGHT_MASK == Configuration.UI_MODE_NIGHT_YES

    vue.evaluateJavascript(
        "window.Faso && Faso.appliquerTheme(" +
            (if (nuit) "'dark'" else "'light'") + ")",
        null,
    )
}

vue.webViewClient = object : WebViewClient() {
    override fun onPageFinished(vue: WebView, url: String) = accorderLeTheme(vue)
}
```

##### Java

```java
private void preparer(WebView vue) {
    WebSettings reglages = vue.getSettings();

    reglages.setJavaScriptEnabled(true);
    reglages.setDomStorageEnabled(true);

    reglages.setSupportZoom(true);
    reglages.setBuiltInZoomControls(true);
    reglages.setDisplayZoomControls(false);

    float echelle = getResources().getConfiguration().fontScale;
    reglages.setTextZoom((int) (echelle * 100));

    if (WebViewFeature.isFeatureSupported(WebViewFeature.ALGORITHMIC_DARKENING)) {
        WebSettingsCompat.setAlgorithmicDarkeningAllowed(reglages, false);
    }
}

private void accorderLeTheme(WebView vue) {
    int mode = getResources().getConfiguration().uiMode
        & Configuration.UI_MODE_NIGHT_MASK;
    boolean nuit = mode == Configuration.UI_MODE_NIGHT_YES;

    vue.evaluateJavascript(
        "window.Faso && Faso.appliquerTheme('" + (nuit ? "dark" : "light") + "')",
        null);
}
```

##### Servir la charte depuis les ressources de l'application

Pour un service consultable hors connexion, on embarque les fichiers. Il ne faut pas les charger en `file://` : le stockage local y est refusé, donc le thème ne se mémorise pas, et les polices peuvent être bloquées. `WebViewAssetLoader` les sert sous une origine `https` locale, ce qui règle les deux.

```kotlin
val chargeur = WebViewAssetLoader.Builder()
    .addPathHandler("/assets/", WebViewAssetLoader.AssetsPathHandler(this))
    .build()

vue.webViewClient = object : WebViewClient() {
    override fun shouldInterceptRequest(
        vue: WebView, requete: WebResourceRequest,
    ) = chargeur.shouldInterceptRequest(requete.url)

    override fun onPageFinished(vue: WebView, url: String) = accorderLeTheme(vue)
}

vue.loadUrl("https://appassets.androidplatform.net/assets/demarche.html")
```

##### Appeler la charte depuis l'application

```js
// Une notification passagère, déclenchée par le code natif.
vue.evaluateJavascript(
    "Faso.notifier({ titre: 'Dossier enregistré', ton: 'succes' })", null)
```

Dans l'autre sens, un pont `@JavascriptInterface` est possible, mais il expose des méthodes natives à la page : à réserver à un contenu servi par l'administration elle-même, jamais à une page distante.

#### 5. Onglet personnalisé

Quand le service n'a pas besoin d'être encadré par l'application, un onglet personnalisé vaut mieux qu'une WebView : il apporte la barre d'adresse, le gestionnaire de mots de passe et les réglages d'accessibilité du navigateur, sans que l'application ait rien à régler.

```kotlin
val couleurs = CustomTabColorSchemeParams.Builder()
    .setToolbarColor(ContextCompat.getColor(this, R.color.faso_action))
    .build()

CustomTabsIntent.Builder()
    .setDefaultColorSchemeParams(couleurs)
    .build()
    .launchUrl(this, Uri.parse("https://demarches.gov.bf/acte-de-naissance"))
```

#### Deux réserves, dites franchement

- **L'emblème pèse 191 Ko en tracé vectoriel.** C'est le prix de ses onze chemins, fidèles à l'original. Il convient à un écran de démarrage ou à un en-tête, où il est dessiné une fois. Dans une liste qui défile, ou en petite taille, il faut lui préférer une image matricielle exportée aux densités voulues : le rendu est identique et le coût d'affichage sans commune mesure.
- **Aucune application de référence n'est fournie.** Les ressources sont produites et vérifiées (XML bien formé, tracés n'employant que des commandes reconnues par Android), mais elles n'ont pas été compilées dans un projet Gradle ni affichées sur un appareil. C'est la première chose à faire avant un déploiement.

#### Recette Android

- Le thème `Theme.Faso` est déclaré dans le manifeste.
- Le mode sombre du système bascule bien les couleurs, sans altérer le rouge, le vert ni l'or.
- Toute commande atteint 48 dp dans les deux dimensions.
- Un réglage de taille de texte à 200 % dans les paramètres du téléphone agrandit le contenu, y compris dans une WebView.
- Les images décoratives portent `importantForAccessibility="no"`, les autres une description.
- TalkBack annonce chaque commande et chaque champ.
- `node outils/android.js --verifier` passe.

### Bilan de sûreté, composant par composant

Relevé de ce que chaque comportement écrit dans le document, et de ce qui peut entrer en conflit avec un cadriciel. Ce bilan est établi par lecture du code, et révisé à chaque version.

#### Les garde-fous de la bibliothèque

Dix mécanismes, pour que les pièges connus ne reposent plus sur la vigilance du développeur.

| Mécanisme | Ce qu'il évite |
| --- | --- |
| **Résolution sans ambiguïté** | Quand l'identifiant ne désigne qu'un élément, le cas ordinaire, le résultat est exactement celui de `getElementById`. Ce n'est qu'en cas de doublon que la recherche remonte depuis le déclencheur : deux lignes d'une liste portant la même modale ne se confondent pas. |
| **Reprise d'état par le projet** | Chaque écriture d'état est précédée d'une annonce annulable. `preventDefault()` et la charte s'abstient d'écrire, tout en continuant d'assurer le clavier, le focus et l'ordre de tabulation. Un attribut lié par le cadriciel n'est plus disputé. |
| **Signalement des écritures** | Quand le script écrit dans un champ, il émet `input` et `change`. Une liaison bidirectionnelle reste à jour. Un drapeau empêche nos propres écouteurs de se réveiller. |
| **Écriture par l'accesseur du prototype** | React ne relit pas le champ. Il compare à ce que son propre accesseur a mémorisé. Écrire par cet accesseur passerait inaperçu et `onChange` ne serait jamais appelé. La charte contourne, sans effet là où il n'y a rien à contourner. |
| **Détection du re-rendu** | Le remplacement des lignes d'un tableau est détecté ; pagination et filtres se rejouent, et **la page courante est conservée** quand le nombre de lignes est inchangé. Un tri, qui réordonne les mêmes nœuds, ne déclenche rien. |
| **Suivi des valeurs** | Jauges et barres suivent `data-valeur` et `data-max`, y compris lorsque le cadriciel remplace les nœuds qui les portent. Aucune réinitialisation à prévoir. |
| **Verrou de défilement compté** | Plusieurs panneaux peuvent être ouverts l'un sur l'autre. Le défilement n'est rendu qu'à la fermeture du dernier, dans l'état où le projet l'avait laissé. Un panneau détruit sans être fermé ne bloque plus la page. |
| **Enfants ajoutés après coup** | Onglets, cases d'un code à usage unique et options d'une autocomplétion sont relus à chaque usage, jamais figés. Un élément qu'une boucle du cadriciel ajoute après l'initialisation est équipé sans qu'aucun appel ne soit nécessaire. |
| **Extraction sans formule** | Une cellule de texte commençant par `=`, `+`, `-` ou `@` est précédée d'une apostrophe. Le tableur l'affiche au lieu de l'exécuter. Les nombres, eux, restent calculables. |
| **Refus d'extraction partielle** | `data-export-total` fait refuser l'export dès que le compte affiché ne correspond pas au total déclaré. |

#### Reprendre un état à son compte

Le conflit d'attribut était le seul risque qu'aucun garde-fou ne pouvait détecter. La charte ne peut pas savoir qu'une liaison surveille le nœud qu'elle écrit. Elle demande donc l'autorisation avant d'écrire.

> **Deux noms pour une même annonce.** Un gabarit d'Angular ne peut pas lier un événement dont le nom contient deux-points : `(fs:onglet)` y désignerait la cible « fs » et le modèle serait refusé à la compilation. Chaque annonce est donc émise sous les deux formes : `fs:onglet` pour le JavaScript nu et les autres cadriciels, `fs-onglet` pour une liaison de gabarit. Annuler l'une suffit.

| Annonce | Émise avant | Si vous l'annulez |
| --- | --- | --- |
| `fs:onglet` `fs-onglet` | D'écrire `aria-selected` et `hidden` | Clavier, focus et `tabindex` restent assurés |
| `fs:bascule` `fs-bascule` | D'écrire `data-ouvert` et `aria-expanded` | Rien n'est écrit ; à vous de refléter l'état |
| `fs:jauge` `fs-jauge` | D'écrire `style.width` et `aria-valuenow` | Le rôle et les bornes restent posés |
| `fs:barre` `fs-barre` | D'écrire la largeur d'une barre | Les autres barres du groupe suivent leur propre annonce |
| `fs:lecture` `fs-lecture` | D'écrire `data-lu` sur une notification | Le compteur reste juste, l'état lu est à vous |
| `fs:fermeture` `fs-fermeture` | De retirer un encart du document | L'encart reste en place, à masquer par votre liaison |

```html
<!-- L'onglet actif vient du composant, pas de la charte.
     data-cle porte l'identifiant que le gestionnaire relit. -->
<div role="tablist" (fs-onglet)="reprendre($event)">
  <button role="tab" data-cle="suivi"
          [attr.aria-selected]="actif === 'suivi'">Suivi</button>
</div>
```

```css
type Onglet = { onglet: HTMLElement; index: number };

// Angular type $event comme Event dès que l'événement ne fait pas
// partie de ceux qu'il connaît. Sous strictTemplates, une signature
// en CustomEvent est refusée à la compilation : on accepte Event,
// puis on précise.
reprendre(ev: Event) {
  const annonce = ev as CustomEvent<Onglet>;
  annonce.preventDefault();               // la charte n'écrira pas
  this.actif = annonce.detail.onglet.dataset['cle'] ?? this.actif;
}
```

Ces annonces remontent. Un écouteur posé sur un conteneur suffit pour tous les composants qu'il contient. Ne rien écouter revient à laisser la charte tenir l'état, ce qui reste le cas ordinaire. La même règle vaut pour `fs:donnees` et `fs-donnees`, émis après chaque tri ou filtre ; celles-là informent seulement et ne s'annulent pas.

#### Niveau 1 Aucun risque

Aucun JavaScript. Ces composants n'écrivent rien et ne peuvent entrer en conflit avec quoi que ce soit. Ils se reprennent par leurs classes, dans n'importe quel gabarit, rendu et re-rendu sans limite.

Boutons, liens, cartes, badges, étiquettes, pastilles d'état, avatars, listes, fil d'Ariane, indicateur d'étapes, chronologie, listes de définitions, figures, filet, repère, motif tissé, tableaux statiques, squelette de chargement, état vide, sablier, infobulle, citation, accordéon, bloc-marque, bandeau d'identification officielle, signalement de fraude, autorité responsable, carte de démarche, pièces à fournir, événements de vie, récapitulatif de paiement, liste de dossiers, guide de photographie, encart non refermable.

> L'infobulle et l'accordéon méritent d'être signalés. Ils paraissent interactifs mais ne dépendent d'aucun script. L'un repose sur `:hover` et `:focus-within`, l'autre sur `<details>`.

#### Niveau 2 Sûrs après initialisation

Ces comportements fonctionnent dès que l'initialisation a eu lieu, ou que l'observation est active. **Douze des quinze ne demandent plus rien** ; les trois réserves restantes se lèvent par une ligne, indiquée.

| Composant | Ce qu'il écrit | Réserve |
| --- | --- | --- |
| Modale | Ouvre et ferme `<dialog>`, rend le focus | Aucune |
| Onglets | `hidden`, `aria-selected`, `tabindex` | Aucune — un onglet ajouté par `@for` est équipé seul ; annuler `fs-onglet` pour tenir l'état soi-même |
| Menu déroulant | `hidden`, `aria-expanded` | Aucune |
| Navigation repliable | `data-ouvert`, `aria-expanded` | Aucune — annuler `fs-bascule` pour tenir l'état soi-même |
| Compteur de caractères | Texte du compteur | Aucune |
| Code de vérification | Valeur des cases, focus | Aucune — les cases sont relues à chaque frappe |
| Saisie de date | Valeur des trois champs, focus | Aucune |
| Dépôt de fichier | `data-survol`, affecte `files` | Aucune — `input` et `change` sont émis après un glisser-déposer |
| Encart refermable | Retire le nœud | Aucune — annuler `fs-fermeture` si le cadriciel possède l'encart |
| Panneau latéral | `data-ouvert`, défilement du corps de page | Aucune — plusieurs panneaux peuvent se superposer |
| Jauge et barres | `style.width`, `aria-valuenow` | Aucune — la valeur est suivie, annuler `fs-jauge` pour lier la largeur soi-même |
| Centre de notifications | `data-lu` | Aucune — annuler `fs-lecture` si la liste vient d'une boucle |
| Notification passagère | Ajoute un nœud à `document.body` | Aucune — l'appel au rendu serveur ne fait rien et rend une fermeture inerte |
| Calendrier | `aria-pressed` | Le mois est rendu par le service : la charte ne le construit pas |
| Bascule de thème | `data-theme`, stockage local | Déclarer `faso-amorce.js` dans `<head>` pour éviter le clignotement |

#### Niveau 3 Conditionnels

Ces comportements prennent le document pour source de données. Ils ne sont pas interdits avec un cadriciel, mais soumis à une condition unique : **le cadriciel doit rendre l'intégralité des lignes**. Tant qu'elle tient, la charte encaisse les re-rendus sans perdre la place de l'usager. Dès qu'elle tombe (pagination côté cadriciel, défilement virtuel, chargement progressif), le cadriciel reprend l'ensemble.

| Composant | Si toutes les lignes sont rendues | Sinon |
| --- | --- | --- |
| Tri de tableau | Fonctionne. Réordonne les mêmes nœuds, sans déclencher de rejeu. | Le tri ne porterait que sur la page visible : à reprendre. |
| Pagination | Fonctionne. Un re-rendu à jeu constant laisse l'usager sur sa page. | Deux paginations superposées : retirer `data-pagination`. |
| Filtres | Fonctionnent, et se rejouent après remplacement des lignes. | Compteurs faux : filtrer la source de données. |
| Autocomplétion | Fonctionne, options ajoutées en cours de route comprises. | Suggestions distantes : au cadriciel, en gardant les classes. |
| Export | Fonctionne. | Refusé par `data-export-total`. Employer `Faso.versCsv()`. |

#### Risques résiduels

Ce qui reste tient à des propriétés du document lui-même, et non à des arbitrages laissés en suspens. Aucun n'est bloquant.

- **Changement du nombre de lignes.** Un re-rendu qui ajoute ou retire des lignes est un jeu de données différent : la pagination revient en tête, comme après un filtre. À jeu constant, la place est conservée.
- **Identifiants dupliqués.** Ils ne gênent plus le fonctionnement, mais les technologies d'assistance s'appuient dessus : cela reste un défaut d'accessibilité, à corriger pour ce motif.
- **Racine fantôme.** La résolution et la délégation la franchissent désormais, mais les feuilles de style ne la traversent pas : il faut les adopter dans chaque racine. Le mode `ViewEncapsulation.Emulated` reste le choix recommandé.
- **Attribut lié sans interception.** Lier `[hidden]` ou `[style.width]` sur un élément que la charte pilote, sans annuler l'annonce correspondante, produit toujours un conflit, mais la charte fournit désormais le moyen de l'éviter.

#### Ce que la charte garantit

- **Surface publique sans DOM.** Les vingt-huit points d'entrée — méthodes et comportements — sont appelables au rendu serveur sans exception ni effet.
- **Enfants dynamiques pris en charge** pour les onglets, le code à usage unique et l'autocomplétion.
- **Aucun écouteur par instance sur `document`** : rien ne retient un nœud détruit par le cadriciel.
- **Extraction inoffensive** : aucune cellule de texte ne peut être interprétée comme une formule par un tableur.
- **Valeurs vues par le cadriciel** : une valeur écrite par la charte déclenche bien `onChange` en React, comme elle met à jour un `ngModel` ou un `wire:model`.
- **Initialisation idempotente.** Quatre appels posent exactement autant de gestionnaires qu'un seul.
- **Délégation enregistrée une fois**, quel que soit le nombre d'appels.
- **Résolution conforme** : identique à `getElementById` quand l'identifiant est unique, et par proximité seulement en cas de doublon.
- **Tout état écrit est annulable** par `preventDefault()` sur l'annonce qui le précède.
- **Écritures de champ signalées** par `input` et `change`, sans réveiller nos propres écouteurs.
- **Aucune place perdue** : un re-rendu à jeu constant laisse la pagination où elle était.
- **Aucun verrou orphelin** : le défilement est toujours rendu, même si un panneau ouvert est détruit.
- **Extraction jamais silencieusement partielle**, dès lors que le total est déclaré.
- **Aucun style ni script en ligne** : pas de `'unsafe-inline'`.
- **Aucune dépendance, aucun appel réseau.**

### Les autres cadriciels

Angular, React et Laravel couvrent les trois manières de construire une page : application compilée, rendu déclaratif, gabarit produit par le serveur. Android couvre le quatrième cas, celui où il n'y a pas de page du tout. Les autres environnements se ramènent à l'un de ces quatre.

| Cadriciel | Se lit comme | Le seul point à vérifier |
| --- | --- | --- |
| Vue, Nuxt | [React](https://chartegraphique-21.mtdpce-test.gov.bf/integration#react) | La forme à trait d'union se lie directement : `@fs-onglet.prevent="…"`. La forme à deux-points, elle, demande une référence de gabarit et un écouteur. |
| Svelte, SvelteKit | [React](https://chartegraphique-21.mtdpce-test.gov.bf/integration#react) | `on:fs-onglet` en Svelte 4 ; en Svelte 5, une action qui pose l'écouteur. Les champs liés par `bind:value` reçoivent bien les événements émis. |
| Symfony, Twig | [Laravel](https://chartegraphique-21.mtdpce-test.gov.bf/integration#laravel) | Même chose à la syntaxe près. Avec Turbo ou Stimulus, appliquer ce qui est dit de Livewire : le balisage est remplacé après coup. |
| Django, Rails, Express | [Laravel](https://chartegraphique-21.mtdpce-test.gov.bf/integration#laravel) | Servir les fichiers depuis le domaine, en conservant l'arborescence `css/` et `polices/`. |
| HTMX, Turbo, Unpoly | [Laravel](https://chartegraphique-21.mtdpce-test.gov.bf/integration#laravel), section Livewire | Ils remplacent des fragments : `Faso.observer()` une fois suffit, ou une réinitialisation à chaque échange. |
| Flutter, React Native, iOS | [Android](https://chartegraphique-21.mtdpce-test.gov.bf/integration#android) | Même partage. Les jetons se transportent, les composants restent sur place. `assets/jetons.json` sert de source ; le générateur Android montre la méthode à reproduire. |
| Page servie telle quelle | — | Rien à faire : l'initialisation est automatique au chargement. |

#### Et un paquet Composer ?

Il n'y en a pas, et il n'y en aura pas tant qu'il n'aurait rien d'autre à faire que recopier des fichiers dans `public/`. Un projet PHP sans chaîne Node fait cette copie une fois, en trois lignes de shell, et maîtrise alors le moment de la mise à jour. Publier un paquet pour cela ajouterait une dépendance, un cycle de version et une chose de plus à maintenir, sans rien résoudre.

### Pourquoi aucun composant par cadriciel

C'est la demande la plus fréquente des équipes. Un refus se motive plutôt qu'il ne se subit.

#### Le raisonnement

- Publier des composants React, Angular et Vue revient à maintenir **trois implémentations** de chaque comportement, plus la version sans cadriciel. Soixante-cinq composants deviennent deux cent soixante.
- Chaque montée de version majeure d'un cadriciel impose une reprise, sur un calendrier que l'administration ne maîtrise pas.
- Un projet qui n'emploie aucun de ces trois cadriciels, et l'administration en compte, se retrouverait citoyen de seconde zone.
- C'est de cette façon que meurent les systèmes de conception publics : non par manque d'idées, mais parce que la charge de maintenance dépasse l'équipe qui en a la garde.

> Le prix à payer est réel. Un développeur Angular aurait préféré `<faso-bouton>` à `<button class="fs-btn fs-btn--principal">`. En contrepartie, le balisage qu'il écrit reste valable quel que soit l'avenir du cadriciel, et la charte survit à ses propres équipes.

#### Ce qu'un projet peut faire de son côté

Rien n'interdit à une équipe d'envelopper les classes dans ses propres composants. C'est même recommandé au-delà de quelques écrans. Ces enveloppes appartiennent au projet, pas à la charte, et n'engagent qu'elle.

```js
@Component({
  selector: 'app-bouton',
  template: `<button [class]="classes" [disabled]="desactive">
               <ng-content />
             </button>`
})
export class Bouton {
  variante = input<'principal' | 'secondaire' | 'tertiaire'>('principal');
  desactive = input(false);
  get classes() { return `fs-btn fs-btn--${this.variante()}`; }
}
```

### Recette d'intégration

À passer avant la première mise en production d'un projet reprenant la charte.

#### Contrôles

- Les trois feuilles sont déclarées, dans l'ordre, et la feuille du projet vient après.
- Les polices sont servies depuis le domaine du service. Aucune requête vers un tiers dans l'onglet réseau.
- Les icônes s'affichent — si elles sont absentes, vérifier `img-src data:` dans la politique de sécurité.
- `faso-amorce.js` est déclaré dans `<head>` si le thème sombre est proposé.
- Les composants en `ShadowDom`, s'il y en a, adoptent les feuilles de la charte dans leur racine.
- Un composant rendu après le chargement réagit : ouvrir une modale rendue dynamiquement.
- Un composant rendu deux fois ne déclenche pas deux fois : cliquer une commande après un aller-retour de navigation.
- Le rendu côté serveur aboutit, le cas échéant.
- Aucun tableau paginé par la charte n'est paginé aussi par le cadriciel.
- Chaque attribut lié par le projet sur un composant de la charte a son annonce annulée.
- Les libellés du script sont dans la langue du service, le cas échéant.
- En React : retenir une suggestion d'autocomplétion met bien à jour l'état du composant, pas seulement l'affichage.
- En React. Chaque élément de liste porte une `key` stable, qui ne soit pas son index.
- Avec Livewire ou Turbo. Une modale ouverte survit à un rafraîchissement du composant, ou porte `wire:ignore`.
- Avec un paginateur côté serveur : `data-export-total` est déclaré, et `data-pagination` ne l'est pas.
- Un export CSV ouvert dans un tableur n'exécute rien : les cellules commençant par `=`, `+`, `-` ou `@` arrivent précédées d'une apostrophe.
- `node outils/jetons.js --verifier` passe, si le projet reprend les jetons en JSON.
- Les cinq vérifications d'[accessibilité](https://chartegraphique-21.mtdpce-test.gov.bf/accessibilite) sont passées.


---

## Gouvernance

Page de référence : https://chartegraphique-21.mtdpce-test.gov.bf/gouvernance

Un système de conception qui n'a pas de propriétaire se fragmente ; un système qui n'a pas de procédure se fige. Cette page fixe qui décide, comment le système évolue, et ce qu'un projet doit faire lorsque la charte ne prévoit pas son cas.

> **Désignations à arrêter**
>
> Les rôles et les procédures décrits ici sont des propositions de conception. Les organismes qui les exercent (autorité de tutelle, équipe du système, comité d'arbitrage), doivent être désignés par acte administratif avant l'entrée en vigueur de la charte. Les mentions `[à désigner]` marquent les points qui relèvent de cette décision et non de la conception.

### Rôles et responsabilités

Trois niveaux, et un principe. La décision revient au plus proche du terrain qui peut la prendre sans compromettre l'unité du système.

#### Les trois niveaux

Niveau 1

##### Autorité de tutelle

Approuve les versions majeures, arbitre les désaccords persistants, engage la charte vis-à-vis des administrations. N'intervient pas dans les décisions courantes.

`[à désigner]`

Niveau 2

##### Équipe du système

Maintient les fichiers et la documentation, instruit les propositions, publie les versions mineures et correctives, tient le registre des dérogations, accompagne les projets.

`[à désigner]`

Niveau 3

##### Référents de projet

Un référent par service numérique. Applique la charte, signale les manques, soumet les dérogations, atteste la conformité avant mise en production.

Désigné par l'administration porteuse du service

#### Qui décide quoi

*Répartition des décisions*

| Décision | Instruite par | Arrêtée par | Délai visé |
| --- | --- | --- | --- |
| Correction d'un défaut | Équipe du système | Équipe du système | 10 jours ouvrés |
| Nouveau composant ou variante | Équipe du système | Équipe du système | 30 jours ouvrés |
| Modification d'un jeton existant | Équipe du système | Comité d'arbitrage | 45 jours ouvrés |
| Dérogation ponctuelle d'un projet | Référent de projet | Équipe du système | 10 jours ouvrés |
| Emploi de l'emblème hors règles | Équipe du système | Autorité de tutelle | Sans délai garanti |
| Version majeure | Équipe du système | Autorité de tutelle | Annuel au plus |

### Versionnement

Le numéro de version se lit `majeure.mineure.corrective`. Ce qui détermine le rang n'est pas l'ampleur du travail mais l'effort imposé aux projets qui ont déjà intégré la charte.

#### Ce qui fait changer chaque rang

| Rang | Déclencheurs | Effet pour un projet |
| --- | --- | --- |
| **Majeure** 2.0 → 3.0 | Suppression ou renommage d'un jeton, d'une classe ou d'un composant ; modification du balisage exigé par un composant ; changement de *sens* d'un jeton ; nouvelle dépendance entre fichiers. | Reprise du code nécessaire. Guide de migration fourni. |
| **Mineure** 2.0 → 2.1 | Nouveau composant, nouvelle variante, nouveau jeton, nouvelle icône, passage d'un composant en *déconseillé*. | Aucune reprise. Mise à jour des fichiers suffisante. |
| **Corrective** 2.0 → 2.0.1 | Correction d'un défaut, ajustement visuel sans changement de balisage, correction de documentation. | Aucune reprise. |

> **La valeur d'un jeton n'est pas son contrat.** Ajuster `--vert-600` de deux points de luminosité est une version corrective. En revanche, faire pointer `--action` vers une autre famille chromatique change le sens du jeton et constitue une rupture, même si aucune ligne de code de projet n'a besoin d'être touchée.

#### Rythme de publication

- Les versions correctives sont publiées au fil de l'eau.
- Les versions mineures sont regroupées et publiées au plus une fois par trimestre, pour que les projets n'aient pas à suivre un flux continu.
- Une version majeure au plus par an, annoncée six mois à l'avance.
- Chaque publication est accompagnée d'une entrée au [journal des versions](https://chartegraphique-21.mtdpce-test.gov.bf/gouvernance#journal).

### Cycle de vie d'un composant

Un composant n'entre pas dans la charte parce qu'il est utile à un projet, mais parce qu'il répond à un besoin constaté dans plusieurs. Il en sort selon une procédure, jamais du jour au lendemain.

#### Les quatre états

*Exemple — États d'un composant*

```html
<ol class="fs-etapes">
  <li class="fs-etape" data-etat="faite">
    <span class="fs-etape-num">État 1</span>
    <span class="fs-etape-titre">Proposé</span>
  </li>
  <li class="fs-etape" data-etat="faite">
    <span class="fs-etape-num">État 2</span>
    <span class="fs-etape-titre">Expérimental</span>
  </li>
  <li class="fs-etape" data-etat="courante">
    <span class="fs-etape-num">État 3</span>
    <span class="fs-etape-titre">Stable</span>
  </li>
  <li class="fs-etape">
    <span class="fs-etape-num">État 4</span>
    <span class="fs-etape-titre">Déconseillé, puis retiré</span>
  </li>
</ol>
```

**Proposé**

Besoin exprimé et instruit. Le composant n'existe pas encore dans les fichiers. Aucun projet ne doit l'anticiper.

**Expérimental**

Publié mais susceptible d'évoluer sans version majeure. Utilisable par les projets volontaires, qui acceptent d'en suivre les modifications. Un composant ne reste pas expérimental plus de deux versions mineures.

**Stable**

État par défaut de tout composant publié dans cette charte. Ne peut changer de contrat qu'à l'occasion d'une version majeure.

**Déconseillé**

Signalé dans la documentation, avec le composant de remplacement et la version de retrait. Continue de fonctionner pendant au moins **deux versions mineures et une version majeure**, soit un an au minimum.

#### Critères d'acceptation

Un composant proposé n'est publié que si les sept conditions sont réunies. Une seule non satisfaite suffit à ajourner la proposition.

- **Besoin partagé.** Le besoin est constaté dans au moins deux services de l'État, ou découle d'une obligation réglementaire.
- **Absence de doublon.** Aucun composant existant ne couvre le cas, même au prix d'une variante.
- **Aucune valeur en dur.** Le composant ne consomme que des jetons.
- **Accessible.** Contrastes conformes, navigation au clavier, cible tactile de 44 px, état visible sans recours à la seule couleur.
- **Dégradation maîtrisée.** Le contenu essentiel reste lisible sans JavaScript et sur écran de 320 px.
- **Documenté.** Intitulé, usage, règles d'emploi, exemple, et au moins un cas à proscrire.
- **Imprimable.** Le composant s'imprime correctement ou est explicitement exclu de l'impression.

#### Signalement dans la documentation

L'état d'un composant est porté par une étiquette placée à côté de son intitulé. L'absence d'étiquette signifie *stable* : c'est le cas de la quasi-totalité de la charte.

*Exemple — Étiquettes d'état*

```html
<h4 style="margin:0;">
  Bouton <code>.fs-btn</code>
</h4>
<h4 style="margin:0;">
  Motif tissé <code>.fs-tissage</code>
  <span class="fs-badge fs-badge--alerte">Expérimental</span>
</h4>
<h4 style="margin:0;">
  Ancien filet <code>.fs-filet--bandes</code>
  <span class="fs-badge fs-badge--danger">Déconseillé — retrait en 3.0</span>
</h4>
```

### Proposer un changement

Toute administration peut proposer. La procédure est volontairement courte : une proposition mal instruite coûte plus cher à traiter qu'un besoin mal formulé mais honnête.

#### La procédure

1. 1

   Vérifier que le besoin n'est pas déjà couvert

   Parcourir les composants de base, les composants métier et les gabarits. La majorité des propositions reçues portent sur un cas déjà traité sous un autre nom.
2. 2

   Décrire le besoin, pas la solution

   Indiquer la tâche que l'usager doit accomplir, le service concerné, et pourquoi les composants existants ne suffisent pas. Une maquette est utile mais ne remplace pas l'énoncé du besoin.
3. 3

   Transmettre au référent de son administration

   Le référent filtre, complète et transmet à l'équipe du système. Une proposition transmise directement est réorientée vers le référent.
4. 4

   Instruction

   L'équipe rapproche la proposition des autres besoins reçus, vérifie les sept critères d'acceptation et répond : retenue, ajournée avec les éléments manquants, ou écartée avec le motif.
5. 5

   Publication

   Le composant retenu est publié en état expérimental à la prochaine version mineure, puis stabilisé après retour des projets.

> **Signaler un défaut**
>
> Un défaut d'accessibilité, de contraste ou de comportement ne suit pas cette procédure. Il est signalé directement à l'équipe du système et traité en version corrective sous dix jours ouvrés.

### Dérogations

Une charte qui n'admet aucune exception est contournée en silence. Mieux vaut une dérogation instruite et inscrite au registre qu'un écart invisible.

#### Ce qui peut faire l'objet d'une dérogation

**Recevable**

- Composant absent du système et nécessaire à une obligation réglementaire.
- Adaptation d'un gabarit imposée par un texte ou par un système tiers imposé.
- Contrainte technique documentée d'une plateforme existante.
- Densité d'information supérieure dans un outil réservé aux agents.

**Irrecevable**

- Toute atteinte aux règles d'emploi de l'emblème, du filet ou de la devise.
- Tout abaissement du niveau d'accessibilité.
- Suppression du bandeau d'identification officielle ou des mentions de confiance.
- Préférence esthétique d'une administration ou d'un prestataire.

#### Régime de la dérogation

**Forme**

Écrite, motivée, portée par le référent de projet.

**Portée**

Limitée à un service et à un écran identifié. Une dérogation ne vaut jamais pour une administration entière.

**Durée**

Douze mois, renouvelable une fois. Au-delà, le besoin est requalifié en proposition de composant.

**Publicité**

Inscrite au registre des dérogations, consultable par toutes les administrations.

**Effet**

Le service reste déclaré conforme à la charte, la dérogation étant mentionnée dans sa déclaration.

> Une dérogation accordée deux fois pour le même motif par deux services différents vaut constat d'un manque du système. L'équipe est alors tenue d'ouvrir une proposition de composant, sans attendre qu'on la lui soumette.

### Ce que la charte dépose sur l'appareil

Un système qui impose le consentement aux services doit commencer par déclarer ce qu'il dépose lui-même. La charte écrit deux choses, et rien d'autre : aucun identifiant, aucune mesure, aucun appel vers un tiers.

| Ce qui est écrit | Où | Combien de temps | Consentement |
| --- | --- | --- | --- |
| `faso-theme` « dark » ou « light » | Stockage local | Jusqu'à effacement par l'usager | Non requis |
| `faso-consentement` la décision, finalité par finalité | Cookie, `SameSite=Lax`, `Secure` en https | Six mois, puis la question est reposée | Non requis |

> Ces deux écritures ne demandent pas de consentement, et la raison est la même dans les deux cas : elles ne servent qu'à exécuter ce que l'usager a demandé. Le thème est un réglage qu'il a posé lui-même. La décision de consentement est ce qui permet d'honorer son refus ; ne pas la conserver reviendrait à reposer la question indéfiniment.

#### Ce que ce site de documentation dépose en plus

Le tableau ci-dessus vaut pour la charte, dans n'importe quel service. Le site que vous lisez ajoute ce qui suit, pour se protéger des robots et recevoir vos messages. La même règle s'applique à lui : il le déclare.

| Ce qui est écrit | Où | Combien de temps | Consentement |
| --- | --- | --- | --- |
| `charte_humain` l'heure de la dernière page vue après la case anti-robot, chiffrée | Cookie, `HttpOnly`, `SameSite=Lax`, réécrit à chaque page | 24 heures sans page vue, puis la case est redemandée | Non requis |
| `charte_session` et `XSRF-TOKEN` session et jeton anti-falsification | Cookies, pages Vérification et Nous écrire seulement | 30 minutes d'inactivité | Non requis |
| Google reCAPTCHA la case « Je ne suis pas un robot » | Script et cadre servis par Google, pages Vérification et Nous écrire | Selon les règles de Google | Tiers |
| Mesure d'audience personnes en ligne et visiteurs du mois, affichés au pied de l'accueil | Rien sur l'appareil. Sur le serveur, une empreinte anonyme : adresse et navigateur hachés avec une clé secrète et le mois en cours, l'adresse elle-même n'étant jamais gardée | Jusqu'à la fin du mois ; rien ne relie un mois au suivant | Non requis |

Deux écarts aux règles de cette page en découlent, et il vaut mieux les écrire : la case est un appel vers un tiers, Google, et elle demande JavaScript, si bien que le site ne se lit plus sans lui. Les fichiers de la charte, eux, restent servis sans case ni appel extérieur, et le site exporté en fichiers statiques n'a ni l'un ni l'autre.

#### Ce que la charte ne fait jamais

- Aucun identifiant d'appareil ni de session n'est créé par la charte.
- Aucune mesure d'audience n'est incluse. Celle du service lui appartient, et n'est déposée qu'après une réponse.
- Aucun appel réseau vers un tiers : les polices, les icônes et l'emblème sont servis par le domaine du service.
- Le stockage est toujours protégé : en navigation privée ou sur un site bloqué, la lecture échoue sans que la page en souffre.

#### Ce qui revient au service

Un téléservice dépose d'autres choses que la charte ne connaît pas : cookie de session, jeton anti-falsification de formulaire, mesure d'audience, lecteur vidéo. Les trois premiers lui sont nécessaires et ne se demandent pas ; ils doivent néanmoins figurer dans sa politique de confidentialité. Le reste passe par le [bandeau de consentement](https://chartegraphique-21.mtdpce-test.gov.bf/composants-metier#confiance).

La protection des données à caractère personnel relève au Burkina Faso d'une autorité de contrôle dédiée. Les obligations déclaratives d'un téléservice, la durée de conservation et les mentions de sa politique de confidentialité s'apprécient au regard du cadre national : la charte fournit le composant et le comportement, pas la qualification juridique.

### Déclaration de conformité

Avant toute mise en production, le référent de projet atteste la conformité de son service. La déclaration est publiée au même titre que la déclaration d'accessibilité.

#### Points attestés

- Le bandeau d'identification officielle, le filet national et le bloc-marque sont repris sans adaptation.
- Les trois feuilles de style du système sont intégrées sans modification locale.
- Aucune couleur, taille ou forme n'est écrite en dur. Toute adaptation passe par une surcharge de jetons.
- Les cinq vérifications d'accessibilité de la recette ont été passées.
- Les dérogations en vigueur sont listées, avec leur date d'échéance.
- La version de la charte utilisée est indiquée, ainsi que la date de l'audit.

#### Niveaux de conformité

| Niveau | Condition | Conséquence |
| --- | --- | --- |
| Conforme | Tous les points attestés, aucune dérogation ou dérogations en cours de validité. | Mise en production autorisée. |
| Conforme sous réserve | Écarts identifiés, sans incidence sur l'accessibilité ni sur l'identification officielle. | Mise en production autorisée, plan de correction daté exigé. |
| Non conforme | Atteinte à l'identification officielle, ou défaut d'accessibilité bloquant. | Mise en production ajournée. |

### Journal des versions

Chaque version publiée est consignée ici. Les ruptures sont listées en premier, parce que ce sont les seules qui demandent du travail aux projets.

#### 2.0 — septembre 2026

Refonte des fondations et première bibliothèque de composants réelle. Version majeure. La reprise d'un projet issu de la 1.0 est nécessaire.

##### Ruptures

| Objet | Avant | Après |
| --- | --- | --- |
| Police de titre | Poppins | Archivo |
| Hébergement des polices | Google Fonts | Servies depuis le domaine du service, en polices variables, sous-ensemble latin : 81 Ko au lieu de 198 |
| Police à chasse fixe | JetBrains Mono, téléchargée | Supprimée. La chasse fixe du système suffit. Elle ne porte aucune identité |
| Neutres | Gris bleutés | Base chaude dérivée de la latérite |
| `--bleu-gouvernement` | Couleur de structure : en-têtes, navigation, pieds de page | Supprimé. Le bleu devient couleur de support, cantonnée au registre informatif |
| `.fs-filet` | Trois bandes égales, 4 px | Composition du drapeau : moitié rouge, moitié verte, étoile d'or. 12 px |
| Feuilles de style | `tokens.css` et `faso.css` | `icones.css` devient obligatoire : plusieurs composants y puisent leurs repères |
| Thème sombre | Suivait la préférence du système d'exploitation | Le thème clair est le défaut ; le sombre s'active sur choix explicite |

##### Ajouts

- **65 composants** documentés en sept familles, là où la 1.0 ne décrivait que des jetons.
- **Iconographie** : 35 icônes en cinq familles, grille de 24 px, trait de 1,5, appliquées en masque CSS.
- **Emblème vectoriel** : 45 Ko compressé au lieu de 916 Ko, net à toute taille.
- **Échelles tonales** de dix paliers sur les trois couleurs nationales, la teinte officielle restant verrouillée à son palier.
- **`.fs-repere`** et **`.fs-tissage`**, ce dernier inspiré des rayures du Faso Dan Fani.
- **Gabarits institutionnels** : portail, fiche de démarche, formulaire long, tableau de bord, authentification, pages d'erreur.
- **Composants métier** de confiance numérique : bandeau officiel, signalement de fraude, canaux de paiement autorisés.
- **Contrôle de contraste** calculé dans la page, selon la formule WCAG 2.1.
- **Panneau de code** sous chaque exemple, restituant le HTML, le CSS et le JavaScript réellement en vigueur.
- Feuille d'impression, thème sombre, contraste renforcé et mouvement réduit traités dans le socle.

##### Corrections

- **Résolution des cibles sans ambiguïté.** Six comportements résolvaient leur cible par identifiant global. Deux instances d'un même composant se disputaient alors le même `id`, et le déclencheur de la seconde agissait sur la première. Quand l'identifiant ne désigne qu'un élément, le résultat reste celui de `getElementById` ; ce n'est qu'en cas de doublon que la recherche remonte depuis le déclencheur. La frontière d'une racine fantôme est franchie.
- **Écritures de valeur signalées.** Cinq endroits où le script écrit dans un champ (code collé, date collée, suggestion retenue, filtre vidé), émettent `input` et `change`. Sans eux, une liaison bidirectionnelle conservait l'ancienne valeur et le formulaire partait incomplet.
- **Valeurs invisibles pour React.** React n'interroge pas le champ. Il compare à ce que son propre accesseur a mémorisé. Une valeur écrite par cet accesseur (date collée, code collé, suggestion retenue, filtre vidé) ne déclenchait donc jamais `onChange` : l'usager lisait la nouvelle valeur à l'écran et le formulaire partait avec l'ancienne. La charte écrit désormais par l'accesseur du prototype, ce qui est sans effet pour les cadriciels qui n'ont rien installé.
- **Consentement réellement géré.** Le bandeau existait mais n'était qu'une image : trois boutons sans comportement, aucune décision conservée, et un texte qui promettait « votre choix est conservé six mois » alors que rien ne conservait. Un service qui recopiait ce balisage obtenait un bandeau qui reparaissait à chaque page. La décision est désormais recueillie finalité par finalité, conservée six mois dans un cookie `SameSite=Lax`, restituée par `Faso.consentement.lire()` et annoncée par `fs:consentement`. Le dépôt des traceurs reste au service.
- **Ce que la charte dépose est déclaré.** Elle écrivait le thème dans le stockage local depuis le début sans qu'aucune page ne le dise. Une section le déclare, avec la durée et le motif pour lequel ces deux écritures ne demandent pas de consentement.
- **Une seule composition pour toutes les marques.** Le filet pleine largeur et le bandeau d'une carte mise en avant étaient divisés gauche-droite, avec l'étoile à la jonction ; seul le repère court portait la composition du drapeau. Les cinq marques du système la portent désormais : **rouge au-dessus, vert au-dessous, étoile centrée à cheval sur les deux**. Une marque de l'État ne se réarrange pas selon le format où elle se pose. La justification contraire, qui figurait dans la page Fondations et dans les commentaires du code, a été retirée. Le contrôle d'identité vérifie maintenant la composition marque par marque, et non plus sa seule présence quelque part dans la feuille.
- **Recette exécutable et chaîne de publication.** La vérification n'était affirmée nulle part qu'en prose. `npm test` exécute désormais vingt contrôles : syntaxe, balisage des neuf pages, classes orphelines, bonne formation des SVG et des 43 ressources Android, correspondance des projections, cohérence du paquet, contraste, et six suites de comportements. Un second mode, `--publication`, refuse de laisser partir un paquet dont les champs restent à renseigner ou dont les conditions de réutilisation ne sont pas arrêtées. Deux chaînes d'intégration, une page d'erreur 404 et trois configurations de serveur complètent l'ensemble.
- **Chiffre de contrôle corrigé.** La vérification de contraste annonçait 150 couples ; elle comptait les couples tentés, y compris ceux qui ne se résolvaient pas. Le nombre de couples réellement éprouvés est de 118, et il est désormais produit par le contrôle lui-même.
- **Intégration d'Android, en Kotlin et en Java.** Le CSS et le JavaScript ne s'exécutent pas dans une application native : ce sont les décisions qui se transportent. `outils/android.js` produit depuis la même feuille de jetons les couleurs des deux thèmes, les dimensions du téléphone et de la tablette, un thème Material 3, un thème Compose, les 35 icônes, l'emblème, le filet et le repère nationaux. Deux traductions y sont assumées : `clamp()` devient deux jeux de dimensions, et la cible tactile passe de 44 à 48 dp, plancher d'Android. En WebView, la charte s'exécute telle quelle ; la section correspondante insiste sur le refus de l'assombrissement automatique, qui déplacerait les couleurs du drapeau.
- **Intégration de React et de Laravel.** Les deux ont désormais leur section, au même niveau de détail qu'Angular : champs contrôlés et écoute des annonces pour le premier, formulaire Blade, paginateur d'Eloquent, Livewire, Alpine, Inertia et politique de sécurité pour le second. Les autres cadriciels sont renvoyés à celui des trois dont ils se rapprochent.
- **Contraste des contours et des mentions.** Le contour d'un champ atteignait 1,66:1 sur blanc, là où le référentiel en exige 3 pour un élément d'interface ; le texte faible atteignait 4,16:1 pour un seuil de 4,5. Deux demi-paliers, `neutre-450` et `neutre-550`, ont été ajoutés à l'échelle et les rôles réaffectés. En thème sombre, le contour ne donnait que 1,96:1 et le texte du bouton de suppression 3,13:1 : un jeton `accent-texte` résout le second. Les 118 couples de couleurs réellement employés par la feuille passent désormais leur seuil, dans les deux thèmes.
- **Injection de formule dans les extractions.** Une cellule valant `=1+1` ou `=HYPERLINK(...)` était livrée telle quelle et s'exécutait à l'ouverture du fichier sur le poste de l'agent. Les cellules de texte commençant par un caractère de formule sont désarmées par une apostrophe ; les nombres, reconnus, restent calculables. L'échappement n'aplatit plus les espaces ni les retours à la ligne, qui appartiennent à la donnée.
- **Surface publique utilisable au rendu serveur.** `Faso.appliquerTheme()` et les vingt et un comportements exposés levaient `ReferenceError: document is not defined`. Seul l'import était protégé. Les vingt-huit points d'entrée sont désormais sans effet plutôt que fatals.
- **Éléments ajoutés après l'initialisation.** Un onglet ajouté à une liste déjà initialisée n'obtenait ni clic ni clavier, et rejouer l'initialisation n'y changeait rien : le marquage portait sur le conteneur. Il porte maintenant sur chaque enfant, et une veille équipe les arrivants. Même traitement pour les cases d'un code à usage unique et les options d'une autocomplétion.
- **Fuite d'écouteur de l'autocomplétion.** Chaque instance posait un écouteur sur `document` qui retenait son bloc bien après la destruction de celui-ci. Un écouteur unique le remplace, qui relit le document à chaque clic.
- **Cible tactile tenue ou dite.** La charte annonçait 44 × 44 px pour des boutons à icône de 36 et des jours de calendrier de 40. La case du calendrier passe à 44 px, le bouton à icône reçoit une zone active débordante, et les deux exceptions qui subsistent sont écrites dans la page Accessibilité.
- **Conformité annoncée ramenée au mesuré.** La page Accessibilité affirmait que les composants satisfaisaient déjà les critères. Elle dit maintenant ce qui a été mesuré, et ce qui ne l'a pas été : ni lecteur d'écran, ni audit tiers, ni test avec des personnes en situation de handicap.
- **Jetons produits, non recopiés.** `jetons.json` était entretenu à la main et avait divergé du CSS sur `police-mono`, tandis que `etoile-or` y manquait. `outils/jetons.js` le produit désormais depuis `tokens.css`, et sa vérification entre dans la recette.
- **Libellés du script rassemblés.** Une vingtaine de textes étaient écrits en dur en français. Ils forment un dictionnaire unique, `Faso.textes`, que le projet complète. Le pays compte une soixantaine de langues : la charte ne pouvait pas en imposer une.
- **Exemple Angular qui compile.** Le gestionnaire d'annonce était écrit avec une signature en `CustomEvent`, refusée par `strictTemplates` puisque Angular type `$event` en `Event`, et le gabarit ne portait pas le `data-cle` que le gestionnaire relisait.
- **Outillage de la documentation retiré du paquet.** `docs.css` et `docs.js` ne servent qu'au site de la charte et pesaient sur chaque projet consommateur.
- **Extraction incomplète rendue impossible.** `data-export-total` fait refuser l'export dès que le compte des lignes affichées ne correspond pas au total déclaré.
- **Pagination et filtres résistent au re-rendu.** Le remplacement des lignes est détecté et les deux se rejouent. La page courante est conservée lorsque le nombre de lignes n'a pas changé : un re-rendu sans rapport ne fait plus perdre sa place à l'usager.
- **Tout état écrit est annulable.** Onglets, navigation repliable, jauges, barres, notifications lues et fermeture d'encart annoncent leur changement avant de l'écrire. Un projet qui lie l'attribut lui-même annule l'annonce : la charte continue d'assurer le clavier, le focus et l'ordre de tabulation, sans toucher à la liaison. Chaque annonce porte deux noms, `fs:onglet` et `fs-onglet`, la seconde forme étant la seule qu'un gabarit d'Angular puisse lier.
- **Verrou de défilement compté.** Un panneau latéral en remplaçait la valeur d'origine par une chaîne vide à la fermeture, et deux panneaux superposés libéraient le défilement trop tôt. Un panneau détruit sans avoir été fermé laissait la page bloquée.
- **Valeurs suivies.** Jauges et barres se mettaient à jour à l'initialisation seulement. Une valeur changée ensuite par le cadriciel restait sans effet sur la largeur affichée.
- **Amorce du thème.** Une page réglée en sombre s'affichait brièvement en clair. `faso-amorce.js` pose l'attribut avant le premier rendu, sans script en ligne.
- **Notification au rendu côté serveur.** `Faso.notifier()` échouait hors navigateur ; l'appel ne fait plus rien et rend une fermeture inerte.
- Navigation principale inatteignable sous 900 px, faute de bouton de repli.
- Zone de dépôt de fichier : le script doublait le clic natif du libellé et ouvrait deux fois le sélecteur.
- Glyphes Unicode d'avertissement rendus en emoji couleur sous Windows, remplacés par des icônes du système.
- Emblème de 916 Ko chargé sur chaque page, en contradiction avec le principe de contrainte réseau.

#### 1.0 — avril 2026 Archivée

Version initiale. Palette issue du drapeau, couleurs institutionnelles, typographie, espacement, rayons et ombres, accompagnés d'une description des usages institutionnels et des modèles de pages. Le système ne comportait pas encore de bibliothèque de composants intégrable.

> **Emplacement de l'archive**
>
> Les fichiers de la 1.0 ont été déplacés dans `archives/v1-avril-2026/`, accompagnés d'une note rappelant qu'ils ne font plus foi. Ils cohabitaient jusque-là avec la 2.0 à la racine du projet, sans rien indiquer de leur obsolescence. Deux jeux de jetons contradictoires s'y présentaient comme également valides.
>
> Le raster d'origine de l'emblème y est conservé. C'est la source dont la version vectorielle a été dérivée, et elle est nécessaire pour refaire ou contrôler ce tracé.

#### Sort des versions retirées

Une version qui cesse de faire foi n'est pas effacée. Elle est archivée. On peut ainsi vérifier après coup ce qu'un service conforme à une version antérieure était censé appliquer.

- Les fichiers sont déplacés dans `archives/<version>-<mois>/`, hors du chemin de production.
- Une note placée dans le dossier énonce, dès sa première ligne, que ces fichiers ne font plus foi et où se trouve la version en vigueur.
- Les sources nécessaires à la reconstitution d'un livrable (fichier d'origine d'un emblème, par exemple), sont conservées avec elle.
- Aucun fichier obsolète ne demeure à la racine. Deux sources contradictoires au même endroit valent absence de source.


---

## Annexe A — Jetons (`tokens.css`)

La source qui fait foi pour toute valeur : couleur, taille, espacement, rayon, ombre, durée. Un composant ne consomme que les jetons de rôle, jamais les palettes brutes.

```css
/* =============================================================
   Charte graphique de l'administration burkinabè
   tokens.css — socle de variables
   Version 2.0

   Ordre du fichier :
   1. Palettes brutes (valeurs absolues, jamais utilisées telles
      quelles dans les composants)
   2. Typographie, espacement, formes, profondeur, mouvement
   3. Rôles sémantiques (ce que les composants consomment)
   4. Thème sombre
   ============================================================= */

/* =============================================================
   0. POLICES

   Les fichiers sont servis depuis le domaine du service, jamais
   depuis un tiers : une dépendance externe ajoute une résolution
   DNS, une connexion, et place la disponibilité du service hors
   du contrôle de l'administration.

   Ce sont des polices variables : un seul fichier couvre toutes
   les graisses, là où cinq fichiers statiques pèseraient le
   triple. Seul le sous-ensemble latin est servi : il couvre le
   français en entier, Œ et œ compris.

   Archivo et Inter sont diffusées sous SIL Open Font License,
   qui autorise l'usage, la modification et la redistribution,
   y compris par une administration. Le texte des licences
   accompagne les fichiers dans assets/polices/.

   font-display: swap : le texte s'affiche immédiatement dans la
   police de repli, puis bascule. Sur réseau lent, une page
   lisible en police système vaut mieux qu'une page blanche.
   ============================================================= */

@font-face {
  font-family: "Archivo";
  font-style: normal;
  font-weight: 400 800;
  font-display: swap;
  src: url("../polices/archivo.woff2") format("woff2");
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6,
                 U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F,
                 U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}

@font-face {
  font-family: "Inter";
  font-style: normal;
  font-weight: 400 700;
  font-display: swap;
  src: url("../polices/inter.woff2") format("woff2");
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6,
                 U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F,
                 U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}

:root {
  /* ---------------------------------------------------------
     1. PALETTES

     Les trois couleurs du drapeau du 4 août 1984 servent de
     souche. Chaque souche est déclinée en dix valeurs pour
     couvrir les besoins réels d'une interface (fonds, bordures,
     textes, états) sans jamais altérer la teinte officielle,
     qui reste fixée au palier indiqué « drapeau ».
     --------------------------------------------------------- */

  /* Rouge — révolution, courage. Teinte officielle : 500 */
  --rouge-50:  #fff1f2;
  --rouge-100: #ffe0e2;
  --rouge-200: #ffc5c9;
  --rouge-300: #f99aa1;
  --rouge-400: #f0636e;
  --rouge-500: #e30613;  /* drapeau */
  --rouge-600: #c40511;
  --rouge-700: #a2040e;
  --rouge-800: #7d0610;
  --rouge-900: #5a0810;

  /* Vert — espérance, richesses agricoles. Teinte officielle : 500 */
  --vert-50:  #ecf7f0;
  --vert-100: #d3eddd;
  --vert-200: #a8dbbc;
  --vert-300: #74c296;
  --vert-400: #3ea46d;
  --vert-500: #00843b;  /* drapeau */
  --vert-600: #026c31;
  --vert-700: #045527;
  --vert-800: #04411f;
  --vert-900: #032e16;

  /* Or — étoile, lumière. Teinte officielle : 300.
     Le jaune pur est clair par nature ; le palier officiel est
     donc placé haut dans l'échelle et les paliers sombres
     tirent vers l'ocre pour rester lisibles sur blanc. */
  --or-50:  #fffce5;
  --or-100: #fff8bf;
  --or-200: #fff287;
  --or-300: #ffed00;  /* drapeau */
  --or-400: #e8d400;
  --or-500: #c9b600;
  --or-600: #a39300;
  --or-700: #7d7100;
  --or-800: #5c5300;
  --or-900: #3d3800;

  /* Neutres — dérivés d'une base chaude (latérite) et non d'un
     gris bleuté. Le bleu n'appartient pas au drapeau : il n'a
     pas à teinter les fonds, les bordures ni les textes. */
  --neutre-0:   #ffffff;
  --neutre-25:  #fcfbf9;
  --neutre-50:  #f7f5f2;
  --neutre-100: #efece7;
  --neutre-200: #e2ddd6;
  --neutre-300: #cfc8bf;
  --neutre-400: #a9a199;
  /* Deux demi-paliers, ajoutés pour la conformité et non pour
     l'esthétique : 450 est le premier palier qui atteigne 3:1 sur
     blanc comme sur la surface enfoncée, seuil exigé par WCAG 1.4.11
     pour le contour d'un contrôle ; 550 est le premier qui atteigne
     4,5:1, seuil du texte courant. Les mesures figurent dans la page
     Accessibilité. */
  --neutre-450: #8e867f;
  --neutre-500: #837b73;
  --neutre-550: #756d66;
  --neutre-600: #635c55;
  --neutre-700: #4a443e;
  --neutre-800: #332e2a;
  --neutre-900: #211d1a;
  --neutre-950: #14110f;

  /* Bleu administratif — couleur de support, réservée au registre
     informatif (messages neutres, repères documentaires). Elle ne
     structure ni l'en-tête, ni la navigation, ni les actions. */
  --bleu-50:  #eef4f9;
  --bleu-100: #d6e5f0;
  --bleu-300: #7ba5c4;
  --bleu-500: #2a6d9b;
  --bleu-700: #173f5f;
  --bleu-900: #0d2639;

  /* Ocre — registre d'avertissement. Dérivé de l'or, assombri
     jusqu'à obtenir un contraste AA sur fond clair. */
  --ocre-50:  #fdf4e3;
  --ocre-100: #f9e6c2;
  --ocre-500: #b07800;
  --ocre-700: #7d5500;
  --ocre-900: #4a3200;

  /* ---------------------------------------------------------
     2. TYPOGRAPHIE

     Archivo pour les titres : grotesque institutionnelle, dessin
     ferme, large palette de graisses. Inter pour le texte courant :
     hauteur d'x généreuse, lisible en petit corps et sur écran
     de faible densité.

     Aucune police à chasse fixe n'est téléchargée. Une chasse
     fixe ne porte aucune identité : celle du système suffit, et
     son absence économise un fichier entier sur le chemin
     critique.
     --------------------------------------------------------- */
  --police-titre: "Archivo", "Segoe UI", system-ui, -apple-system, sans-serif;
  --police-texte: "Inter", "Segoe UI", system-ui, -apple-system, sans-serif;
  --police-mono:  ui-monospace, "Cascadia Mono", "Segoe UI Mono", Consolas,
                  "Liberation Mono", Menlo, monospace;

  /* Échelle fluide. Les bornes correspondent à 360 px et 1280 px
     de largeur de fenêtre. */
  --taille-2xs: 0.6875rem;                              /* 11px */
  --taille-xs:  0.75rem;                                /* 12px */
  --taille-sm:  0.8125rem;                              /* 13px */
  --taille-md:  0.9375rem;                              /* 15px — corps */
  --taille-lg:  1.0625rem;                              /* 17px */
  --taille-h6:  clamp(0.9375rem, 0.90rem + 0.16vw, 1rem);
  --taille-h5:  clamp(1.0625rem, 1.01rem + 0.25vw, 1.1875rem);
  --taille-h4:  clamp(1.1875rem, 1.10rem + 0.39vw, 1.4375rem);
  --taille-h3:  clamp(1.375rem, 1.23rem + 0.65vw, 1.8125rem);
  --taille-h2:  clamp(1.625rem, 1.36rem + 1.17vw, 2.375rem);
  --taille-h1:  clamp(2rem, 1.54rem + 2.04vw, 3.3125rem);

  --graisse-normale:  400;
  --graisse-moyenne:  500;
  --graisse-demi:     600;
  --graisse-grasse:   700;
  --graisse-noire:    800;

  --interligne-serre:  1.15;
  --interligne-titre:  1.25;
  --interligne-normal: 1.6;
  --interligne-large:  1.75;

  --approche-titre: -0.02em;
  --approche-texte: 0;
  --approche-capitale: 0.08em;

  /* Longueur de ligne de confort pour le texte courant */
  --mesure: 68ch;

  /* ---------------------------------------------------------
     3. ESPACEMENT — base 4 px
     --------------------------------------------------------- */
  --espace-0:  0;
  --espace-1:  0.25rem;   /*  4px */
  --espace-2:  0.5rem;    /*  8px */
  --espace-3:  0.75rem;   /* 12px */
  --espace-4:  1rem;      /* 16px */
  --espace-5:  1.25rem;   /* 20px */
  --espace-6:  1.5rem;    /* 24px */
  --espace-8:  2rem;      /* 32px */
  --espace-10: 2.5rem;    /* 40px */
  --espace-12: 3rem;      /* 48px */
  --espace-16: 4rem;      /* 64px */
  --espace-20: 5rem;      /* 80px */
  --espace-24: 6rem;      /* 96px */

  /* Respiration verticale des sections, fluide */
  --section-y: clamp(2.5rem, 1.6rem + 4vw, 5rem);

  /* ---------------------------------------------------------
     4. FORMES ET PROFONDEUR
     --------------------------------------------------------- */
  --rayon-xs:   2px;
  --rayon-sm:   4px;
  --rayon-md:   6px;
  --rayon-lg:   10px;
  --rayon-xl:   16px;
  --rayon-2xl:  24px;
  --rayon-plein: 999px;

  /* Ombres teintées chaud, cohérentes avec les neutres */
  --ombre-xs: 0 1px 2px rgba(33, 29, 26, 0.06);
  --ombre-sm: 0 1px 3px rgba(33, 29, 26, 0.08), 0 1px 2px rgba(33, 29, 26, 0.04);
  --ombre-md: 0 4px 12px rgba(33, 29, 26, 0.08), 0 1px 3px rgba(33, 29, 26, 0.05);
  --ombre-lg: 0 12px 28px rgba(33, 29, 26, 0.10), 0 4px 8px rgba(33, 29, 26, 0.05);
  --ombre-xl: 0 24px 56px rgba(33, 29, 26, 0.14), 0 8px 16px rgba(33, 29, 26, 0.06);
  --ombre-interne: inset 0 1px 2px rgba(33, 29, 26, 0.07);

  /* Épaisseurs de trait */
  --trait-fin:    1px;
  --trait-moyen:  2px;
  --trait-epais:  3px;

  /* Étoile à cinq branches du drapeau.

     C'est le seul endroit du système où une couleur est inscrite
     dans une forme plutôt que consommée par un rôle. L'étoile du
     drapeau est d'or, toujours : elle ne suit ni le thème ni le
     contexte, et une variante sombre n'aurait aucun sens pour un
     symbole national. La figer permet de la poser en couche de
     fond, donc de composer un drapeau complet dans un seul
     élément — y compris dans un pseudo-élément, où l'on ne peut
     rien superposer. */
  --etoile-or: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 95'%3E%3Cpath fill='%23ffed00' d='M50 0 61.8 36.3 100 36.3 69.1 58.7 80.9 95 50 72.6 19.1 95 30.9 58.7 0 36.3 38.2 36.3Z'/%3E%3C/svg%3E");

  /* ---------------------------------------------------------
     5. MOUVEMENT
     --------------------------------------------------------- */
  --duree-instant: 80ms;
  --duree-courte:  140ms;
  --duree-moyenne: 220ms;
  --duree-longue:  360ms;

  --courbe-sortie:  cubic-bezier(0.16, 0.84, 0.44, 1);
  --courbe-entree:  cubic-bezier(0.55, 0, 1, 0.45);
  --courbe-douce:   cubic-bezier(0.4, 0, 0.2, 1);

  /* ---------------------------------------------------------
     6. PLANS DE SUPERPOSITION
     --------------------------------------------------------- */
  --plan-base:     0;
  --plan-sticky:   100;
  --plan-entete:   200;
  --plan-menu:     300;
  --plan-voile:    400;
  --plan-modale:   410;
  --plan-infobulle: 500;
  --plan-alerte:   600;

  /* ---------------------------------------------------------
     7. GABARIT
     --------------------------------------------------------- */
  --largeur-contenu:  1200px;
  --largeur-texte:    760px;
  --largeur-etroite:  560px;
  --gouttiere:  clamp(1rem, 0.6rem + 1.8vw, 2rem);
  --colonnes: 12;

  /* Cible tactile minimale imposée par la charte */
  --cible-min: 44px;

  /* ---------------------------------------------------------
     8. RÔLES SÉMANTIQUES — thème clair

     Les composants ne référencent que ces variables. Changer de
     thème consiste à les réaffecter, jamais à retoucher un
     composant.
     --------------------------------------------------------- */

  /* Surfaces */
  --surface:            var(--neutre-0);
  --surface-basse:      var(--neutre-25);
  --surface-enfoncee:   var(--neutre-50);
  --surface-haute:      var(--neutre-0);
  --surface-inverse:    var(--neutre-900);
  --surface-voile:      rgba(33, 29, 26, 0.55);

  /* Textes */
  --texte:              var(--neutre-900);
  --texte-attenue:      var(--neutre-600);
  /* 550 et non 500 : le texte faible sert aux mentions en petit
     corps, où le seuil de 4,5:1 s'applique pleinement. Le palier 500
     n'atteignait que 4,16:1 sur blanc. */
  --texte-faible:       var(--neutre-550);
  --texte-inverse:      var(--neutre-0);
  --texte-sur-couleur:  var(--neutre-0);

  /* Bordures */
  --bordure:            var(--neutre-200);
  /* Le contour d'un champ, d'une case à cocher ou d'une zone de
     dépôt est la seule chose qui en signale l'existence : il relève
     du contraste non textuel, à 3:1. Le palier 300 n'atteignait que
     1,66:1. --bordure reste réservée aux séparations décoratives,
     qui n'entrent pas dans le critère. */
  --bordure-forte:      var(--neutre-450);
  --bordure-inverse:    var(--neutre-700);

  /* Action principale — vert du drapeau */
  --action:             var(--vert-500);
  --action-survol:      var(--vert-600);
  --action-active:      var(--vert-700);
  --action-tenue:       var(--vert-50);
  --action-texte:       var(--neutre-0);
  --action-sur-clair:   var(--vert-700);

  /* Accent — rouge du drapeau, réservé au registre grave */
  --accent:             var(--rouge-500);
  /* Le texte posé sur l'accent a son propre jeton : en thème sombre
     l'accent s'éclaircit, et le blanc n'y tient plus que 3,13:1. */
  --accent-texte:       var(--neutre-0);
  --accent-survol:      var(--rouge-600);
  --accent-actif:       var(--rouge-700);
  --accent-tenue:       var(--rouge-50);
  --accent-sur-clair:   var(--rouge-700);

  /* Mise en avant — or du drapeau. Jamais de texte blanc dessus. */
  --relief:             var(--or-300);
  --relief-survol:      var(--or-400);
  --relief-texte:       var(--neutre-900);
  --relief-sur-clair:   var(--or-700);

  /* Registres d'état */
  --succes:        var(--vert-500);
  --succes-fond:   var(--vert-50);
  --succes-bord:   var(--vert-200);
  --succes-texte:  var(--vert-700);

  --alerte:        var(--ocre-500);
  --alerte-fond:   var(--ocre-50);
  --alerte-bord:   var(--ocre-100);
  --alerte-texte:  var(--ocre-700);

  --danger:        var(--rouge-500);
  --danger-fond:   var(--rouge-50);
  --danger-bord:   var(--rouge-200);
  --danger-texte:  var(--rouge-700);

  --info:          var(--bleu-500);
  --info-fond:     var(--bleu-50);
  --info-bord:     var(--bleu-100);
  --info-texte:    var(--bleu-700);

  /* Séries de données.

     Ordonnées par écart de luminosité et non par teinte : un
     graphique doit rester lisible en niveaux de gris et pour un
     daltonien. La couleur ne distingue jamais seule une série,
     elle accompagne toujours une étiquette posée sur la donnée. */
  --serie-1: var(--vert-600);
  --serie-2: var(--or-400);
  --serie-3: var(--rouge-700);
  --serie-4: var(--neutre-400);
  --serie-5: var(--vert-300);
  --serie-6: var(--bleu-700);

  /* Focus — le halo doit rester perceptible sur toutes les
     surfaces, y compris colorées. */
  --focus-couleur: var(--vert-600);
  --focus-halo:    0 0 0 3px var(--neutre-0), 0 0 0 5px var(--vert-600);
  --focus-trait:   var(--trait-moyen);

  /* Liens */
  --lien:          var(--vert-700);
  --lien-survol:   var(--vert-800);
  --lien-visite:   var(--vert-900);

  color-scheme: light;
}

/* =============================================================
   THÈME SOMBRE

   Appliqué par attribut (choix explicite de l'usager) et par
   préférence système lorsque aucun choix n'a été exprimé.
   Les teintes du drapeau sont éclaircies d'un palier : les
   valeurs officielles perdent trop de contraste sur fond noir
   pour porter du texte.
   ============================================================= */

:root[data-theme="dark"] {
  --surface:            var(--neutre-950);
  --surface-basse:      #191512;
  --surface-enfoncee:   #1f1a17;
  --surface-haute:      var(--neutre-900);
  --surface-inverse:    var(--neutre-50);
  --surface-voile:      rgba(0, 0, 0, 0.7);

  --texte:              #f2efec;
  --texte-attenue:      #b3aca4;
  --texte-faible:       #8d857d;
  --texte-inverse:      var(--neutre-950);
  --texte-sur-couleur:  var(--neutre-0);

  --bordure:            #332e2a;
  /* Même exigence en thème sombre, où l'ancien #4a443e ne donnait
     que 1,96:1 sur la surface. */
  --bordure-forte:      var(--neutre-550);
  --bordure-inverse:    var(--neutre-300);

  --action:             var(--vert-400);
  --action-survol:      var(--vert-300);
  --action-active:      var(--vert-200);
  --action-tenue:       rgba(62, 164, 109, 0.14);
  --action-texte:       #04211a;
  --action-sur-clair:   var(--vert-300);

  --accent:             var(--rouge-400);
  --accent-texte:       var(--neutre-950);
  --accent-survol:      var(--rouge-300);
  --accent-actif:       var(--rouge-200);
  --accent-tenue:       rgba(240, 99, 110, 0.14);
  --accent-sur-clair:   var(--rouge-300);

  --relief:             var(--or-300);
  --relief-survol:      var(--or-200);
  --relief-texte:       var(--neutre-950);
  --relief-sur-clair:   var(--or-200);

  --succes:        var(--vert-400);
  --succes-fond:   rgba(62, 164, 109, 0.12);
  --succes-bord:   rgba(62, 164, 109, 0.32);
  --succes-texte:  var(--vert-200);

  --alerte:        #d9a441;
  --alerte-fond:   rgba(217, 164, 65, 0.12);
  --alerte-bord:   rgba(217, 164, 65, 0.32);
  --alerte-texte:  #edc57d;

  --danger:        var(--rouge-400);
  --danger-fond:   rgba(240, 99, 110, 0.12);
  --danger-bord:   rgba(240, 99, 110, 0.32);
  --danger-texte:  var(--rouge-200);

  --info:          var(--bleu-300);
  --info-fond:     rgba(123, 165, 196, 0.12);
  --info-bord:     rgba(123, 165, 196, 0.30);
  --info-texte:    #b7d2e4;

  --focus-couleur: var(--vert-300);
  --focus-halo:    0 0 0 3px var(--neutre-950), 0 0 0 5px var(--vert-300);

  --lien:          var(--vert-300);
  --lien-survol:   var(--vert-200);
  --lien-visite:   #9fd4b6;

  --ombre-xs: 0 1px 2px rgba(0, 0, 0, 0.4);
  --ombre-sm: 0 1px 3px rgba(0, 0, 0, 0.5), 0 1px 2px rgba(0, 0, 0, 0.3);
  --ombre-md: 0 4px 12px rgba(0, 0, 0, 0.5), 0 1px 3px rgba(0, 0, 0, 0.35);
  --ombre-lg: 0 12px 28px rgba(0, 0, 0, 0.55), 0 4px 8px rgba(0, 0, 0, 0.35);
  --ombre-xl: 0 24px 56px rgba(0, 0, 0, 0.6), 0 8px 16px rgba(0, 0, 0, 0.4);

  color-scheme: dark;
}

/* Le thème clair est le thème par défaut, quelle que soit la
   préférence du système d'exploitation. Le thème sombre reste
   disponible, mais uniquement sur choix explicite de l'usager
   (attribut data-theme="dark" posé par faso.js). */

/* Contraste renforcé : on durcit bordures et focus plutôt que de
   modifier la palette, pour ne pas dénaturer les teintes. */
@media (prefers-contrast: more) {
  :root {
    --bordure:       var(--neutre-400);
    --bordure-forte: var(--neutre-600);
    --texte-attenue: var(--neutre-700);
    --trait-fin:     2px;
  }
}

@media (prefers-reduced-motion: reduce) {
  :root {
    --duree-instant: 1ms;
    --duree-courte:  1ms;
    --duree-moyenne: 1ms;
    --duree-longue:  1ms;
  }
}
```

---

## Annexe B — Classes CSS disponibles

Les seules classes de la charte (`faso.css`, `icones.css`). Une classe absente de cette liste n'existe pas : ne pas l'inventer.

- `fs-accordeon`
- `fs-accordeon-corps`
- `fs-actions`
- `fs-actions--fin`
- `fs-affixe`
- `fs-aide`
- `fs-ariane`
- `fs-attenue`
- `fs-auto-grille`
- `fs-autorite`
- `fs-autorite-contacts`
- `fs-autorite-nom`
- `fs-autorite-tutelle`
- `fs-avatar`
- `fs-avatar--lg`
- `fs-avatar--sm`
- `fs-avatars`
- `fs-avec-aside`
- `fs-avec-aside--gauche`
- `fs-badge`
- `fs-badge--alerte`
- `fs-badge--danger`
- `fs-badge--info`
- `fs-badge--neutre`
- `fs-badge--relief`
- `fs-badge--succes`
- `fs-bandeau`
- `fs-bandeau-etiquette`
- `fs-barre`
- `fs-barre--s2`
- `fs-barre--s3`
- `fs-barre--s4`
- `fs-barre--s5`
- `fs-barre--s6`
- `fs-barre-libelle`
- `fs-barre-piste`
- `fs-barre-remplissage`
- `fs-barre-service`
- `fs-barre-valeur`
- `fs-barres`
- `fs-btn`
- `fs-btn--bloc`
- `fs-btn--contour-clair`
- `fs-btn--danger`
- `fs-btn--fantome`
- `fs-btn--icone`
- `fs-btn--inverse`
- `fs-btn--lg`
- `fs-btn--principal`
- `fs-btn--relief`
- `fs-btn--secondaire`
- `fs-btn--sm`
- `fs-btn--tertiaire`
- `fs-btn-groupe`
- `fs-calendrier`
- `fs-calendrier-entete`
- `fs-calendrier-legende`
- `fs-calendrier-mois`
- `fs-carte`
- `fs-carte--cliquable`
- `fs-carte--marquee`
- `fs-carte-corps`
- `fs-carte-media`
- `fs-carte-pied`
- `fs-carte-texte`
- `fs-carte-titre`
- `fs-centre`
- `fs-champ`
- `fs-chapeau`
- `fs-choix`
- `fs-choix--carte`
- `fs-choix-aide`
- `fs-choix-texte`
- `fs-choix-titre`
- `fs-chrono`
- `fs-chrono-date`
- `fs-chrono-titre`
- `fs-cle`
- `fs-code`
- `fs-col-1`
- `fs-col-10`
- `fs-col-11`
- `fs-col-12`
- `fs-col-2`
- `fs-col-3`
- `fs-col-4`
- `fs-col-5`
- `fs-col-6`
- `fs-col-7`
- `fs-col-8`
- `fs-col-9`
- `fs-col-md-12`
- `fs-col-md-6`
- `fs-combo`
- `fs-combo-liste`
- `fs-combo-option`
- `fs-combo-vide`
- `fs-compteur`
- `fs-consentement`
- `fs-conteneur`
- `fs-conteneur--etroit`
- `fs-conteneur--large`
- `fs-conteneur--texte`
- `fs-courbe`
- `fs-courbe-aire`
- `fs-courbe-axe`
- `fs-courbe-etiquette`
- `fs-courbe-grille`
- `fs-courbe-point`
- `fs-courbe-trace`
- `fs-curseur`
- `fs-date`
- `fs-date-annee`
- `fs-date-jour`
- `fs-date-mois`
- `fs-definitions`
- `fs-demarche`
- `fs-demarche-entete`
- `fs-demarche-meta`
- `fs-demarche-resume`
- `fs-demarche-titre`
- `fs-depot`
- `fs-depot-contraintes`
- `fs-depot-titre`
- `fs-dossier`
- `fs-dossier-cote`
- `fs-dossier-depot`
- `fs-dossier-entete`
- `fs-dossier-item`
- `fs-dossier-meta`
- `fs-dossier-objet`
- `fs-dossier-ref`
- `fs-dossier-reference`
- `fs-droite`
- `fs-ellipse`
- `fs-encart`
- `fs-encart--alerte`
- `fs-encart--danger`
- `fs-encart--info`
- `fs-encart--succes`
- `fs-encart-corps`
- `fs-encart-fermer`
- `fs-encart-marque`
- `fs-encart-titre`
- `fs-entete`
- `fs-entete--collant`
- `fs-entete-haut`
- `fs-entete-outils`
- `fs-etape`
- `fs-etape-num`
- `fs-etape-titre`
- `fs-etapes`
- `fs-etiquette`
- `fs-evenement`
- `fs-evenement-nb`
- `fs-evenement-titre`
- `fs-evitement`
- `fs-faible`
- `fs-fichier`
- `fs-fichier--apercu`
- `fs-fichier-actions`
- `fs-fichier-detail`
- `fs-fichier-nom`
- `fs-fichier-poids`
- `fs-fichier-vignette`
- `fs-fieldset`
- `fs-figure`
- `fs-figure--1-1`
- `fs-figure--16-9`
- `fs-figure--4-3`
- `fs-figure-credit`
- `fs-filet`
- `fs-filet--epais`
- `fs-filet--fin`
- `fs-filtres`
- `fs-filtres-actifs`
- `fs-filtres-actifs-titre`
- `fs-filtres-vider`
- `fs-fond-basse`
- `fs-fond-enfoncee`
- `fs-graphe`
- `fs-graphe-donnees`
- `fs-graphe-note`
- `fs-graphe-titre`
- `fs-grille`
- `fs-groupe-saisie`
- `fs-icone`
- `fs-icone--aide`
- `fs-icone--alerte`
- `fs-icone--bouclier`
- `fs-icone--cadenas`
- `fs-icone--calendrier`
- `fs-icone--carte-identite`
- `fs-icone--chevron-bas`
- `fs-icone--chevron-droite`
- `fs-icone--chevron-gauche`
- `fs-icone--chevron-haut`
- `fs-icone--coche`
- `fs-icone--courriel`
- `fs-icone--document`
- `fs-icone--dossier`
- `fs-icone--erreur`
- `fs-icone--fermer`
- `fs-icone--filtre`
- `fs-icone--fleche-droite`
- `fs-icone--horloge`
- `fs-icone--imprimer`
- `fs-icone--information`
- `fs-icone--institution`
- `fs-icone--lg`
- `fs-icone--lien-externe`
- `fs-icone--lieu`
- `fs-icone--menu`
- `fs-icone--oeil`
- `fs-icone--paiement`
- `fs-icone--recherche`
- `fs-icone--signalement`
- `fs-icone--sm`
- `fs-icone--succes`
- `fs-icone--telecharger`
- `fs-icone--telephone`
- `fs-icone--televerser`
- `fs-icone--tete`
- `fs-icone--trier`
- `fs-icone--utilisateur`
- `fs-icone--xl`
- `fs-icone--xxl`
- `fs-impression-entete`
- `fs-impression-meta`
- `fs-impression-titre`
- `fs-infobulle`
- `fs-infobulle-contenu`
- `fs-interrupteur`
- `fs-interrupteur-piste`
- `fs-invisible`
- `fs-jauge`
- `fs-jauge--danger`
- `fs-jauge--relief`
- `fs-jauge-valeur`
- `fs-jour`
- `fs-jour-dispo`
- `fs-label`
- `fs-legende`
- `fs-lien-discret`
- `fs-lien-externe`
- `fs-lien-fichier`
- `fs-liste-controle`
- `fs-liste-dossiers`
- `fs-liste-nue`
- `fs-liste-puces`
- `fs-liste-separee`
- `fs-marque`
- `fs-marque-devise`
- `fs-marque-entite`
- `fs-marque-pays`
- `fs-marque-texte`
- `fs-masque-bureau`
- `fs-masque-mobile`
- `fs-mb-0`
- `fs-menu`
- `fs-menu-liste`
- `fs-menu-separateur`
- `fs-message`
- `fs-message--erreur`
- `fs-message--succes`
- `fs-mesure`
- `fs-modale`
- `fs-modale--large`
- `fs-modale-corps`
- `fs-modale-entete`
- `fs-modale-pied`
- `fs-modale-titre`
- `fs-mt-0`
- `fs-mt-4`
- `fs-mt-6`
- `fs-mt-8`
- `fs-nav`
- `fs-nav-bascule`
- `fs-nav-lien`
- `fs-nav-liste`
- `fs-notification`
- `fs-notification-corps`
- `fs-notification-date`
- `fs-notification-icone`
- `fs-notification-puce`
- `fs-notification-texte`
- `fs-notification-titre`
- `fs-notifications`
- `fs-notifications-entete`
- `fs-notifications-liste`
- `fs-notifications-titre`
- `fs-nowrap`
- `fs-num`
- `fs-officiel`
- `fs-officiel-sceau`
- `fs-onglet`
- `fs-onglets`
- `fs-optionnel`
- `fs-pagination`
- `fs-paiement`
- `fs-paiement-lignes`
- `fs-paiement-total`
- `fs-paiement-total-libelle`
- `fs-paiement-total-montant`
- `fs-panneau`
- `fs-panneau-lateral`
- `fs-photo-cadre`
- `fs-photo-guide`
- `fs-photo-legende`
- `fs-photo-reperes`
- `fs-photo-silhouette`
- `fs-pieces`
- `fs-pieces-detail`
- `fs-pieces-nom`
- `fs-pieces-num`
- `fs-pied`
- `fs-pied-bas`
- `fs-pied-corps`
- `fs-pied-titre`
- `fs-pile`
- `fs-pile--large`
- `fs-pile--serree`
- `fs-poids`
- `fs-rangee`
- `fs-rangee--entre`
- `fs-rangee--fin`
- `fs-rangee--haut`
- `fs-recap-erreurs`
- `fs-recherche`
- `fs-repartition`
- `fs-repartition-legende`
- `fs-repere`
- `fs-repere--centre`
- `fs-requis`
- `fs-sablier`
- `fs-sablier--lg`
- `fs-saisie`
- `fs-section`
- `fs-segment`
- `fs-select`
- `fs-signalement`
- `fs-sommaire`
- `fs-sommaire-titre`
- `fs-squelette`
- `fs-squelette--bloc`
- `fs-squelette--texte`
- `fs-squelette--titre`
- `fs-stat`
- `fs-stat-libelle`
- `fs-stat-valeur`
- `fs-stat-variation`
- `fs-stat-variation--baisse`
- `fs-stat-variation--hausse`
- `fs-statut`
- `fs-statut--alerte`
- `fs-statut--attente`
- `fs-statut--cours`
- `fs-statut--rejet`
- `fs-statut--succes`
- `fs-surtitre`
- `fs-tableau`
- `fs-tableau--compact`
- `fs-tableau--empile`
- `fs-tableau--zebre`
- `fs-tableau-bloc`
- `fs-tableau-cadre`
- `fs-tableau-compte`
- `fs-tableau-export`
- `fs-tableau-intervalle`
- `fs-tableau-outils`
- `fs-tableau-pied`
- `fs-tableau-taille`
- `fs-tableau-vide`
- `fs-tableau-vide-titre`
- `fs-tabulaire`
- `fs-tissage`
- `fs-tissage--fin`
- `fs-tissage--vertical`
- `fs-toast`
- `fs-toast--alerte`
- `fs-toast--danger`
- `fs-toast--succes`
- `fs-toast-corps`
- `fs-toast-titre`
- `fs-toasts`
- `fs-valeur`
- `fs-vide`
- `fs-vide-titre`
- `fs-voile`
- `fs-zone`

---

## Annexe C — API JavaScript (`faso.d.ts`)

Les comportements de `faso.js`, importés par `import Faso from '@govbf/charte-graphique'`.

```ts
/**
 * Charte graphique de l'administration burkinabè
 * Déclarations de types pour faso.js
 *
 * Le fichier JavaScript est volontairement sans dépendance et sans
 * étape de compilation. Ces déclarations existent pour que les
 * projets en TypeScript — Angular en particulier — puissent
 * l'appeler en mode strict.
 */

/** Registre d'un message passager. */
export interface OptionsNotification {
  /** Première ligne, en gras. Facultative. */
  titre?: string;
  /** Corps du message. */
  texte?: string;
  /** Registre visuel. Sans valeur, le message est neutre. */
  ton?: "succes" | "alerte" | "danger";
  /**
   * Durée d'affichage en millisecondes. 6000 par défaut.
   * Une valeur nulle ou négative laisse le message jusqu'à
   * fermeture par l'usager.
   */
  duree?: number;
}

/**
 * Annonces émises par la charte.
 *
 * Chacune porte deux noms : `fs:<nom>` et `fs-<nom>`. Les deux
 * transportent le même détail et sont émises l'une après l'autre.
 * La forme à trait d'union existe parce qu'un gabarit d'Angular ne
 * peut pas lier un événement dont le nom contient deux-points :
 * `(fs:onglet)` y désignerait la cible « fs ».
 *
 * Les annonces annulables précèdent une écriture d'état.
 * `preventDefault()` empêche la charte d'écrire l'attribut, sans
 * rien lui retirer de ce qu'elle assure par ailleurs : clavier,
 * focus, ordre de tabulation, annonce vocale.
 */
export interface AnnoncesFaso {
  /** Avant `aria-selected`, `hidden` et `tabindex` d'un jeu d'onglets. Annulable. */
  "fs:onglet": CustomEvent<{ onglet: HTMLElement; index: number }>;
  /** Avant `data-ouvert` et `aria-expanded` d'un élément repliable. Annulable. */
  "fs:bascule": CustomEvent<{ ouvert: boolean }>;
  /** Avant la largeur et `aria-valuenow` d'une jauge. Annulable. */
  "fs:jauge": CustomEvent<{ valeur: number }>;
  /** Avant la largeur d'une barre d'un groupe. Annulable. */
  "fs:barre": CustomEvent<{ valeur: number; max: number }>;
  /** Avant `data-lu` sur une notification. Annulable. */
  "fs:lecture": CustomEvent<{ tout: boolean }>;
  /** Avant le retrait d'un encart du document. Annulable. */
  "fs:fermeture": CustomEvent<null>;
  /**
   * Après un tri ou un changement de filtre. Informe seulement.
   * `rejeu` distingue un re-rendu du cadriciel d'un changement
   * demandé par l'usager.
   */
  "fs:donnees": CustomEvent<{
    annoncer: boolean; rejeu: boolean; retenues: number;
  } | null>;
}

/**
 * Libellés produits par le script, et langue de mise en forme des
 * nombres et des dates.
 *
 * L'objet exposé par `Faso.textes` est celui que la bibliothèque lit :
 * on le complète, on ne le remplace pas.
 *
 *     Object.assign(Faso.textes, { pageSuivante: '…' });
 *
 * Les accolades délimitent les valeurs à insérer : `{n}`, `{page}`,
 * `{pages}`, `{debut}`, `{fin}`, `{total}`, `{date}`, `{filtres}`,
 * `{fichier}`, `{presentes}`. La marque `{s}` porte le pluriel.
 */
export interface TextesFaso {
  /** Étiquette BCP 47 passée à `toLocaleString`. « fr-FR » par défaut. */
  langue: string;
  /** Les douze mois, en minuscules, pour la date d'extraction. */
  mois: string[];
  [cle: string]: string | string[];
}

/**
 * Décision de consentement : une clé par finalité déclarée dans le
 * balisage par `data-finalite`, plus la date d'enregistrement.
 */
export interface DecisionConsentement {
  /** Jour de l'enregistrement, au format AAAA-MM-JJ. */
  date: string;
  [finalite: string]: boolean | string;
}

export interface Faso {
  /** Version du système, alignée sur celle du paquet. */
  readonly version: string;

  /**
   * Initialise les comportements liés élément par élément.
   *
   * Sans argument, parcourt le document entier. Avec un élément,
   * se limite à son sous-arbre — c'est la forme à employer depuis
   * un cadriciel, après le rendu d'un composant.
   *
   * L'opération est idempotente : un élément déjà initialisé est
   * ignoré, si bien qu'un appel répété n'empile pas les
   * gestionnaires. Sans DOM — rendu côté serveur — l'appel est
   * sans effet.
   */
  initialiser(racine?: ParentNode): void;

  /**
   * Active l'initialisation automatique des sous-arbres ajoutés
   * au document. Une fois appelée, plus rien n'est à faire après
   * chaque rendu.
   *
   * `observer(false)` la désactive. Renvoie l'état effectif :
   * `false` hors navigateur.
   */
  observer(actif?: boolean): boolean;

  /**
   * Affiche un message passager. Renvoie une fonction qui le
   * retire avant l'échéance.
   */
  notifier(options: OptionsNotification): () => void;

  /**
   * Compose un CSV à partir de données, et non du document.
   *
   * C'est la voie à suivre lorsque le cadriciel possède les
   * lignes : il fournit l'intégralité du jeu, la charte se charge
   * de l'échappement, du séparateur et des fins de ligne.
   */
  versCsv(entetes: string[], lignes: Array<Array<string | number>>): string;

  /**
   * Déclenche le téléchargement d'un CSV, avec l'indicateur
   * d'ordre des octets qu'attendent les tableurs.
   */
  telechargerCsv(nom: string, contenu: string): void;

  /**
   * Force un thème. `"dark"` ou `"light"` ; toute valeur vide
   * rétablit le thème clair, qui est le défaut du système.
   */
  appliquerTheme(valeur?: "dark" | "light" | null): void;

  /**
   * Libellés produits par le script. À compléter avant
   * l'initialisation, jamais à remplacer.
   */
  readonly textes: TextesFaso;

  /**
   * Consentement au dépôt de traceurs.
   *
   * La charte recueille la décision, la conserve six mois dans un
   * cookie et la restitue. Elle ne dépose ni ne retire aucun traceur :
   * cela appartient au service, qui écoute `fs:consentement`.
   */
  readonly consentement: {
    /**
     * La décision, ou `null` tant que l'usager n'a pas répondu.
     * `null` n'est pas un refus : c'est une absence de réponse, et
     * aucun traceur non essentiel ne doit être déposé dans cet état.
     */
    lire(): DecisionConsentement | null;
    /** Enregistre une décision et prévient le service. */
    definir(finalites: Record<string, boolean>): DecisionConsentement;
    /** Efface la décision : la question sera reposée. */
    oublier(): void;
  };

  /**
   * Comportements pris isolément. Exposés pour la documentation
   * et les tests ; un projet n'a normalement pas à les appeler.
   */
  readonly comportements: Readonly<Record<string, (racine?: ParentNode) => void>>;
}

declare const Faso: Faso;
export default Faso;

declare global {
  interface Window {
    /** Présent dès le chargement de faso.js dans un navigateur. */
    Faso: Faso;

    /**
     * Posé à `true` avant le chargement, empêche l'initialisation
     * automatique. L'application appelle alors `initialiser()` ou
     * `observer()` au moment qu'elle choisit — ce que réclame un
     * rendu côté serveur avec hydratation.
     */
    FASO_SANS_DEMARRAGE?: boolean;
  }
}
```
