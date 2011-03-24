<?php
require_once 'HTML/QuickForm/radio.php'; 

class block_ucla_help_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Use HTML editor
        $mform->addElement('editor', 'config_helpbox_text', get_string('config_helpbox', 'block_ucla_help'));
        $mform->setType('config_helpbox_text', PARAM_RAW);
                
        // adding radio button options as suggested in following post:
        // "QuickForm and Radio buttons"
        // http://www.geeknewz.com/blog/jamie/index.php?showentry=426
        $send_to = array(
                    new HTML_QuickForm_radio('config_send_to', null, 
                            get_string('config_send_to_email_option', 'block_ucla_help'), 
                            'email', array('class' => 'ucla_help_send_to_radio')),
                    new HTML_QuickForm_radio('config_send_to', null, 
                            get_string('config_send_to_jira_option', 'block_ucla_help'), 
                            'jira', array('class' => 'ucla_help_send_to_radio'))
                );        
        $mform->addGroup($send_to, 'config_send_to', get_string('config_send_to', 'block_ucla_help'), null, false);
        
        // make sure that at least one send_to option is selected
        $mform->addRule('config_send_to', get_string('error_empty_send_to', 
                'block_ucla_help'), 'required', '', 'server');          
        
        // add options for email
        $mform->addElement('text', 'config_email', get_string('config_email', 'block_ucla_help'));
        
        // add options for jira
        $mform->addElement('text', 'config_jira_endpoint', get_string('config_jira_endpoint', 'block_ucla_help'));
        $mform->addElement('text', 'config_jira_user', get_string('config_jira_user', 'block_ucla_help'));
        $mform->addElement('text', 'config_jira_password', get_string('config_jira_password', 'block_ucla_help'));
        $mform->addElement('text', 'config_jira_pid', get_string('config_jira_pid', 'block_ucla_help'));
        $mform->addElement('text', 'config_jira_default_assignee', get_string('config_jira_default_assignee', 'block_ucla_help'));
    }

}
