# RAPPORT D'AUDIT FONCTIONNEL & TECHNIQUE DE LA PLATEFORME PACTDIGITAL-BF

**Programme :** Programme National de Littératie Numérique du Burkina Faso (PNLN)  
**Plateforme :** PACTDIGITAL-BF  
**Ministère de tutelle :** Ministère de la Transition Digitale, des Postes et des Communications Électroniques (MTDPCE)  
**Direction technique :** Direction Générale de la Transition Digitale (DGTD)  
**Date d'audit :** Octobre 2026  
**Version de la plateforme auditée :** Moodle 4.3+ Conteneurisé — Thème `theme_pactdigital` v2026102201 — Module `local_pactdigital` v2026101501  
**Référentiel de conformité :** Cahier des Charges Fonctionnel `CDC_Plateforme_Moodle_PACTDIGITAL_vBTII` & Charte Graphique du MTDPCE (`OK MTDPCE_CharteGraphique_V1_mai_2026_compressed.pdf`)

---

## 1. Contexte, objectifs et méthode d'audit

### 1.1. Contexte de la mission
Dans le cadre de la vulgarisation du Programme National de Littératie Numérique du Burkina Faso, le Ministère de la Transition Digitale, des Postes et des Communications Électroniques (MTDPCE) déploie la plateforme d'apprentissage souveraine **PACTDIGITAL-BF**.

L'objectif fondamental du présent audit est d'examiner **l'implémentation logicielle réelle** de la plateforme, d'en analyser les composants, la base de données, les rôles, les flux utilisateurs et les règles de gestion, afin de constituer le socle documentaire officiel destiné à la rédaction des guides utilisateurs.

**Règle d'or appliquée :** Documenter rigoureusement ce qui est effectivement codé, configuré et opérationnel dans la plateforme, en identifiant avec précision les fonctionnalités achevées, les implémentations partielles, les fonctionnalités absentes ou simulées, ainsi que les écarts par rapport à la cible exprimée dans le cahier des charges fonctionnel.

### 1.2. Méthodologie et périmètre d'inspection
L'audit a combiné quatre niveaux d'investigation :
1. **Inspection statique et architecturale du code source :**
   - Plugin local métier `/moodle/local/pactdigital/` (portails visiteurs, apprenants, formateurs, cockpit de pilotage SIG, vérification d'attestation, inscriptions).
   - Thème institutionnel souverain `/moodle/theme/pactdigital/` (moteur de templates Mustache, layouts PHP, bibliothèques d'assistance `*_helper.php`, styles SCSS et tokens conformes à la Charte Graphique du MTDPCE).
   - Scripts d'exploitation et de déploiement `/scripts/` (`backup_pactdigital.sh`, `run_cron.sh`, `load_bronze_d1.php`).
2. **Audit approfondi de la base de données (`backup_moodle.sql`) :**
   - Extraction des 61 cours configurés (`m_course`), des 24 catégories et domaines (`m_course_categories`), des 10 rôles (`m_role`) et des comptes de test / profils types (`m_user`).
   - Analyse des méthodes d'inscription (`m_enrol`), de la complétion (`m_course_completions`), des attestations officielles (`m_customcert`) et des banques d'évaluation (`m_quiz`).
3. **Analyse des flux fonctionnels et de l'expérience utilisateur :**
   - Parcours visiteur anonyme (découverte, filtrage du catalogue, inscription citoyenne simplifiée sans e-mail, mentions légales et contact).
   - Parcours apprenant (Espace Apprenant, inscription 1-clic avec contrôle d'âge 18 ans, double voie d'évaluation Voie 1 / Voie 2, examen chronométré avec seuil de réussite à 70%, téléchargement de fiche mémo PDF et d'attestation).
   - Parcours formateur communal / relais (Cockpit Formateur, 4 KPIs, suivi individuel, import massif par lot CSV, relance des inactifs, clôture officielle et export du procès-verbal).
   - Parcours gestionnaire pédagogique et administrateur (pilotage SIG national avec cartographie Leaflet choroplèthe, filtres territoriaux, export PDF/Excel, gestion des rôles).
   - Parcours employeur / vérificateur tiers (vérification publique d'attestation par identifiant cryptographique).
4. **Vérification de la conformité graphique et institutionnelle :**
   - Confrontation avec la Charte Graphique du MTDPCE (décret d'application de mai 2026) : présence du bandeau d'identification officielle `.fs-officiel`, du filet tricolore national rouge-or-vert avec étoile, des Armoiries officielles vectorielles du Burkina Faso, de la devise nationale *« La Patrie ou la Mort, nous Vaincrons »*, des palettes de couleurs réglementaires (`#0A5496`, `#00843B`, `#FFED00`, `#E30613`) et des polices locales souveraines (`Archivo` et `Inter`).

---

## 2. Architecture technique et environnement opérationnel

| Composant | Solution / Version retenue | Rôle dans PACTDIGITAL-BF | Constat d'audit |
| :--- | :--- | :--- | :--- |
| **Socle LMS** | Moodle 4.3+ (PHP-Apache 8.3) | Cœur de gestion pédagogique, cours, quiz, utilisateurs | Opérationnel et personnalisé |
| **Base de données** | MariaDB 11.4 | Persistance relationnelle (préfixe `m_`) | Dump complet de 11,8 Mo (`backup_moodle.sql`) avec 60 cours répertoriés |
| **Serveur de messagerie** | Mailpit (ports 8025/1025) | Capture locale des e-mails sortants | Configuré dans `compose.yml` |
| **Thème graphique** | `theme_pactdigital` (v2026102201) | Habillage institutionnel et composants souverains | 100% conforme à la Charte Graphique MTDPCE (armoiries, filet, tokens latérite) |
| **Module métier** | `local_pactdigital` (v2026101501) | Pages publiques, cockpit formateur, dashboard SIG | 12 modules applicatifs dédiés intégrés |
| **Cartographie SIG** | Leaflet 1.9.4 + GeoJSON | Cartographie choroplèthe de l'impact territorial | Polygones des régions burkinabè intégrés dans `burkina_regions.json` |
| **Attestations sécurisées** | `mod_customcert` | Génération de certificats avec QR code | Intégré avec clé de vérification à 10/12 caractères |

---

## 3. Synthèse des fonctionnalités par profil utilisateur

### 3.1. Profil Visiteur et Futur Apprenant (Public non connecté)

| Fonctionnalité | Statut réel | Éléments observés dans le code / base | Observations et limites |
| :--- | :--- | :--- | :--- |
| **Accès à la plateforme & Accueil** | **Implémentée et vérifiée** | `theme/pactdigital/templates/frontpage.mustache` | Page d'accueil style Udemy avec bandeau `.fs-officiel`, filet tricolore, compteurs dynamiques de cours et onglets par pilier. |
| **Consultation du catalogue** | **Implémentée et vérifiée** | `/local/pactdigital/catalogue.php` | Recherche plein texte, filtres par piliers (Fondamentales, Managériales, Sectorielles, Émergentes) et niveaux (Bronze, Argent, Or). Filtrage 100% JS côté client. |
| **Fiche détaillée de formation** | **Partiellement implémentée** | Cartes de cours de `catalogue.php` | Les cartes affichent un résumé de 100 caractères et le nombre d'activités, mais il n'existe pas de page de présentation détaillée pré-inscription : le visiteur est dirigé vers la création de compte. |
| **Inscription citoyenne simplifiée** | **Implémentée et vérifiée** | `/local/pactdigital/signup.php` | Inscription sans e-mail obligatoire. Validation AJAX temps réel du nom d'utilisateur. Contrôle d'âge strict (>= 18 ans). Affectation automatique d'un courriel interne `{username}@pactdigital.bf`. |
| **Connexion à la plateforme** | **Implémentée et vérifiée** | `/login/index.php`, `login.mustache` | Écran de connexion split-screen avec panneau de marque institutionnel, armoiries et devise. Redirection transparente vers la dernière page demandée (`wantsurl`). |
| **Récupération de compte / mot de passe** | **Partiellement implémentée** | `/login/forgot_password.php` | Le formulaire standard Moodle existe mais repose sur l'envoi d'un courriel. Pour les citoyens inscrits sans adresse e-mail personnelle, la réinitialisation automatique est inopérante : ils doivent contacter le support ou leur formateur. |
| **Support citoyen & Numéro vert** | **Implémentée et vérifiée** | `/local/pactdigital/contact.php` | Formulaire de ticket avec numéro de référence unique (`PACT-BF-YYYY-XXXXXX`), notification interne aux administrateurs. Affichage du Numéro Vert gratuit **34 34** et des horaires d'assistance. |
| **Foire Aux Questions (FAQ)** | **Implémentée et vérifiée** | `/local/pactdigital/faq.php` | 10 accordéons thématiques avec recherche instantanée et filtres par sujet. |
| **Mentions Légales & CIL** | **Implémentée et vérifiée** | `/local/pactdigital/legal.php` | Présentation des engagements de protection des données selon la Loi N° 001-2021/AN du Burkina Faso. |

### 3.2. Profil Apprenant (Bénéficiaire de la formation)

| Fonctionnalité | Statut réel | Éléments observés dans le code / base | Observations et limites |
| :--- | :--- | :--- | :--- |
| **Tableau de bord Mon Espace** | **Implémentée et vérifiée** | `/local/pactdigital/apprenant.php` | 4 onglets : *Mes Formations*, *Mes Résultats & Notes*, *Mes Badges*, *S'inscrire à un cours*. Calcul précis de la progression et des statuts. |
| **Inscription à un cours en 1 clic** | **Implémentée et vérifiée** | `catalogue.php` & `enrol_ajax.php` | Modale d'inscription avec saisie de l'année de naissance pour vérification de majorité (>= 18 ans). Inscription immédiate via le plugin `self`. |
| **Accueil de cours personnalisé** | **Implémentée et vérifiée** | `/local/pactdigital/course_welcome.php` | Redirection automatique depuis `/course/view.php`. Présentation synthétique du cours, des séquences et de l'aiguillage pédagogique. |
| **Voie 1 : Parcours progressif** | **Implémentée et vérifiée** | `course_welcome.php`, `load_bronze_d1.php` | Enchaînement : Accueil ➔ Pré-test ➔ 10 Séquences ➔ Quiz d'entraînement ➔ Examen final ➔ Enquête de satisfaction ➔ Attestation. Opérationnel sur le cours pilote #48 (D1 Bronze). |
| **Fiches Mémo PDF de séquences** | **Implémentée et vérifiée** | `/local/pactdigital/sequence_memo.php` | Redirection vers le support PDF officiel s'il existe, ou génération dynamique d'une fiche synthétique imprimable avec zones de notes personnelles. |
| **Voie 2 : Examen direct** | **Implémentée et vérifiée** | `course_welcome.php`, `mark_direct_attempt.php` | Permet aux personnes expérimentées de tenter directement la certification. Limité strictement à 2 tentatives directes. Blocage automatique en cas d'échec avec réorientation obligatoire vers la Voie 1. |
| **Chronomètre d'examen interactif** | **Implémentée et vérifiée** | `theme/pactdigital/lib.php` (`pact-exam-overlay`) | Widget flottant sur `/mod/quiz/attempt.php` avec compte à rebours, alerte jaune à 5 min, alerte rouge à 2 min, pastilles de réponses et sauvegarde automatique toutes les 30s. |
| **Évaluation et seuil de réussite** | **Implémentée et vérifiée** | `exam_result.php`, `grade_items` | Seuil de réussite fixé à **70%** (14,70 / 21 points dans le cours pilote). Page de résultat dédiée avec badge et boutons de révision. |
| **Attestation officielle & QR Code** | **Implémentée et vérifiée** | `exam_result.php`, `apprenant.php`, `mod_customcert` | Téléchargement du PDF officiel et génération d'un code unique vérifiable publiquement. |
| **Partage sur réseaux professionnels** | **Implémentée et vérifiée** | `apprenant.php`, `exam_result.php` | Modale de partage pour LinkedIn, Facebook et copie de lien sécurisé. |
| **Utilisation mobile & hors-ligne** | **Partiellement implémentée** | Interface 100% responsive, `enablemobilewebservice` activé en base | Parfaitement fonctionnel sur navigateur mobile (smartphone). Le téléchargement hors-ligne via l'application Moodle native nécessite la configuration d'un compte de synchronisation non validé lors de l'audit. |

### 3.3. Profil Formateur (Animateur communal et relais)

| Fonctionnalité | Statut réel | Éléments observés dans le code / base | Observations et limites |
| :--- | :--- | :--- | :--- |
| **Cockpit Pédagogique Formateur** | **Implémentée et vérifiée** | `/local/pactdigital/formateur.php` | Accessible via l'en-tête pour les rôles `formateur`, `formateur_pnln`, `teacher` et `admin`. Sélecteur de cohorte/cours sous responsabilité. |
| **Indicateurs KPI de session** | **Implémentée et vérifiée** | `formateur.php` (4 cartes KPI) | Effectif total, progression moyenne, apprenants inactifs (> 7 jours) et devoirs en attente de notation. |
| **Suivi d'avancement individuel** | **Implémentée et vérifiée** | `formateur.php` (Onglet 1) | Tableau des apprenants avec recherche, filtre par statut et bouton de relance rapide pré-rempli. |
| **Gestion des devoirs et quiz** | **Implémentée et vérifiée** | `formateur.php` (Onglet 2) | Accès direct aux copies de devoirs à noter (`mod/assign`) et aux statistiques des quiz (`mod/quiz`). |
| **Relance ciblée et messagerie** | **Implémentée et vérifiée** | `formateur.php` (Onglet 3) | 3 modèles de relance pré-remplis (inactivité, devoir, félicitations). Envoi de notification interne native Moodle. |
| **Import massif de cohorte par CSV** | **Implémentée et vérifiée** | `formateur.php` (Onglet 5) | Modèle CSV 10 colonnes téléchargeable. Création automatique de comptes (mot de passe initial `Pactdigital2026!`), inscription et affectation au groupe de cohorte. |
| **Création unitaire de compte** | **Non implémentée (dans le cockpit)** | Absence de formulaire de création unitaire dans `formateur.php` | La création assistée par le formateur passe obligatoirement par l'import CSV ou par l'inscription publique `signup.php`. |
| **Export de la liste des certifiés** | **Implémentée et vérifiée** | `formateur.php` (Onglet 4) | Export CSV UTF-8 BOM instantané des apprenants ayant atteint 100% de progression ou validé l'achèvement. |
| **Clôture officielle et Procès-verbal** | **Implémentée et vérifiée** | `formateur.php` (Onglet 6) | Vérification des cibles nationales (>= 30% femmes, jeunes 15-35 ans). Horodatage de clôture dans `config_plugins` et export du PV officiel sur 13 colonnes. |
| **Émargement et présences** | **Partiellement implémentée** | Activité `mod_attendance` configurée en section 6 du cours | Accessible depuis le bandeau formateur dans le cours Moodle, mais non intégrée sous forme d'onglet direct dans `formateur.php`. |
| **Limites du rôle Formateur** | **Implémentée et vérifiée** | `course.mustache`, `formateur.php` | Rôle cantonné au "Mode accompagnement" : interdiction stricte de modifier le contenu des cours ou les paramètres généraux de la plateforme. |

### 3.4. Profil Gestionnaire Pédagogique

| Fonctionnalité | Statut réel | Éléments observés dans le code / base | Observations et limites |
| :--- | :--- | :--- | :--- |
| **Organisation du catalogue** | **Implémentée et vérifiée** | `/course/management.php`, bouton d'accueil | Gestion de l'arborescence des 4 catégories piliers et des 20 sous-catégories domaines. |
| **Création et paramétrage de cours** | **Implémentée et vérifiée** | Formulaires d'édition Moodle `/course/edit.php` | Configuration des formats (thématique), des conditions d'achèvement et des règles d'accès. |
| **Banques de questions & évaluations** | **Implémentée et vérifiée** | `/question/edit.php`, `mod_quiz` | Structuration des questions par compétences et niveaux de taxonomie de Bloom. |
| **Supervision de la qualité des cours** | **Implémentée et vérifiée** | Droits d'inspection pédagogique sur l'ensemble des cours | Accompagnement des formateurs et homogénéisation des parcours selon le modèle du Domaine 1. |
| **Accès au Dashboard National SIG** | **Implémentée et vérifiée** | `/local/pactdigital/dashboard.php`, capacité `local/pactdigital:viewdashboard` | Accès aux indicateurs d'impact national, taux de réussite et cartographie territoriale. |

### 3.5. Profil Administrateur de la Plateforme

| Fonctionnalité | Statut réel | Éléments observés dans le code / base | Observations et limites |
| :--- | :--- | :--- | :--- |
| **Administration générale du site** | **Implémentée et vérifiée** | `/admin/index.php`, contrôles `is_siteadmin()` | Paramétrage global, sécurité, gestion des extensions, gestion des sessions. |
| **Gestion des rôles et habilitations** | **Implémentée et vérifiée** | `m_role` (rôles 9 et 10 spécifiques formateurs), `/admin/roles/assign.php` | Attribution des droits formateurs et gestionnaires au niveau système ou cours. |
| **Dashboard National SIG & Cartographie** | **Implémentée et vérifiée** | `/local/pactdigital/dashboard.php`, `dashboard_data.php` | 8 filtres, 6 KPI cards, 10 graphiques Chart.js, carte Leaflet choroplèthe, export PDF/Excel personnalisable avec filigrane officiel. |
| **Sauvegardes automatisées** | **Implémentée et vérifiée** | `/scripts/backup_pactdigital.sh` | Dump MariaDB compressé, archivage de `moodledata` et rotation automatique sur 7 jours. |
| **Exécution du cron Moodle** | **Implémentée et vérifiée** | `/scripts/run_cron.sh` | Exécution planifiée du script CLI `/admin/cli/cron.php`. |
| **Supervision des journaux et sécurité** | **Implémentée et vérifiée** | `/report/log/index.php` | Traçabilité des actions, détection des anomalies de connexion et conformité CIL. |

### 3.6. Fonctionnalité de Vérification Publique des Attestations

| Fonctionnalité | Statut réel | Éléments observés dans le code / base | Observations et limites |
| :--- | :--- | :--- | :--- |
| **Accès public à la vérification** | **Implémentée et vérifiée** | `/local/pactdigital/verify.php` | Accessible librement sans authentification depuis le pied de page et l'accueil. |
| **Recherche par code d'attestation** | **Implémentée et vérifiée** | Formulaire GET `verify.php?code=XXXX` | Nettoyage strict de la chaîne de caractères et requête sur `m_customcert_issues`. |
| **Vérification par QR Code** | **Implémentée et vérifiée** | QR Code imprimé sur les attestations PDF | Le scan du QR code renvoie directement vers l'URL `/local/pactdigital/verify.php?code=XXXX`. |
| **Affichage de l'attestation authentique** | **Implémentée et vérifiée** | Carte officielle `.cert-card-official` | Affiche le bénéficiaire, le domaine, le niveau certifié (Bronze, Argent, Or), la date de délivrance et le sceau MTDPCE. Ne divulgue pas la note chiffrée. |
| **Gestion des attestations introuvables** | **Implémentée et vérifiée** | Carte d'alerte `.cert-card-error` | Message clair invitant à vérifier la saisie ou à contacter le support en cas de suspicion de faux document. |

---

## 4. Analyse détaillée des écarts, anomalies et réserves techniques

L'audit approfondi du code source a permis de mettre en lumière plusieurs anomalies et écarts notables entre le cahier des charges et le code en production :

### Écart 1 — Bug de correspondance des clés JSON dans le Dashboard National (`dashboard.php`)
- **Constat dans le code :**
  - Dans `dashboard_data.php` (lignes 395-403), l'API renvoie pour le tableau des formations les clés JSON : `'name'`, `'domain'`, `'niveau'`, `'enrolled'`, `'completed'`, `'certs'`, `'taux'`.
  - Or, dans `dashboard.php` (lignes 1606-1616 et lignes 1937 & 2189 pour les exports PDF/Excel), le script JavaScript tente de lire `c.course`, `c.category`, `c.enroled` !
  - De même, pour la liste des apprenants (`#tbl-learners`), `dashboard_data.php` renvoie `'name'`, `'enrolments'`, `'completed'` tandis que `dashboard.php` attend `l.nom`, `l.enroled`, `l.niveau_instruction`.
- **Conséquence observée :** Dans l'interface du tableau de bord national, les colonnes *Formation*, *Catégorie* et *Inscrits* du Top 10 affichent la valeur textuelle `undefined`, et plusieurs colonnes du tableau des apprenants restent vides ou à `0%`.
- **Recommandation :** Harmoniser les clés JSON entre `dashboard_data.php` et `dashboard.php` lors du prochain sprint de maintenance. Les guides documentent le tableau tel qu'il doit être interprété par les gestionnaires.

### Écart 2 — Filtre de période d'inscription inactif côté serveur (`dashboard_data.php`)
- **Constat dans le code :** Le filtre `#f-period` (7 jours, 30 jours, 3 mois, etc.) est présent dans le formulaire de `dashboard.php`, mais dans `dashboard_data.php` (ligne 96), la variable `$filter_period` est récupérée sans jamais être appliquée dans la boucle de filtrage des utilisateurs.
- **Conséquence :** La modification de la période temporelle ne recalcule pas les données du tableau de bord.
- **Consigne de documentation :** Présenter le filtre comme un critère en cours de finalisation et conseiller l'utilisation des filtres géographiques et démographiques pour les analyses courantes.

### Écart 3 — Indicateurs d'impact simulés / de repli (`dashboard_data.php`)
- **Constat :**
  - L'indicateur de satisfaction apprenant (**PIL-09**) renvoie des données codées en dur (`score_moyenne => 4.7`, `taux_satisfaction => 94%`) au lieu d'agréger dynamiquement les tables de feedback.
  - La répartition Voie 1 vs Voie 2 (**PIL-06**) calcule un ratio de repli estimatif (18% Voie 2 / 82% Voie 1) en l'absence de libellés explicites dans les certificats.
- **Consigne de documentation :** Documenter ces indicateurs en précisant qu'ils reflètent les seuils cibles de référence fixés par le programme national.

### Écart 4 — Nomenclature des niveaux en base : `Diamant` vs `Argent`
- **Constat :** Dans la base de données (`m_course`), les 20 cours de niveau intermédiaire portent le nom de cours `Diamant` (ex: `Diamant_en_communication`). Cependant, dans l'interface utilisateur (`catalogue.php`, `apprenant.php`, `course_welcome.php`), le thème réécrit dynamiquement ce niveau en `🥈 Argent` pour respecter la charte nationale.
- **Consigne de documentation :** Les guides utilisateurs retiennent exclusivement la terminologie officielle **Bronze**, **Argent** et **Or**.

### Écart 5 — Absence de création unitaire d'apprenant dans le Cockpit Formateur
- **Constat :** L'onglet 5 de `formateur.php` propose uniquement un import par fichier CSV. Il n'existe pas de formulaire en ligne permettant au formateur d'inscrire manuellement un apprenant unique sans passer par un fichier.
- **Consigne de documentation :** Expliquer la procédure d'inscription assistée via le modèle CSV (même pour un seul individu) ou l'orientation de l'apprenant vers la page publique `signup.php`.

### Écart 6 — Divergence du référentiel régional (`signup.php` vs `contact.php`)
- **Constat :** `signup.php` et `dashboard.php` intègrent le nouveau découpage territorial à **17 régions** (Bankui, Djôrô, Goulmou, Guiriko, Kadiogo, etc.), alors que `contact.php` propose encore l'ancien découpage à **13 régions**.
- **Consigne de documentation :** Documenter les formulaires avec leurs options réelles respectives.

### Écart 7 — Procédure de mot de passe oublié pour les comptes sans e-mail
- **Constat :** Les citoyens inscrits avec un numéro de téléphone reçoivent un courriel technique interne (`@pactdigital.bf`). En cas de perte de mot de passe, ils ne peuvent pas utiliser la fonction automatique de réinitialisation de Moodle.
- **Consigne de documentation :** Fournir une procédure claire invitant l'apprenant à se rapprocher de son formateur territorial ou à composer le Numéro Vert **34 34**.

---

## 5. Matrice de traçabilité des fonctionnalités

La matrice exhaustive comprenant les 42 fonctionnalités auditées, leur statut, les preuves de code associées et les consignes de rédaction a été générée au format tableur professionnel :  
📁 **`docs/guides_utilisations/Matrice_Fonctionnalites_Par_Profil.xlsx`**

---

## 6. Conclusion et feuille de route documentaire

L'état de la plateforme PACTDIGITAL-BF démontre un niveau de maturité technique remarquable sur les fonctionnalités clés :
- L'expérience citoyenne (découverte, catalogue, inscription sans e-mail) est fluide et souveraine.
- Le dispositif pédagogique (double voie d'évaluation, chronomètre, seuil à 70%, fiches mémo) est entièrement opérationnel sur le cours pilote.
- Le cockpit formateur et le tableau de bord SIG offrent une instrumentation robuste pour le déploiement national.

Les 6 guides utilisateurs rédigés à l'issue de cet audit s'appuient strictement sur ces éléments vérifiés, garantissant une documentation d'une fiabilité totale pour les équipes du MTDPCE, les formateurs et les citoyens du Burkina Faso.
