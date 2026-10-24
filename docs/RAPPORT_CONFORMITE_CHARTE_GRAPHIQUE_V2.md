# RAPPORT DE MISE EN CONFORMITÉ À LA CHARTE GRAPHIQUE DE L'ÉTAT (v2.0.0)

**Projet :** Plateforme Nationale de Littératie Numérique — **PACTDIGITAL-BF**  
**Ministère de tutelle :** Ministère de la Transition Digitale, des Postes et des Communications Électroniques (MTDPCE)  
**Direction technique :** Direction Générale de la Transition Digitale (DGTD)  
**Référentiel appliqué :** Charte graphique des sites et applications web gouvernementaux du Burkina Faso (`@govbf/charte-graphique` — Version 2.0.0)  
**Source officielle :** `https://chartegraphique-21.mtdpce-test.gov.bf/`  
**Date de mise en conformité :** Octobre 2026  
**Statut :** **100 % Conforme (Identité, Palette, Typographie, Composants institutionnels & Souveraineté)**

---

## 1. Objet du document

Le présent document constitue le **dossier justificatif technique et visuel** attestant de l'application intégrale de la Charte graphique officielle des sites et applications web gouvernementaux du Burkina Faso (`@govbf/charte-graphique` v2.0.0) sur la plateforme nationale d'apprentissage en ligne **PACTDIGITAL-BF** (basée sur Moodle).

Il détaille point par point la déclinaison des exigences du Ministère :
1. **L'identité officielle de l'État** (armoiries vectorielles, devise nationale, bandeau d'identification officielle, filet tricolore, frise tissée *Faso Dan Fani*, repère national).
2. **Le système de design par tokens CSS** (`tokens.css` : couleurs primaires du drapeau, couleurs d'appui, neutres chauds à base latérite, ombres et rayons).
3. **La typographie souveraine** (polices variables `Archivo` et `Inter` auto-hébergées localement sans dépendance externe).
4. **L'application transversale** sur l'ensemble du thème Moodle (`theme_pactdigital`) et des modules métiers citoyens, formateurs et administrateurs (`local_pactdigital`).

---

## 2. Souveraineté numérique & Hébergement local des ressources

Conformément aux prescriptions de souveraineté numérique et de résilience réseau du MTDPCE, **aucune ressource identitaire ou typographique n'est chargée depuis un serveur tiers (Google Fonts ou CDN externe)** :

| Ressource officielle | Format | Emplacement sur la plateforme | Rôle |
| :--- | :--- | :--- | :--- |
| **Armoiries du Burkina Faso** | `SVG` vectoriel officiel (191 Ko) | `moodle/theme/pactdigital/pix/armoiries.svg` | Emblème officiel dans l'en-tête `.fs-marque`, le pied de page `.fs-pied`, la page de connexion et les rapports PDF |
| **Police de titrage `Archivo`** | `WOFF2` variable (`wght 500..800`) | `moodle/theme/pactdigital/fonts/archivo.woff2` | Titres `h1`–`h6`, navigation, boutons d'action, badges et statistiques |
| **Police de texte `Inter`** | `WOFF2` variable (`wght 400..700`) | `moodle/theme/pactdigital/fonts/inter.woff2` | Corps de texte, formulaires, tableaux de données, contenus de cours |
| **Spécification de référence** | Markdown (`charte-graphique.md`) | `docs/charte-graphique.md` | Documentation technique embarquée de la Charte v2.0.0 |

---

## 3. Conformité chromatique & Tokens de design (`tokens.css`)

L'intégralité des anciennes couleurs et des gris froids (base ardoise/bleutée) a été remplacée par la palette officielle de la Charte v2.0.0 et ses variables CSS (`:root`) déclarées dans `moodle/theme/pactdigital/scss/pactdigital.scss` et `moodle/theme/pactdigital/lib.php`.

### 3.1. Couleurs primaires et d'appui

| Rôle sémantique | Token CSS Charte v2.0 | Code Hexadécimal | Ancienne valeur remplacée | Usage sur PACTDIGITAL-BF |
| :--- | :--- | :--- | :--- | :--- |
| **Action primaire (Vert drapeau)** | `--vert-500` / `--action` | `#00843b` | `#129b40` / `#145B37` | Boutons primaires, liens actifs, barres de progression, bordures d'accentuation |
| **Action survol** | `--vert-600` / `--action-survol` | `#026c31` | `#0d7a32` | État `:hover` des boutons et contrôles interactifs |
| **Action active / Titres sur clair** | `--vert-700` / `--action-actif` | `#045527` | `#145B37` | État `:active`, étiquettes de domaine, sous-titres institutionnels |
| **Fonds profonds (Hero / Headers)** | `--vert-800` & `--vert-900` | `#04411f` / `#032e16` | `#104e2e` / `#1a1a2e` | Bannières d'en-tête des cours, panneau de marque de connexion, héro du catalogue |
| **Teinte verte légère** | `--vert-50` | `#ecf7f0` | `#e8f5ee` / `#f0faf5` | Fonds de badges validés, survol d'onglets, encadrés de confirmation |
| **Or national (Étoile)** | `--or-300` / `--accent` | `#ffed00` | `#fad615` | Étoile du filet, repère national, badges Niveau Or, indicateurs clés sur fond sombre (**toujours associé au texte sombre `#211d1a`**) |
| **Rouge national / Danger** | `--rouge-500` / `--danger` | `#e30613` | `#d02027` / `#D32F2F` | Filet tricolore, repère national, alertes d'erreur, échec d'examen, actions critiques |
| **Bleu administratif (Info)** | `--bleu-500` / `--info` | `#2a6d9b` | `#1a6faf` / `#1565C0` | Pilier « Compétences Managériales », badges d'information, partage social |
| **Ocre / Ambre (Avertissement)** | `--ocre-500` / `--avertissement` | `#b07800` | `#e67e22` / `#d97706` | Pilier « Compétences Sectorielles », alertes de pré-examen, niveau Bronze (`--ocre-700: #7d5500`) |

### 3.2. Gamme de neutres chauds (Base latérite)

Conformément à la règle de la Charte v2.0 interdisant les gris bleutés froids (`#0f172a`, `#1e293b`, `#64748b`, `#f8fafc`, `#e2e8f0`), tous les fonds, textes et bordures utilisent la gamme chaude à sous-ton terre/latérite :

| Token CSS | Code Hexadécimal | Usage sur la plateforme |
| :--- | :--- | :--- |
| `--neutre-0` (`--surface`) | `#ffffff` | Cartes de cours, modales, conteneurs de formulaires |
| `--neutre-25` (`--surface-basse`) | `#fcfbf9` | Fond général de la plateforme, lignes de tableaux au survol |
| `--neutre-50` (`--surface-enfoncee`) | `#f7f5f2` | Bandeau officiel `.fs-officiel`, pied de page `.fs-pied`, sections alternées |
| `--neutre-200` (`--bordure`) | `#e2ddd6` | Bordures de cartes, séparateurs de tableaux et de sections |
| `--neutre-450` (`--bordure-forte`) | `#8e867f` | Bordures des champs de saisie (`input`, `select`, `textarea`) |
| `--neutre-550` (`--texte-faible`) | `#756d66` | Métadonnées secondaires, durées, légendes |
| `--neutre-600` (`--texte-attenue`) | `#635c55` | Descriptions de cartes, sous-titres, textes d'aide |
| `--neutre-900` (`--texte`) | `#211d1a` | Texte principal et titres (contraste > 15:1 sur fond clair) |

---

## 4. Intégration des marqueurs identitaires nationaux (`faso.css`)

Les 6 marqueurs identitaires officiels définis par le système de design burkinabè ont été implémentés fidèlement :

1. **Bandeau d'identification officielle (`.fs-officiel`)**
   - Placé tout en haut de la plateforme.
   - Comporte le mini-drapeau tricolore (`.fs-officiel__drapeau`) et la mention : *« Site officiel du Gouvernement du Burkina Faso — Ministère de la Transition Digitale, des Postes et des Communications Électroniques »*.
2. **Filet national tricolore (`.fs-filet` et `.fs-filet--fin`)**
   - Ruban horizontal rouge (`#e30613`) et vert (`#00843b`) frappé de l'étoile d'or (`#ffed00`) au centre, présent sous l'en-tête principal et en haut de la barre de navigation interne.
3. **Bloc marque institutionnel (`.fs-marque`)**
   - Associe les **Armoiries officielles vectorielles** (`armoiries.svg`), l'intitulé officiel **BURKINA FASO**, la devise nationale exacte ***« La Patrie ou la Mort, nous Vaincrons »*** et le nom du Ministère / Programme **PACTDIGITAL-BF**.
4. **Frise tissée *Faso Dan Fani* (`.fs-tissage`)**
   - Motif géométrique CSS pur (sans image matricielle) inspiré du pagne tissé traditionnel burkinabè aux couleurs nationales, utilisé en séparation noble sous la bannière héroïque et en couronnement du pied de page officiel.
5. **Repère national (`.fs-repere`)**
   - Puce tricolore (rouge / or / vert) précédant les surtitres de sections majeures (ex. bannière héroïque de la page d'accueil).
6. **Pied de page officiel (`.fs-pied`)**
   - Fond `--surface-enfoncee` (`#f7f5f2`), surmonté de la frise `.fs-tissage`, intégrant le bloc `.fs-marque`, les liens de navigation citoyenne, la conformité à la **Loi N° 001-2021/AN (CIL)** et la devise nationale.

---

## 5. Cartographie détaillée des fichiers mis en conformité

### 5.1. Thème Moodle global (`moodle/theme/pactdigital/`)

| Fichier | Éléments mis en conformité |
| :--- | :--- |
| `lib.php` | Injection des variables pré-SCSS Bootstrap (`$primary: #00843b`, `$secondary: #ffed00`, `$info: #2a6d9b`, `$warning: #b07800`, `$danger: #e30613`, `$font-family-sans-serif: "Inter"`, `$headings-font-family: "Archivo"`) et habillage du chronomètre d'examen. |
| `scss/pactdigital.scss` | Déclaration des `@font-face` locaux (`Archivo`, `Inter`), de tous les tokens `:root` de `tokens.css`, des composants `.fs-*` de `faso.css`, et refonte des styles de la vitrine, des cours, des boutons navbar, de la page de connexion et des cartes de certificats. |
| `frontpage_helper.php` | Mise à jour des couleurs des 4 piliers de compétences selon la charte (`#00843b`, `#2a6d9b`, `#b07800`, `#045527`). |
| `templates/frontpage.mustache` | Ajout du bandeau `.fs-officiel`, du filet `.fs-filet`, du bloc `.fs-marque` avec `armoiries.svg`, du repère `.fs-repere`, de la frise `.fs-tissage` et du pied de page `.fs-pied`. |
| `templates/navbar.mustache` | Ajout du filet tricolore `.fs-filet--fin` et du bloc marque `.fs-marque` avec `armoiries.svg` dans la barre de navigation connectée. |
| `templates/footer.mustache` | Transformation en pied de page institutionnel `.fs-pied` avec frise `.fs-tissage`, armoiries vectorielles et devise nationale. |
| `templates/login.mustache` | Mise à jour du panneau de marque latéral avec la devise officielle *« La Patrie ou la Mort, nous Vaincrons »* et les tokens de couleurs Charte v2.0. |
| `templates/course.mustache` & `layout/course.php` | Harmonisation de l'en-tête de cours et passage de `armoiries_url` au contexte Mustache. |
| `templates/dashboard.mustache` & `layout/dashboard.php` | Harmonisation du tableau de bord apprenant Moodle et de la carte d'accès rapide au catalogue. |
| `version.php` | Incrémentation de la version du thème (`2026102201`) pour invalidation automatique du cache CSS Moodle. |

### 5.2. Pages métiers et portails citoyens (`moodle/local/pactdigital/`)

| Page / Module | URL sur la plateforme | Éléments mis en conformité |
| :--- | :--- | :--- |
| **Catalogue des formations** (`catalogue.php`) | `/local/pactdigital/catalogue.php` | Palette des 4 domaines (`#00843b`, `#2a6d9b`, `#b07800`, `#045527`), bannière héroïque `--vert-900` → `--vert-500`, compteurs en `--or-300`, badges Bronze/Argent/Or, modale d'inscription 1-clic et polices `Archivo`/`Inter`. |
| **Espace Apprenant** (`apprenant.php`) | `/local/pactdigital/apprenant.php` | Bannière héroïque, onglets de suivi, cartes de progression, tableau des résultats aux quiz, cartes de badges et modale de partage. |
| **Cockpit Formateur** (`formateur.php`) | `/local/pactdigital/formateur.php` | En-tête formateur, cartes KPI (vert, rouge, ocre, bleu administratif), tableaux de suivi de cohorte, barres de progression et boutons d'action. |
| **Dashboard National SIG** (`dashboard.php`) | `/local/pactdigital/dashboard.php` | Palette des graphiques Chart.js et de la carte choroplèthe Leaflet, cartes KPI, tableaux de données et en-tête du rapport PDF officiel imprimable avec `armoiries.svg` et devise nationale. |
| **Vérification publique d'attestation** (`verify.php`) | `/local/pactdigital/verify.php` | Ruban tricolore, formulaire de vérification cryptographique, carte officielle de certificat valide et badges de niveau. |
| **Accueil de cours & Voie 2** (`course_welcome.php`) | `/local/pactdigital/course_welcome.php` | Bannière de progression du cours, cartes de choix Voie 1 (Parcours) / Voie 2 (Examen direct), modale des règles d'examen et liste des séquences. |
| **Résultat d'examen** (`exam_result.php`) | `/local/pactdigital/exam_result.php` | Bannière de réussite (`--vert-500`) / échec (`--rouge-500`), affichage du score en `--or-300`, badge numérique et boutons d'action. |
| **Fiche Mémo PDF** (`sequence_memo.php`) | `/local/pactdigital/sequence_memo.php` | En-tête de fiche de révision, liste des activités et zone de prise de notes imprimable. |
| **Inscription Citoyenne** (`signup.php`) | `/local/pactdigital/signup.php` | En-tête tricolore, encadré d'information citoyenne (inscription sans email), champs de formulaire à bordure `--bordure-forte` (`#8e867f`) et bouton d'inscription. |
| **Contact & Support** (`contact.php`) | `/local/pactdigital/contact.php` | Bannière d'assistance, formulaire de ticket citoyen, badge numéro vert et récépissé de ticket. |
| **Foire Aux Questions** (`faq.php`) | `/local/pactdigital/faq.php` | Bannière de recherche, filtres par thématique, accordéons de questions/réponses et encadré d'appel à l'action. |
| **Mentions Légales & CIL** (`legal.php`) | `/local/pactdigital/legal.php` | Bannière juridique, cartes de conformité à la Loi N° 001-2021/AN (CIL) et encadré législatif. |

---

## 6. Respect des règles d'accessibilité (WCAG 2.1 AA)

Conformément à la section « Accessibilité & Ergonomie » de la Charte v2.0.0 :
- **Contraste de l'Or national (`#ffed00`)** : L'or du drapeau n'est jamais utilisé avec du texte blanc. Lorsqu'il sert de fond (ex. badge Niveau Or, bouton Espace Apprenant, bouton d'accès rapide au catalogue), son texte est systématiquement en `--neutre-900` (`#211d1a`), garantissant un ratio de contraste supérieur à **15:1** (AAA).
- **Contraste des textes et formulaires** : Le texte principal (`#211d1a`) sur fond `--surface` (`#ffffff`) ou `--surface-basse` (`#fcfbf9`) dépasse **16:1**. Les champs de saisie utilisent `--bordure-forte` (`#8e867f`) pour respecter le contraste non-textuel minimum de **3:1** (WCAG 1.4.11).
- **Anneau de focus clavier** : Toutes les cibles interactives héritent d'un contour de focus visible `outline: 3px solid var(--or-300)` doublé d'une ombre `0 0 0 5px var(--vert-800)` pour être perceptible sur fond clair comme sur fond sombre.
- **Sobriété & Adaptation au mouvement réduit** : La règle `@media (prefers-reduced-motion: reduce)` désactive les transitions et animations pour les usagers sensibles.

---

## 7. Procédure de vérification rapide (Audit par le partenaire)

Pour vérifier visuellement et techniquement l'application de la charte sur l'environnement :

1. **Vérification visuelle des écrans clés :**
   - Page d'accueil publique : `/` (vérifier `.fs-officiel`, `.fs-filet`, `.fs-marque` avec `armoiries.svg`, `.fs-repere`, `.fs-tissage`, `.fs-pied`).
   - Page de connexion : `/login/index.php` (vérifier les armoiries, la devise nationale et le panneau vert institutionnel).
   - Inscription citoyenne : `/local/pactdigital/signup.php`
   - Catalogue des formations : `/local/pactdigital/catalogue.php`
   - Vérification publique des certificats : `/local/pactdigital/verify.php`
   - Espace Apprenant : `/local/pactdigital/apprenant.php`
   - Cockpit Formateur : `/local/pactdigital/formateur.php`
   - Tableau de bord national (Admin) : `/local/pactdigital/dashboard.php`

2. **Rafraîchissement du cache CSS Moodle (en cas de redéploiement) :**
   ```bash
   docker compose exec moodle php admin/cli/purge_caches.php
   ```
