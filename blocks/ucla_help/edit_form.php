<?php

class block_ucla_help_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // A sample string variable with a default value.
        $mform->addElement('text', 'config_title', get_string('config_title', 'block_help'));
        $mform->setType('config_text', PARAM_MULTILANG);


        // A sample string variable with a default value.
        $mform->addElement('text', 'config_text', get_string('config_text', 'block_help'));
        $mform->setDefault('config_text', 'default value');
        $mform->setType('config_text', PARAM_MULTILANG);
    }

}