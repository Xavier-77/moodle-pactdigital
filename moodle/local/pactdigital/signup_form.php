<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class local_pactdigital_signup_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'categoryheader', get_string('signup', 'local_pactdigital'));

        // Username
        $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="12"');
        $mform->setType('username', PARAM_ALPHANUMEXT);
        $mform->addRule('username', get_string('missingusername'), 'required', null, 'server');

        // Password
        $mform->addElement('passwordunmask', 'password', get_string('password'), 'maxlength="32" size="12"');
        $mform->setType('password', PARAM_RAW);
        $mform->addRule('password', get_string('missingpassword'), 'required', null, 'server');

        // Firstname
        $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
        $mform->setType('firstname', PARAM_NOTAGS);
        $mform->addRule('firstname', get_string('missingfirstname'), 'required', null, 'server');

        // Lastname
        $mform->addElement('text', 'lastname', get_string('lastname'), 'maxlength="100" size="30"');
        $mform->setType('lastname', PARAM_NOTAGS);
        $mform->addRule('lastname', get_string('missinglastname'), 'required', null, 'server');

        // Email (Optional)
        $mform->addElement('text', 'email', get_string('emailoptional', 'local_pactdigital'), 'maxlength="100" size="30"');
        $mform->setType('email', PARAM_RAW);
        $mform->addHelpButton('email', 'emailoptional', 'local_pactdigital');

        // Optional fields (City, Country) that were missing in default signup
        $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="20"');
        $mform->setType('city', PARAM_TEXT);

        $mform->addElement('select', 'country', get_string('country'), get_string_manager()->get_list_of_countries());
        $mform->setDefault('country', 'BF'); // Burkina Faso par défaut

        // Champs de profil personnalisés (Sexe, Région, Statut, etc.)
        require_once($CFG->dirroot.'/user/profile/lib.php');
        profile_signup_fields($mform);

        // Submit button
        $this->add_action_buttons(false, get_string('createaccount'));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check if username exists
        if (core_user::get_user_by_username($data['username'])) {
            $errors['username'] = get_string('usernameexists');
        }

        // Check if email is provided and if it is valid
        if (!empty($data['email'])) {
            if (!validate_email($data['email'])) {
                $errors['email'] = get_string('invalidemail');
            } else {
                // Check if email already exists (unless duplicates allowed)
                global $DB, $CFG;
                if (empty($CFG->allowaccountssameemail)) {
                    if ($DB->record_exists('user', array('email' => $data['email'], 'deleted' => 0))) {
                        $errors['email'] = get_string('emailexists');
                    }
                }
            }
        }

        return $errors;
    }
}
