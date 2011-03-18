<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

class help_form extends moodleform {
 
    function definition() {
        global $CFG;
 
        $mform =& $this->_form;
 
        // css should be used to define widths of input/textarea fields
        $mform->addElement('text', 'ucla_help_name', 
                get_string('name_field', 'block_ucla_help'));
        $mform->addElement('text', 'ucla_help_email', 
                get_string('email_field', 'block_ucla_help'));            
        $mform->addElement('textarea', 'ucla_help_description', 
                get_string("description_field", "block_ucla_help"), 
                'wrap="virtual" rows="6"');        
        
        // no point in having a cancel option
        $this->add_action_buttons(false, get_string('submit_button', 'block_ucla_help'));
        
        // make description field a required field with client and 
        // server-side validation
        $mform->addRule('ucla_help_description', get_string('empty_description', 
                'block_ucla_help'), 'required', '', 'server');        
        $mform->addRule('ucla_help_description', get_string('empty_description', 
                'block_ucla_help'), 'required', '', 'client');
    }                           
}          
?>
