<?php
/**
 * PACTDIGITAL-BF — Script d'intégration complète du Module Bronze (Domaine 1 : Découvrir l'informatique)
 *
 * Ce script CLI Moodle peuple de manière idempotente le Cours #48 ("Bronze" dans la sous-catégorie
 * "Découvrir l'informatique", id=21) en respectant strictement l'architecture en 7 sections (0 à 6) :
 *   - Section 0 (📋 Accueil & Cadrage) : Fiche d'identité, Objectif général, OS1-OS5 (Bloom), 7 compétences (D1-B-01 à D1-B-07), annonce de bienvenue
 *   - Section 1 (📝 Pré-test de positionnement) : Quiz initial de 16 questions (C1 à C7) avec feedback pédagogique
 *   - Section 2 (🎓 Séquences d'apprentissage) :
 *       * 10 pages HTML interactives illustrées (Introduction + Compétences 1 à 7 avec pratiques en binôme + Travaux de groupe + Synthèse/Glossaire/Sitographie)
 *       * Fiche mémo PDF officielle générée via TCPDF (détectée par course_welcome.php et sequence_memo.php)
 *       * Support de cours complet PowerPoint (.pptx)
 *       * Quiz formatif d'entraînement (16 questions, tentatives illimitées, corrigé détaillé)
 *   - Section 3 (🏆 Examen final) : QCM de certification de 21 questions (3 par compétence, 30 min, seuil 70% = 14.7/21, 2 tentatives max)
 *   - Section 4 (📊 Enquête de satisfaction) : Auto-évaluation sur les 7 compétences (Slide 95) + Questionnaire de satisfaction 1 à 4 et questions ouvertes (Slide 96)
 *   - Section 5 (🎖️ Attestation de réussite) : Certificat Customcert débloqué après l'enquête
 *   - Section 6 (🔒 Espace Formateur, caché aux apprenants) :
 *       * Dossier "Supports et Corrigés" peuplé avec les 3 fichiers officiels (.pptx, Guide formateur .docx, Évaluation & corrigé .docx)
 *       * Ressources dédiées "📘 Guide Pédagogique du Formateur" et "🔑 Corrigé & Barème de l'Évaluation" (détectées par course_helper.php)
 */

define('CLI_SCRIPT', true);

require_once('/var/www/html/config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/pdflib.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

$STAGE_DIR = '/tmp/bronze_stage';
$MEDIA_DIR = $STAGE_DIR . '/media';
$FILES_DIR = $STAGE_DIR . '/files';
$COURSE_ID = 48;

echo "=================================================================\n";
echo "  PACTDIGITAL-BF — Chargement du Module Bronze (Domaine 1)\n";
echo "  Cours cible : #{$COURSE_ID} (Découvrir l'informatique — Bronze)\n";
echo "=================================================================\n";

$course = $DB->get_record('course', ['id' => $COURSE_ID], '*', MUST_EXIST);
$coursecontext = context_course::instance($COURSE_ID);
$fs = get_file_storage();
$now = time();

// -----------------------------------------------------------------------------
// HELPER FUNCTIONS
// -----------------------------------------------------------------------------

/**
 * Upload or replace a file in Moodle file storage.
 */
function pact_store_file_from_path(int $contextid, string $component, string $filearea, int $itemid, string $filepath, string $filename, string $DiskPath, int $sortorder = 0) {
    global $fs;
    if (!file_exists($DiskPath)) {
        echo "  [WARN] Fichier introuvable : {$DiskPath}\n";
        return null;
    }
    $existing = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename);
    if ($existing) {
        $existing->delete();
    }
    $filerecord = [
        'contextid' => $contextid,
        'component' => $component,
        'filearea'  => $filearea,
        'itemid'    => $itemid,
        'filepath'  => $filepath,
        'filename'  => $filename,
        'sortorder' => $sortorder,
        'userid'    => 2,
    ];
    return $fs->create_file_from_pathname($filerecord, $DiskPath);
}

/**
 * Upload or replace a file from string content in Moodle file storage.
 */
function pact_store_file_from_string(int $contextid, string $component, string $filearea, int $itemid, string $filepath, string $filename, string $content, int $sortorder = 0) {
    global $fs;
    $existing = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename);
    if ($existing) {
        $existing->delete();
    }
    $filerecord = [
        'contextid' => $contextid,
        'component' => $component,
        'filearea'  => $filearea,
        'itemid'    => $itemid,
        'filepath'  => $filepath,
        'filename'  => $filename,
        'sortorder' => $sortorder,
        'userid'    => 2,
    ];
    return $fs->create_file_from_string($filerecord, $content);
}

/**
 * Create or update a course module (page, resource) idempotently using idnumber.
 */
function pact_upsert_module(int $courseid, int $sectionnum, string $modname, string $idnumber, stdClass $instance_data, array $cm_options = []): array {
    global $DB;
    $now = time();
    $module = $DB->get_record('modules', ['name' => $modname], '*', MUST_EXIST);
    $existing_cm = $DB->get_record('course_modules', ['course' => $courseid, 'idnumber' => $idnumber]);

    if ($existing_cm) {
        $instance_data->id = $existing_cm->instance;
        $instance_data->timemodified = $now;
        $DB->update_record($modname, $instance_data);

        if (isset($cm_options['completion'])) {
            $existing_cm->completion = $cm_options['completion'];
        }
        if (isset($cm_options['completionview'])) {
            $existing_cm->completionview = $cm_options['completionview'];
        }
        if (isset($cm_options['showdescription'])) {
            $existing_cm->showdescription = $cm_options['showdescription'];
        }
        $existing_cm->visible = $cm_options['visible'] ?? 1;
        $existing_cm->visibleoncoursepage = 1;
        $DB->update_record('course_modules', $existing_cm);
        $cmid = $existing_cm->id;
        $instanceid = $existing_cm->instance;
    } else {
        $instance_data->course = $courseid;
        $instance_data->timemodified = $now;
        if (!isset($instance_data->timecreated)) {
            $instance_data->timecreated = $now;
        }
        $instanceid = $DB->insert_record($modname, $instance_data);

        $cm = new stdClass();
        $cm->course = $courseid;
        $cm->module = $module->id;
        $cm->instance = $instanceid;
        $cm->section = 0;
        $cm->idnumber = $idnumber;
        $cm->added = $now;
        $cm->score = 0;
        $cm->indent = 0;
        $cm->visible = $cm_options['visible'] ?? 1;
        $cm->visibleoncoursepage = 1;
        $cm->visibleold = 1;
        $cm->groupmode = 0;
        $cm->groupingid = 0;
        $cm->completion = $cm_options['completion'] ?? 1;
        $cm->completionview = $cm_options['completionview'] ?? 0;
        $cm->completionexpected = 0;
        $cm->completionpassgrade = $cm_options['completionpassgrade'] ?? 0;
        $cm->showdescription = $cm_options['showdescription'] ?? 0;
        $cm->availability = $cm_options['availability'] ?? null;
        $cm->deletioninprogress = 0;
        $cm->downloadcontent = 1;

        $cmid = add_course_module($cm);
        course_add_cm_to_section($courseid, $cmid, $sectionnum);
    }

    $modcontext = context_module::instance($cmid);
    return [$cmid, $instanceid, $modcontext];
}

/**
 * Populate a Moodle 4.x/5.x Quiz with multichoice single-answer questions idempotently.
 */
function pact_populate_quiz_questions(int $quizid, int $cmid, string $quiz_prefix, array $questions): void {
    global $DB;
    $now = time();
    $modcontext = context_module::instance($cmid);

    // 1. Ensure question categories exist for this quiz context
    $topcat = $DB->get_record('question_categories', ['contextid' => $modcontext->id, 'parent' => 0]);
    if (!$topcat) {
        $topcat = new stdClass();
        $topcat->name = 'top';
        $topcat->contextid = $modcontext->id;
        $topcat->info = '';
        $topcat->infoformat = 0;
        $topcat->stamp = make_unique_id_code();
        $topcat->parent = 0;
        $topcat->sortorder = 0;
        $topcat->idnumber = null;
        $topcat->id = $DB->insert_record('question_categories', $topcat);
    }

    $defcat = $DB->get_record_select('question_categories', 'contextid = :ctx AND parent > 0', ['ctx' => $modcontext->id]);
    if (!$defcat) {
        $defcat = new stdClass();
        $defcat->name = 'Défaut pour le quiz';
        $defcat->contextid = $modcontext->id;
        $defcat->info = 'Catégorie par défaut pour les questions du quiz.';
        $defcat->infoformat = 0;
        $defcat->stamp = make_unique_id_code();
        $defcat->parent = $topcat->id;
        $defcat->sortorder = 999;
        $defcat->idnumber = null;
        $defcat->id = $DB->insert_record('question_categories', $defcat);
    }

    // 2. Clean existing quiz slots, references, and sections for this quiz (if no attempts exist)
    $existing_attempts = $DB->count_records('quiz_attempts', ['quiz' => $quizid]);
    if ($existing_attempts > 0) {
        $DB->delete_records('quiz_attempts', ['quiz' => $quizid]);
        $DB->delete_records('quiz_grades', ['quiz' => $quizid]);
    }

    $DB->delete_records('question_references', [
        'usingcontextid' => $modcontext->id,
        'component'      => 'mod_quiz',
        'questionarea'   => 'slot',
    ]);
    $DB->delete_records('quiz_slots', ['quizid' => $quizid]);
    $DB->delete_records('quiz_sections', ['quizid' => $quizid]);

    // Insert default quiz_section
    $qsec = new stdClass();
    $qsec->quizid = $quizid;
    $qsec->firstslot = 1;
    $qsec->heading = '';
    $qsec->shufflequestions = 0;
    $DB->insert_record('quiz_sections', $qsec);

    // 3. Insert or update each question and bind slot
    $slot_num = 1;
    $questions_per_page = 3;
    foreach ($questions as $idx => $qdata) {
        $q_idnumber = $quiz_prefix . '_q' . str_pad((string)($idx + 1), 2, '0', STR_PAD_LEFT);
        $entry = $DB->get_record('question_bank_entries', [
            'questioncategoryid' => $defcat->id,
            'idnumber'           => $q_idnumber,
        ]);

        if ($entry) {
            $ver = $DB->get_record('question_versions', ['questionbankentryid' => $entry->id], '*', IGNORE_MULTIPLE);
            $questionid = $ver->questionid;
            $qrec = $DB->get_record('question', ['id' => $questionid], '*', MUST_EXIST);
            $qrec->name = $qdata['name'];
            $qrec->questiontext = $qdata['questiontext'];
            $qrec->generalfeedback = $qdata['generalfeedback'];
            $qrec->timemodified = $now;
            $DB->update_record('question', $qrec);
            $DB->delete_records('question_answers', ['question' => $questionid]);
            $DB->delete_records('qtype_multichoice_options', ['questionid' => $questionid]);
        } else {
            $entry = new stdClass();
            $entry->questioncategoryid = $defcat->id;
            $entry->idnumber = $q_idnumber;
            $entry->ownerid = 2;
            $entry->nextversion = 2;
            $entry->id = $DB->insert_record('question_bank_entries', $entry);

            $qrec = new stdClass();
            $qrec->parent = 0;
            $qrec->name = $qdata['name'];
            $qrec->questiontext = $qdata['questiontext'];
            $qrec->questiontextformat = FORMAT_HTML;
            $qrec->generalfeedback = $qdata['generalfeedback'];
            $qrec->generalfeedbackformat = FORMAT_HTML;
            $qrec->defaultmark = 1.0000000;
            $qrec->penalty = 0.3333333;
            $qrec->qtype = 'multichoice';
            $qrec->length = 1;
            $qrec->stamp = make_unique_id_code();
            $qrec->timecreated = $now;
            $qrec->timemodified = $now;
            $qrec->createdby = 2;
            $qrec->modifiedby = 2;
            $questionid = $DB->insert_record('question', $qrec);

            $ver = new stdClass();
            $ver->questionbankentryid = $entry->id;
            $ver->version = 1;
            $ver->questionid = $questionid;
            $ver->status = 'ready';
            $DB->insert_record('question_versions', $ver);
        }

        // Insert multichoice options
        $mopt = new stdClass();
        $mopt->questionid = $questionid;
        $mopt->layout = 0;
        $mopt->single = 1;
        $mopt->shuffleanswers = 1;
        $mopt->correctfeedback = '<p><strong>✅ Bonne réponse !</strong></p>';
        $mopt->correctfeedbackformat = FORMAT_HTML;
        $mopt->partiallycorrectfeedback = '<p>Réponse partiellement correcte.</p>';
        $mopt->partiallycorrectfeedbackformat = FORMAT_HTML;
        $mopt->incorrectfeedback = '<p><strong>❌ Réponse incorrecte.</strong> Consultez l\'explication ci-dessous.</p>';
        $mopt->incorrectfeedbackformat = FORMAT_HTML;
        $mopt->answernumbering = 'abc';
        $mopt->shownumcorrect = 0;
        $mopt->showstandardinstruction = 1;
        $DB->insert_record('qtype_multichoice_options', $mopt);

        // Insert answers
        foreach ($qdata['options'] as $opt_idx => $opt_text) {
            $is_correct = ($opt_idx === $qdata['correct']);
            $ans = new stdClass();
            $ans->question = $questionid;
            $ans->answer = '<p>' . $opt_text . '</p>';
            $ans->answerformat = FORMAT_HTML;
            $ans->fraction = $is_correct ? 1.0000000 : 0.0000000;
            $ans->feedback = $is_correct
                ? '<p>✅ Exact ! ' . $qdata['explanation'] . '</p>'
                : '<p>💡 Rappel : ' . $qdata['explanation'] . '</p>';
            $ans->feedbackformat = FORMAT_HTML;
            $DB->insert_record('question_answers', $ans);
        }

        // Insert quiz slot
        $page = (int) floor(($slot_num - 1) / $questions_per_page) + 1;
        $slot = new stdClass();
        $slot->slot = $slot_num;
        $slot->quizid = $quizid;
        $slot->page = $page;
        $slot->displaynumber = null;
        $slot->requireprevious = 0;
        $slot->maxmark = 1.0000000;
        $slot->quizgradeitemid = null;
        $slotid = $DB->insert_record('quiz_slots', $slot);

        // Insert question reference
        $ref = new stdClass();
        $ref->usingcontextid = $modcontext->id;
        $ref->component = 'mod_quiz';
        $ref->questionarea = 'slot';
        $ref->itemid = $slotid;
        $ref->questionbankentryid = $entry->id;
        $ref->version = null; // Always latest version
        $DB->insert_record('question_references', $ref);

        $slot_num++;
    }

    $total_marks = count($questions);
    $DB->set_field('quiz', 'sumgrades', $total_marks, ['id' => $quizid]);
    $DB->set_field('quiz', 'grade', $total_marks, ['id' => $quizid]);
}

echo "\n[Étape 1/7] Mise à jour du Cours #48 et de la Section 0 (📋 Accueil & Cadrage)...\n";

$course->summary = '<div class="pact-course-summary" style="font-family:\'Plus Jakarta Sans\',Segoe UI,sans-serif;line-height:1.6;">
    <p><strong>Domaine 1 : Découvrir l\'informatique — Pack Bronze (Niveau Base · 6 heures)</strong></p>
    <p>À la fin de ce module, vous utiliserez seul un <strong>smartphone Android</strong>, un <strong>ordinateur Windows 11</strong> et un <strong>ordinateur Ubuntu 24.04</strong> : vous saurez les reconnaître, les démarrer et vous y repérer, ouvrir une application, écrire un texte correct, vous connecter à internet et entretenir votre matériel, jusqu\'à concevoir une fiche mémo utile à un usager.</p>
    <ul>
        <li><strong>Public cible :</strong> Agents publics, étudiants et entrepreneurs débutants ou peu à l\'aise avec l\'ordinateur, y compris ceux qui utilisent déjà un smartphone.</li>
        <li><strong>Prérequis :</strong> Aucun. Savoir lire des mots simples est souhaitable ; une variante avec pictogrammes est prévue.</li>
        <li><strong>Référence officielle :</strong> Référentiel MTDPCE / DGTD — Domaine 1, Niveau Bronze, 7 compétences (codes <code>D1-B-01</code> à <code>D1-B-07</code>).</li>
    </ul>
</div>';
$course->summaryformat = FORMAT_HTML;
$course->enablecompletion = 1;
$course->showcompletionconditions = 1;
$course->timemodified = $now;
$DB->update_record('course', $course);

// Upload Course Overview Image (image3.png: les 3 appareils de la formation)
$fs->delete_area_files($coursecontext->id, 'course', 'overviewfiles');
pact_store_file_from_path(
    $coursecontext->id,
    'course',
    'overviewfiles',
    0,
    '/',
    'decouvrir_informatique_bronze.png',
    $MEDIA_DIR . '/image3.png'
);

// Update Section 0 (📋 Accueil & Cadrage)
$sec0 = $DB->get_record('course_sections', ['course' => $COURSE_ID, 'section' => 0], '*', MUST_EXIST);
$sec0->name = '📋 Accueil & Cadrage';
$sec0->summary = '
<div style="font-family:\'Plus Jakarta Sans\',Segoe UI,Arial,sans-serif;color:#1e293b;line-height:1.65;">
    <div style="background:linear-gradient(135deg,#0d7a32 0%,#129b40 100%);color:#fff;padding:1.5rem 1.75rem;border-radius:14px;margin-bottom:1.25rem;">
        <div style="font-size:0.78rem;text-transform:uppercase;letter-spacing:0.06em;opacity:0.9;font-weight:700;">Ministère de la Transition Digitale, des Postes et des Communications Électroniques (MTDPCE / DGTD)</div>
        <h3 style="margin:0.35rem 0 0.4rem;color:#fff;font-size:1.35rem;font-weight:800;">🥉 Domaine 1 : Découvrir l\'informatique — Module Bronze (Niveau Base)</h3>
        <p style="margin:0;font-size:0.95rem;opacity:0.95;">Identifier les appareils, démarrer et éteindre, naviguer, ouvrir une application, saisir du texte, se connecter à internet et entretenir son matériel sur <strong>Android</strong>, <strong>Windows 11</strong> et <strong>Ubuntu 24.04</strong>.</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:0.9rem;margin-bottom:1.25rem;">
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-left:4px solid #129b40;border-radius:10px;padding:0.9rem 1rem;">
            <div style="font-size:0.75rem;color:#64748b;font-weight:700;text-transform:uppercase;">⏱️ Durée & Format</div>
            <div style="font-weight:700;color:#0f172a;margin-top:0.2rem;">6 heures (1 journée)</div>
            <div style="font-size:0.85rem;color:#475569;">320 min d\'apprentissage actif + 40 min de pauses (8 séquences)</div>
        </div>
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-left:4px solid #0284c7;border-radius:10px;padding:0.9rem 1rem;">
            <div style="font-size:0.75rem;color:#64748b;font-weight:700;text-transform:uppercase;">👥 Public cible & Prérequis</div>
            <div style="font-weight:700;color:#0f172a;margin-top:0.2rem;">Débutants (Aucun prérequis)</div>
            <div style="font-size:0.85rem;color:#475569;">Agents publics, étudiants, commerçants et entrepreneurs</div>
        </div>
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-left:4px solid #d97706;border-radius:10px;padding:0.9rem 1rem;">
            <div style="font-size:0.75rem;color:#64748b;font-weight:700;text-transform:uppercase;">🎯 Objectif Général</div>
            <div style="font-size:0.85rem;color:#334155;margin-top:0.2rem;">Utiliser seul un smartphone Android, un PC Windows 11 et un PC Ubuntu 24.04 jusqu\'à concevoir une fiche mémo utile à un usager.</div>
        </div>
    </div>

    <details style="background:#fff;border:1px solid #cbd5e1;border-radius:10px;padding:0.85rem 1.1rem;margin-bottom:0.85rem;">
        <summary style="font-weight:700;color:#0d7a32;cursor:pointer;">🪜 Les 5 Objectifs Spécifiques (Progression de Bloom : OS1 à OS5)</summary>
        <ul style="margin:0.65rem 0 0;padding-left:1.25rem;font-size:0.9rem;color:#334155;">
            <li><strong>OS1 — Connaissance / Compréhension :</strong> Nommer les appareils, les systèmes et les touches, et expliquer leur rôle.</li>
            <li><strong>OS2 — Application :</strong> Démarrer, ouvrir une application, écrire et se connecter sur les trois systèmes (Android, Windows 11, Ubuntu 24.04).</li>
            <li><strong>OS3 — Analyse :</strong> Repérer sur un écran ou dans un texte ce qui bloque, et en trouver la cause.</li>
            <li><strong>OS4 — Évaluation :</strong> Choisir et justifier le bon geste : veille ou arrêt, Wi-Fi ou données mobiles, quel chargeur, onduleur ou parasurtenseur.</li>
            <li><strong>OS5 — Création :</strong> Concevoir en groupe la fiche mémo d\'un usager (Awa, Issouf ou Mariam), puis la tester sur l\'appareil.</li>
        </ul>
    </details>

    <details style="background:#fff;border:1px solid #cbd5e1;border-radius:10px;padding:0.85rem 1.1rem;">
        <summary style="font-weight:700;color:#0d7a32;cursor:pointer;">🧭 Les 7 Compétences du Référentiel MTDPCE / DGTD (Codes D1-B-01 à D1-B-07)</summary>
        <table style="width:100%;border-collapse:collapse;margin-top:0.75rem;font-size:0.88rem;">
            <thead>
                <tr style="background:#f1f5f9;text-align:left;">
                    <th style="padding:8px 10px;border:1px solid #e2e8f0;">Code</th>
                    <th style="padding:8px 10px;border:1px solid #e2e8f0;">Compétence visée</th>
                    <th style="padding:8px 10px;border:1px solid #e2e8f0;">Systèmes étudiés</th>
                    <th style="padding:8px 10px;border:1px solid #e2e8f0;">Niveau Bloom</th>
                </tr>
            </thead>
            <tbody>
                <tr><td style="padding:7px 10px;border:1px solid #e2e8f0;"><code>D1-B-01</code> (C1)</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Identifier les appareils numériques et leur système d\'exploitation</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Android, Windows 11, Ubuntu 24.04</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">OS1</td></tr>
                <tr><td style="padding:7px 10px;border:1px solid #e2e8f0;"><code>D1-B-02</code> (C2)</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Démarrer, verrouiller, redémarrer et éteindre un appareil</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Android, Windows 11, Ubuntu 24.04</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">OS2, OS4</td></tr>
                <tr><td style="padding:7px 10px;border:1px solid #e2e8f0;"><code>D1-B-03</code> (C3)</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Naviguer dans l\'interface de base et manipuler la souris</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Android, Windows 11, Ubuntu 24.04</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">OS2, OS3</td></tr>
                <tr><td style="padding:7px 10px;border:1px solid #e2e8f0;"><code>D1-B-04</code> (C4)</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Localiser, rechercher et ouvrir une application</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Android, Windows 11, Ubuntu 24.04</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">OS1, OS2</td></tr>
                <tr><td style="padding:7px 10px;border:1px solid #e2e8f0;"><code>D1-B-05</code> (C5)</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Saisir du texte (accents, majuscules, ponctuation, relecture)</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Android, Windows 11, Ubuntu 24.04</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">OS1 à OS4</td></tr>
                <tr><td style="padding:7px 10px;border:1px solid #e2e8f0;"><code>D1-B-06</code> (C6)</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Connecter un appareil à internet (Wi-Fi, données mobiles, mode avion)</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Android, Windows 11, Ubuntu 24.04</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">OS1 à OS3</td></tr>
                <tr><td style="padding:7px 10px;border:1px solid #e2e8f0;"><code>D1-B-07</code> (C7)</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Entretenir et protéger son appareil (chaleur, poussière, eau, courant)</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">Android, Windows 11, Ubuntu 24.04</td><td style="padding:7px 10px;border:1px solid #e2e8f0;">OS1, OS2, OS4</td></tr>
            </tbody>
        </table>
    </details>
</div>';
$sec0->summaryformat = FORMAT_HTML;
$sec0->timemodified = $now;
$DB->update_record('course_sections', $sec0);

// Create Welcome discussion in Forum Annonces (id=47) if not exists
$forum = $DB->get_record('forum', ['course' => $COURSE_ID, 'type' => 'news']);
if ($forum) {
    $exists_disc = $DB->record_exists('forum_discussions', ['forum' => $forum->id, 'course' => $COURSE_ID]);
    if (!$exists_disc) {
        $disc = new stdClass();
        $disc->course = $COURSE_ID;
        $disc->forum = $forum->id;
        $disc->name = '👋 Bienvenue dans le Module Bronze — Domaine 1 : Découvrir l\'informatique';
        $disc->assessed = 0;
        $disc->timemodified = $now;
        $disc->usermodified = 2;
        $disc->timestart = 0;
        $disc->timeend = 0;
        $disc->pinned = 1;

        $post = new stdClass();
        $post->userid = 2;
        $post->created = $now;
        $post->modified = $now;
        $post->mailed = 1;
        $post->subject = $disc->name;
        $post->message = '<p>Bonjour et bienvenue dans ce module <strong>Découvrir l\'informatique (Niveau Bronze — Base)</strong> du Programme National de Littératie Numérique (<strong>PACTDIGITAL-BF</strong>).</p>
<p>Tout au long de ce parcours, vous allez pratiquer les <strong>7 compétences fondamentales</strong> sur trois environnements complémentaires : le <strong>smartphone Android</strong>, l\'ordinateur <strong>Windows 11</strong> et l\'ordinateur <strong>Ubuntu 24.04</strong>, en compagnie de nos quatre personnages fil rouge : <strong>Awa</strong> (agente administrative à la mairie de Koudougou), <strong>Issouf</strong> (commerçant sur smartphone Android), <strong>Mariam</strong> (étudiante sur Ubuntu au cyber) et <strong>Boureima</strong> (cultivateur près de Dédougou).</p>
<p><strong>Comment réussir votre parcours ?</strong></p>
<ol>
    <li>Commencez par passer le <strong>Pré-test de positionnement</strong> (Section 1) pour situer votre niveau de départ.</li>
    <li>Parcourez les <strong>10 séquences interactives</strong> de la Section 2, téléchargez la <strong>Fiche mémo PDF</strong> et entraînez-vous avec le <strong>Quiz formatif (16 questions)</strong>.</li>
    <li>Validez vos acquis avec l\'<strong>Examen final (QCM de 21 questions)</strong> en Section 3 (seuil de réussite : <strong>70 %</strong>, soit 15/21).</li>
    <li>Remplissez l\'<strong>Enquête de satisfaction et d\'auto-évaluation</strong> (Section 4) pour débloquer votre <strong>Attestation officielle de réussite</strong> (Section 5).</li>
</ol>';
        $post->messageformat = FORMAT_HTML;
        $post->messagetrust = 1;

        $postid = $DB->insert_record('forum_posts', $post);
        $disc->firstpost = $postid;
        $disc->userid = 2;
        $discid = $DB->insert_record('forum_discussions', $disc);
        $DB->set_field('forum_posts', 'discussion', $discid, ['id' => $postid]);
    }
}
echo "  ✓ Cours #48 et Section 0 mis à jour avec succès.\n";

// =============================================================================
// ÉTAPE 2 : SECTION 1 — PRÉ-TEST DE POSITIONNEMENT (16 QUESTIONS — SLIDES 76-91)
// =============================================================================
echo "\n[Étape 2/7] Configuration et peuplement du Pré-test de positionnement (Section 1, quizid=53)...\n";

$sec1 = $DB->get_record('course_sections', ['course' => $COURSE_ID, 'section' => 1], '*', MUST_EXIST);
$sec1->name = '📝 Pré-test de positionnement';
$sec1->summary = '<p>Avant de débuter les séquences d\'apprentissage, répondez à ces <strong>16 questions de positionnement</strong> couvrant les 7 compétences du module (Android, Windows 11, Ubuntu 24.04). Une erreur ici n\'est pas une sanction : elle vous indique simplement les points à observer attentivement pendant le cours.</p>';
$sec1->summaryformat = FORMAT_HTML;
$DB->update_record('course_sections', $sec1);

$questions_16 = [
    [
        'name' => 'Q01 — C1 (OS1) : Identifier un système d\'exploitation',
        'questiontext' => '<p><strong>[Compétence 1 — Identifier les appareils numériques]</strong></p><p>Lequel de ces noms <strong>n\'est pas</strong> un système d\'exploitation ?</p>',
        'generalfeedback' => '<p><strong>Correction (C1 — OS1) :</strong> WhatsApp est une <em>application</em>. Android, Windows et Ubuntu sont des <em>systèmes d\'exploitation</em> : ce sont eux qui font tourner les applications. Tecno ou Samsung sont des marques d\'appareils.</p>',
        'options' => [
            'Android',
            'WhatsApp',
            'Windows',
            'Ubuntu',
        ],
        'correct' => 1,
        'explanation' => 'WhatsApp est une application. Android, Windows et Ubuntu sont des systèmes d\'exploitation : ce sont eux qui font tourner les applications.',
    ],
    [
        'name' => 'Q02 — C2 (OS2) : Éteindre un ordinateur Windows 11',
        'questiontext' => '<p><strong>[Compétence 2 — Démarrer et éteindre un appareil]</strong></p><p>Sous <strong>Windows 11</strong>, quel est le bon chemin pour éteindre l\'ordinateur ?</p>',
        'generalfeedback' => '<p><strong>Correction (C2 — OS2) :</strong> On éteint toujours par le menu (<em>Démarrer → Marche/Arrêt → Arrêter</em>). Sur beaucoup de PC, un appui sur le bouton d\'alimentation met seulement l\'ordinateur en veille ; débrancher revient à provoquer une coupure de courant.</p>',
        'options' => [
            'Démarrer, Marche/Arrêt, Arrêter',
            'Appuyer sur le bouton du PC',
            'Débrancher la prise',
            'Fermer toutes les fenêtres',
        ],
        'correct' => 0,
        'explanation' => 'On éteint par le menu : Démarrer, Marche/Arrêt, Arrêter. Sur beaucoup de PC, un appui sur le bouton d\'alimentation met seulement l\'ordinateur en veille.',
    ],
    [
        'name' => 'Q03 — C2 (OS2) : Bouton d\'extinction sous Ubuntu 24.04',
        'questiontext' => '<p><strong>[Compétence 2 — Démarrer et éteindre un appareil]</strong></p><p>Sous <strong>Ubuntu 24.04</strong>, où trouve-t-on le bouton d\'extinction ?</p>',
        'generalfeedback' => '<p><strong>Correction (C2 — OS2) :</strong> Sous Ubuntu 24.04, on clique dans le <em>menu système, tout en haut à droite</em>, puis sur le bouton d\'extinction, et on choisit Éteindre ou Redémarrer. Le menu Démarrer appartient à Windows.</p>',
        'options' => [
            'Dans le dock, à gauche',
            'Dans le menu système, en haut à droite',
            'Dans le menu Démarrer',
            'Sur l\'horloge',
        ],
        'correct' => 1,
        'explanation' => 'On clique en haut à droite dans le menu système, puis sur le bouton d\'extinction. Le menu Démarrer appartient à Windows.',
    ],
    [
        'name' => 'Q04 — C3 (OS3) : Reconnaître le bureau d\'Ubuntu 24.04',
        'questiontext' => '<p><strong>[Compétence 3 — Naviguer dans l\'interface de base]</strong></p><p>Mariam voit une <strong>barre d\'icônes à gauche</strong> de l\'écran et une <strong>horloge en haut</strong>. Sur quel système est-elle ?</p>',
        'generalfeedback' => '<p><strong>Correction (C3 — OS3) :</strong> Le dock à gauche et l\'horloge dans la barre du haut caractérisent le bureau d\'<em>Ubuntu 24.04</em>. Windows 11 a sa barre des tâches en bas.</p>',
        'options' => [
            'Android',
            'Windows 11',
            'Ubuntu 24.04',
        ],
        'correct' => 2,
        'explanation' => 'Le dock à gauche et l\'horloge dans la barre du haut : c\'est le bureau d\'Ubuntu 24.04.',
    ],
    [
        'name' => 'Q05 — C4 (OS1) : Touche Super sous Ubuntu 24.04',
        'questiontext' => '<p><strong>[Compétence 4 — Localiser et ouvrir une application]</strong></p><p>Sous <strong>Ubuntu 24.04</strong>, quelle touche ouvre la vue d\'ensemble pour chercher une application ?</p>',
        'generalfeedback' => '<p><strong>Correction (C4 — OS1) :</strong> La touche <em>Super</em> (située près de la touche Alt et portant souvent le logo Windows) ouvre la vue d\'ensemble des Activités ; on tape ensuite le nom de l\'application.</p>',
        'options' => [
            'Entrée',
            'Verr. Maj',
            'Super',
            'Suppr',
        ],
        'correct' => 2,
        'explanation' => 'La touche Super, près de la touche Alt, ouvre la vue d\'ensemble des Activités ; on tape ensuite le nom de l\'application.',
    ],
    [
        'name' => 'Q06 — C5 (OS1) : Rôle de la touche Suppr',
        'questiontext' => '<p><strong>[Compétence 5 — Saisir du texte]</strong></p><p>Vrai ou faux : la touche <strong>Suppr</strong> efface le caractère situé <strong>à gauche</strong> du curseur.</p>',
        'generalfeedback' => '<p><strong>Correction (C5 — OS1) :</strong> <strong>Faux.</strong> La touche <em>Suppr</em> efface le caractère situé <strong>à droite</strong> du curseur. C\'est la touche <em>Retour arrière</em> (flèche vers la gauche) qui efface à gauche du curseur.</p>',
        'options' => [
            'Vrai (elle efface à gauche du curseur)',
            'Faux (Suppr efface à droite du curseur ; c\'est Retour arrière qui efface à gauche)',
        ],
        'correct' => 1,
        'explanation' => 'Suppr efface le caractère à droite du curseur. C\'est Retour arrière qui efface à gauche.',
    ],
    [
        'name' => 'Q07 — C6 (OS3) : Icône du mode avion sur Android',
        'questiontext' => '<p><strong>[Compétence 6 — Connecter un appareil à internet]</strong></p><p>Le téléphone de Boureima montre un <strong>petit avion</strong> en haut de l\'écran, et il n\'a plus internet. Pourquoi ?</p>',
        'generalfeedback' => '<p><strong>Correction (C6 — OS3) :</strong> Le <em>mode avion</em> coupe d\'un coup les connexions sans fil : plus de réseau mobile, donc plus de données mobiles. Il suffit de le désactiver dans les réglages rapides.</p>',
        'options' => [
            'Son forfait est épuisé',
            'Le mode avion coupe les connexions sans fil',
            'Sa batterie est pleine',
        ],
        'correct' => 1,
        'explanation' => 'Le mode avion désactive les connexions sans fil : plus de réseau mobile, donc plus de données. Il suffit de le couper.',
    ],
    [
        'name' => 'Q08 — C7 (OS4) : Choix du chargeur de téléphone',
        'questiontext' => '<p><strong>[Compétence 7 — Entretenir son appareil]</strong></p><p>Vrai ou faux : le chargeur sans marque d\'un voisin convient toujours pour recharger son téléphone.</p>',
        'generalfeedback' => '<p><strong>Correction (C7 — OS4) :</strong> <strong>Faux.</strong> Un chargeur sans marque ou non adapté peut charger lentement, faire chauffer l\'appareil ou abîmer la batterie. Il faut utiliser le chargeur de la marque en bon état.</p>',
        'options' => [
            'Vrai (tous les chargeurs se valent tant que le téléphone charge)',
            'Faux (un chargeur sans marque peut faire chauffer ou abîmer le téléphone)',
        ],
        'correct' => 1,
        'explanation' => 'Google prévient qu\'un autre chargeur peut charger lentement ou abîmer le téléphone ; Samsung cite le chargeur tiers parmi les causes de chauffe.',
    ],
    [
        'name' => 'Q09 — C1 (OS1) : Appareil sans batterie face aux coupures',
        'questiontext' => '<p><strong>[Compétence 1 — Identifier les appareils numériques]</strong></p><p>Coupure de courant à la mairie : quel appareil s\'éteint net, faute de batterie intégrée ?</p>',
        'generalfeedback' => '<p><strong>Correction (C1 — OS1) :</strong> L\'<em>ordinateur de bureau</em> n\'a pas de batterie : la coupure l\'éteint net. L\'ordinateur portable et le smartphone tiennent quelques heures sur leur batterie.</p>',
        'options' => [
            'L\'ordinateur portable',
            'L\'ordinateur de bureau',
            'Le smartphone Android',
        ],
        'correct' => 1,
        'explanation' => 'L\'ordinateur de bureau n\'a pas de batterie : la coupure l\'éteint net. Le portable tient quelques heures sur sa batterie.',
    ],
    [
        'name' => 'Q10 — C2 (OS4) : Verrouiller son poste pour une courte absence',
        'questiontext' => '<p><strong>[Compétence 2 — Démarrer et éteindre un appareil]</strong></p><p>Awa quitte son poste <strong>cinq minutes</strong>, son courrier reste ouvert sur Windows 11. Quel est le bon geste ?</p>',
        'generalfeedback' => '<p><strong>Correction (C2 — OS4) :</strong> <code>Windows + L</code> verrouille l\'écran sans fermer les applications : le courrier reste ouvert, et il faudra le mot de passe pour revenir.</p>',
        'options' => [
            'Éteindre par Démarrer, Marche/Arrêt',
            'Verrouiller avec Windows + L',
            'Débrancher l\'unité centrale',
            'Laisser le poste tel quel sans verrouiller',
        ],
        'correct' => 1,
        'explanation' => 'Windows + L verrouille l\'écran sans fermer les applications : le courrier reste ouvert, et il faut le mot de passe pour revenir.',
    ],
    [
        'name' => 'Q11 — C3 (OS2) : Afficher le bureau sous Windows 11',
        'questiontext' => '<p><strong>[Compétence 3 — Naviguer dans l\'interface de base]</strong></p><p>Sous <strong>Windows 11</strong>, plusieurs fenêtres cachent le bureau. Quel raccourci l\'affiche d\'un coup ?</p>',
        'generalfeedback' => '<p><strong>Correction (C3 — OS2) :</strong> <code>Windows + D</code> affiche immédiatement le bureau. <code>Windows + L</code> verrouille, <code>Alt + Tab</code> change de fenêtre et <code>Alt + F4</code> ferme la fenêtre active.</p>',
        'options' => [
            'Windows + D',
            'Windows + L',
            'Alt + Tab',
            'Alt + F4',
        ],
        'correct' => 0,
        'explanation' => 'Windows + D affiche le bureau. Windows + L verrouille, Alt + Tab passe d\'une fenêtre à l\'autre, Alt + F4 ferme la fenêtre.',
    ],
    [
        'name' => 'Q12 — C4 (OS3) : Application absente de l\'écran d\'accueil Android',
        'questiontext' => '<p><strong>[Compétence 4 — Localiser et ouvrir une application]</strong></p><p>Issouf ne voit plus sa calculatrice sur l\'écran d\'accueil de son téléphone Android. Que concluez-vous ?</p>',
        'generalfeedback' => '<p><strong>Correction (C4 — OS3) :</strong> L\'écran d\'accueil ne montre qu\'une partie des applications. Toutes sont rangées dans le <em>tiroir d\'applications</em>, qui s\'ouvre en balayant du bas vers le haut.</p>',
        'options' => [
            'Elle a été supprimée du téléphone',
            'Elle est dans le tiroir d\'applications',
            'Le téléphone doit être redémarré',
        ],
        'correct' => 1,
        'explanation' => 'L\'accueil ne montre qu\'une partie des applications. Toutes sont dans le tiroir, qui s\'ouvre en balayant du bas vers le haut.',
    ],
    [
        'name' => 'Q13 — C5 (OS2) : Taper l\'accent circonflexe (ê) au clavier',
        'questiontext' => '<p><strong>[Compétence 5 — Saisir du texte]</strong></p><p>Sous Ubuntu, Mariam doit écrire « <strong>fête</strong> » au clavier. Comment obtient-elle le <strong>ê</strong> ?</p>',
        'generalfeedback' => '<p><strong>Correction (C5 — OS2) :</strong> Le <code>^</code> est une <em>touche morte</em> : on appuie sur <code>^</code>, on relâche, puis on tape <code>e</code>. Windows et Ubuntu partagent le même clavier AZERTY.</p>',
        'options' => [
            'Appui long sur la touche e',
            'Touche ^, puis la touche e',
            'Maj maintenue, puis e',
            'Verr. Maj, puis e',
        ],
        'correct' => 1,
        'explanation' => 'Le ^ est une touche morte : on appuie, on relâche, puis on tape e. Windows et Ubuntu partagent le même clavier AZERTY.',
    ],
    [
        'name' => 'Q14 — C5 (OS3) : Règles de majuscule et de ponctuation',
        'questiontext' => '<p><strong>[Compétence 5 — Saisir du texte]</strong></p><p>Boureima relit son message. Lequel respecte les règles de majuscule et de ponctuation ?</p>',
        'generalfeedback' => '<p><strong>Correction (C5 — OS3) :</strong> Majuscule au début de la phrase et au nom propre, virgule et point <em>collés au mot qui précède</em>, puis un espace après la virgule : « <strong>Merci, Madame.</strong> ».</p>',
        'options' => [
            '« Merci , Madame. »',
            '« merci, Madame. »',
            '« Merci, Madame. »',
            '« Merci,Madame. »',
        ],
        'correct' => 2,
        'explanation' => 'Majuscule au début de la phrase, virgule et point collés au mot d\'avant, puis un espace après la virgule.',
    ],
    [
        'name' => 'Q15 — C6 (OS3) : Diagnostic d\'une panne Wi-Fi sur un poste',
        'questiontext' => '<p><strong>[Compétence 6 — Connecter un appareil à internet]</strong></p><p>Le PC d\'Awa n\'a pas internet, mais un téléphone se connecte au même Wi-Fi de la mairie. D\'où vient le souci ?</p>',
        'generalfeedback' => '<p><strong>Correction (C6 — OS3) :</strong> Puisqu\'un autre appareil se connecte au même Wi-Fi, la box et l\'opérateur fonctionnent. Le souci vient donc du <em>PC d\'Awa</em>, sur lequel on vérifie les 5 questions de dépannage.</p>',
        'options' => [
            'De la box de la mairie',
            'Du PC d\'Awa',
            'De l\'opérateur internet',
        ],
        'correct' => 1,
        'explanation' => 'Un autre appareil se connecte au même Wi-Fi : la box fonctionne. Le souci vient donc du PC d\'Awa ; on reprend les cinq questions sur ce poste.',
    ],
    [
        'name' => 'Q16 — C7 (OS4) : Choisir entre onduleur et parasurtenseur',
        'questiontext' => '<p><strong>[Compétence 7 — Entretenir son appareil]</strong></p><p>La mairie veut que le PC de bureau tienne assez longtemps, pendant une coupure de courant, pour enregistrer le travail et éteindre proprement. Que choisir ?</p>',
        'generalfeedback' => '<p><strong>Correction (C7 — OS4) :</strong> L\'<em>onduleur</em> contient une batterie qui prend le relais quelques minutes dès la coupure : on enregistre, puis on éteint proprement. Le parasurtenseur protège uniquement contre les variations de tension.</p>',
        'options' => [
            'Un parasurtenseur',
            'Un onduleur',
            'Une multiprise simple',
        ],
        'correct' => 1,
        'explanation' => 'L\'onduleur prend le relais un moment dès la coupure : on enregistre, puis on éteint proprement.',
    ],
];

// Update Pre-test Quiz (id=53, cmid=214)
$pretest_quiz = $DB->get_record('quiz', ['id' => 53, 'course' => $COURSE_ID], '*', MUST_EXIST);
$pretest_quiz->name = 'Pré-test de positionnement';
$pretest_quiz->intro = '<div style="font-family:\'Plus Jakarta Sans\',Segoe UI,sans-serif;line-height:1.6;">
    <p>Ce test évalue votre niveau initial avant la formation sur les <strong>7 compétences du Domaine 1 (Découvrir l\'informatique — Niveau Bronze)</strong>.</p>
    <ul>
        <li><strong>Nombre de questions :</strong> 16 questions à choix unique (Android, Windows 11, Ubuntu 24.04)</li>
        <li><strong>Objectif :</strong> Identifier vos acquis de départ et les points à observer en priorité. Une erreur n\'est pas une sanction !</li>
    </ul>
</div>';
$pretest_quiz->introformat = FORMAT_HTML;
$pretest_quiz->overduehandling = 'autosubmit';
$pretest_quiz->preferredbehaviour = 'deferredfeedback';
$pretest_quiz->attempts = 0; // Autoriser plusieurs passages de positionnement si besoin
$pretest_quiz->questionsperpage = 4;
$pretest_quiz->navmethod = 'free';
$pretest_quiz->shuffleanswers = 1;
$pretest_quiz->reviewattempt = 69888;
$pretest_quiz->reviewcorrectness = 4352;
$pretest_quiz->reviewmaxmarks = 69888;
$pretest_quiz->reviewmarks = 4352;
$pretest_quiz->reviewspecificfeedback = 4352;
$pretest_quiz->reviewgeneralfeedback = 4352;
$pretest_quiz->reviewrightanswer = 4352;
$pretest_quiz->reviewoverallfeedback = 4352;
$pretest_quiz->sumgrades = 16.00000;
$pretest_quiz->grade = 16.00000;
$pretest_quiz->timemodified = $now;
$DB->update_record('quiz', $pretest_quiz);

pact_populate_quiz_questions(53, 214, 'd1_b_pretest', $questions_16);
echo "  ✓ Pré-test de positionnement (quizid=53, cmid=214) peuplé avec 16 questions.\n";

// =============================================================================
// ÉTAPE 3 : SECTION 2 — 🎓 SÉQUENCES D'APPRENTISSAGE (OPTION A)
// =============================================================================
echo "\n[Étape 3/7] Peuplement de la Section 2 (🎓 Séquences d'apprentissage)...\n";

$sec2 = $DB->get_record('course_sections', ['course' => $COURSE_ID, 'section' => 2], '*', MUST_EXIST);
$sec2->name = '🎓 Séquences d\'apprentissage';
$sec2->summary = '<div style="font-family:\'Plus Jakarta Sans\',Segoe UI,sans-serif;color:#1e293b;line-height:1.6;">
    <p>Parcourez dans l\'ordre les <strong>10 séquences interactives</strong> du module Bronze (Domaine 1 : <em>Découvrir l\'informatique</em>), téléchargez votre <strong>Fiche mémo PDF officielle</strong> et le <strong>Support PowerPoint complet</strong>, puis testez vos acquis avec le <strong>Quiz formatif d\'entraînement (16 questions)</strong> avant de passer à l\'examen final.</p>
</div>';
$sec2->summaryformat = FORMAT_HTML;
$sec2->timemodified = $now;
$DB->update_record('course_sections', $sec2);

$css_common = '
<style>
.d1b-wrap { font-family: "Plus Jakarta Sans", "Segoe UI", Roboto, Arial, sans-serif; color: #1e293b; line-height: 1.7; max-width: 980px; margin: 0 auto; }
.d1b-hero { background: linear-gradient(135deg, #0d7a32 0%, #129b40 60%, #15803d 100%); color: #fff; padding: 1.6rem 2rem; border-radius: 16px; margin-bottom: 1.5rem; box-shadow: 0 8px 24px rgba(18,155,64,0.15); }
.d1b-hero .badge-pill { display: inline-block; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35); padding: 4px 12px; border-radius: 999px; font-size: 0.76rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
.d1b-hero h2 { color: #fff; font-size: 1.55rem; font-weight: 800; margin: 0 0 0.4rem; }
.d1b-hero p { margin: 0; opacity: 0.95; font-size: 0.96rem; }
.d1b-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.35rem 1.5rem; margin-bottom: 1.25rem; box-shadow: 0 2px 8px rgba(15,23,42,0.04); }
.d1b-card h3 { color: #0f172a; font-size: 1.2rem; font-weight: 800; margin-top: 0; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 0.5rem; }
.d1b-grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 1.25rem; align-items: start; margin: 1rem 0; }
.d1b-grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin: 1rem 0; }
.d1b-os-box { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; padding: 1rem 1.15rem; }
.d1b-os-box.android { border-top: 4px solid #16a34a; }
.d1b-os-box.windows { border-top: 4px solid #0284c7; }
.d1b-os-box.ubuntu { border-top: 4px solid #ea580c; }
.d1b-os-box h4 { margin: 0 0 0.5rem; font-size: 1.02rem; font-weight: 800; color: #0f172a; }
.d1b-alert-retenir { background: #ecfdf5; border-left: 5px solid #10b981; padding: 0.95rem 1.2rem; border-radius: 8px; margin: 1rem 0; color: #065f46; font-weight: 600; }
.d1b-alert-warn { background: #fffbeb; border-left: 5px solid #f59e0b; padding: 0.95rem 1.2rem; border-radius: 8px; margin: 1rem 0; color: #92400e; font-weight: 600; }
.d1b-story { background: #f0f9ff; border-left: 5px solid #0284c7; padding: 1rem 1.25rem; border-radius: 8px; margin: 1rem 0; color: #0c4a6e; }
.d1b-pratique { background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%); border: 2px solid #facc15; border-radius: 14px; padding: 1.35rem 1.5rem; margin-top: 1.5rem; }
.d1b-pratique h3 { color: #854d0e; margin-top: 0; border-bottom-color: #fde047; }
.d1b-fig { text-align: center; margin: 1rem 0; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 0.85rem; }
.d1b-fig img { max-width: 100%; height: auto; max-height: 360px; border-radius: 8px; }
.d1b-fig figcaption { font-size: 0.83rem; color: #64748b; margin-top: 0.45rem; font-style: italic; }
.d1b-table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.92rem; }
.d1b-table th { background: #0f172a; color: #fff; padding: 10px 12px; text-align: left; border: 1px solid #1e293b; }
.d1b-table td { padding: 9px 12px; border: 1px solid #cbd5e1; vertical-align: top; }
.d1b-table tr:nth-child(even) td { background: #f8fafc; }
.d1b-kbd { background: #f1f5f9; border: 1px solid #94a3b8; border-bottom-width: 2px; border-radius: 5px; padding: 2px 7px; font-family: monospace; font-size: 0.88em; font-weight: 700; color: #0f172a; }
</style>';

$pages_spec = [
    // -------------------------------------------------------------------------
    // PAGE 1 : SÉQUENCE 1 — INTRODUCTION & MISE EN CONTEXTE (Slides 1 à 11)
    // -------------------------------------------------------------------------
    [
        'idnumber' => 'd1_b_page_01_intro',
        'name'     => '🎬 Séquence 1 — Introduction & Mise en contexte : Awa et l\'ordinateur du service',
        'intro'    => 'Présentation du module Bronze, des 5 objectifs (OS1 à OS5), des 7 compétences et du cas fil rouge d\'Awa à la mairie de Koudougou (Diapositives 1 à 11).',
        'images'   => ['image3.png', 'image4.jpg', 'image5.jpg'],
        'content'  => $css_common . '
<div class="d1b-wrap">
    <div class="d1b-hero">
        <span class="badge-pill">Séquence 1 · Durée : 15 min · Niveau Bloom : OS1 & OS3</span>
        <h2>🎬 Introduction et mise en contexte : Découvrir l\'informatique</h2>
        <p>Bienvenue dans le Pack Bronze (Niveau Base) du Domaine 1. Aujourd\'hui, nous apprivoisons trois appareils complémentaires : le <strong>smartphone Android</strong>, l\'ordinateur <strong>Windows 11</strong> et l\'ordinateur <strong>Ubuntu 24.04</strong>.</p>
    </div>

    <div class="d1b-card">
        <h3>🪪 1. Carte d\'identité de votre formation</h3>
        <div class="d1b-grid-2">
            <div>
                <ul>
                    <li><strong>Titre officiel :</strong> Découvrir l\'informatique, niveau Base — les 7 compétences du Référentiel sur smartphone Android, ordinateur Windows 11 et ordinateur Ubuntu 24.04.</li>
                    <li><strong>Durée :</strong> 6 heures en une journée (320 minutes d\'activités + deux pauses de 20 minutes).</li>
                    <li><strong>Public cible :</strong> Agents publics, étudiants, commerçants et entrepreneurs débutants ou peu à l\'aise avec l\'ordinateur, y compris ceux qui utilisent déjà un smartphone.</li>
                    <li><strong>Prérequis :</strong> Aucun. Ici, personne n\'est jugé : celui qui n\'a jamais touché un ordinateur est aussi bienvenu que celui qui s\'en sert déjà.</li>
                    <li><strong>Référence :</strong> MTDPCE / DGTD, Domaine 1, Niveau Bronze (codes <code>D1-B-01</code> à <code>D1-B-07</code>).</li>
                </ul>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image3.png" alt="Les trois appareils de la formation : Smartphone Android, PC Windows 11 et PC Ubuntu 24.04" />
                <figcaption>Les trois appareils de la formation : Smartphone Android, PC Windows 11 et PC Ubuntu 24.04</figcaption>
            </figure>
        </div>
        <div class="d1b-alert-retenir">
            🎯 <strong>Objectif général :</strong> À la fin de la journée, le participant utilise seul un smartphone Android, un ordinateur Windows 11 et un ordinateur Ubuntu 24.04 : il les reconnaît, les démarre et s\'y repère, ouvre une application, écrit un texte correct, se connecte à internet et entretient son matériel, jusqu\'à concevoir une fiche mémo utile à un usager.
        </div>
    </div>

    <div class="d1b-card">
        <h3>🪜 2. L\'escalier des 5 objectifs spécifiques (Taxonomie de Bloom)</h3>
        <table class="d1b-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Niveau de Bloom</th>
                    <th>Ce que vous serez capable de faire à l\'issue du module</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><strong>OS1</strong></td><td>Connaissance &amp; Compréhension</td><td>Nommer les appareils, les systèmes et les touches, et expliquer leur rôle.</td></tr>
                <tr><td><strong>OS2</strong></td><td>Application</td><td>Démarrer, ouvrir une application, écrire et se connecter sur les trois systèmes.</td></tr>
                <tr><td><strong>OS3</strong></td><td>Analyse</td><td>Repérer sur un écran ou dans un texte ce qui bloque, et en trouver la cause.</td></tr>
                <tr><td><strong>OS4</strong></td><td>Évaluation</td><td>Choisir et justifier le bon geste : veille ou arrêt, Wi-Fi ou données, quel chargeur, onduleur ou parasurtenseur.</td></tr>
                <tr><td><strong>OS5</strong></td><td>Création</td><td>Concevoir en groupe la fiche mémo d\'un usager, puis la tester sur l\'appareil réel.</td></tr>
            </tbody>
        </table>
    </div>

    <div class="d1b-card">
        <h3>👩🏾‍💼 3. Mise en situation : Awa et l\'ordinateur du service</h3>
        <div class="d1b-grid-2">
            <div>
                <div class="d1b-story">
                    <p style="margin-top:0;"><strong>Cas fil rouge — Awa Ouédraogo, 38 ans (personnage fictif) :</strong></p>
                    <ul>
                        <li>Agente administrative à la <strong>mairie de Koudougou</strong>.</li>
                        <li>Sur <strong>WhatsApp</strong> avec son téléphone, elle est très à l\'aise au quotidien.</li>
                        <li>Mais l\'unique ordinateur de bureau du service l\'intimide : elle a peur d\'appuyer sur le mauvais bouton.</li>
                        <li>Hier, une <strong>coupure de courant</strong> a tout éteint d\'un coup, et elle ne sait pas si son courrier a été enregistré.</li>
                    </ul>
                </div>
                <p>Tout au long du module, trois autres personnages nous accompagneront également :</p>
                <ul>
                    <li><strong>Issouf :</strong> commerçant au marché sur smartphone <em>Android</em>.</li>
                    <li><strong>Mariam :</strong> étudiante qui travaille au cyber sur un ordinateur <em>Ubuntu 24.04</em>.</li>
                    <li><strong>Boureima :</strong> cultivateur près de Dédougou qui utilise son smartphone en milieu rural.</li>
                </ul>
            </div>
            <div>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image5.jpg" alt="Awa devant l\'ordinateur du service" />
                    <figcaption>Awa au bureau : à l\'aise sur téléphone, mais intimidée par l\'ordinateur du service</figcaption>
                </figure>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image4.jpg" alt="Un atelier numérique au Burkina Faso" />
                    <figcaption>Un atelier numérique au Burkina Faso : apprendre par la pratique en binôme</figcaption>
                </figure>
            </div>
        </div>
        <div class="d1b-alert-warn">
            💬 <strong>Question de réflexion :</strong> Qu\'est-ce qui vous freine parfois devant un ordinateur ? Rassurez-vous : la plupart de ces hésitations viennent simplement du manque d\'habitude, et l\'habitude se prend dès aujourd\'hui !
        </div>
    </div>
</div>',
    ],

    // -------------------------------------------------------------------------
    // PAGE 2 : COMPÉTENCE 1 (D1-B-01) — IDENTIFIER LES APPAREILS (Slides 12-16, 66)
    // -------------------------------------------------------------------------
    [
        'idnumber' => 'd1_b_page_02_c1',
        'name'     => '📱 Compétence 1 (D1-B-01) — Identifier les appareils numériques et leur système + Pratique 1',
        'intro'    => 'Reconnaître un smartphone, un ordinateur de bureau et un ordinateur portable ; comprendre le rôle du système d\'exploitation (Android, Windows 11, Ubuntu 24.04) (Diapositives 12 à 16 et Pratique 1).',
        'images'   => ['image6.jpg', 'image8.jpg', 'image9.png', 'image10.jpg'],
        'content'  => $css_common . '
<div class="d1b-wrap">
    <div class="d1b-hero">
        <span class="badge-pill">Compétence 1 sur 7 · Code D1-B-01 · Niveau Bloom : OS1 (Connaissance &amp; Compréhension)</span>
        <h2>📱 Identifier les appareils numériques et leur système</h2>
        <p>Reconnaître un smartphone Android, un ordinateur de bureau ou portable sous Windows 11 et Ubuntu 24.04, et distinguer matériel, système d\'exploitation et application.</p>
    </div>

    <div class="d1b-card">
        <h3>📱 1. Le smartphone (téléphone multifonction)</h3>
        <div class="d1b-grid-2">
            <div>
                <ul>
                    <li>Un téléphone équipé d\'un <strong>écran tactile</strong> : on le commande directement avec le doigt.</li>
                    <li>Il fait tourner des <strong>applications</strong> (WhatsApp, Appareil photo, Calculatrice, Mobile Money…).</li>
                    <li>Le logiciel qui fait marcher tout cela s\'appelle, dans ce module, <strong>Android</strong>.</li>
                </ul>
                <div class="d1b-alert-retenir">
                    📖 <strong>Définition — Application :</strong> Programme, ou logiciel, qui permet de réaliser une ou plusieurs tâches (ex. : WhatsApp pour envoyer des messages, Calculatrice pour calculer).
                </div>
                <div class="d1b-alert-warn">
                    ⚠️ <strong>Ne pas confondre :</strong> <em>Tecno</em>, <em>Infinix</em> ou <em>Samsung</em> est la <strong>marque</strong> du téléphone ; <strong>Android</strong> est son <strong>système d\'exploitation</strong> ; <em>WhatsApp</em> est une <strong>application</strong>.
                </div>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image6.jpg" alt="Un smartphone Android vu de face" />
                <figcaption>Un smartphone Android vu de face : écran tactile et icônes d\'applications</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🖥️ 2. L\'ordinateur : de bureau ou portable</h3>
        <div class="d1b-grid-2">
            <div>
                <div class="d1b-os-box windows" style="margin-bottom:0.85rem;">
                    <h4>🏢 L\'ordinateur de bureau (PC fixe)</h4>
                    <ul>
                        <li>Composé de <strong>plusieurs éléments reliés par des câbles</strong> : l\'<strong>écran</strong>, le <strong>clavier</strong>, la <strong>souris</strong> et l\'<strong>unité centrale</strong> (le boîtier).</li>
                        <li>Il ne se transporte pas et <strong>n\'a pas de batterie intégrée</strong> : en cas de coupure de courant, il s\'éteint net !</li>
                    </ul>
                </div>
                <div class="d1b-os-box android">
                    <h4>💻 L\'ordinateur portable</h4>
                    <ul>
                        <li>Réunit tout en un seul appareil pliable (écran, clavier, pavé tactile), avec une <strong>batterie intégrée</strong>.</li>
                        <li>Usages courants des ordinateurs : rédiger des courriers administratifs, créer des tableaux, imprimer.</li>
                    </ul>
                </div>
                <div class="d1b-alert-retenir">
                    💡 <strong>À retenir :</strong> En cas de coupure de courant, l\'ordinateur portable tient quelques heures grâce à sa batterie, ce qui protège le travail d\'Awa.
                </div>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image8.jpg" alt="Ordinateur de bureau : unité centrale, écran, clavier, souris, haut-parleurs" />
                <figcaption>Les éléments d\'un ordinateur de bureau : unité centrale, écran, clavier, souris et haut-parleurs</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>⚙️ 3. Le système d\'exploitation : l\'interprète entre vous et la machine</h3>
        <div class="d1b-grid-2">
            <div>
                <ul>
                    <li>Il fait le <strong>lien entre vous et la machine</strong> (le matériel).</li>
                    <li>Il fait tourner les <strong>applications</strong>.</li>
                    <li>Il gère l\'écran, le clavier, la souris et la mémoire.</li>
                    <li><strong>Trois exemples étudiés dans ce module :</strong> <code>Android</code> (sur smartphone), <code>Windows 11</code> et <code>Ubuntu 24.04</code> (sur ordinateur).</li>
                </ul>
                <div class="d1b-story">
                    🗣️ <strong>Image du marché de Koudougou :</strong> Pensez à un interprète au marché : l\'acheteur parle mooré, le vendeur parle dioula, et l\'interprète transmet fidèlement. Le système d\'exploitation joue ce rôle : il transmet vos gestes à la machine !
                </div>
                <div class="d1b-alert-retenir">
                    📖 <strong>Définition — Système d\'exploitation (OQLF) :</strong> Logiciel qui relie le matériel et l\'utilisateur et fait fonctionner les programmes.
                </div>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image9.png" alt="Schéma en couches : Vous, Applications, Système, Matériel" />
                <figcaption>Schéma en couches : Vous → Applications → Système d\'exploitation → Matériel</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>📊 4. Au Burkina Faso, le téléphone d\'abord (Chiffres INSD)</h3>
        <div class="d1b-grid-2">
            <div>
                <ul>
                    <li>Selon l\'Enquête Démographique et de Santé de l\'<strong>INSD</strong> (données 2021) :
                        <ul>
                            <li><strong>96 % des ménages</strong> possèdent un téléphone mobile.</li>
                            <li><strong>8 % seulement</strong> possèdent un ordinateur.</li>
                        </ul>
                    </li>
                    <li>La plupart d\'entre nous ont donc découvert le numérique sur le téléphone avant de toucher un ordinateur.</li>
                </ul>
                <div class="d1b-alert-retenir">
                    💡 <strong>À retenir :</strong> L\'ordinateur est plus rare à la maison : il est tout à fait normal qu\'il intimide au début. Voilà pourquoi Awa est très à l\'aise sur WhatsApp mais hésite devant le PC de la mairie !
                </div>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image10.jpg" alt="Un vendeur de téléphones" />
                <figcaption>Au Burkina Faso, 96 % des ménages disposent d\'un téléphone mobile contre 8 % pour l\'ordinateur (INSD)</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-pratique">
        <h3>🛠️ Activité pratique 1 — Identifier les appareils numériques (Durée : 7 min)</h3>
        <p><strong>Consigne (en binôme ou en auto-vérification sur les 3 systèmes) :</strong></p>
        <div class="d1b-grid-3">
            <div class="d1b-os-box android">
                <h4>📱 Smartphone Android</h4>
                <ol>
                    <li>Nommez le type d\'appareil et son système d\'exploitation (<em>Android</em>, sans le confondre avec la marque du téléphone).</li>
                    <li>Citez deux usages courants du téléphone à part téléphoner.</li>
                </ol>
            </div>
            <div class="d1b-os-box windows">
                <h4>🪟 Ordinateur Windows 11</h4>
                <ol>
                    <li>Repérez s\'il s\'agit d\'un PC de bureau (avec unité centrale) ou d\'un portable.</li>
                    <li>Reconnaissez Windows 11 grâce au bouton <strong>Démarrer</strong> dans la barre des tâches en bas.</li>
                </ol>
            </div>
            <div class="d1b-os-box ubuntu">
                <h4>🐧 Ordinateur Ubuntu 24.04</h4>
                <ol>
                    <li>Désignez l\'écran, le clavier et la souris.</li>
                    <li>Reconnaissez Ubuntu 24.04 grâce au <strong>dock à gauche</strong> et à l\'horloge tout en haut.</li>
                </ol>
            </div>
        </div>
        <p style="margin-bottom:0;font-weight:700;color:#854d0e;">✅ Critère de réussite : Les 3 appareils et leur système d\'exploitation sont nommés sans aucune confusion entre marque, système et application.</p>
    </div>
</div>',
    ],

    // -------------------------------------------------------------------------
    // PAGE 3 : COMPÉTENCE 2 (D1-B-02) — DÉMARRER ET ÉTEINDRE (Slides 17-24, 67)
    // -------------------------------------------------------------------------
    [
        'idnumber' => 'd1_b_page_03_c2',
        'name'     => '⚡ Compétence 2 (D1-B-02) — Démarrer, verrouiller et éteindre un appareil + Pratique 2',
        'intro'    => 'Repérer le bouton d\'alimentation, ouvrir et verrouiller sa session (Windows + L, Super + L), éteindre proprement par le menu et choisir entre veille, arrêt et arrêt forcé (Diapositives 17 à 24 et Pratique 2).',
        'images'   => ['image11.jpg', 'image13.jpg', 'image14.png', 'image15.jpg', 'image16.png', 'image17.jpg'],
        'content'  => $css_common . '
<div class="d1b-wrap">
    <div class="d1b-hero">
        <span class="badge-pill">Compétence 2 sur 7 · Code D1-B-02 · Niveau Bloom : OS2 (Application) &amp; OS4 (Évaluation)</span>
        <h2>⚡ Démarrer, verrouiller et éteindre un appareil</h2>
        <p>Allumer, verrouiller, redémarrer et éteindre proprement un smartphone Android, un PC Windows 11 et un PC Ubuntu 24.04, et choisir le bon geste selon la situation.</p>
    </div>

    <div class="d1b-card">
        <h3>🔘 1. Allumer : le bouton d\'alimentation et la patience</h3>
        <div class="d1b-grid-2">
            <div>
                <ul>
                    <li>Cherchez le <strong>symbole universel d\'alimentation</strong> : un cercle ouvert en haut avec un trait vertical.</li>
                    <li><strong>Sur un PC de bureau :</strong> appuyez sur le bouton de l\'<strong>unité centrale</strong> (et vérifiez que l\'écran est allumé).</li>
                    <li><strong>Sur un ordinateur portable :</strong> bouton situé au-dessus ou sur le côté du clavier.</li>
                    <li><strong>Sur le smartphone Android :</strong> faites un <strong>appui long</strong> sur le bouton <em>Marche/Arrêt</em> sur le côté.</li>
                    <li><strong>Patience au démarrage :</strong> un ordinateur met en général <strong>30 secondes à 2 minutes</strong> pour démarrer.</li>
                </ul>
                <div class="d1b-alert-retenir">
                    💡 <strong>À retenir :</strong> Rien ne bouge tout de suite pendant les premières secondes ? Pas d\'inquiétude, patientez ! Ne rappuyez pas sur le bouton, car l\'ordinateur est déjà en train de démarrer.
                </div>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image11.jpg" alt="Symbole du bouton d\'alimentation" />
                <figcaption>Le symbole universel du bouton d\'alimentation (Marche / Arrêt)</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🔒 2. Déverrouiller, ouvrir sa session et verrouiller quand on s\'absente</h3>
        <div class="d1b-grid-2">
            <div>
                <ul>
                    <li><strong>Sur téléphone :</strong> vous déverrouillez avec votre code PIN ou votre schéma.</li>
                    <li><strong>Sur ordinateur :</strong> vous ouvrez votre session avec votre <strong>mot de passe</strong> (en formation, utilisez toujours les codes et sessions d\'essai affichés au tableau).</li>
                    <li><strong>Vous vous absentez 5 minutes ? Verrouillez votre écran !</strong> Vos applications restent ouvertes, mais il faudra le mot de passe pour revenir :
                        <ul>
                            <li>Sous <strong>Windows 11</strong> : appuyez sur <span class="d1b-kbd">Windows</span> + <span class="d1b-kbd">L</span></li>
                            <li>Sous <strong>Ubuntu 24.04</strong> : appuyez sur <span class="d1b-kbd">Super</span> + <span class="d1b-kbd">L</span></li>
                            <li>Sur <strong>Android</strong> : faites un <strong>appui court</strong> sur le bouton <em>Marche/Arrêt</em></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image13.jpg" alt="Mains sur le clavier d\'un ordinateur portable" />
                <figcaption>Ouvrir sa session par mot de passe et verrouiller dès qu\'on s\'absente (Windows + L ou Super + L)</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🔄 3. Comparatif : Démarrer, verrouiller et éteindre sur les 3 systèmes</h3>
        <table class="d1b-table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>📱 Android</th>
                    <th>🪟 Windows 11</th>
                    <th>🐧 Ubuntu 24.04</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Allumer</strong></td>
                    <td>Appui long sur le bouton <strong>Marche/Arrêt</strong></td>
                    <td>Bouton de l\'<strong>unité centrale</strong> (ou du portable)</td>
                    <td>Bouton d\'<strong>alimentation</strong></td>
                </tr>
                <tr>
                    <td><strong>Éteindre</strong></td>
                    <td>Menu d\'alimentation → <strong>Éteindre</strong></td>
                    <td><strong>Démarrer</strong> → <strong>Marche/Arrêt</strong> → <strong>Arrêter</strong></td>
                    <td><strong>Menu système</strong> (en haut à droite) → <strong>Bouton d\'extinction</strong> → Éteindre</td>
                </tr>
                <tr>
                    <td><strong>Redémarrer</strong></td>
                    <td>Menu d\'alimentation → <strong>Redémarrer</strong></td>
                    <td><strong>Démarrer</strong> → <strong>Marche/Arrêt</strong> → <strong>Redémarrer</strong></td>
                    <td><strong>Menu système</strong> → Bouton d\'extinction → <strong>Redémarrer</strong></td>
                </tr>
                <tr>
                    <td><strong>Verrouiller</strong></td>
                    <td>Bouton Marche/Arrêt (<strong>appui court</strong>)</td>
                    <td><span class="d1b-kbd">Windows</span> + <span class="d1b-kbd">L</span></td>
                    <td><span class="d1b-kbd">Super</span> + <span class="d1b-kbd">L</span></td>
                </tr>
            </tbody>
        </table>
        <div class="d1b-alert-retenir">
            🛑 <strong>Règle d\'or :</strong> Sur les trois systèmes, on allume par un bouton, mais <strong>on éteint toujours par le menu, jamais en débranchant la prise !</strong>
        </div>
    </div>

    <div class="d1b-card">
        <h3>⚖️ 4. Choisir le bon geste : Veille, Arrêt ou Arrêt forcé ? (OS4)</h3>
        <div class="d1b-grid-2">
            <div>
                <p><strong>Veille ou Arrêt ? L\'image de la lampe tempête :</strong></p>
                <ul>
                    <li><strong>La veille</strong> (baisser la mèche de la lampe tempête) : consomme très peu d\'énergie, le travail reste ouvert et on reprend tout de suite. Idéal pour une <strong>courte pause</strong> (après avoir enregistré son travail par prudence).</li>
                    <li><strong>L\'arrêt complet</strong> (souffler la lampe) : ne consomme plus rien. Indispensable <strong>le soir en quittant le service</strong> ou quand la <strong>batterie est faible</strong>.</li>
                </ul>
                <p><strong>L\'arrêt forcé : uniquement en dernier recours !</strong></p>
                <ul>
                    <li>Seulement si l\'appareil est totalement <strong>figé</strong> (bloqué), et toujours <em>après avoir essayé le menu</em>.</li>
                    <li>Sur Android : maintenir Marche/Arrêt appuyé (~60 s) ou sur Samsung : <em>Bouton latéral + Volume bas</em> pendant plus de 7 secondes.</li>
                    <li>Sur un PC de formation figé : <strong>appelez d\'abord le formateur</strong> ou le responsable du poste.</li>
                </ul>
            </div>
            <div>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image14.png" alt="Schéma : veille, arrêt, redémarrage, quand les utiliser" />
                    <figcaption>Quand choisir la veille, l\'arrêt complet ou le redémarrage ?</figcaption>
                </figure>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image15.jpg" alt="Bouton d\'alimentation d\'un portable" />
                    <figcaption>L\'appui prolongé sur le bouton d\'alimentation (arrêt forcé) ne s\'utilise qu\'en dernier recours</figcaption>
                </figure>
            </div>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🖥️ 5. Démonstrations guidées : Windows 11 et Ubuntu 24.04</h3>
        <div class="d1b-grid-2">
            <div>
                <h4>🪟 Sur Windows 11</h4>
                <ol>
                    <li>Allumez par le bouton de l\'unité centrale et ouvrez la session.</li>
                    <li>Appuyez sur <span class="d1b-kbd">Windows</span> + <span class="d1b-kbd">L</span> pour verrouiller, puis revenez avec le mot de passe.</li>
                    <li>Cliquez sur <strong>Démarrer</strong> (3), puis sur le bouton <strong>Marche/Arrêt</strong> (4).</li>
                    <li>Choisissez <strong>Redémarrer</strong> ou <strong>Arrêter</strong>.</li>
                </ol>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image16.png" alt="Menu Démarrer de Windows 11 et bouton Marche/Arrêt" />
                    <figcaption>Menu Démarrer de Windows 11 : le bouton Marche/Arrêt est en bas à droite du menu (zone 4)</figcaption>
                </figure>
            </div>
            <div>
                <h4>🐧 Sur Ubuntu 24.04</h4>
                <ol>
                    <li>Cliquez tout <strong>en haut à droite</strong> pour ouvrir le <strong>menu système</strong>.</li>
                    <li>Cliquez sur le <strong>bouton d\'extinction</strong> (numéro 1 sur la capture).</li>
                    <li>Choisissez <strong>Redémarrer</strong> (<em>Restart</em>) ou <strong>Éteindre</strong> (<em>Power Off</em>).</li>
                    <li>Pour une courte pause : <span class="d1b-kbd">Super</span> + <span class="d1b-kbd">L</span> suffit !</li>
                </ol>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image17.jpg" alt="Menu système d\'Ubuntu 24.04 en haut à droite" />
                    <figcaption>Menu système d\'Ubuntu 24.04 : le bouton d\'extinction (1) est en haut à droite du panneau</figcaption>
                </figure>
            </div>
        </div>
    </div>

    <div class="d1b-pratique">
        <h3>🛠️ Activité pratique 2 — Démarrer et éteindre un appareil (Durée : 8 min)</h3>
        <div class="d1b-grid-3">
            <div class="d1b-os-box android">
                <h4>📱 Smartphone Android</h4>
                <ol>
                    <li>Appui court sur Marche/Arrêt pour verrouiller, puis déverrouillez.</li>
                    <li>Appui long sur Marche/Arrêt (ou Latéral + Volume bas sur Samsung) pour afficher le menu : repérez <em>Redémarrer</em> et <em>Éteindre</em>.</li>
                </ol>
            </div>
            <div class="d1b-os-box windows">
                <h4>🪟 Ordinateur Windows 11</h4>
                <ol>
                    <li>Allumez et ouvrez la session, puis verrouillez avec <span class="d1b-kbd">Windows</span> + <span class="d1b-kbd">L</span>.</li>
                    <li>Éteignez proprement par <strong>Démarrer → Marche/Arrêt → Arrêter</strong>.</li>
                </ol>
            </div>
            <div class="d1b-os-box ubuntu">
                <h4>🐧 Ordinateur Ubuntu 24.04</h4>
                <ol>
                    <li>Verrouillez l\'écran avec <span class="d1b-kbd">Super</span> + <span class="d1b-kbd">L</span> puis rouvrez la session.</li>
                    <li>Éteignez par le <strong>menu système en haut à droite → bouton d\'extinction</strong>.</li>
                </ol>
            </div>
        </div>
        <p style="margin-bottom:0;font-weight:700;color:#854d0e;">✅ Critère de réussite : Arrêt réalisé par le menu sur les 3 appareils, sans jamais débrancher ni forcer l\'arrêt.</p>
    </div>
</div>',
    ],

    // -------------------------------------------------------------------------
    // PAGE 4 : COMPÉTENCE 3 (D1-B-03) — NAVIGUER DANS L'INTERFACE (Slides 25-32, 68)
    // -------------------------------------------------------------------------
    [
        'idnumber' => 'd1_b_page_04_c3',
        'name'     => '🧭 Compétence 3 (D1-B-03) — Naviguer dans l\'interface de base et maîtriser la souris + Pratique 3',
        'intro'    => 'Se repérer sur l\'écran d\'accueil Android, le bureau Windows 11 et le bureau Ubuntu 24.04 ; maîtriser les 4 gestes de la souris et passer d\'une fenêtre à l\'autre (Alt + Tab, Super + Tab) (Diapositives 25 à 32 et Pratique 3).',
        'images'   => ['image18.jpg', 'image19.png', 'image20.jpg', 'image21.png', 'image22.png'],
        'content'  => $css_common . '
<div class="d1b-wrap">
    <div class="d1b-hero">
        <span class="badge-pill">Compétence 3 sur 7 · Code D1-B-03 · Niveau Bloom : OS2 (Application) &amp; OS3 (Analyse)</span>
        <h2>🧭 Naviguer dans l\'interface de base et maîtriser la souris</h2>
        <p>Lire et utiliser les zones clés de l\'écran d\'accueil d\'Android, du bureau de Windows 11 et du bureau d\'Ubuntu 24.04, manipuler la souris et basculer entre plusieurs fenêtres.</p>
    </div>

    <div class="d1b-card">
        <h3>📱 1. L\'écran d\'accueil Android : les 5 zones à repérer</h3>
        <div class="d1b-grid-2">
            <div>
                <ol>
                    <li><strong>Zone 1 (tout en haut) — La barre d\'état :</strong> affiche l\'<strong>heure</strong>, le <strong>réseau</strong> (Wi-Fi, barres SIM, mode avion) et le niveau de <strong>batterie</strong>. Balayez du haut vers le bas pour voir les <em>notifications</em>, et balayez encore pour ouvrir les <em>réglages rapides</em>.</li>
                    <li><strong>Zone 2 :</strong> La barre de recherche.</li>
                    <li><strong>Zone 3 :</strong> Les icônes de vos applications sur l\'écran d\'accueil (balayez sur le côté s\'il y a plusieurs écrans).</li>
                    <li><strong>Zone 4 :</strong> Vos applications favorites en bas.</li>
                    <li><strong>Zone 5 (tout en bas) — La barre de navigation :</strong> les trois boutons <strong>Retour</strong>, <strong>Accueil</strong> et <strong>Récents</strong> (ou gestes équivalents).</li>
                </ol>
                <div class="d1b-alert-retenir">
                    📖 <strong>Définition — Écran d\'accueil :</strong> Écran principal d\'Android où se trouvent les applications ; on peut en avoir plusieurs. Pour voir <em>toutes</em> les applications, balayez du bas vers le haut.
                </div>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image18.jpg" alt="Capture d\'un écran d\'accueil Android 14 avec zones numérotées de 1 à 5" />
                <figcaption>Les 5 zones de l\'écran d\'accueil Android : (1) Barre d\'état, (2) Recherche, (3) Applications, (4) Favoris, (5) Navigation</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🪟 2. Le bureau de Windows 11 et sa barre des tâches</h3>
        <div class="d1b-grid-2">
            <div>
                <p>Sur l\'ordinateur d\'Awa, regardez la <strong>barre des tâches</strong> tout en bas de l\'écran :</p>
                <ol>
                    <li><strong>Démarrer (1) :</strong> le menu principal (portant le logo Windows). La touche <span class="d1b-kbd">Windows</span> du clavier l\'ouvre aussi.</li>
                    <li><strong>Rechercher (2) :</strong> tapez le nom d\'une application pour la trouver immédiatement.</li>
                    <li><strong>Applications épinglées (3) :</strong> un simple clic suffit pour les ouvrir.</li>
                    <li><strong>Zone d\'état à droite (4) :</strong> réseau Wi-Fi, son, batterie et heure (équivalent de la barre d\'état du téléphone).</li>
                </ol>
                <div class="d1b-alert-retenir">
                    ⌨️ <strong>Astuce clavier :</strong> Trop de fenêtres cachent votre bureau ? Appuyez sur <span class="d1b-kbd">Windows</span> + <span class="d1b-kbd">D</span> pour afficher le bureau d\'un seul coup !
                </div>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image19.png" alt="Schéma du bureau de Windows 11 avec zones numérotées" />
                <figcaption>Le bureau de Windows 11 : Démarrer (1), Rechercher (2), Applications épinglées (3) et zone système à droite (4)</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🐧 3. Le bureau d\'Ubuntu 24.04</h3>
        <div class="d1b-grid-2">
            <div>
                <p>Sous Ubuntu 24.04, les mêmes repères existent mais sont disposés autrement :</p>
                <ol>
                    <li><strong>Le dock, à gauche (1) :</strong> barre latérale verticale qui contient vos lanceurs d\'applications favorites.</li>
                    <li><strong>En bas du dock (2) :</strong> le bouton à neuf points qui affiche la grille de <strong>toutes les applications</strong>.</li>
                    <li><strong>En haut au milieu (3) :</strong> la barre supérieure et l\'<strong>horloge</strong>.</li>
                    <li><strong>En haut à droite (4) :</strong> le <strong>menu système</strong> (Wi-Fi, son, batterie, extinction).</li>
                </ol>
                <div class="d1b-alert-retenir">
                    📖 <strong>Définitions clés :</strong><br/>
                    • <strong>Dock :</strong> Barre latérale gauche d\'Ubuntu contenant les lanceurs d\'applications.<br/>
                    • <strong>Touche Super :</strong> Touche située près de <code>Alt</code> (portant souvent le logo Windows) qui ouvre la vue d\'ensemble des Activités sous Ubuntu.
                </div>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image20.jpg" alt="Capture du bureau d\'Ubuntu 24.04 avec zones numérotées" />
                <figcaption>Le bureau d\'Ubuntu 24.04 : (1) Dock à gauche, (2) Bouton Toutes les applications, (3) Horloge en haut, (4) Menu système à droite</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🖱️ 4. Maîtriser la souris et passer d\'une fenêtre à l\'autre</h3>
        <div class="d1b-grid-2">
            <div>
                <h4>Les 4 gestes de la souris (index sur le bouton gauche)</h4>
                <ul>
                    <li><strong>Clic (clic gauche) :</strong> un appui court sur le bouton de gauche pour sélectionner ou activer un bouton.</li>
                    <li><strong>Double-clic :</strong> deux appuis rapides sur le bouton gauche <em>sans bouger la souris</em> pour ouvrir une icône sur le bureau ou agrandir une fenêtre par sa barre de titre.</li>
                    <li><strong>Clic droit :</strong> ouvre un petit menu d\'options adapté à l\'endroit pointé.</li>
                    <li><strong>Glisser-déposer :</strong> garder le bouton gauche appuyé, déplacer la souris, puis relâcher.</li>
                </ul>
                <div class="d1b-alert-warn">
                    ⚠️ <strong>Piège fréquent :</strong> Votre double-clic sélectionne l\'icône mais n\'ouvre rien ? Vos deux appuis sont <strong>trop lents</strong> et comptent comme deux clics simples : réessayez un peu plus vite sans bouger la souris !
                </div>
            </div>
            <div>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image21.png" alt="Schéma de la souris : clic principal, clic droit, molette" />
                    <figcaption>La souris : bouton gauche (clic et double-clic), molette au centre, bouton droit (menu d\'options)</figcaption>
                </figure>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image22.png" alt="Grille des applications d\'Ubuntu 24.04" />
                    <figcaption>La grille des applications d\'Ubuntu 24.04 ouverte par le bouton en bas du dock</figcaption>
                </figure>
            </div>
        </div>

        <h4>Même besoin, trois façons de faire :</h4>
        <table class="d1b-table">
            <thead>
                <tr>
                    <th>Besoin</th>
                    <th>📱 Android</th>
                    <th>🪟 Windows 11</th>
                    <th>🐧 Ubuntu 24.04</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Voir toutes les applications</strong></td>
                    <td>Balayer du bas vers le haut</td>
                    <td>Démarrer, puis « Toutes »</td>
                    <td>Bouton à 9 points en bas du dock</td>
                </tr>
                <tr>
                    <td><strong>Passer à une autre fenêtre ouverte</strong></td>
                    <td>Bouton ou geste <strong>Récents</strong></td>
                    <td>Garder <span class="d1b-kbd">Alt</span> appuyé et taper <span class="d1b-kbd">Tab</span></td>
                    <td>Garder <span class="d1b-kbd">Super</span> appuyé et taper <span class="d1b-kbd">Tab</span></td>
                </tr>
                <tr>
                    <td><strong>Fermer une application</strong></td>
                    <td>Balayer l\'aperçu vers le haut dans Récents</td>
                    <td>Croix en haut à droite ou <span class="d1b-kbd">Alt</span> + <span class="d1b-kbd">F4</span></td>
                    <td>Croix en haut à droite ou <span class="d1b-kbd">Alt</span> + <span class="d1b-kbd">F4</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d1b-pratique">
        <h3>🛠️ Activité pratique 3 — Naviguer dans l\'interface de base (Durée : 8 min)</h3>
        <div class="d1b-grid-3">
            <div class="d1b-os-box android">
                <h4>📱 Smartphone Android</h4>
                <ol>
                    <li>Balayez du haut vers le bas pour afficher les notifications et réglages rapides.</li>
                    <li>Ouvrez <em>Récents</em> puis revenez à l\'écran d\'accueil.</li>
                </ol>
            </div>
            <div class="d1b-os-box windows">
                <h4>🪟 Ordinateur Windows 11</h4>
                <ol>
                    <li>Ouvrez Démarrer et repérez Rechercher.</li>
                    <li>Avec deux fenêtres ouvertes, passez de l\'une à l\'autre avec <span class="d1b-kbd">Alt</span> + <span class="d1b-kbd">Tab</span> (en gardant Alt enfoncé).</li>
                </ol>
            </div>
            <div class="d1b-os-box ubuntu">
                <h4>🐧 Ordinateur Ubuntu 24.04</h4>
                <ol>
                    <li>Montrez le dock, l\'horloge et le menu système.</li>
                    <li>Passez d\'une fenêtre à l\'autre avec <span class="d1b-kbd">Super</span> + <span class="d1b-kbd">Tab</span> et agrandissez une fenêtre par double-clic sur sa barre de titre.</li>
                </ol>
            </div>
        </div>
        <p style="margin-bottom:0;font-weight:700;color:#854d0e;">✅ Critère de réussite : 2 éléments d\'interface nommés et 1 changement de fenêtre réussi sur chaque appareil.</p>
    </div>
</div>',
    ],

    // -------------------------------------------------------------------------
    // PAGE 5 : COMPÉTENCE 4 (D1-B-04) — LOCALISER ET OUVRIR UNE APPLICATION (Slides 33-39, 69)
    // -------------------------------------------------------------------------
    [
        'idnumber' => 'd1_b_page_05_c4',
        'name'     => '🔍 Compétence 4 (D1-B-04) — Localiser, chercher et ouvrir une application + Pratique 4',
        'intro'    => 'Trouver et lancer une application par le tiroir d\'applications Android, le menu Démarrer de Windows 11 ou le dock/touche Super d\'Ubuntu 24.04, et la garder à portée de clic (Diapositives 33 à 39 et Pratique 4).',
        'images'   => ['image23.jpg', 'image16.png', 'image20.jpg', 'image24.png'],
        'content'  => $css_common . '
<div class="d1b-wrap">
    <div class="d1b-hero">
        <span class="badge-pill">Compétence 4 sur 7 · Code D1-B-04 · Niveau Bloom : OS1 (Compréhension) &amp; OS2 (Application)</span>
        <h2>🔍 Localiser, chercher et ouvrir une application</h2>
        <p>Retrouver n\'importe quelle application même lorsqu\'elle n\'est pas visible sur l\'écran d\'accueil ou le bureau, la lancer par son nom et l\'épingler pour la garder à portée de main.</p>
    </div>

    <div class="d1b-card">
        <h3>📱 1. Sur Android : le tiroir d\'applications</h3>
        <div class="d1b-grid-2">
            <div>
                <ul>
                    <li>L\'écran d\'accueil ne montre qu\'<strong>une partie</strong> de vos applications.</li>
                    <li><strong>Toutes vos applications</strong> sont rangées dans le <strong>tiroir d\'applications</strong>.</li>
                    <li><strong>Comment l\'ouvrir :</strong> balayez l\'écran du <strong>bas vers le haut</strong> (ou touchez l\'icône <em>Toutes les applications</em>).</li>
                    <li>En haut du tiroir, une barre de recherche permet aussi de taper le nom de l\'application (ex. : <em>Calculatrice</em>).</li>
                </ul>
                <div class="d1b-story">
                    🏪 <strong>Le cas d\'Issouf au marché :</strong> Issouf croyait avoir supprimé sa Calculatrice parce qu\'il ne la voyait plus sur son écran d\'accueil. En réalité, elle était simplement rangée dans le tiroir d\'applications !
                </div>
                <div class="d1b-alert-retenir">
                    💡 <strong>À retenir :</strong> Une application absente de l\'écran d\'accueil n\'est pas perdue : elle vous attend dans le tiroir d\'applications.
                </div>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image23.jpg" alt="Le tiroir d\'applications d\'un téléphone Android" />
                <figcaption>Le tiroir d\'applications d\'Android : toutes les applications installées y sont classées</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🖥️ 2. Sur Windows 11 et Ubuntu 24.04 : chercher par le nom et garder à portée</h3>
        <div class="d1b-grid-2">
            <div class="d1b-os-box windows">
                <h4>🪟 Windows 11 : le menu Démarrer</h4>
                <ol>
                    <li>Cliquez une seule fois sur le <strong>logo Windows</strong> en bas (ou appuyez sur la touche <span class="d1b-kbd">Windows</span>).</li>
                    <li>Les <strong>applications épinglées</strong> s\'affichent d\'abord ; le bouton <em>Toutes les applications</em> donne la liste alphabétique complète.</li>
                    <li><strong>Le chemin le plus court :</strong> dès que le menu Démarrer est ouvert, <strong>tapez directement le nom</strong> (ex. : <code>Bloc-notes</code>) <em>sans cliquer ailleurs</em>, puis cliquez sur le résultat.</li>
                    <li><strong>Garder à portée de main :</strong> faites un <strong>clic droit</strong> sur le résultat → <strong>Épingler à Démarrer</strong>.</li>
                </ol>
            </div>
            <div class="d1b-os-box ubuntu">
                <h4>🐧 Ubuntu 24.04 : le dock et la touche Super</h4>
                <ol>
                    <li>Dans le <strong>dock à gauche</strong>, un clic ouvre une application favorite.</li>
                    <li>En bas du dock, le <strong>bouton à neuf points</strong> affiche la liste complète des applications.</li>
                    <li><strong>Le chemin le plus court :</strong> appuyez sur la touche <span class="d1b-kbd">Super</span> et <strong>tapez directement le début du nom</strong> (ex. : <code>texte</code> pour l\'Éditeur de texte ou <code>calc</code> pour la Calculatrice).</li>
                    <li><strong>Garder à portée de main :</strong> clic droit sur l\'icône → <strong>Ajouter aux favoris</strong> (dans le dock).</li>
                </ol>
            </div>
        </div>

        <figure class="d1b-fig">
            <img src="@@PLUGINFILE@@/image24.png" alt="Schéma : chercher une application par son nom" />
            <figcaption>Sur ordinateur, ouvrir Démarrer (Windows) ou appuyer sur Super (Ubuntu) puis taper le début du nom est le chemin le plus rapide</figcaption>
        </figure>

        <div class="d1b-alert-warn">
            ⚠️ <strong>Deux erreurs fréquentes à éviter sous Windows 11 :</strong><br/>
            1. <em>Double-cliquer</em> sur le logo Windows : le premier clic ouvre le menu Démarrer, le second le referme aussitôt !<br/>
            2. <em>Cliquer au milieu de l\'écran</em> avant de taper le nom : cela referme le menu Démarrer. Cliquez sur Démarrer et tapez immédiatement au clavier.
        </div>

        <table class="d1b-table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>📱 Android (Issouf)</th>
                    <th>🪟 Windows 11 (Awa)</th>
                    <th>🐧 Ubuntu 24.04 (Mariam)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Liste complète</strong></td>
                    <td>Balayer du bas vers le haut (Tiroir)</td>
                    <td>Démarrer → <em>Toutes les applications</em></td>
                    <td>Bouton à <strong>neuf points</strong> en bas du dock</td>
                </tr>
                <tr>
                    <td><strong>Chercher par le nom</strong></td>
                    <td>Barre de recherche du tiroir</td>
                    <td>Démarrer, puis taper le nom</td>
                    <td>Touche <span class="d1b-kbd">Super</span>, puis taper le nom</td>
                </tr>
                <tr>
                    <td><strong>Garder à portée</strong></td>
                    <td>Appui long → Glisser sur l\'accueil</td>
                    <td>Clic droit → <strong>Épingler à Démarrer</strong></td>
                    <td>Clic droit → <strong>Ajouter aux favoris</strong> (dock)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d1b-pratique">
        <h3>🛠️ Activité pratique 4 — Localiser et ouvrir une application (Durée : 8 min)</h3>
        <p><strong>Consigne :</strong> N\'utilisez pas une icône déjà posée sur le bureau : cherchez chaque application par son nom !</p>
        <div class="d1b-grid-3">
            <div class="d1b-os-box android">
                <h4>📱 Smartphone Android</h4>
                <ol>
                    <li>Balayez vers le haut pour ouvrir le tiroir d\'applications.</li>
                    <li>Cherchez et ouvrez la <strong>Calculatrice</strong>.</li>
                </ol>
            </div>
            <div class="d1b-os-box windows">
                <h4>🪟 Ordinateur Windows 11</h4>
                <ol>
                    <li>Cliquez sur Démarrer et tapez <code>Bloc-notes</code> sans cliquer ailleurs.</li>
                    <li>Épinglez-le à Démarrer (clic droit), ouvrez-le puis fermez-le.</li>
                </ol>
            </div>
            <div class="d1b-os-box ubuntu">
                <h4>🐧 Ordinateur Ubuntu 24.04</h4>
                <ol>
                    <li>Appuyez sur <span class="d1b-kbd">Super</span> et tapez <code>texte</code>.</li>
                    <li>Ouvrez l\'<strong>Éditeur de texte</strong>, puis fermez-le.</li>
                </ol>
            </div>
        </div>
        <p style="margin-bottom:0;font-weight:700;color:#854d0e;">✅ Critère de réussite : Une application est localisée par la recherche, ouverte puis refermée sur chacun des trois systèmes.</p>
    </div>
</div>',
    ],

    // -------------------------------------------------------------------------
    // PAGE 6 : COMPÉTENCE 5 (D1-B-05) — SAISIR DU TEXTE (Slides 40-48, 70)
    // -------------------------------------------------------------------------
    [
        'idnumber' => 'd1_b_page_06_c5',
        'name'     => '⌨️ Compétence 5 (D1-B-05) — Saisir, ponctuer, relire et corriger du texte + Pratique 5',
        'intro'    => 'Maîtriser Maj et Verr. Maj, les accents et chiffres du clavier AZERTY, les accents sur clavier tactile Android, la ponctuation et la relecture en 3 passages (Diapositives 40 à 48 et Pratique 5).',
        'images'   => ['image25.png', 'image26.png', 'image27.png', 'image28.png', 'image29.png'],
        'content'  => $css_common . '
<div class="d1b-wrap">
    <div class="d1b-hero">
        <span class="badge-pill">Compétence 5 sur 7 · Code D1-B-05 · Niveau Bloom : OS1 à OS4</span>
        <h2>⌨️ Saisir du texte : accents, majuscules, ponctuation et relecture</h2>
        <p>Écrire un texte propre et sans faute sur le clavier tactile d\'Android et sur le clavier physique AZERTY de Windows 11 et Ubuntu 24.04.</p>
    </div>

    <div class="d1b-card">
        <h3>⬆️ 1. Les 6 touches essentielles du clavier : distinguer Maj et Verr. Maj</h3>
        <div class="d1b-grid-2">
            <div>
                <ul>
                    <li><span class="d1b-kbd">Maj</span> (flèche vers le haut, présente à gauche et à droite du clavier) :
                        <ul>
                            <li>Maintenue enfoncée + une lettre = <strong>une seule majuscule</strong>.</li>
                            <li>Maintenue enfoncée + une touche à deux signes = donne <strong>le signe du haut</strong> (par exemple un chiffre ou le point <code>.</code>).</li>
                        </ul>
                    </li>
                    <li><span class="d1b-kbd">Verr. Maj</span> (Verrouillage Majuscules) :
                        <ul>
                            <li>Bloque le clavier en capitales : <strong>tout s\'écrit en MAJUSCULES</strong> et un <strong>petit voyant lumineux s\'allume</strong> sur le clavier.</li>
                            <li>Rappuyez une fois sur <span class="d1b-kbd">Verr. Maj</span> pour revenir aux minuscules.</li>
                        </ul>
                    </li>
                    <li><span class="d1b-kbd">Espace</span> : la longue barre en bas pour séparer les mots.</li>
                    <li><span class="d1b-kbd">Entrée</span> : valide ou passe à la ligne suivante.</li>
                    <li><span class="d1b-kbd">Retour arrière</span> (flèche vers la gauche <code>←</code>) : efface le caractère situé <strong>à gauche</strong> du curseur.</li>
                    <li><span class="d1b-kbd">Suppr</span> : efface le caractère situé <strong>à droite</strong> du curseur.</li>
                </ul>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image25.png" alt="Schéma : les six touches utiles pour écrire" />
                <figcaption>Les 6 touches indispensables : Maj, Verr. Maj, Espace, Entrée, Retour arrière et Suppr</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🇫🇷 2. Les accents et les chiffres sur un clavier AZERTY (Windows 11 &amp; Ubuntu 24.04)</h3>
        <div class="d1b-grid-2">
            <div>
                <ul>
                    <li><strong>Les accents en une seule touche (sans Maj) :</strong>
                        <ul>
                            <li>Sur la rangée des chiffres tout en haut : <span class="d1b-kbd">2 é</span>, <span class="d1b-kbd">7 è</span>, <span class="d1b-kbd">9 ç</span>, <span class="d1b-kbd">0 à</span> se tapent <strong>directement</strong>, sans maintenir Maj !</li>
                            <li>Le <span class="d1b-kbd">ù</span> possède sa propre touche à droite de la lettre M.</li>
                        </ul>
                    </li>
                    <li><strong>Comment taper les chiffres de la rangée du haut ?</strong>
                        <ul>
                            <li>Sur un clavier AZERTY, il faut <strong>maintenir <span class="d1b-kbd">Maj</span></strong> + appuyer sur la touche !</li>
                            <li>Exemple : <span class="d1b-kbd">Maj</span> + <span class="d1b-kbd">é</span> donne le chiffre <strong>2</strong>.</li>
                        </ul>
                    </li>
                    <li><strong>Et l\'accent circonflexe (ê, ô, î) ? La touche morte <code>^</code> :</strong>
                        <ul>
                            <li>Appuyez une fois sur la touche <span class="d1b-kbd">^</span> (à droite du P) et <strong>relâchez-la</strong> (rien ne s\'affiche encore : c\'est une <em>touche morte</em>).</li>
                            <li>Tapez ensuite la lettre <span class="d1b-kbd">e</span> : le caractère <strong>ê</strong> apparaît !</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image26.png" alt="Schéma : les accents sur un clavier AZERTY" />
                <figcaption>Clavier AZERTY : é, è, ç, à en accès direct ; touche morte ^ puis e pour obtenir ê ; Maj + touche du haut pour les chiffres</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>📱 3. Le clavier tactile du téléphone Android</h3>
        <div class="d1b-grid-2">
            <div>
                <ul>
                    <li><strong>Obtenir un accent (é, è, ê, ë) :</strong> faites un <strong>appui long</strong> sur la lettre <code>e</code>. Une bulle s\'ouvre avec <code>é, è, ê, ë</code> : glissez le doigt jusqu\'à la lettre voulue puis relevez le doigt.</li>
                    <li><strong>Une majuscule :</strong> touchez la flèche vers le haut <code>⇧</code> une fois.</li>
                    <li><strong>Bloquer les majuscules :</strong> touchez <strong>deux fois rapidement</strong> la flèche <code>⇧</code> (équivalent de <em>Verr. Maj</em>).</li>
                    <li><strong>Saisie vocale (dictée) :</strong> le petit <strong>micro</strong> du clavier sur Android (ou <span class="d1b-kbd">Windows</span> + <span class="d1b-kbd">H</span> sous Windows 11 ; Ubuntu 24.04 n\'a pas d\'outil de dictée intégré par défaut). <em>Relisez toujours après avoir dicté ou utilisé les suggestions !</em></li>
                </ul>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image27.png" alt="Schéma : le clavier d\'un téléphone Android" />
                <figcaption>Sur Android : appui long sur la lettre e, puis glisser vers l\'accent souhaité (é, è, ê)</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>✍️ 4. Règles de ponctuation et méthode de relecture en 3 passages</h3>
        <div class="d1b-grid-2">
            <div>
                <h4>Règles de base de la ponctuation</h4>
                <ul>
                    <li>Une <strong>majuscule</strong> au début de chaque phrase et aux noms propres (<em>Koudougou, Mariam, Bobo-Dioulasso</em>).</li>
                    <li>La <strong>virgule (,)</strong> et le <strong>point (.)</strong> se <strong>collent au mot qui précède</strong>, puis sont suivis d\'<strong>un espace</strong>.
                        <br/>✅ Correct : <code>« Merci, Madame. »</code>
                        <br/>❌ Incorrect : <code>« Merci , Madame. »</code> ou <code>« Merci,Madame. »</code>
                    </li>
                    <li>Les <strong>deux-points (:)</strong> sont précédés d\'un espace et suivis d\'un espace : <code>« Ordre du jour : fête de fin d\'année. »</code></li>
                </ul>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image28.png" alt="Pictogramme : fiche de règles de ponctuation" style="max-height:160px;" />
                    <figcaption>Ponctuation soignée = courrier professionnel</figcaption>
                </figure>
            </div>
            <div>
                <h4>La méthode des 3 relectures successives</h4>
                <ol>
                    <li><strong>1<sup>re</sup> lecture — Le sens :</strong> la date, l\'heure, le lieu et les mots sont-ils exacts ? (ex. corriger « <em>jeudu</em> » en « <em>jeudi</em> »).</li>
                    <li><strong>2<sup>e</sup> lecture — Les accents et les majuscules :</strong> début de phrase, noms propres, <code>é</code>, <code>à</code>, <code>ê</code>.</li>
                    <li><strong>3<sup>e</sup> lecture — Les espaces et les signes :</strong> virgules, points finaux, deux-points.</li>
                </ol>
                <p><strong>Pour corriger :</strong> cliquez juste après l\'erreur, effacez avec <span class="d1b-kbd">Retour arrière</span> et retapez. Vous avez effacé trop loin ? <span class="d1b-kbd">Ctrl</span> + <span class="d1b-kbd">Z</span> annule la dernière action !</p>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image29.png" alt="Pictogramme : liste de relecture en 3 étapes" style="max-height:160px;" />
                    <figcaption>Chercher un seul type d\'erreur à la fois permet de n\'en laisser passer aucune</figcaption>
                </figure>
            </div>
        </div>
    </div>

    <div class="d1b-pratique">
        <h3>🛠️ Activité pratique 5 — Saisir et relire un texte sans faute (Durée : 10 min)</h3>
        <p><strong>Phrase à saisir et à faire relire par votre binôme :</strong> <code>Réunion à 9 h.</code> (puis en entraînement : <code>Fête du service : mercredi à 10 h, salle de réunion. Merci, Mariam.</code>)</p>
        <div class="d1b-grid-3">
            <div class="d1b-os-box android">
                <h4>📱 Smartphone Android (Notes)</h4>
                <ol>
                    <li>Utilisez la flèche pour le <code>R</code> majuscule et l\'appui long sur <code>e</code> pour <code>é</code> et sur <code>a</code> pour <code>à</code>.</li>
                    <li>Tapez <code>Réunion à 9 h.</code> avec le point final.</li>
                </ol>
            </div>
            <div class="d1b-os-box windows">
                <h4>🪟 Windows 11 (Bloc-notes)</h4>
                <ol>
                    <li>Tapez <code>é</code> et <code>à</code> directement, et <code>ê</code> avec <span class="d1b-kbd">^</span> puis <span class="d1b-kbd">e</span>.</li>
                    <li>Tapez le chiffre <code>9</code> avec <span class="d1b-kbd">Maj</span> + <span class="d1b-kbd">ç</span>.</li>
                </ol>
            </div>
            <div class="d1b-os-box ubuntu">
                <h4>🐧 Ubuntu 24.04 (Éditeur de texte)</h4>
                <ol>
                    <li>Même clavier AZERTY que sous Windows 11 !</li>
                    <li>Relisez en 3 passages et corrigez avec <span class="d1b-kbd">Retour arrière</span>.</li>
                </ol>
            </div>
        </div>
        <p style="margin-bottom:0;font-weight:700;color:#854d0e;">✅ Critère de réussite : Phrase saisie sans aucune erreur d\'accent, de majuscule, de chiffre ni de ponctuation.</p>
    </div>
</div>',
    ],

    // -------------------------------------------------------------------------
    // PAGE 7 : COMPÉTENCE 6 (D1-B-06) — CONNECTER À INTERNET (Slides 49-57, 71)
    // -------------------------------------------------------------------------
    [
        'idnumber' => 'd1_b_page_07_c6',
        'name'     => '🌐 Compétence 6 (D1-B-06) — Connecter un appareil à internet (Wi-Fi, données, dépannage) + Pratique 6',
        'intro'    => 'Distinguer Wi-Fi, données mobiles et partage de connexion ; se connecter à un réseau Wi-Fi protégé sur Android, Windows 11 et Ubuntu 24.04 ; diagnostiquer une panne avec les 5 questions (mode avion, mot de passe) (Diapositives 49 à 57 et Pratique 6).',
        'images'   => ['image30.png', 'image31.png', 'image32.png', 'image33.png', 'image17.jpg'],
        'content'  => $css_common . '
<div class="d1b-wrap">
    <div class="d1b-hero">
        <span class="badge-pill">Compétence 6 sur 7 · Code D1-B-06 · Niveau Bloom : OS1, OS2 &amp; OS3</span>
        <h2>🌐 Connecter un appareil à internet et diagnostiquer une panne</h2>
        <p>Comprendre d\'où vient internet (Wi-Fi, données mobiles, partage de connexion), rejoindre un réseau Wi-Fi protégé sur les trois systèmes et vérifier méthodiquement pourquoi internet ne passe pas.</p>
    </div>

    <div class="d1b-card">
        <h3>📡 1. Trois façons d\'aller sur internet : qui fournit et qui paie ?</h3>
        <div class="d1b-grid-2">
            <div>
                <ol>
                    <li><strong>Le Wi-Fi :</strong> réseau sans fil fourni par une <strong>box</strong> ou un routeur (à la mairie, à la boutique, au cyber ou à la maison).
                        <br/>👉 <em>Sur un Wi-Fi, votre téléphone n\'utilise pas son propre forfait mobile !</em>
                    </li>
                    <li><strong>Les données mobiles :</strong> internet passe par votre <strong>carte SIM</strong>. Chaque page ouverte, chaque photo ou vidéo téléchargée est <strong>décomptée de votre forfait</strong> opérateur. Un interrupteur permet de les activer ou de les couper pour épargner son forfait.</li>
                    <li><strong>Le partage de connexion (point d\'accès) :</strong> votre téléphone partage son internet avec un autre appareil (un ordinateur par exemple). Attention : <strong>c\'est le forfait du téléphone qui est consommé !</strong></li>
                </ol>
                <div class="d1b-alert-retenir">
                    🔒 <strong>Reconnaître un Wi-Fi protégé :</strong> Dans la liste des réseaux Wi-Fi, un <strong>petit verrou (cadenas)</strong> à côté du nom indique que le réseau est protégé par un <strong>mot de passe</strong>. Demandez-le au responsable du réseau et respectez scrupuleusement les <strong>majuscules et minuscules</strong> !
                </div>
            </div>
            <div>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image30.png" alt="Schéma : Wi-Fi, données mobiles, partage de connexion" />
                    <figcaption>Les 3 modes d\'accès : Wi-Fi (box), Données mobiles (carte SIM) et Partage de connexion</figcaption>
                </figure>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image31.png" alt="Schéma : se connecter à un Wi-Fi en quatre étapes" />
                    <figcaption>La logique universelle en 4 étapes : ouvrir la liste → choisir le réseau → taper le mot de passe → vérifier l\'icône</figcaption>
                </figure>
            </div>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🔄 2. Se connecter au Wi-Fi sur les 3 systèmes : même logique partout !</h3>
        <table class="d1b-table">
            <thead>
                <tr>
                    <th>Étape</th>
                    <th>📱 Android (Issouf)</th>
                    <th>🪟 Windows 11 (Awa)</th>
                    <th>🐧 Ubuntu 24.04 (Mariam)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>1. Ouvrir la liste</strong></td>
                    <td>Balayer du haut vers le bas → toucher <strong>Internet</strong> (ou appui long sur l\'icône Wi-Fi)</td>
                    <td>Cliquer sur l\'<strong>icône Réseau</strong> en bas à droite → <strong>Gérer les connexions Wi-Fi</strong></td>
                    <td>Cliquer sur le <strong>menu système</strong> en haut à droite → ouvrir la section <strong>Wi-Fi</strong></td>
                </tr>
                <tr>
                    <td><strong>2. Choisir le réseau</strong></td>
                    <td>Toucher le <strong>nom du réseau</strong> (avec cadenas)</td>
                    <td>Cliquer sur le <strong>nom du réseau</strong> → <strong>Connecter</strong></td>
                    <td>Cliquer sur le <strong>nom du réseau</strong></td>
                </tr>
                <tr>
                    <td><strong>3. Mot de passe</strong></td>
                    <td>Taper le mot de passe (majuscules/minuscules) → <strong>Valider</strong></td>
                    <td>Taper le mot de passe → cliquer sur <strong>Suivant</strong></td>
                    <td>Taper le mot de passe → cliquer sur <strong>Se connecter</strong></td>
                </tr>
                <tr>
                    <td><strong>4. Vérifier</strong></td>
                    <td>L\'<strong>éventail Wi-Fi</strong> apparaît en haut de l\'écran</td>
                    <td>L\'icône d\'ondes Wi-Fi s\'affiche en bas à droite</td>
                    <td>L\'icône Wi-Fi (point surmonté d\'ondes) s\'affiche en haut à droite</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d1b-card">
        <h3>✈️ 3. Le mode avion et les 5 questions de dépannage (OS3 — Analyse)</h3>
        <div class="d1b-grid-2">
            <div>
                <div class="d1b-story">
                    🌾 <strong>Le cas de Boureima près de Dédougou :</strong> Boureima n\'avait plus internet sur son téléphone et s\'apprêtait à racheter un forfait. En regardant tout en haut de son écran, il a vu une <strong>petite icône d\'avion</strong> : le <strong>mode avion</strong> s\'était activé par erreur dans sa poche, coupant toutes les connexions sans fil !
                </div>
                <h4>Pas d\'internet ? Posez-vous ces 5 questions dans l\'ordre :</h4>
                <ol>
                    <li><strong>Le mode avion est-il coupé ?</strong> (Vérifiez qu\'il n\'y a pas d\'icône d\'avion dans la barre d\'état).</li>
                    <li><strong>Le Wi-Fi ou les données mobiles sont-ils bien activés ?</strong></li>
                    <li><strong>L\'icône montre-t-elle un réseau connecté ?</strong></li>
                    <li><strong>Le forfait mobile est-il épuisé ?</strong></li>
                    <li><strong>Oublier le réseau Wi-Fi, puis le rejoindre à nouveau</strong> en retapant soigneusement le mot de passe (attention aux majuscules/minuscules !).</li>
                </ol>
                <div class="d1b-alert-retenir">
                    💡 <strong>Astuce de diagnostic :</strong> Si un téléphone se connecte au Wi-Fi de la mairie mais pas le PC d\'Awa, la box fonctionne ! Le problème vient uniquement du PC d\'Awa.
                </div>
            </div>
            <div>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image32.png" alt="Schéma : les icônes de la barre d\'état du téléphone (Wi-Fi, données mobiles, mode avion)" />
                    <figcaption>Lire la barre d\'état : éventail Wi-Fi, barres de réseau mobile et icône du mode avion</figcaption>
                </figure>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image33.png" alt="Pictogramme : se poser les 5 bonnes questions dans l\'ordre" style="max-height:180px;" />
                    <figcaption>On vérifie dans l\'ordre au lieu de tout redémarrer ou de racheter un forfait inutilement</figcaption>
                </figure>
            </div>
        </div>
    </div>

    <div class="d1b-pratique">
        <h3>🛠️ Activité pratique 6 — Connecter un appareil à internet (Durée : 10 min)</h3>
        <div class="d1b-grid-3">
            <div class="d1b-os-box android">
                <h4>📱 Smartphone Android</h4>
                <ol>
                    <li>Vérifiez que le mode avion est désactivé.</li>
                    <li>Balayez du haut → <strong>Internet</strong> → choisissez le Wi-Fi à verrou → tapez le mot de passe et ouvrez une page web.</li>
                </ol>
            </div>
            <div class="d1b-os-box windows">
                <h4>🪟 Ordinateur Windows 11</h4>
                <ol>
                    <li>Icône <strong>Réseau</strong> en bas à droite → Gérer les connexions Wi-Fi.</li>
                    <li>Réseau → <strong>Connecter</strong> → mot de passe → <strong>Suivant</strong>, puis ouvrez une page web.</li>
                </ol>
            </div>
            <div class="d1b-os-box ubuntu">
                <h4>🐧 Ordinateur Ubuntu 24.04</h4>
                <ol>
                    <li><strong>Menu système</strong> en haut à droite → <strong>Wi-Fi</strong>.</li>
                    <li>Nom du réseau → mot de passe → <strong>Se connecter</strong>, puis ouvrez une page web.</li>
                </ol>
            </div>
        </div>
        <p style="margin-bottom:0;font-weight:700;color:#854d0e;">✅ Critère de réussite : Une page web s\'affiche réellement sur chacun des 3 appareils.</p>
    </div>
</div>',
    ],

    // -------------------------------------------------------------------------
    // PAGE 8 : COMPÉTENCE 7 (D1-B-07) — ENTRETENIR SON APPAREIL (Slides 58-64, 72)
    // -------------------------------------------------------------------------
    [
        'idnumber' => 'd1_b_page_08_c7',
        'name'     => '🛡️ Compétence 7 (D1-B-07) — Entretenir et protéger son appareil (chaleur, poussière, courant) + Pratique 7',
        'intro'    => 'Protéger ses équipements contre les 4 ennemis (chaleur 0-35 °C, poussière d\'harmattan, eau, coupures/surtensions), bien charger sa batterie (chargeur de la marque, limite 80 %) et choisir entre onduleur et parasurtenseur (Diapositives 58 à 64 et Pratique 7).',
        'images'   => ['image34.png', 'image35.jpg', 'image36.png', 'image37.jpg', 'image8.jpg'],
        'content'  => $css_common . '
<div class="d1b-wrap">
    <div class="d1b-hero">
        <span class="badge-pill">Compétence 7 sur 7 · Code D1-B-07 · Niveau Bloom : OS1, OS2 &amp; OS4</span>
        <h2>🛡️ Entretenir et protéger physiquement son appareil</h2>
        <p>Protéger son smartphone et son ordinateur contre leurs quatre ennemis au Burkina Faso : la chaleur, la poussière d\'harmattan, l\'eau et les coupures ou surtensions électriques.</p>
    </div>

    <div class="d1b-card">
        <h3>⚠️ 1. Les quatre ennemis de vos appareils numériques</h3>
        <div class="d1b-grid-2">
            <div>
                <ol>
                    <li><strong>☀️ La chaleur :</strong> plein soleil, sac fermé, voiture garée au soleil.</li>
                    <li><strong>🌪️ La poussière :</strong> vent d\'harmattan, bord de route, atelier.</li>
                    <li><strong>💧 L\'eau et les liquides :</strong> pluie, verre d\'eau ou de café renversé sur le clavier, chute.</li>
                    <li><strong>⚡ Le courant électrique :</strong> coupures brusques et surtensions au retour du courant ou pendant l\'orage.</li>
                </ol>
                <div class="d1b-alert-retenir">
                    💡 <strong>À retenir :</strong> Chaque ennemi a son geste de protection, simple et gratuit la plupart du temps ! En cas de chute dans l\'eau : éteindre tout de suite, essuyer avec un chiffon sec, laisser sécher à l\'ombre <em>sans sèche-cheveux</em>, ne pas brancher et faire contrôler.
                </div>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image34.png" alt="Schéma : les 4 ennemis de l\'appareil (chaleur, poussière, eau, coupures de courant)" />
                <figcaption>Les 4 ennemis physiques : chaleur, poussière, eau et instabilité du courant</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🔋 2. Chaleur et recharge : préserver la durée de vie de la batterie</h3>
        <div class="d1b-grid-2">
            <div>
                <ul>
                    <li><strong>Plage de température recommandée (Samsung) :</strong> de <strong>0 à 35 °C</strong>. Au-delà, et de façon prolongée, la batterie s\'use beaucoup plus vite.</li>
                    <li><strong>Ne jamais charger un téléphone brûlant :</strong> après une matinée au champ au soleil, Boureima laisse d\'abord son téléphone <strong>refroidir à l\'ombre</strong> avant de le brancher dans un endroit aéré.</li>
                    <li><strong>Toujours utiliser le chargeur de la marque en bon état :</strong> un chargeur sans marque emprunté au voisin ou un câble abîmé fait chauffer le téléphone et peut l\'endommager.</li>
                    <li><strong>Protection de la batterie (Samsung One UI 6.1) :</strong>
                        <ul>
                            <li>Débranchez lorsque la batterie atteint 100 %.</li>
                            <li>Si vous chargez la nuit : ouvrez <strong>Paramètres → Batterie → Protection de la batterie</strong> et choisissez <strong>Maximale</strong> pour arrêter automatiquement la charge à <strong>80 %</strong>.</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image35.jpg" alt="Un cultivateur au téléphone en milieu rural" />
                    <figcaption>En plein soleil, garder le téléphone à l\'ombre et le laisser refroidir avant toute recharge</figcaption>
                </figure>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image36.png" alt="Schéma : trois façons de charger un téléphone" />
                    <figcaption>Chargeur authentique de la marque, à l\'ombre dans un lieu aéré, et option Protection de la batterie (80 %)</figcaption>
                </figure>
            </div>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🧹 3. Poussière d\'harmattan (nettoyer sans abîmer) et protection électrique (Onduleur vs Parasurtenseur)</h3>
        <div class="d1b-grid-2">
            <div>
                <h4>🌪️ Nettoyer son appareil sans risque</h4>
                <ol>
                    <li><strong>Éteignez et débranchez d\'abord</strong> l\'appareil (téléphone ou ordinateur).</li>
                    <li>Passez un <strong>chiffon en microfibre sec ou à peine humide</strong> sur l\'écran et le boîtier.</li>
                    <li><strong>Pour le clavier du PC :</strong> retournez-le doucement et <strong>tapotez</strong> pour faire tomber la poussière et les miettes, puis passez le chiffon.</li>
                    <li><strong>Interdictions absolues :</strong> jamais de liquide versé directement, et <strong>jamais d\'objet métallique, d\'aiguille ou de cure-dent</strong> dans les prises ou ports de charge !</li>
                </ol>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image37.jpg" alt="Un clavier AZERTY d\'ordinateur de bureau" />
                    <figcaption>En période d\'harmattan, un coup de chiffon sec quotidien sur appareil éteint protège durablement le poste</figcaption>
                </figure>
            </div>
            <div>
                <h4>⚡ Coupures et orages : Onduleur ou Parasurtenseur ?</h4>
                <ul>
                    <li><strong>Le problème :</strong> un PC de bureau n\'a pas de batterie. À la moindre coupure, il s\'éteint net et le travail non enregistré est perdu.</li>
                    <li><strong>L\'onduleur (avec batterie intégrée) :</strong> prend immédiatement le relais pendant quelques minutes lors d\'une coupure. Il vous laisse le temps d\'<strong>enregistrer votre travail puis d\'éteindre proprement par le menu</strong> !</li>
                    <li><strong>Le parasurtenseur :</strong> prise spéciale qui protège contre les <strong>variations de tension (surtensions)</strong>, mais <em>ne remplace pas le courant coupé</em> (il n\'a pas de batterie).</li>
                    <li><strong>En cas d\'orage :</strong> débranchez les appareils de la prise murale.</li>
                </ul>
                <figure class="d1b-fig">
                    <img src="@@PLUGINFILE@@/image8.jpg" alt="Un ordinateur de bureau et ses éléments" />
                    <figcaption>Pour protéger le PC de bureau d\'Awa contre les coupures de courant, c\'est l\'onduleur qu\'il faut choisir</figcaption>
                </figure>
            </div>
        </div>
    </div>

    <div class="d1b-pratique">
        <h3>🛠️ Activité pratique 7 — Entretenir et protéger son appareil (Durée : 8 min)</h3>
        <div class="d1b-grid-3">
            <div class="d1b-os-box android">
                <h4>📱 Smartphone Android</h4>
                <ol>
                    <li>Choisissez le chargeur de la marque (et expliquez pourquoi écarter le chargeur sans marque).</li>
                    <li>Sur Samsung : ouvrez <em>Paramètres → Batterie → Protection de la batterie → Maximale (80 %)</em>.</li>
                </ol>
            </div>
            <div class="d1b-os-box windows">
                <h4>🪟 Ordinateur Windows 11</h4>
                <ol>
                    <li>Expliquez et montrez le nettoyage d\'un écran et d\'un clavier (appareil éteint et débranché, chiffon microfibre sec).</li>
                    <li>Expliquez pourquoi débrancher pendant l\'orage.</li>
                </ol>
            </div>
            <div class="d1b-os-box ubuntu">
                <h4>🐧 Ordinateur Ubuntu 24.04</h4>
                <ol>
                    <li>Vérifiez que les grilles d\'aération du PC sont bien dégagées.</li>
                    <li>Distinguez l\'<strong>onduleur</strong> (contre les coupures) du <strong>parasurtenseur</strong> (contre les surtensions).</li>
                </ol>
            </div>
        </div>
        <p style="margin-bottom:0;font-weight:700;color:#854d0e;">✅ Critère de réussite : 3 gestes d\'entretien réalisés et justifiés sans erreur.</p>
    </div>
</div>',
    ],

    // -------------------------------------------------------------------------
    // PAGE 9 : SÉQUENCES 5 & 6 — TRAVAUX DE GROUPE & RESTITUTION (Slides 65, 73-75)
    // -------------------------------------------------------------------------
    [
        'idnumber' => 'd1_b_page_09_groupe',
        'name'     => '🤝 Séquences 5 & 6 — Travaux de groupe & Restitution : Fiche mémo d\'un usager et Affiche de l\'espace numérique',
        'intro'    => 'Concevoir en groupe la fiche mémo d\'Awa (Windows 11), d\'Issouf (Android) ou de Mariam (Ubuntu 24.04) et l\'affiche de dépannage internet de l\'espace numérique, avec les corrigés modèles (Diapositives 65, 73 à 75 et 113-114).',
        'images'   => ['image38.png'],
        'content'  => $css_common . '
<div class="d1b-wrap">
    <div class="d1b-hero">
        <span class="badge-pill">Séquences 5 &amp; 6 · Durée : 60 min + 30 min · Niveau Bloom : OS4 (Évaluation) &amp; OS5 (Création)</span>
        <h2>🤝 Travaux de groupe &amp; Restitution : Concevoir une fiche mémo utile à un usager</h2>
        <p>Vous passez maintenant au sommet de l\'escalier de Bloom (OS5 — Création) : concevoir une fiche mémo claire et exacte permettant à un usager de se débrouiller seul devant son appareil.</p>
    </div>

    <div class="d1b-card">
        <h3>📝 1. Atelier principal : La fiche mémo d\'un usager (45 min)</h3>
        <div class="d1b-grid-2">
            <div>
                <h4>Consigne de l\'atelier (par groupe de 3 ou 4)</h4>
                <ol>
                    <li><strong>Choisissez votre personnage :</strong>
                        <ul>
                            <li>👩🏾‍💼 <strong>Awa</strong> à la mairie de Koudougou sur <strong>Windows 11</strong></li>
                            <li>🏪 <strong>Issouf</strong> dans sa boutique au marché sur <strong>Smartphone Android</strong></li>
                            <li>🎓 <strong>Mariam</strong> au cyber sur <strong>Ubuntu 24.04</strong></li>
                        </ul>
                    </li>
                    <li><strong>Listez ce qu\'il ou elle fait dans sa journée</strong> (allumer, ouvrir son application de texte, écrire un message/avis, vérifier internet, verrouiller à midi, éteindre le soir).</li>
                    <li><strong>Écrivez 5 gestes, dans l\'ordre chronologique, avec les mots exacts affichés à l\'écran</strong> (sans jargon).</li>
                    <li><strong>Testez la fiche sur le vrai appareil (15 min) :</strong> un membre du groupe suit la fiche à la lettre sans aucune aide orale !</li>
                </ol>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image38.png" alt="Pratique et travail collaboratif en binôme ou en groupe" />
                <figcaption>Concevoir en équipe, puis tester la fiche à la lettre sur l\'appareil réel</figcaption>
            </figure>
        </div>

        <details style="background:#f8fafc;border:1px solid #cbd5e1;border-radius:10px;padding:1rem 1.2rem;margin-top:1rem;">
            <summary style="font-weight:800;color:#0d7a32;cursor:pointer;">🔑 Voir le modèle corrigé — Exemple de Fiche mémo pour Awa (Windows 11)</summary>
            <ol style="margin-top:0.75rem;">
                <li><strong>Matin — Allumer et ouvrir sa session :</strong> Appuyer une fois sur le bouton de l\'<em>unité centrale</em>, patienter 30 s à 2 min, puis taper son mot de passe.</li>
                <li><strong>Ouvrir le Bloc-notes :</strong> Cliquer sur le bouton <em>Démarrer</em> (logo Windows en bas), taper <code>Bloc-notes</code> sans cliquer ailleurs, puis cliquer sur le résultat.</li>
                <li><strong>Écrire et relire son courrier :</strong> Taper le texte (accents <code>é, è, à</code> directs ; <code>^</code> puis <code>e</code> pour <code>ê</code> ; <span class="d1b-kbd">Maj</span> pour les majuscules et les chiffres), puis relire 3 fois (sens, accents/majuscules, ponctuation).</li>
                <li><strong>Vérifier le Wi-Fi :</strong> Cliquer sur l\'icône <em>Réseau</em> en bas à droite → <em>Gérer les connexions Wi-Fi</em> → choisir le réseau de la mairie → <em>Connecter</em>.</li>
                <li><strong>Pause midi et départ le soir :</strong> À midi, verrouiller avec <span class="d1b-kbd">Windows</span> + <span class="d1b-kbd">L</span>. Le soir, enregistrer le travail puis éteindre par <em>Démarrer → Marche/Arrêt → Arrêter</em>.</li>
            </ol>
        </details>
    </div>

    <div class="d1b-card">
        <h3>🪧 2. Exercice complémentaire : L\'affiche de l\'espace numérique (15 min — C6 &amp; C7)</h3>
        <p><strong>Mission :</strong> Concevoir une affiche qui sera collée au mur de l\'espace numérique pour aider tout usager qui n\'a plus internet et lui rappeler comment protéger l\'appareil.</p>
        <div class="d1b-grid-2">
            <div class="d1b-os-box android">
                <h4>Partie A de l\'affiche : « Pas d\'internet : les 5 vérifications dans l\'ordre »</h4>
                <ol>
                    <li>Le <strong>mode avion</strong> est-il bien coupé ? (Pas d\'icône d\'avion en haut).</li>
                    <li>Le <strong>Wi-Fi</strong> ou les <strong>données mobiles</strong> sont-ils activés ?</li>
                    <li>L\'icône montre-t-elle un <strong>réseau disponible</strong> ?</li>
                    <li>Le <strong>forfait mobile</strong> est-il épuisé ?</li>
                    <li><strong>Oublier le réseau Wi-Fi</strong>, puis le rejoindre en vérifiant les majuscules et minuscules du mot de passe.</li>
                </ol>
            </div>
            <div class="d1b-os-box windows">
                <h4>Partie B de l\'affiche : « Les gestes d\'or pour protéger nos appareils »</h4>
                <ul>
                    <li><strong>Batterie &amp; Chaleur :</strong> Charger à l\'ombre (0 à 35 °C) avec le <strong>chargeur de la marque</strong> ; activer la protection à 80 %.</li>
                    <li><strong>Poussière :</strong> Nettoyer l\'appareil <strong>éteint et débranché</strong> avec un chiffon microfibre sec (rien dans les prises !).</li>
                    <li><strong>Courant :</strong> Brancher le PC de bureau sur un <strong>onduleur</strong> contre les coupures et débrancher pendant l\'orage.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="d1b-card">
        <h3>🗣️ 3. Grille de restitution et de retours croisés (Séquence 6 — 30 min)</h3>
        <p>Lors de la présentation de chaque groupe (3 min de présentation + 2 min de retours), appréciez chaque fiche mémo selon ces <strong>3 critères officiels</strong> :</p>
        <ol>
            <li><strong>Exactitude des gestes :</strong> Le chemin indiqué fonctionne-t-il réellement sur l\'appareil sans étape manquante ?</li>
            <li><strong>Clarté des mots :</strong> Les mots écrits sur la fiche sont-ils exactement ceux visibles à l\'écran (sans jargon technique) ?</li>
            <li><strong>Adaptation à l\'usager choisi :</strong> La fiche répond-elle bien aux besoins quotidiens d\'Awa (Windows 11), d\'Issouf (Android) ou de Mariam (Ubuntu 24.04) ?</li>
        </ol>
    </div>
</div>',
    ],

    // -------------------------------------------------------------------------
    // PAGE 10 : SÉQUENCE 8 — SYNTHÈSE, FICHES MÉMO (1 & 2), GLOSSAIRE & SITOGRAPHIE (Slides 92-112)
    // -------------------------------------------------------------------------
    [
        'idnumber' => 'd1_b_page_10_synthese',
        'name'     => '📌 Séquence 8 — Synthèse générale, Fiches mémo (1 & 2), Glossaire (15 termes) et Sitographie officielle',
        'intro'    => 'Les 7 idées clés à retenir, votre guide de poche recto-verso (Fiches de synthèse 1 et 2), le Glossaire complet des 15 termes du module et la Bibliographie / Sitographie officielle (Diapositives 92 à 112).',
        'images'   => ['image39.jpg', 'image29.png'],
        'content'  => $css_common . '
<div class="d1b-wrap">
    <div class="d1b-hero">
        <span class="badge-pill">Séquence 8 · Synthèse, Guide de poche &amp; Références · Niveau Bloom : OS1 à OS5</span>
        <h2>📌 Synthèse générale, Fiches mémo (1 &amp; 2), Glossaire et Sitographie</h2>
        <p>Retrouvez ici toute la synthèse de votre journée de formation : les 7 idées clés, les deux tableaux aide-mémoire comparatifs et les 15 définitions officielles du module Bronze.</p>
    </div>

    <div class="d1b-card">
        <h3>🌟 1. Ce qu\'il faut retenir : 7 compétences, 7 idées clés</h3>
        <div class="d1b-grid-2">
            <div>
                <ol>
                    <li><strong>C1 — Identifier :</strong> Trois appareils, trois systèmes d\'exploitation : <strong>Android</strong>, <strong>Windows 11</strong>, <strong>Ubuntu 24.04</strong> (ne pas confondre marque, système et application).</li>
                    <li><strong>C2 — Démarrer / Éteindre :</strong> Allumer par le <strong>bouton</strong>, verrouiller dès qu\'on s\'absente (<span class="d1b-kbd">Windows + L</span> / <span class="d1b-kbd">Super + L</span>), éteindre toujours par le <strong>menu</strong>.</li>
                    <li><strong>C3 — Se repérer :</strong> Écran d\'accueil et barre d\'état sur Android, barre des tâches en bas sur Windows, <strong>dock à gauche</strong> et barre du haut sur Ubuntu.</li>
                    <li><strong>C4 — Ouvrir une appli :</strong> Tiroir d\'applications (Android), menu <strong>Démarrer</strong> puis taper le nom (Windows), touche <strong>Super</strong> puis taper le nom (Ubuntu).</li>
                    <li><strong>C5 — Écrire :</strong> Accents directs (<code>é, è, ç, à</code>), touche morte <code>^</code> puis <code>e</code> pour <code>ê</code>, <span class="d1b-kbd">Maj</span> pour les majuscules et les chiffres du haut, puis <strong>3 relectures</strong>.</li>
                    <li><strong>C6 — Se connecter :</strong> Wi-Fi protégé par un verrou (épargne le forfait) ou données mobiles (carte SIM) ; vérifier d\'abord le <strong>mode avion</strong> en cas de panne.</li>
                    <li><strong>C7 — Entretenir :</strong> Chargeur de la marque, à l\'ombre (0–35 °C), chiffon microfibre sec sur appareil éteint et débranché, <strong>onduleur</strong> contre les coupures.</li>
                </ol>
            </div>
            <figure class="d1b-fig">
                <img src="@@PLUGINFILE@@/image39.jpg" alt="Un formateur accompagne une participante sur un ordinateur portable" />
                <figcaption>Vous avez désormais toutes les clés en main pour pratiquer en autonomie et passer au niveau Intermédiaire</figcaption>
            </figure>
        </div>
    </div>

    <div class="d1b-card">
        <h3>📋 2. Fiche de synthèse 1 : Démarrer, se repérer et ouvrir une application</h3>
        <div class="d1b-alert-retenir">
            🛑 <strong>À retenir :</strong> Sur les trois systèmes, on éteint par le menu, jamais en débranchant.
        </div>
        <table class="d1b-table">
            <thead>
                <tr>
                    <th>Geste</th>
                    <th>📱 Android</th>
                    <th>🪟 Windows 11</th>
                    <th>🐧 Ubuntu 24.04</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Allumer, éteindre</strong></td>
                    <td>Appui long sur Marche/Arrêt ; menu d\'alimentation</td>
                    <td>Bouton de l\'unité centrale ; Démarrer, Marche/Arrêt, Arrêter</td>
                    <td>Bouton d\'alimentation ; menu système, bouton d\'extinction</td>
                </tr>
                <tr>
                    <td><strong>Verrouiller</strong></td>
                    <td>Appui court sur Marche/Arrêt</td>
                    <td><span class="d1b-kbd">Windows</span> + <span class="d1b-kbd">L</span></td>
                    <td><span class="d1b-kbd">Super</span> + <span class="d1b-kbd">L</span></td>
                </tr>
                <tr>
                    <td><strong>Voir toutes les applications</strong></td>
                    <td>Balayer du bas vers le haut (tiroir)</td>
                    <td>Démarrer, puis « Toutes »</td>
                    <td>Bouton à neuf points en bas du dock</td>
                </tr>
                <tr>
                    <td><strong>Chercher une application</strong></td>
                    <td>Recherche du tiroir</td>
                    <td>Démarrer, taper le nom</td>
                    <td>Touche <span class="d1b-kbd">Super</span>, taper le nom</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d1b-card">
        <h3>📋 3. Fiche de synthèse 2 : Écrire, se connecter et entretenir son appareil</h3>
        <div class="d1b-alert-retenir">
            🌐 <strong>À retenir :</strong> Même logique partout pour le Wi-Fi : la liste, le nom du réseau, le mot de passe.
        </div>
        <table class="d1b-table">
            <thead>
                <tr>
                    <th>Geste</th>
                    <th>📱 Android</th>
                    <th>🪟 Windows 11</th>
                    <th>🐧 Ubuntu 24.04</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Changer de fenêtre</strong></td>
                    <td>Bouton ou geste Récents</td>
                    <td><span class="d1b-kbd">Alt</span> + <span class="d1b-kbd">Tab</span></td>
                    <td><span class="d1b-kbd">Super</span> + <span class="d1b-kbd">Tab</span></td>
                </tr>
                <tr>
                    <td><strong>Écrire é et ê</strong></td>
                    <td>Appui long sur <code>e</code></td>
                    <td>Touche <code>é</code> directe ; <code>^</code> puis <code>e</code></td>
                    <td>Touche <code>é</code> directe ; <code>^</code> puis <code>e</code></td>
                </tr>
                <tr>
                    <td><strong>Se connecter au Wi-Fi</strong></td>
                    <td>Balayer du haut, toucher Internet, choisir le réseau, mot de passe</td>
                    <td>Icône Réseau en bas à droite, choisir le réseau, Connecter, mot de passe</td>
                    <td>Menu système en haut à droite, Wi-Fi, choisir le réseau, Se connecter</td>
                </tr>
                <tr>
                    <td><strong>Entretenir</strong></td>
                    <td>Chargeur de la marque, à l\'ombre (0–35 °C), protection batterie 80 %</td>
                    <td>Écran éteint, chiffon sec ; débrancher pendant l\'orage</td>
                    <td>Aérations dégagées ; onduleur (coupures) ou parasurtenseur (surtensions)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d1b-card">
        <h3>📖 4. Glossaire officiel du module (Les 15 termes clés)</h3>
        <table class="d1b-table">
            <thead>
                <tr>
                    <th style="width:28%;">Terme</th>
                    <th>Définition simple et opérationnelle</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><strong>1. Système d\'exploitation</strong></td><td>Logiciel qui fait le lien entre le matériel et l\'utilisateur et fait fonctionner les programmes (ex. : Android, Windows 11, Ubuntu 24.04).</td></tr>
                <tr><td><strong>2. Application</strong></td><td>Programme, ou logiciel, qui permet de réaliser une ou plusieurs tâches (ex. : WhatsApp, Bloc-notes, Calculatrice).</td></tr>
                <tr><td><strong>3. Écran d\'accueil</strong></td><td>Écran principal d\'Android où se trouvent les applications ; on peut en avoir plusieurs en balayant sur le côté.</td></tr>
                <tr><td><strong>4. Menu Démarrer</strong></td><td>Menu de Windows ouvert par le bouton Démarrer ou la touche Windows pour trouver applications, recherche et bouton Marche/Arrêt.</td></tr>
                <tr><td><strong>5. Dock</strong></td><td>Barre latérale gauche d\'Ubuntu qui contient les lanceurs d\'applications favorites ; aussi appelée <em>dash</em>.</td></tr>
                <tr><td><strong>6. Touche Super</strong></td><td>Touche souvent marquée du logo Windows, située près de Alt ; sous Ubuntu, elle ouvre la vue d\'ensemble des Activités.</td></tr>
                <tr><td><strong>7. Veille</strong></td><td>État de faible consommation d\'énergie qui garde le travail ouvert et permet de le reprendre tout de suite.</td></tr>
                <tr><td><strong>8. Arrêt forcé</strong></td><td>Appui prolongé sur le bouton d\'alimentation pour éteindre un appareil totalement figé ; à utiliser uniquement en dernier recours.</td></tr>
                <tr><td><strong>9. Maj (Majuscule)</strong></td><td>Touche maintenue enfoncée pour taper une lettre capitale ou le signe du haut d\'une touche (comme les chiffres sur AZERTY).</td></tr>
                <tr><td><strong>10. Touche morte</strong></td><td>Touche d\'accent, comme <code>^</code>, qui n\'écrit rien seule mais s\'applique à la lettre tapée juste après (ex. : <code>^</code> puis <code>e</code> donne <code>ê</code>).</td></tr>
                <tr><td><strong>11. Saisie vocale</strong></td><td>Fonction qui écrit automatiquement le texte que l\'on dicte à voix haute dans le micro.</td></tr>
                <tr><td><strong>12. Wi-Fi</strong></td><td>Réseau sans fil, fourni par une box ou un routeur, qui relie un appareil à internet sans consommer le forfait de la carte SIM.</td></tr>
                <tr><td><strong>13. Données mobiles</strong></td><td>Connexion à internet passant par la carte SIM, décomptée du forfait de l\'opérateur téléphonique.</td></tr>
                <tr><td><strong>14. Mode avion</strong></td><td>Réglage qui coupe d\'un coup les connexions sans fil de l\'appareil ; le Wi-Fi peut être rallumé à part.</td></tr>
                <tr><td><strong>15. Parasurtenseur</strong></td><td>Prise qui protège les appareils des variations de tension (surtensions), mais pas des coupures de courant (contrairement à l\'onduleur).</td></tr>
            </tbody>
        </table>
    </div>

    <div class="d1b-card">
        <h3>📚 5. Bibliographie et Sitographie officielle</h3>
        <ul>
            <li><strong>Cadres de référence :</strong>
                <ul>
                    <li>MTDPCE / DGTD (2026). <em>Référentiel de compétences de base (version 4) et Curriculum de formation en littératie numérique, Domaine 1 — Niveau Bronze</em>.</li>
                    <li>UNESCO (2018). <em>A Global Framework of Reference on Digital Literacy Skills for Indicator 4.4.2</em>.</li>
                    <li>Commission européenne (2022). <em>DigComp 2.2 : The Digital Competence Framework for Citizens</em>.</li>
                    <li>INSD Burkina Faso &amp; ICF (2023). <em>Enquête Démographique et de Santé du Burkina Faso 2021 : Rapport de synthèse</em>.</li>
                </ul>
            </li>
            <li><strong>Ressources officielles systèmes :</strong>
                <ul>
                    <li><strong>Windows 11 :</strong> <a href="https://support.microsoft.com/fr-fr/windows" target="_blank" rel="noopener">Support Microsoft Windows 11 (Menu Démarrer, Raccourcis clavier, Bloc-notes, Wi-Fi)</a></li>
                    <li><strong>Android &amp; Samsung :</strong> <a href="https://support.google.com/android/?hl=fr" target="_blank" rel="noopener">Aide Google Android &amp; Gboard</a> · <a href="https://www.samsung.com/africa_fr/support/" target="_blank" rel="noopener">Assistance Samsung Afrique (Protection de la batterie One UI 6.1, Entretien)</a></li>
                    <li><strong>Ubuntu 24.04 LTS :</strong> <a href="https://ubuntu.com/desktop/docs" target="_blank" rel="noopener">Documentation officielle Ubuntu Desktop 24.04 &amp; Aide GNOME</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>',
    ],
];

$section2_cmids = [];

foreach ($pages_spec as $pspec) {
    $page_rec = new stdClass();
    $page_rec->name = $pspec['name'];
    $page_rec->intro = '<p>' . $pspec['intro'] . '</p>';
    $page_rec->introformat = FORMAT_HTML;
    $page_rec->content = $pspec['content'];
    $page_rec->contentformat = FORMAT_HTML;
    $page_rec->legacyfiles = 0;
    $page_rec->display = 5;
    $page_rec->displayoptions = serialize(['printheading' => 1, 'printintro' => 0, 'printlastmodified' => 1]);
    $page_rec->revision = 1;

    [$cmid, $instanceid, $modcontext] = pact_upsert_module(
        $COURSE_ID,
        2,
        'page',
        $pspec['idnumber'],
        $page_rec,
        ['completion' => 1, 'completionview' => 1]
    );

    // Attach embedded images into mod_page/content/0
    foreach ($pspec['images'] as $img_name) {
        pact_store_file_from_path(
            $modcontext->id,
            'mod_page',
            'content',
            0,
            '/',
            $img_name,
            $MEDIA_DIR . '/' . $img_name
        );
    }

    $section2_cmids[] = $cmid;
    echo "  ✓ Page créée/mise à jour : {$pspec['name']} (cmid={$cmid})\n";
}

// -----------------------------------------------------------------------------
// 3B. GÉNÉRATION DE LA FICHE MÉMO PDF OFFICIELLE (TCPDF) & RESSOURCES SECTION 2
// -----------------------------------------------------------------------------
echo "  → Génération de la Fiche mémo PDF officielle (Fiche_memo_Domaine1_Module_Bronze.pdf)...\n";

$pdf = new pdf('P', 'mm', 'A4', true, 'UTF-8');
$pdf->SetCreator('PACTDIGITAL-BF / MTDPCE');
$pdf->SetAuthor('MTDPCE — Programme National de Littératie Numérique');
$pdf->SetTitle('Fiche Mémo & Glossaire — Domaine 1 : Découvrir l\'informatique (Module Bronze)');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(14, 14, 14);
$pdf->SetAutoPageBreak(true, 14);
$pdf->AddPage();

$pdf_html_p1 = '
<style>
h1 { color: #0d7a32; font-size: 16pt; font-weight: bold; margin-bottom: 4px; }
h2 { color: #0f172a; font-size: 12pt; font-weight: bold; background-color: #e8f7ee; padding: 6px 8px; }
p, li { font-size: 9.5pt; color: #1e293b; line-height: 1.45; }
table { border-collapse: collapse; width: 100%; font-size: 8.8pt; }
th { background-color: #0d7a32; color: #ffffff; font-weight: bold; padding: 6px; border: 1px solid #0d7a32; }
td { padding: 5px 6px; border: 1px solid #cbd5e1; }
.retenir { background-color: #fef9c3; border-left: 4px solid #ca8a04; padding: 6px 8px; font-weight: bold; font-size: 9.2pt; }
</style>
<p style="font-size:8.5pt;color:#475569;margin-bottom:2px;"><strong>BURKINA FASO — Ministère de la Transition Digitale, des Postes et des Communications Électroniques (MTDPCE / DGTD)</strong></p>
<h1>FICHE MÉMO DE POCHE — DOMAINE 1 : DÉCOUVRIR L\'INFORMATIQUE (BRONZE)</h1>
<p style="font-size:9.5pt;color:#334155;"><strong>Les 7 compétences du Référentiel (D1-B-01 à D1-B-07) sur Smartphone Android, PC Windows 11 et PC Ubuntu 24.04</strong></p>

<h2>1. Les 7 idées clés à garder près de son appareil</h2>
<ol>
    <li><strong>C1 — Identifier :</strong> Trois appareils, trois systèmes d\'exploitation : <strong>Android</strong> (téléphone), <strong>Windows 11</strong> et <strong>Ubuntu 24.04</strong> (ordinateurs). Ne pas confondre la marque (Tecno, Samsung), le système (Android) et l\'application (WhatsApp).</li>
    <li><strong>C2 — Démarrer et éteindre :</strong> On allume par le <strong>bouton d\'alimentation</strong> (patienter 30 s à 2 min sur PC), on verrouille dès qu\'on s\'absente (<strong>Windows + L</strong> ou <strong>Super + L</strong>), et on éteint toujours par le <strong>menu</strong>, jamais en débranchant !</li>
    <li><strong>C3 — Naviguer :</strong> Barre d\'état en haut et accueil sur Android ; barre des tâches en bas sur Windows 11 (<strong>Windows + D</strong> affiche le bureau) ; <strong>dock à gauche</strong> et horloge en haut sur Ubuntu 24.04.</li>
    <li><strong>C4 — Ouvrir une application :</strong> Tiroir d\'applications (balayer vers le haut sur Android) ; menu <strong>Démarrer</strong> puis taper le nom (Windows 11) ; touche <strong>Super</strong> puis taper le nom (Ubuntu 24.04).</li>
    <li><strong>C5 — Saisir du texte :</strong> Sur clavier AZERTY, <strong>é, è, ç, à</strong> sont directs ; <strong>^</strong> puis <strong>e</strong> donne <strong>ê</strong> ; maintenir <strong>Maj</strong> donne une majuscule ou les chiffres du haut. Relire 3 fois (sens, accents/majuscules, ponctuation).</li>
    <li><strong>C6 — Se connecter à internet :</strong> Le Wi-Fi protégé par un verrou épargne le forfait mobile. En cas de panne : vérifier d\'abord si le <strong>mode avion</strong> est coupé.</li>
    <li><strong>C7 — Entretenir son matériel :</strong> Chargeur de la marque à l\'ombre (0 à 35 °C, protection batterie 80 %), chiffon microfibre sec sur appareil éteint et débranché, <strong>onduleur</strong> contre les coupures de courant.</li>
</ol>

<h2>2. Fiche de synthèse 1 : Démarrer, se repérer et ouvrir une application</h2>
<p class="retenir">À retenir : Sur les trois systèmes, on éteint toujours par le menu, jamais en débranchant.</p>
<table cellpadding="5">
    <thead>
        <tr>
            <th width="22%">Geste</th>
            <th width="26%">Smartphone Android</th>
            <th width="26%">Ordinateur Windows 11</th>
            <th width="26%">Ordinateur Ubuntu 24.04</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Allumer / Éteindre</strong></td>
            <td>Appui long sur Marche/Arrêt ; menu d\'alimentation</td>
            <td>Bouton de l\'unité centrale ; Démarrer → Marche/Arrêt → Arrêter</td>
            <td>Bouton d\'alimentation ; menu système (haut droite) → extinction</td>
        </tr>
        <tr>
            <td><strong>Verrouiller</strong></td>
            <td>Appui court sur Marche/Arrêt</td>
            <td><strong>Windows + L</strong></td>
            <td><strong>Super + L</strong></td>
        </tr>
        <tr>
            <td><strong>Voir toutes les applis</strong></td>
            <td>Balayer du bas vers le haut (tiroir)</td>
            <td>Démarrer, puis « Toutes »</td>
            <td>Bouton à 9 points en bas du dock</td>
        </tr>
        <tr>
            <td><strong>Chercher une appli</strong></td>
            <td>Recherche du tiroir d\'applications</td>
            <td>Démarrer, puis taper le nom</td>
            <td>Touche <strong>Super</strong>, puis taper le nom</td>
        </tr>
    </tbody>
</table>

<h2>3. Fiche de synthèse 2 : Écrire, se connecter et entretenir son appareil</h2>
<p class="retenir">À retenir : Même logique partout pour le Wi-Fi : ouvrir la liste → choisir le nom du réseau → taper le mot de passe.</p>
<table cellpadding="5">
    <thead>
        <tr>
            <th width="22%">Geste</th>
            <th width="26%">Smartphone Android</th>
            <th width="26%">Ordinateur Windows 11</th>
            <th width="26%">Ordinateur Ubuntu 24.04</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Changer de fenêtre</strong></td>
            <td>Bouton ou geste Récents</td>
            <td>Garder <strong>Alt</strong> appuyé + <strong>Tab</strong></td>
            <td>Garder <strong>Super</strong> appuyé + <strong>Tab</strong></td>
        </tr>
        <tr>
            <td><strong>Écrire é et ê</strong></td>
            <td>Appui long sur la lettre <strong>e</strong></td>
            <td>Touche <strong>é</strong> directe ; <strong>^</strong> puis <strong>e</strong></td>
            <td>Touche <strong>é</strong> directe ; <strong>^</strong> puis <strong>e</strong></td>
        </tr>
        <tr>
            <td><strong>Se connecter au Wi-Fi</strong></td>
            <td>Balayer du haut → Internet → réseau → mot de passe</td>
            <td>Icône Réseau (bas droite) → réseau → Connecter → mot de passe</td>
            <td>Menu système (haut droite) → Wi-Fi → réseau → Se connecter</td>
        </tr>
        <tr>
            <td><strong>Entretenir</strong></td>
            <td>Chargeur de la marque, à l\'ombre (0–35 °C), limite 80 %</td>
            <td>Éteint et débranché, chiffon sec ; débrancher pendant l\'orage</td>
            <td>Aérations dégagées ; onduleur (coupures) / parasurtenseur</td>
        </tr>
    </tbody>
</table>';

$pdf->writeHTML($pdf_html_p1, true, false, true, false, '');
$pdf->AddPage();

$pdf_html_p2 = '
<style>
h2 { color: #0f172a; font-size: 12pt; font-weight: bold; background-color: #e8f7ee; padding: 6px 8px; }
table { border-collapse: collapse; width: 100%; font-size: 9pt; }
th { background-color: #0d7a32; color: #ffffff; font-weight: bold; padding: 6px; border: 1px solid #0d7a32; }
td { padding: 5px 6px; border: 1px solid #cbd5e1; }
</style>
<h2>4. Glossaire officiel du module Bronze (Les 15 mots clés)</h2>
<table cellpadding="5">
    <thead>
        <tr>
            <th width="28%">Terme</th>
            <th width="72%">Définition</th>
        </tr>
    </thead>
    <tbody>
        <tr><td><strong>1. Système d\'exploitation</strong></td><td>Logiciel qui fait le lien entre le matériel et l\'utilisateur et fait fonctionner les programmes (Android, Windows 11, Ubuntu 24.04).</td></tr>
        <tr><td><strong>2. Application</strong></td><td>Programme, ou logiciel, qui permet de réaliser une ou plusieurs tâches (WhatsApp, Bloc-notes, Calculatrice).</td></tr>
        <tr><td><strong>3. Écran d\'accueil</strong></td><td>Écran principal d\'Android où se trouvent les applications ; on peut en avoir plusieurs.</td></tr>
        <tr><td><strong>4. Menu Démarrer</strong></td><td>Menu de Windows ouvert par le bouton Démarrer ou la touche Windows pour trouver applications et réglages.</td></tr>
        <tr><td><strong>5. Dock</strong></td><td>Barre latérale gauche d\'Ubuntu qui contient les lanceurs d\'applications ; aussi appelée dash.</td></tr>
        <tr><td><strong>6. Touche Super</strong></td><td>Touche souvent marquée du logo Windows, près de Alt ; sous Ubuntu, elle ouvre la vue d\'ensemble des Activités.</td></tr>
        <tr><td><strong>7. Veille</strong></td><td>État de faible consommation qui garde le travail ouvert et permet de le reprendre tout de suite.</td></tr>
        <tr><td><strong>8. Arrêt forcé</strong></td><td>Appui prolongé sur le bouton d\'alimentation pour éteindre un appareil figé ; à utiliser en dernier recours.</td></tr>
        <tr><td><strong>9. Maj</strong></td><td>Touche maintenue enfoncée pour taper une lettre capitale ou le signe du haut (comme les chiffres sur un clavier AZERTY).</td></tr>
        <tr><td><strong>10. Touche morte</strong></td><td>Touche d\'accent, comme ^, qui n\'écrit rien seule mais s\'applique à la lettre tapée juste après (^ puis e donne ê).</td></tr>
        <tr><td><strong>11. Saisie vocale</strong></td><td>Fonction qui écrit le texte que l\'on dicte à voix haute dans le micro.</td></tr>
        <tr><td><strong>12. Wi-Fi</strong></td><td>Réseau sans fil, fourni par une box ou un routeur, qui relie un appareil à internet sans utiliser le forfait SIM.</td></tr>
        <tr><td><strong>13. Données mobiles</strong></td><td>Connexion à internet par la carte SIM, décomptée du forfait de l\'opérateur.</td></tr>
        <tr><td><strong>14. Mode avion</strong></td><td>Réglage qui coupe les connexions sans fil ; le Wi-Fi peut être rallumé à part.</td></tr>
        <tr><td><strong>15. Parasurtenseur</strong></td><td>Prise qui protège les appareils des variations de tension, mais pas des coupures (c\'est l\'onduleur qui prend le relais pendant une coupure).</td></tr>
    </tbody>
</table>';

$pdf->writeHTML($pdf_html_p2, true, false, true, false, '');
$pdf_bytes = $pdf->Output('Fiche_memo_Domaine1_Module_Bronze.pdf', 'S');

// Resource 1 in Section 2: Fiche mémo PDF
$res_memo = new stdClass();
$res_memo->name = '📄 Fiche mémo PDF — Synthèse & Glossaire (Domaine 1 Bronze)';
$res_memo->intro = '<p>Téléchargez et conservez votre <strong>Fiche mémo officielle (PDF)</strong> regroupant les 7 points clés à retenir, les Fiches de synthèse 1 et 2 (Android, Windows 11, Ubuntu 24.04) et le Glossaire des 15 termes du module.</p>';
$res_memo->introformat = FORMAT_HTML;
$res_memo->tobemigrated = 0;
$res_memo->legacyfiles = 0;
$res_memo->display = 0;
$res_memo->displayoptions = serialize(['showsize' => 1, 'showtype' => 1, 'showdate' => 1, 'printintro' => 1]);
$res_memo->filterfiles = 0;
$res_memo->revision = 1;

[$memo_cmid, $memo_instid, $memo_ctx] = pact_upsert_module(
    $COURSE_ID,
    2,
    'resource',
    'd1_b_res_memo_pdf',
    $res_memo,
    ['completion' => 1, 'completionview' => 1, 'showdescription' => 1]
);

pact_store_file_from_string(
    $memo_ctx->id,
    'mod_resource',
    'content',
    0,
    '/',
    'Fiche_memo_Domaine1_Module_Bronze.pdf',
    $pdf_bytes,
    1
);
$section2_cmids[] = $memo_cmid;
echo "  ✓ Ressource Fiche mémo PDF créée/mise à jour (cmid={$memo_cmid}).\n";

// Resource 2 in Section 2: Support de cours complet PowerPoint (.pptx)
$res_pptx = new stdClass();
$res_pptx->name = '📊 Support de cours complet — Découvrir l\'informatique (Diaporama PowerPoint)';
$res_pptx->intro = '<p>Support visuel complet de la formation (115 diapositives illustrées : cours, démonstrations pas à pas, 7 pratiques en binôme, travaux de groupe, fiches de synthèse et glossaire).</p>';
$res_pptx->introformat = FORMAT_HTML;
$res_pptx->tobemigrated = 0;
$res_pptx->legacyfiles = 0;
$res_pptx->display = 0;
$res_pptx->displayoptions = serialize(['showsize' => 1, 'showtype' => 1, 'showdate' => 1, 'printintro' => 1]);
$res_pptx->filterfiles = 0;
$res_pptx->revision = 1;

[$pptx_cmid, $pptx_instid, $pptx_ctx] = pact_upsert_module(
    $COURSE_ID,
    2,
    'resource',
    'd1_b_res_pptx_apprenant',
    $res_pptx,
    ['completion' => 1, 'completionview' => 1, 'showdescription' => 1]
);

pact_store_file_from_path(
    $pptx_ctx->id,
    'mod_resource',
    'content',
    0,
    '/',
    'DOMAINE 1_MODULE BRONZE.pptx',
    $FILES_DIR . '/DOMAINE 1_MODULE BRONZE.pptx',
    1
);
$section2_cmids[] = $pptx_cmid;
echo "  ✓ Ressource Support PowerPoint créée/mise à jour (cmid={$pptx_cmid}).\n";

// Quiz formatif in Section 2: 16 questions d'entraînement (Séquence 7)
// Note: Name intentionally avoids "examen", "final", "certification" so course_welcome.php picks quizid=54 as the final exam!
$formative_quiz_rec = new stdClass();
$formative_quiz_rec->name = '✅ Quiz formatif d\'entraînement — Les 7 compétences (16 questions)';
$formative_quiz_rec->intro = '<div style="font-family:\'Plus Jakarta Sans\',Segoe UI,sans-serif;line-height:1.6;">
    <p><strong>Séquence 7 — Évaluation formative interactive (16 questions)</strong></p>
    <p>Vérifiez votre maîtrise des 7 compétences du module avant de vous présenter à l\'examen final. Les tentatives sont <strong>illimitées</strong> et chaque question comporte une explication détaillée après validation.</p>
</div>';
$formative_quiz_rec->introformat = FORMAT_HTML;
$formative_quiz_rec->timeopen = 0;
$formative_quiz_rec->timeclose = 0;
$formative_quiz_rec->timelimit = 1800; // 30 min indicatif
$formative_quiz_rec->overduehandling = 'autosubmit';
$formative_quiz_rec->graceperiod = 0;
$formative_quiz_rec->preferredbehaviour = 'deferredfeedback';
$formative_quiz_rec->canredoquestions = 0;
$formative_quiz_rec->attempts = 0; // Tentatives illimitées
$formative_quiz_rec->attemptonlast = 0;
$formative_quiz_rec->grademethod = 1;
$formative_quiz_rec->decimalpoints = 2;
$formative_quiz_rec->questiondecimalpoints = -1;
$formative_quiz_rec->reviewattempt = 69888;
$formative_quiz_rec->reviewcorrectness = 4352;
$formative_quiz_rec->reviewmaxmarks = 69888;
$formative_quiz_rec->reviewmarks = 4352;
$formative_quiz_rec->reviewspecificfeedback = 4352;
$formative_quiz_rec->reviewgeneralfeedback = 4352;
$formative_quiz_rec->reviewrightanswer = 4352;
$formative_quiz_rec->reviewoverallfeedback = 4352;
$formative_quiz_rec->questionsperpage = 4;
$formative_quiz_rec->navmethod = 'free';
$formative_quiz_rec->shuffleanswers = 1;
$formative_quiz_rec->sumgrades = 16.00000;
$formative_quiz_rec->grade = 16.00000;
$formative_quiz_rec->password = '';
$formative_quiz_rec->subnet = '';
$formative_quiz_rec->browsersecurity = '-';
$formative_quiz_rec->delay1 = 0;
$formative_quiz_rec->delay2 = 0;
$formative_quiz_rec->showuserpicture = 0;
$formative_quiz_rec->showblocks = 0;
$formative_quiz_rec->completionattemptsexhausted = 0;
$formative_quiz_rec->completionminattempts = 1;
$formative_quiz_rec->allowofflineattempts = 0;

[$fquiz_cmid, $fquiz_instid, $fquiz_ctx] = pact_upsert_module(
    $COURSE_ID,
    2,
    'quiz',
    'd1_b_quiz_formatif',
    $formative_quiz_rec,
    ['completion' => 1]
);

pact_populate_quiz_questions($fquiz_instid, $fquiz_cmid, 'd1_b_formatif', $questions_16);
$section2_cmids[] = $fquiz_cmid;

// Ensure clean ordering of Section 2 sequence
$sec2->sequence = implode(',', $section2_cmids);
$DB->update_record('course_sections', $sec2);
echo "  ✓ Quiz formatif d'entraînement créé/mis à jour (quizid={$fquiz_instid}, cmid={$fquiz_cmid}).\n";

// =============================================================================
// ÉTAPE 4 : SECTION 3 — 🏆 EXAMEN FINAL (21 QUESTIONS QCM — PARTIE A)
// =============================================================================
echo "\n[Étape 4/7] Configuration et peuplement de l'Examen final (Section 3, quizid=54, cmid=215)...\n";

$sec3 = $DB->get_record('course_sections', ['course' => $COURSE_ID, 'section' => 3], '*', MUST_EXIST);
$sec3->name = '🏆 Examen final';
$sec3->summary = '<p><strong>Épreuve finale de validation des compétences (Module Bronze — Domaine 1 : Découvrir l\'informatique)</strong> : 21 questions à choix unique (3 questions par compétence C1 à C7). Durée : <strong>30 minutes</strong> · Seuil de réussite : <strong>70 % (15 / 21)</strong> · <strong>2 tentatives maximum</strong>.</p>';
$sec3->summaryformat = FORMAT_HTML;
$DB->update_record('course_sections', $sec3);

$questions_21_exam = [
    // C1 : Identifier les appareils numériques (Q1 à Q3)
    [
        'name' => 'Examen Q01 — C1 : Rôle du système d\'exploitation',
        'questiontext' => '<p><strong>[C1 : Identifier les appareils numériques — Question 1/21]</strong></p><p>Quel est le rôle du système d\'exploitation d\'un appareil ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C1) :</strong> Le système d\'exploitation relie le matériel et l\'utilisateur et fait tourner les applications, comme un interprète au marché.</p>',
        'options' => [
            'Protéger l\'appareil contre les coupures de courant',
            'Envoyer des messages, comme WhatsApp',
            'Faire le lien entre l\'utilisateur et le matériel, et faire fonctionner les applications',
            'Indiquer la marque du fabricant de l\'appareil',
        ],
        'correct' => 2,
        'explanation' => 'Le système d\'exploitation relie le matériel et l\'utilisateur et fait tourner les applications, comme un interprète au marché.',
    ],
    [
        'name' => 'Examen Q02 — C1 : Reconnaître un ordinateur de bureau',
        'questiontext' => '<p><strong>[C1 : Identifier les appareils numériques — Question 2/21]</strong></p><p>À la mairie, un poste se compose d\'un écran, d\'un clavier, d\'une souris et d\'un boîtier, reliés par des câbles et sans batterie. De quel appareil s\'agit-il ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C1) :</strong> L\'ordinateur de bureau réunit plusieurs éléments reliés, dont l\'unité centrale, et n\'a pas de batterie ; le portable réunit tout en un seul appareil.</p>',
        'options' => [
            'Un ordinateur de bureau',
            'Un ordinateur portable',
            'Un smartphone',
            'Une box Wi-Fi',
        ],
        'correct' => 0,
        'explanation' => 'L\'ordinateur de bureau réunit plusieurs éléments reliés, dont l\'unité centrale, et n\'a pas de batterie ; le portable réunit tout en un seul appareil.',
    ],
    [
        'name' => 'Examen Q03 — C1 : Distinguer marque et système d\'exploitation',
        'questiontext' => '<p><strong>[C1 : Identifier les appareils numériques — Question 3/21]</strong></p><p>Issouf affirme : « Mon téléphone est un Tecno, donc son système s\'appelle Tecno. » Que lui répondez-vous ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C1) :</strong> Il ne faut pas confondre la marque de l\'appareil (Tecno, Samsung) avec son système d\'exploitation (Android), ni avec une application.</p>',
        'options' => [
            'Il a raison : la marque et le système portent toujours le même nom',
            'Son système est WhatsApp, l\'application qu\'il utilise le plus',
            'Son système est Windows, comme sur l\'ordinateur de la mairie',
            'Tecno est la marque ; le système qui fait marcher son téléphone est Android',
        ],
        'correct' => 3,
        'explanation' => 'Il ne faut pas confondre la marque de l\'appareil (Tecno, Samsung) avec son système d\'exploitation (Android), ni avec une application.',
    ],

    // C2 : Démarrer et éteindre un appareil (Q4 à Q6)
    [
        'name' => 'Examen Q04 — C2 : Verrouiller l\'écran sous Ubuntu 24.04',
        'questiontext' => '<p><strong>[C2 : Démarrer et éteindre un appareil — Question 4/21]</strong></p><p>Sur un ordinateur Ubuntu 24.04, quel raccourci verrouille l\'écran sans éteindre l\'ordinateur ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C2) :</strong> <code>Super + L</code> verrouille le PC Ubuntu ; on revient par un clic ou Entrée, puis le mot de passe.</p>',
        'options' => [
            'Alt + F4',
            'Super + L',
            'Windows + D',
            'Super + Tab',
        ],
        'correct' => 1,
        'explanation' => 'Super + L verrouille le PC Ubuntu ; on revient par un clic ou Entrée, puis le mot de passe.',
    ],
    [
        'name' => 'Examen Q05 — C2 : Patience au démarrage d\'un PC',
        'questiontext' => '<p><strong>[C2 : Démarrer et éteindre un appareil — Question 5/21]</strong></p><p>Vous venez d\'appuyer sur le bouton de l\'unité centrale. Après 20 secondes, l\'écran est toujours noir. Que faites-vous ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C2) :</strong> Le démarrage prend du temps : l\'ordinateur démarre déjà, rappuyer ou débrancher ne sert à rien et peut nuire.</p>',
        'options' => [
            'Vous rappuyez sur le bouton de l\'unité centrale',
            'Vous débranchez puis rebranchez l\'ordinateur',
            'Vous gardez le bouton appuyé pour forcer l\'arrêt',
            'Vous patientez, car un PC met en général 30 secondes à 2 minutes à démarrer',
        ],
        'correct' => 3,
        'explanation' => 'Le démarrage prend du temps : l\'ordinateur démarre déjà, rappuyer ou débrancher ne sert à rien et peut nuire.',
    ],
    [
        'name' => 'Examen Q06 — C2 : Choisir entre veille et arrêt en fin de journée',
        'questiontext' => '<p><strong>[C2 : Démarrer et éteindre un appareil — Question 6/21]</strong></p><p>En fin de journée, la batterie du portable d\'Awa est faible et le courant est coupé. Elle a fini son courrier. Quel est le meilleur choix ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C2) :</strong> La veille convient à une courte pause ; on éteint le soir et quand la batterie est faible, après avoir enregistré son travail.</p>',
        'options' => [
            'Mettre le portable en veille pour reprendre plus vite demain',
            'Verrouiller avec Windows + L et partir',
            'Enregistrer le courrier, puis éteindre par Démarrer, Marche/Arrêt, Arrêter',
            'Rabattre l\'écran sans rien enregistrer',
        ],
        'correct' => 2,
        'explanation' => 'La veille convient à une courte pause ; on éteint le soir et quand la batterie est faible, après avoir enregistré son travail.',
    ],

    // C3 : Naviguer dans l'interface de base (Q7 à Q9)
    [
        'name' => 'Examen Q07 — C3 : Barre d\'état de l\'écran d\'accueil Android',
        'questiontext' => '<p><strong>[C3 : Naviguer dans l\'interface de base — Question 7/21]</strong></p><p>Sur l\'écran d\'accueil d\'un téléphone Android, que trouve-t-on dans la barre d\'état, tout en haut ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C3) :</strong> La barre d\'état, zone 1 de l\'écran d\'accueil, affiche l\'heure, le réseau et la batterie ; les boutons de navigation sont en bas.</p>',
        'options' => [
            'L\'heure, le réseau et la batterie',
            'Les boutons Retour, Accueil et Récents',
            'La liste de toutes les applications',
            'Les applications favorites',
        ],
        'correct' => 0,
        'explanation' => 'La barre d\'état, zone 1 de l\'écran d\'accueil, affiche l\'heure, le réseau et la batterie ; les boutons de navigation sont en bas.',
    ],
    [
        'name' => 'Examen Q08 — C3 : Changer de fenêtre avec Alt + Tab sous Windows 11',
        'questiontext' => '<p><strong>[C3 : Naviguer dans l\'interface de base — Question 8/21]</strong></p><p>Sous Windows 11, Awa a deux fenêtres ouvertes et veut passer de l\'une à l\'autre au clavier. Comment fait-elle ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C3) :</strong> Pour <code>Alt + Tab</code>, Alt reste appuyé pendant les appuis sur Tab ; lâcher Alt trop tôt empêche de changer de fenêtre, et Alt + F4 ferme la fenêtre.</p>',
        'options' => [
            'Elle appuie sur Alt, le relâche, puis appuie sur Tab',
            'Elle appuie sur Windows + L',
            'Elle garde Alt appuyé et tape Tab jusqu\'à la fenêtre voulue',
            'Elle appuie sur Alt + F4',
        ],
        'correct' => 2,
        'explanation' => 'Pour Alt + Tab, Alt reste appuyé pendant les appuis sur Tab ; lâcher Alt trop tôt empêche de changer de fenêtre, et Alt + F4 ferme la fenêtre.',
    ],
    [
        'name' => 'Examen Q09 — C3 : Cause d\'un double-clic qui n\'ouvre pas l\'application',
        'questiontext' => '<p><strong>[C3 : Naviguer dans l\'interface de base — Question 9/21]</strong></p><p>Mariam double-clique sur une icône du PC Ubuntu, mais l\'application ne s\'ouvre pas : l\'icône est seulement sélectionnée. Quelle est la cause la plus probable ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C3) :</strong> Un double-clic trop lent compte comme deux clics simples : il faut recommencer un peu plus vite, sans bouger la souris.</p>',
        'options' => [
            'La souris est en panne',
            'Ses deux appuis sont trop lents et comptent comme deux clics simples',
            'Elle a appuyé sur le bouton droit de la souris',
            'Ubuntu n\'accepte pas le double-clic',
        ],
        'correct' => 1,
        'explanation' => 'Un double-clic trop lent compte comme deux clics : il faut recommencer un peu plus vite, sans bouger la souris.',
    ],

    // C4 : Localiser et ouvrir une application (Q10 à Q12)
    [
        'name' => 'Examen Q10 — C4 : Bouton à neuf points du dock sous Ubuntu 24.04',
        'questiontext' => '<p><strong>[C4 : Localiser et ouvrir une application — Question 10/21]</strong></p><p>Sous Ubuntu 24.04, à quoi sert le bouton à neuf points placé en bas du dock ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C4) :</strong> Le bouton à neuf points en bas du dock affiche toutes les applications ; le menu système est en haut à droite.</p>',
        'options' => [
            'À éteindre l\'ordinateur',
            'À ouvrir le menu système',
            'À changer de fenêtre',
            'À afficher la liste complète des applications',
        ],
        'correct' => 3,
        'explanation' => 'Le bouton à neuf points en bas du dock affiche toutes les applications ; le menu système est en haut à droite.',
    ],
    [
        'name' => 'Examen Q11 — C4 : Rechercher le Bloc-notes dans le menu Démarrer',
        'questiontext' => '<p><strong>[C4 : Localiser et ouvrir une application — Question 11/21]</strong></p><p>Sous Windows 11, Awa clique sur le logo Windows pour chercher le Bloc-notes. Que fait-elle ensuite ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C4) :</strong> On tape le nom tout de suite : cliquer ailleurs ou double-cliquer sur le logo referme le menu Démarrer.</p>',
        'options' => [
            'Elle clique au milieu de l\'écran, puis tape « Bloc-notes »',
            'Elle tape « Bloc-notes » sans cliquer ailleurs, puis clique sur le résultat',
            'Elle double-clique sur le logo Windows',
            'Elle appuie sur Windows + D',
        ],
        'correct' => 1,
        'explanation' => 'On tape le nom tout de suite : cliquer ailleurs ou double-cliquer sur le logo referme le menu Démarrer.',
    ],
    [
        'name' => 'Examen Q12 — C4 : Garder une application à portée sous Ubuntu 24.04',
        'questiontext' => '<p><strong>[C4 : Localiser et ouvrir une application — Question 12/21]</strong></p><p>Au cyber, Mariam utilise chaque jour l\'Éditeur de texte sous Ubuntu 24.04 et veut le garder à portée de clic. Quelle solution choisissez-vous pour elle ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C4) :</strong> Sous Ubuntu, on garde une application à portée en l\'ajoutant aux favoris du dock ; épingler à Démarrer est le geste de Windows, glisser sur l\'accueil celui d\'Android.</p>',
        'options' => [
            'L\'ajouter aux favoris du dock',
            'L\'épingler à Démarrer',
            'Le glisser sur l\'écran d\'accueil',
            'L\'ouvrir par le menu système',
        ],
        'correct' => 0,
        'explanation' => 'Sous Ubuntu, on garde une application à portée en l\'ajoutant aux favoris du dock ; épingler à Démarrer est le geste de Windows, glisser sur l\'accueil celui d\'Android.',
    ],

    // C5 : Saisir du texte (Q13 à Q15)
    [
        'name' => 'Examen Q13 — C5 : Effet de la touche Verr. Maj',
        'questiontext' => '<p><strong>[C5 : Saisir du texte — Question 13/21]</strong></p><p>Que se passe-t-il quand la touche <strong>Verr. Maj</strong> est activée sur un clavier d\'ordinateur ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C5) :</strong> Verr. Maj bloque les majuscules et allume un voyant ; on rappuie dessus pour revenir aux minuscules.</p>',
        'options' => [
            'Le clavier efface le caractère à droite du curseur',
            'Les accents ne s\'affichent plus',
            'Tout s\'écrit en capitales et un voyant s\'allume',
            'La dictée vocale se lance',
        ],
        'correct' => 2,
        'explanation' => 'Verr. Maj bloque les majuscules et allume un voyant ; on rappuie dessus pour revenir aux minuscules.',
    ],
    [
        'name' => 'Examen Q14 — C5 : Taper le chiffre 2 sur un clavier AZERTY',
        'questiontext' => '<p><strong>[C5 : Saisir du texte — Question 14/21]</strong></p><p>Dans le Bloc-notes, Awa doit taper le chiffre <strong>2</strong>. Sur son clavier AZERTY, cette touche porte aussi le <strong>é</strong>. Comment obtient-elle le 2 ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C5) :</strong> Sur la rangée du haut d\'un clavier AZERTY, l\'accent sort directement et le chiffre demande Maj : Maj plus é donne 2.</p>',
        'options' => [
            'Par un appui long sur la touche é',
            'En tapant ^, puis la touche é',
            'En appuyant deux fois de suite sur la touche é',
            'En maintenant Maj et en appuyant sur la touche é',
        ],
        'correct' => 3,
        'explanation' => 'Sur la rangée du haut d\'un clavier AZERTY, l\'accent sort directement et le chiffre demande Maj : Maj plus é donne 2.',
    ],
    [
        'name' => 'Examen Q15 — C5 : Relecture et correction d\'une note',
        'questiontext' => '<p><strong>[C5 : Saisir du texte — Question 15/21]</strong></p><p>Issouf relit la note qu\'il vient de taper sur son téléphone : « <code>livraison prête jeudu</code> ». Quelles corrections faut-il faire ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C5) :</strong> La relecture en trois passages repère le sens (jeudi), la majuscule de début de phrase et la ponctuation ; les suggestions et la dictée peuvent se tromper.</p>',
        'options' => [
            'Aucune, le clavier du téléphone corrige tout seul',
            'Une majuscule à « Livraison », « jeudi » au lieu de « jeudu » et un point final',
            'Seulement un point final',
            'Réécrire toute la phrase en capitales',
        ],
        'correct' => 1,
        'explanation' => 'La relecture en trois passages repère le sens (jeudi), la majuscule de début de phrase et la ponctuation ; les suggestions et la dictée peuvent se tromper.',
    ],

    // C6 : Connecter un appareil à internet (Q16 à Q18)
    [
        'name' => 'Examen Q16 — C6 : Signification du verrou sur un réseau Wi-Fi',
        'questiontext' => '<p><strong>[C6 : Connecter un appareil à internet — Question 16/21]</strong></p><p>Dans la liste des Wi-Fi, que signale un petit verrou à côté du nom d\'un réseau ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C6) :</strong> Le verrou indique un réseau protégé ; on demande le mot de passe au responsable du réseau.</p>',
        'options' => [
            'Le réseau est protégé : il faut un mot de passe',
            'Le réseau est en panne',
            'Le réseau utilise le forfait du téléphone',
            'Le mode avion est activé',
        ],
        'correct' => 0,
        'explanation' => 'Le verrou indique un réseau protégé ; on demande le mot de passe au responsable du réseau.',
    ],
    [
        'name' => 'Examen Q17 — C6 : Chemin de connexion Wi-Fi sous Windows 11',
        'questiontext' => '<p><strong>[C6 : Connecter un appareil à internet — Question 17/21]</strong></p><p>Sous Windows 11, Awa veut connecter le PC au Wi-Fi de la mairie. Quel chemin suit-elle ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C6) :</strong> Sous Windows 11, on passe par l\'icône Réseau en bas à droite ; le menu système est le chemin d\'Ubuntu, le balayage celui d\'Android.</p>',
        'options' => [
            'Menu système en haut à droite, Wi-Fi, nom du réseau, Se connecter',
            'Démarrer, Marche/Arrêt, puis Connecter',
            'Icône Réseau en bas à droite, Gérer les connexions Wi-Fi, nom du réseau, Connecter, mot de passe, Suivant',
            'Balayer du haut vers le bas, toucher Internet, nom du réseau',
        ],
        'correct' => 2,
        'explanation' => 'Sous Windows 11, on passe par l\'icône Réseau en bas à droite ; le menu système est le chemin d\'Ubuntu, le balayage celui d\'Android.',
    ],
    [
        'name' => 'Examen Q18 — C6 : Mot de passe Wi-Fi refusé',
        'questiontext' => '<p><strong>[C6 : Connecter un appareil à internet — Question 18/21]</strong></p><p>Le mot de passe du Wi-Fi de la salle est refusé deux fois sur le téléphone d\'Issouf, alors qu\'il l\'a recopié du tableau. Que vérifie-t-il d\'abord ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C6) :</strong> Une majuscule tapée à la place d\'une minuscule suffit à faire échouer la connexion : on retape le mot de passe avec soin avant de chercher ailleurs.</p>',
        'options' => [
            'S\'il doit acheter un nouveau forfait',
            'Si la box de la salle doit être redémarrée',
            'S\'il faut changer de téléphone',
            'S\'il a bien respecté les majuscules et les minuscules du mot de passe',
        ],
        'correct' => 3,
        'explanation' => 'Une majuscule tapée à la place d\'une minuscule suffit à faire échouer la connexion : on retape le mot de passe avec soin avant de chercher ailleurs.',
    ],

    // C7 : Entretenir son appareil (Q19 à Q21)
    [
        'name' => 'Examen Q19 — C7 : Plage de température conseillée par Samsung',
        'questiontext' => '<p><strong>[C7 : Entretenir son appareil — Question 19/21]</strong></p><p>Quelle plage de température Samsung conseille-t-il pour ses téléphones, d\'après le module ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C7) :</strong> Samsung donne une plage de 0 à 35 °C ; au-delà, et longtemps, la batterie s\'use plus vite.</p>',
        'options' => [
            'De 10 à 50 °C',
            'De 0 à 35 °C',
            'De 20 à 45 °C',
            'De 0 à 80 °C',
        ],
        'correct' => 1,
        'explanation' => 'Samsung donne une plage de 0 à 35 °C ; au-delà, et longtemps, la batterie s\'use plus vite.',
    ],
    [
        'name' => 'Examen Q20 — C7 : Nettoyer un clavier en période d\'harmattan',
        'questiontext' => '<p><strong>[C7 : Entretenir son appareil — Question 20/21]</strong></p><p>Un lundi matin, en période d\'harmattan, Awa nettoie le clavier de la mairie. Quelle méthode applique-t-elle ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C7) :</strong> On nettoie un appareil éteint et débranché, avec un chiffon sec ou à peine humide, sans liquide versé et sans rien enfoncer.</p>',
        'options' => [
            'Elle éteint et débranche, retourne le clavier et tapote, puis passe un chiffon microfibre sec ou à peine humide',
            'Elle verse un peu d\'eau savonneuse entre les touches',
            'Elle gratte entre les touches avec un cure-dent, ordinateur allumé',
            'Elle sèche le clavier au sèche-cheveux après l\'avoir rincé',
        ],
        'correct' => 0,
        'explanation' => 'On nettoie un appareil éteint et débranché, avec un chiffon sec ou à peine humide, sans liquide versé et sans rien enfoncer.',
    ],
    [
        'name' => 'Examen Q21 — C7 : Téléphone brûlant et batterie faible',
        'questiontext' => '<p><strong>[C7 : Entretenir son appareil — Question 21/21]</strong></p><p>Après une matinée au champ, le téléphone de Boureima est brûlant et sa batterie est faible. Que lui conseillez-vous ?</p>',
        'generalfeedback' => '<p><strong>Corrigé officiel (C7) :</strong> Un téléphone brûlant doit refroidir avant la charge ; on charge à l\'ombre, dans un endroit aéré, avec le chargeur de la marque.</p>',
        'options' => [
            'Le charger tout de suite avec le chargeur sans marque du voisin',
            'Le charger tout de suite au soleil, pour gagner du temps',
            'Le laisser refroidir à l\'ombre, puis le charger avec le chargeur de la marque dans un endroit aéré',
            'Le charger dans la voiture fermée, à l\'abri de la poussière',
        ],
        'correct' => 2,
        'explanation' => 'Un téléphone brûlant doit refroidir avant la charge ; on charge à l\'ombre, dans un endroit aéré, avec le chargeur de la marque.',
    ],
];

// Update Final Exam Quiz (id=54, cmid=215)
$exam_quiz = $DB->get_record('quiz', ['id' => 54, 'course' => $COURSE_ID], '*', MUST_EXIST);
$exam_quiz->name = 'Examen final';
$exam_quiz->intro = '<div style="font-family:\'Plus Jakarta Sans\',Segoe UI,sans-serif;line-height:1.6;">
    <p><strong>Épreuve finale d\'évaluation des acquis — Module Bronze (Domaine 1 : Découvrir l\'informatique)</strong></p>
    <ul>
        <li><strong>Structure :</strong> 21 questions à choix unique (3 questions pour chacune des 7 compétences C1 à C7).</li>
        <li><strong>Durée :</strong> 30 minutes.</li>
        <li><strong>Barème &amp; Seuil de réussite :</strong> 1 point par bonne réponse (total sur 21 points). Seuil de validation fixé à <strong>70 %</strong> (au moins <strong>15 / 21</strong>).</li>
        <li><strong>Tentatives autorisées :</strong> 2 tentatives maximum.</li>
    </ul>
</div>';
$exam_quiz->introformat = FORMAT_HTML;
$exam_quiz->timelimit = 1800; // 30 minutes
$exam_quiz->overduehandling = 'autosubmit';
$exam_quiz->preferredbehaviour = 'deferredfeedback';
$exam_quiz->attempts = 2; // 2 tentatives maximum
$exam_quiz->grademethod = 1; // Note la plus haute
$exam_quiz->questionsperpage = 3; // 3 questions par page = 1 compétence par page !
$exam_quiz->navmethod = 'free';
$exam_quiz->shuffleanswers = 1;
$exam_quiz->reviewattempt = 69888;
$exam_quiz->reviewcorrectness = 4352;
$exam_quiz->reviewmaxmarks = 69888;
$exam_quiz->reviewmarks = 4352;
$exam_quiz->reviewspecificfeedback = 4352;
$exam_quiz->reviewgeneralfeedback = 4352;
$exam_quiz->reviewrightanswer = 4352;
$exam_quiz->reviewoverallfeedback = 4352;
$exam_quiz->sumgrades = 21.00000;
$exam_quiz->grade = 21.00000;
$exam_quiz->timemodified = $now;
$DB->update_record('quiz', $exam_quiz);

pact_populate_quiz_questions(54, 215, 'd1_b_examen', $questions_21_exam);

// Update grade_items for quizid=54 so grademax=21.00000, gradepass=14.70000 (70%), hidden=0
$gitem = $DB->get_record('grade_items', ['courseid' => $COURSE_ID, 'itemmodule' => 'quiz', 'iteminstance' => 54]);
if ($gitem) {
    $gitem->grademax = 21.00000;
    $gitem->gradepass = 14.70000; // 70% de 21 = 14.7 (soit 15/21)
    $gitem->hidden = 0;
    $gitem->timemodified = $now;
    $DB->update_record('grade_items', $gitem);
}
echo "  ✓ Examen final (quizid=54, cmid=215) configuré (30 min, 2 tentatives, seuil 14.7/21) et peuplé avec les 21 QCM.\n";

// =============================================================================
// ÉTAPE 5 : SECTION 4 — 📊 ENQUÊTE DE SATISFACTION & AUTO-ÉVALUATION (SLIDES 95-96)
// =============================================================================
echo "\n[Étape 5/7] Peuplement de l'Enquête de satisfaction et d'auto-évaluation (Section 4, feedbackid=26, cmid=216)...\n";

$sec4 = $DB->get_record('course_sections', ['course' => $COURSE_ID, 'section' => 4], '*', MUST_EXIST);
$sec4->name = '📊 Enquête de satisfaction';
$sec4->summary = '<p>Mesurez votre progression sur les <strong>7 compétences du module</strong> (auto-évaluation) et donnez votre avis anonyme sur la formation (de 1 à 4) afin de débloquer votre attestation de réussite.</p>';
$sec4->summaryformat = FORMAT_HTML;
$DB->update_record('course_sections', $sec4);

$feedback = $DB->get_record('feedback', ['id' => 26, 'course' => $COURSE_ID], '*', MUST_EXIST);
$feedback->name = 'Enquête de satisfaction et d\'auto-évaluation';
$feedback->intro = '<div style="font-family:\'Plus Jakarta Sans\',Segoe UI,sans-serif;line-height:1.6;">
    <p>Merci de compléter cette fiche finale (anonyme) en deux parties issues du module :</p>
    <ol>
        <li><strong>Mon auto-évaluation sur les 7 compétences (C1 à C7) :</strong> indiquez où vous vous situez maintenant (<em>Pas encore · Avec aide · Seul · Je peux l\'expliquer</em>).</li>
        <li><strong>Votre avis sur le module :</strong> notez les 6 affirmations de <strong>1 (Pas du tout d\'accord)</strong> à <strong>4 (Tout à fait d\'accord)</strong> et partagez vos remarques.</li>
    </ol>
</div>';
$feedback->introformat = FORMAT_HTML;
$feedback->anonymous = 1;
$feedback->multiple_submit = 1;
$feedback->completionsubmit = 1;
$feedback->page_after_submit = '<p><strong>🎉 Merci pour vos réponses !</strong> Vous pouvez maintenant télécharger votre <strong>Attestation de réussite</strong> dans la section suivante.</p>';
$feedback->page_after_submitformat = FORMAT_HTML;
$feedback->timemodified = $now;
$DB->update_record('feedback', $feedback);

$DB->delete_records('feedback_item', ['feedback' => $feedback->id]);

$fb_items = [
    [
        'typ' => 'label',
        'name' => '',
        'label' => '',
        'presentation' => '<h4 style="color:#0d7a32;margin:0.5rem 0;">Partie 1 — Mon auto-évaluation sur les 7 compétences du Référentiel (Diapositive 95)</h4><p>Pour chaque compétence, cochez votre niveau d\'autonomie actuel :</p>',
        'hasvalue' => 0,
        'required' => 0,
    ],
    [
        'typ' => 'multichoice',
        'name' => 'C1 — Je sais identifier les appareils numériques (Android, Windows 11, Ubuntu 24.04)',
        'label' => 'C1',
        'presentation' => "r>>>>>Pas encore|Avec aide|Seul|Je peux l'expliquer",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'multichoice',
        'name' => 'C2 — Je sais démarrer, verrouiller et éteindre un appareil par le menu',
        'label' => 'C2',
        'presentation' => "r>>>>>Pas encore|Avec aide|Seul|Je peux l'expliquer",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'multichoice',
        'name' => 'C3 — Je sais naviguer dans l\'interface de base et utiliser la souris',
        'label' => 'C3',
        'presentation' => "r>>>>>Pas encore|Avec aide|Seul|Je peux l'expliquer",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'multichoice',
        'name' => 'C4 — Je sais localiser et ouvrir une application (Tiroir, Démarrer, touche Super)',
        'label' => 'C4',
        'presentation' => "r>>>>>Pas encore|Avec aide|Seul|Je peux l'expliquer",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'multichoice',
        'name' => 'C5 — Je sais saisir du texte (accents, majuscules, ponctuation) et me relire',
        'label' => 'C5',
        'presentation' => "r>>>>>Pas encore|Avec aide|Seul|Je peux l'expliquer",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'multichoice',
        'name' => 'C6 — Je sais connecter un appareil à internet (Wi-Fi, données mobiles) et vérifier le mode avion',
        'label' => 'C6',
        'presentation' => "r>>>>>Pas encore|Avec aide|Seul|Je peux l'expliquer",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'multichoice',
        'name' => 'C7 — Je sais entretenir et protéger mon appareil (chaleur, poussière, chargeur, onduleur)',
        'label' => 'C7',
        'presentation' => "r>>>>>Pas encore|Avec aide|Seul|Je peux l'expliquer",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'label',
        'name' => '',
        'label' => '',
        'presentation' => '<hr/><h4 style="color:#0d7a32;margin:0.5rem 0;">Partie 2 — Votre avis sur le module de 1 (Pas du tout d\'accord) à 4 (Tout à fait d\'accord) (Diapositive 96)</h4>',
        'hasvalue' => 0,
        'required' => 0,
    ],
    [
        'typ' => 'multichoicerated',
        'name' => '1. Les explications étaient claires.',
        'label' => 'Q1_clarte',
        'presentation' => "r>>>>>1####1 — Pas du tout d'accord|2####2 — Plutôt pas d'accord|3####3 — Plutôt d'accord|4####4 — Tout à fait d'accord",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'multichoicerated',
        'name' => '2. Ce que j\'ai appris me sera utile.',
        'label' => 'Q2_utilite',
        'presentation' => "r>>>>>1####1 — Pas du tout d'accord|2####2 — Plutôt pas d'accord|3####3 — Plutôt d'accord|4####4 — Tout à fait d'accord",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'multichoicerated',
        'name' => '3. J\'ai eu assez de temps pour pratiquer.',
        'label' => 'Q3_pratique',
        'presentation' => "r>>>>>1####1 — Pas du tout d'accord|2####2 — Plutôt pas d'accord|3####3 — Plutôt d'accord|4####4 — Tout à fait d'accord",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'multichoicerated',
        'name' => '4. Les diapositives et les fiches m\'ont aidé.',
        'label' => 'Q4_supports',
        'presentation' => "r>>>>>1####1 — Pas du tout d'accord|2####2 — Plutôt pas d'accord|3####3 — Plutôt d'accord|4####4 — Tout à fait d'accord",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'multichoicerated',
        'name' => '5. Le rythme convenait à mon niveau.',
        'label' => 'Q5_rythme',
        'presentation' => "r>>>>>1####1 — Pas du tout d'accord|2####2 — Plutôt pas d'accord|3####3 — Plutôt d'accord|4####4 — Tout à fait d'accord",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'multichoicerated',
        'name' => '6. La salle et le matériel étaient corrects.',
        'label' => 'Q6_materiel',
        'presentation' => "r>>>>>1####1 — Pas du tout d'accord|2####2 — Plutôt pas d'accord|3####3 — Plutôt d'accord|4####4 — Tout à fait d'accord",
        'hasvalue' => 1,
        'required' => 1,
    ],
    [
        'typ' => 'textarea',
        'name' => 'Ce que je vais utiliser dès demain :',
        'label' => 'Q7_demain',
        'presentation' => '60|3',
        'hasvalue' => 1,
        'required' => 0,
    ],
    [
        'typ' => 'textarea',
        'name' => 'Ce qu\'il faut améliorer :',
        'label' => 'Q8_ameliorer',
        'presentation' => '60|3',
        'hasvalue' => 1,
        'required' => 0,
    ],
];

$pos = 1;
foreach ($fb_items as $fbi) {
    $rec = new stdClass();
    $rec->feedback = $feedback->id;
    $rec->template = 0;
    $rec->name = $fbi['name'];
    $rec->label = $fbi['label'];
    $rec->presentation = $fbi['presentation'];
    $rec->typ = $fbi['typ'];
    $rec->hasvalue = $fbi['hasvalue'];
    $rec->position = $pos++;
    $rec->required = $fbi['required'];
    $rec->dependitem = 0;
    $rec->dependvalue = '';
    $rec->options = '';
    $DB->insert_record('feedback_item', $rec);
}
echo "  ✓ Enquête de satisfaction et d'auto-évaluation peuplée avec " . count($fb_items) . " éléments.\n";

// =============================================================================
// ÉTAPE 6 : SECTION 6 — 🔒 ESPACE FORMATEUR (SUPPORTS, GUIDE & CORRIGÉS)
// =============================================================================
echo "\n[Étape 6/7] Peuplement de la Section 6 (🔒 Espace Formateur, caché aux apprenants)...\n";

$sec6 = $DB->get_record('course_sections', ['course' => $COURSE_ID, 'section' => 6], '*', MUST_EXIST);
$sec6->name = '🔒 Espace Formateur';
$sec6->summary = '<p><strong>Espace strictement réservé aux formateurs PACTDIGITAL-BF / PNLN :</strong> retrouvez ici le <em>Guide pédagogique du formateur</em> (scénario minute par minute, préparation de la salle, variantes sans matériel), le <em>Dossier d\'évaluation complet</em> (QCM + 7 tâches pratiques sur poste avec grilles d\'observation et corrigés détaillés), le <em>Diaporama PowerPoint avec notes d\'animation</em> et la <em>Fiche de présence numérique</em>.</p>';
$sec6->summaryformat = FORMAT_HTML;
$sec6->visible = 0; // Caché aux apprenants
$DB->update_record('course_sections', $sec6);

// 1. Populate existing Folder "Supports et Corrigés" (folder id=25, cmid=218)
$folder_cm = $DB->get_record('course_modules', ['id' => 218, 'course' => $COURSE_ID], '*', MUST_EXIST);
$folder_ctx = context_module::instance($folder_cm->id);
$folder_rec = $DB->get_record('folder', ['id' => $folder_cm->instance], '*', MUST_EXIST);
$folder_rec->name = 'Supports et Corrigés — Pack Complet Formateur (Domaine 1 Bronze)';
$folder_rec->intro = '<p>Ce dossier contient l\'ensemble des fichiers sources officiels destinés au formateur :<ul>
    <li><strong>DOMAINE 1_MODULE BRONZE.pptx :</strong> Support de projection (115 diapositives avec toutes les notes d\'animation, minutages et corrigés).</li>
    <li><strong>Guide du formateur_D1_Module Bronze.docx :</strong> Guide complet d\'animation, déroulé minute par minute, checklists matériel, variantes sans équipement et grilles d\'observation.</li>
    <li><strong>Evaluation des apprenants_D1_Module Bronze.docx :</strong> Sujet papier des 21 QCM (Partie A), grilles d\'observation des 7 tâches pratiques sur poste (Partie B), fiche de résultats, barème et corrigé réservé au formateur.</li>
    <li><strong>Fiche_memo_Domaine1_Module_Bronze.pdf :</strong> Fiche de synthèse recto-verso et glossaire à imprimer pour les participants.</li>
</ul></p>';
$folder_rec->introformat = FORMAT_HTML;
$folder_rec->revision = ($folder_rec->revision ?? 1) + 1;
$folder_rec->timemodified = $now;
$DB->update_record('folder', $folder_rec);

pact_store_file_from_path(
    $folder_ctx->id,
    'mod_folder',
    'content',
    0,
    '/',
    'DOMAINE 1_MODULE BRONZE.pptx',
    $FILES_DIR . '/DOMAINE 1_MODULE BRONZE.pptx',
    1
);
pact_store_file_from_path(
    $folder_ctx->id,
    'mod_folder',
    'content',
    0,
    '/',
    'Guide du formateur_D1_Module Bronze.docx',
    $FILES_DIR . '/Guide du formateur_D1_Module Bronze.docx',
    2
);
pact_store_file_from_path(
    $folder_ctx->id,
    'mod_folder',
    'content',
    0,
    '/',
    'Evaluation des apprenants_D1_Module Bronze.docx',
    $FILES_DIR . '/Evaluation des apprenants_D1_Module Bronze.docx',
    3
);
pact_store_file_from_string(
    $folder_ctx->id,
    'mod_folder',
    'content',
    0,
    '/',
    'Fiche_memo_Domaine1_Module_Bronze.pdf',
    $pdf_bytes,
    4
);
echo "  ✓ Dossier 'Supports et Corrigés' (cmid=218) peuplé avec les 4 fichiers officiels.\n";

// 2. Dedicated Resource: Guide Pédagogique du Formateur (detected as '📘 Guide Pédagogique' by course_helper.php)
$res_guide = new stdClass();
$res_guide->name = '📘 Guide du formateur — Domaine 1 Module Bronze (Scénario & Animation)';
$res_guide->intro = '<p>Guide pédagogique officiel du formateur : préparation de la salle la veille, déroulé minute par minute des 8 séquences (6 h), réponses aux questions fréquentes, variantes avec peu ou pas d\'appareils et grille d\'observation des 7 pratiques.</p>';
$res_guide->introformat = FORMAT_HTML;
$res_guide->tobemigrated = 0;
$res_guide->legacyfiles = 0;
$res_guide->display = 0;
$res_guide->displayoptions = serialize(['showsize' => 1, 'showtype' => 1, 'showdate' => 1, 'printintro' => 1]);
$res_guide->filterfiles = 0;
$res_guide->revision = 1;

[$guide_cmid, $guide_instid, $guide_ctx] = pact_upsert_module(
    $COURSE_ID,
    6,
    'resource',
    'd1_b_res_guide_formateur',
    $res_guide,
    ['completion' => 0, 'visible' => 1, 'showdescription' => 1]
);

pact_store_file_from_path(
    $guide_ctx->id,
    'mod_resource',
    'content',
    0,
    '/',
    'Guide du formateur_D1_Module Bronze.docx',
    $FILES_DIR . '/Guide du formateur_D1_Module Bronze.docx',
    1
);
echo "  ✓ Ressource Formateur 'Guide du formateur' créée/mise à jour (cmid={$guide_cmid}).\n";

// 3. Dedicated Resource: Corrigé & Barème de l'Évaluation (detected as '🔑 Corrigé & Barème' by course_helper.php)
$res_corrige = new stdClass();
$res_corrige->name = '🔑 Corrigé & Barème — Évaluation des apprenants D1 Bronze (QCM + 7 tâches pratiques)';
$res_corrige->intro = '<p>Document officiel d\'évaluation comprenant le sujet papier des 21 QCM (Partie A), les 7 épreuves pratiques en circuit avec grilles de critères Acquis / Non acquis (Partie B), la fiche individuelle de résultats, le barème (seuil 70 %), le protocole de remédiation et le corrigé complet réservé au formateur.</p>';
$res_corrige->introformat = FORMAT_HTML;
$res_corrige->tobemigrated = 0;
$res_corrige->legacyfiles = 0;
$res_corrige->display = 0;
$res_corrige->displayoptions = serialize(['showsize' => 1, 'showtype' => 1, 'showdate' => 1, 'printintro' => 1]);
$res_corrige->filterfiles = 0;
$res_corrige->revision = 1;

[$corr_cmid, $corr_instid, $corr_ctx] = pact_upsert_module(
    $COURSE_ID,
    6,
    'resource',
    'd1_b_res_corrige_eval',
    $res_corrige,
    ['completion' => 0, 'visible' => 1, 'showdescription' => 1]
);

pact_store_file_from_path(
    $corr_ctx->id,
    'mod_resource',
    'content',
    0,
    '/',
    'Evaluation des apprenants_D1_Module Bronze.docx',
    $FILES_DIR . '/Evaluation des apprenants_D1_Module Bronze.docx',
    1
);
echo "  ✓ Ressource Formateur 'Corrigé & Barème' créée/mise à jour (cmid={$corr_cmid}).\n";

// Ensure clean ordering of Section 6 sequence (218, guide, corrige, 403)
$sec6_cmids = array_unique([218, $guide_cmid, $corr_cmid, 403]);
$sec6->sequence = implode(',', $sec6_cmids);
$DB->update_record('course_sections', $sec6);

// =============================================================================
// ÉTAPE 7 : RECONSTRUCTION DU CACHE DU COURS & PURGE DES CACHES MOODLE
// =============================================================================
echo "\n[Étape 7/7] Reconstruction du cache du cours #48 et purge des caches...\n";
rebuild_course_cache($COURSE_ID, true);
purge_all_caches();

echo "\n=================================================================\n";
echo "  ✅ INTÉGRATION DU MODULE BRONZE (DOMAINE 1) TERMINÉE AVEC SUCCÈS\n";
echo "=================================================================\n";

