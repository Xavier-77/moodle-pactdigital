# DOCUMENTATION UTILISATEUR OFFICIELLE — PLATEFORME PACTDIGITAL-BF

**Programme National de Littératie Numérique du Burkina Faso (PNLN)**  
**Ministère de tutelle :** Ministère de la Transition Digitale, des Postes et des Communications Électroniques (MTDPCE)  
**Direction technique :** Direction Générale de la Transition Digitale (DGTD)  
**Date d'édition :** Octobre 2026 — Version 1.0 Officielle  
**Référence charte graphique :** MTDPCE v1.0 (mai 2026) & v2.0 (`@govbf/charte-graphique`)

---

## 📁 1. Répertoire des Livrables Produits

L'ensemble des guides et documents contractuels a été généré et déposé dans le répertoire officiel :  
📁 `docs/guides_utilisations/` *(dupliqué par sécurité dans `documentation/guides_utilisateurs/`)*

| N° | Fichier PDF Officiel | Source DOCX Éditables | Profil Cible | Finalité & Portée Fonctionnelle |
| :---: | :--- | :--- | :--- | :--- |
| **01** | [`01_Guide_Visiteur_Inscription.pdf`](01_Guide_Visiteur_Inscription.pdf) | [`01_Guide_Visiteur_Inscription.docx`](01_Guide_Visiteur_Inscription.docx) | **Visiteur, Citoyen, Futur Apprenant** | Découverte sans compte, consultation du catalogue des 60 cours, inscription citoyenne simplifiée sans e-mail obligatoire, connexion, réinitialisation et contact d'assistance. |
| **02** | [`02_Guide_Apprenant.pdf`](02_Guide_Apprenant.pdf) | [`02_Guide_Apprenant.docx`](02_Guide_Apprenant.docx) | **Apprenant (Bénéficiaire)** | Prise en main de Mon Espace, inscription 1-clic avec contrôle d'âge (18 ans), aiguillage Voie 1 (Parcours) / Voie 2 (Examen direct), fiches mémo PDF, chronomètre d'examen, seuil de 70%, téléchargement d'attestation QR code et badges. |
| **03** | [`03_Guide_Formateur.pdf`](03_Guide_Formateur.pdf) | [`03_Guide_Formateur.docx`](03_Guide_Formateur.docx) | **Formateur Certifié, Animateur** | Cockpit Formateur (`formateur.php`), 4 KPIs, suivi individuel, correction des devoirs/quiz, relance des inactifs, import massif par lot CSV (modèle 10 colonnes, mot de passe `Pactdigital2026!`), clôture de session et export du PV officiel sur 13 colonnes. |
| **04** | [`04_Guide_Gestionnaire_Pedagogique.pdf`](04_Guide_Gestionnaire_Pedagogique.pdf) | [`04_Guide_Gestionnaire_Pedagogique.docx`](04_Guide_Gestionnaire_Pedagogique.docx) | **Gestionnaire Pédagogique** | Organisation du catalogue (4 piliers, 20 domaines), création de cours conforme au standard national D1 Bronze, banques de questions, chaînage d'achèvement, paramétrage de la double voie et supervision qualité SIG. |
| **05** | [`05_Guide_Administrateur.pdf`](05_Guide_Administrateur.pdf) | [`05_Guide_Administrateur.docx`](05_Guide_Administrateur.docx) | **Administrateur Système (DGTD)** | Administration générale Moodle, habilitation des formateurs (Rôles 9 et 10), supervision SIG nationale avec filtres 17 régions et exports PDF/Excel, scripts d'exploitation (`backup_pactdigital.sh`, `run_cron.sh`), purge de cache et sécurité CIL. |
| **06** | [`06_Guide_Verification_Attestations.pdf`](06_Guide_Verification_Attestations.pdf) | [`06_Guide_Verification_Attestations.docx`](06_Guide_Verification_Attestations.docx) | **Employeurs, Administrations, Partenaires** | Authentification publique des attestations via scan de QR code ou saisie du code cryptographique (`verify.php`), interprétation du certificat officiel valide et conduite à tenir en cas de document introuvable ou frauduleux. |
| **—** | [`Matrice_Fonctionnalites_Par_Profil.xlsx`](Matrice_Fonctionnalites_Par_Profil.xlsx) | — | **Comité de Pilotage & Auditeurs** | Matrice d'audit de 42 fonctionnalités croisées par profil, statut de vérification (Vérifiée, Partielle, Non implémentée), preuve observée dans le code et écarts identifiés. |
| **—** | [`Rapport_Audit_Fonctionnel.md`](Rapport_Audit_Fonctionnel.md) | — | **Direction Technique (DGTD / MTDPCE)** | Rapport d'audit exhaustif détaillant l'architecture logicielle, les comportements réels constatés, les anomalies de clés JSON et les recommandations de mise en conformité. |

---

## 🏛️ 2. Conformité à la Charte Graphique du MTDPCE

Tous les guides ont été conçus selon les prescriptions strictes de la Charte Graphique du MTDPCE (version mai 2026 / v2.0) :
- **Symboles de Souveraineté :** Armoiries officielles du Burkina Faso, devise nationale républicaine *« La Patrie ou la Mort, nous Vaincrons »*, bandeau d'identification `.fs-officiel` et filet tricolore national rouge-or-vert.
- **Palette chromatique normalisée :**
  - Bleu institutionnel MTDPCE : `#0A5496`
  - Vert national : `#00843B` / `#0E9C49`
  - Or / Jaune national : `#FFED00` / `#F9CF0C`
  - Rouge national : `#E30613` / `#ED1C24`
  - Neutres chauds de fond : `#F7F5F2` / `#FCFBF9`
- **Typographie souveraine :** Polices variables locales `Archivo` (titrages) et `Inter` (textes et tableaux), substituées par `Segoe UI` / `Helvetica` pour l'interopérabilité bureautique DOCX et PDF.
- **Accessibilité :** Contrastes conformes à la norme WCAG 2.1 AA (contraste supérieur à 15:1 pour les textes sur fonds clairs).

---

## 📋 3. Synthèse des Écarts et Réserves Techniques Documentés

Conformément à la règle d'or de la mission, les guides documentent **ce qui existe réellement** sur la plateforme et alertent sur les écarts constatés :
1. **Bug d'affichage Top 10 Formations & Apprenants dans `dashboard.php` :** Mauvaise correspondance de clés JSON (`c.course` au lieu de `c.name`, `c.enroled` au lieu de `c.enrolled`). Documenté dans le Guide Administrateur et le Rapport d'audit.
2. **Filtre de période d'inscription inactif côté serveur (`dashboard_data.php`) :** Le paramètre est lu sans être appliqué sur la sélection SQL.
3. **Indicateurs de satisfaction et répartition Voie 1/Voie 2 codés en dur :** Indiqué dans le Rapport d'audit comme données cibles de référence.
4. **Nomenclature en base (`Diamant` vs `Argent`) :** Les cours intermédiaires portent le code `Diamant` en base de données mais sont affichés sous le label `Argent` dans toute la documentation conformément à la volonté du Ministère.
5. **Absence de création unitaire directe dans `formateur.php` :** L'inscription assistée par le formateur passe par le téléversement du fichier CSV ou par le formulaire public `signup.php`.
6. **Récupération de compte sans e-mail :** Les apprenants inscrits avec un numéro de téléphone doivent contacter leur formateur communal ou le Numéro Vert **34 34** pour réinitialiser leur accès.

---

## 📞 4. Contacts Officiels d'Assistance

- **Numéro Vert National Gratuit :** **34 34** (accessible sans frais depuis tous les réseaux nationaux Orange, Moov Africa et Telecel Faso).
- **Courriel du Support Citoyen :** `support@pactdigital.bf`
- **Courriel Institutionnel DGTD :** `contact@transition-digitale.gov.bf`
- **Siège physique :** Ministère de la Transition Digitale, des Postes et des Communications Électroniques — Avenue de l'Indépendance, Ouagadougou, Burkina Faso.
