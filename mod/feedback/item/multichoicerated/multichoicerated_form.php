<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_multichoicerated_form extends feedback_item_form {
    var $type = "multichoicerated";

    function definition() {
        $item = $this->_customdata['item'];
        $common = $this->_customdata['common'];
        $positionlist = $this->_customdata['positionlist'];
        $position = $this->_customdata['position'];

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));

        $mform->addElement('checkbox', 'required', get_string('required', 'feedback'));

        $mform->addElement('text', 'name', get_string('item_name', 'feedback'), array('size="'.FEEDBACK_ITEM_NAME_TEXTBOX_SIZE.'"','maxlength="255"'));
        $mform->addElement('text', 'label', get_string('item_label', 'feedback'), array('size="'.FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE.'"','maxlength="255"'));

        $mform->addElement('select',
                            'horizontal',
                            get_string('adjustment', 'feedback').'&nbsp;',
                            array(0 => get_string('vertical', 'feedback'), 1 => get_string('horizontal', 'feedback')));

        $mform->addElement('select',
                            'subtype',
                            get_string('multichoicetype', 'feedback').'&nbsp;',
                            array('r'=>get_string('radio', 'feedback'),
                                  'd'=>get_string('dropdown', 'feedback')));

        $mform->addElement('selectyesno', 'ignoreempty', get_string('do_not_analyse_empty_submits', 'feedback'));
        $mform->addElement('selectyesno', 'hidenoselect', get_string('hide_no_select_option', 'feedback'));
        
        $mform->addElement('static', 'hint', get_string('multichoice_values', 'feedback'), get_string('use_one_line_for_each_value', 'feedback'));

        $this->values = $mform->addElement('textarea', 'values', '', 'wrap="virtual" rows="10" cols="65"');

        
        ////////////////////////////////////////////////////////////////////////
        //the following is used in all itemforms
        ////////////////////////////////////////////////////////////////////////
        //itemdepending
        if($common['items']) {
            $mform->addElement('select',
                                'dependitem',
                                get_string('dependitem', 'feedback').'&nbsp;',
                                $common['items']
                                );
            $mform->addHelpButton('dependitem', 'depending', 'feedback');
            $mform->addElement('text', 'dependvalue', get_string('dependvalue', 'feedback'), array('size="'.FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE.'"','maxlength="255"'));
        }else {
            $mform->addElement('hidden', 'dependitem', 0);
            $mform->setType('dependitem', PARAM_INT);
            $mform->addElement('hidden', 'dependvalue', '');
            $mform->setType('dependitem', PARAM_ALPHA);
        }

        $position_select = $mform->addElement('select',
                                            'position',
                                            get_string('position', 'feedback').'&nbsp;',
                                            $positionlist);
        $position_select->setValue($position);
        

        $mform->addElement('hidden', 'cmid', $common['cmid']);
        $mform->setType('cmid', PARAM_INT);
        
        $mform->addElement('hidden', 'id', $common['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'feedback', $common['feedback']);
        $mform->setType('feedback', PARAM_INT);
        
        $mform->addElement('hidden', 'template', 0);
        $mform->setType('template', PARAM_INT);
        
        $mform->setType('name', PARAM_RAW);
        $mform->setType('label', PARAM_ALPHANUM);
        
        $mform->addElement('hidden', 'typ', $this->type);
        $mform->setType('typ', PARAM_ALPHA);

        $mform->addElement('hidden', 'hasvalue', 0);
        $mform->setType('hasvalue', PARAM_INT);

        $mform->addElement('hidden', 'options', '');
        $mform->setType('options', PARAM_ALPHA);

        $buttonarray = array();
        if(!empty($item->id)){
            $buttonarray[] = &$mform->createElement('submit', 'update_item', get_string('update_item', 'feedback'));
        }else{
            $buttonarray[] = &$mform->createElement('submit', 'save_item', get_string('save_item', 'feedback'));
        }
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '&nbsp;', array(' '), false);
        
        $this->set_data($item);

    }

    function set_data($item) {
        $info = $this->_customdata['info'];

        $item->horizontal = $info->horizontal;

        $item->subtype = $info->subtype;

        $item->values = $info->values;

        return parent::set_data($item); 
    }
    
    function get_data() {
        if(!$item = parent::get_data()) {
            return false;
        }
        
        $itemobj = new feedback_item_multichoicerated();
        
        $presentation = $itemobj->prepare_presentation_values_save(trim($item->values), FEEDBACK_MULTICHOICERATED_VALUE_SEP2, FEEDBACK_MULTICHOICERATED_VALUE_SEP);
        if(!isset($item->subtype)) {
            $subtype = 'r';
        }else {
            $subtype = substr($item->subtype, 0, 1);
        }
        if(isset($item->horizontal) AND $item->horizontal == 1 AND $subtype != 'd') {
            $presentation .= FEEDBACK_MULTICHOICERATED_ADJUST_SEP.'1';
        }
        $item->presentation = $subtype.FEEDBACK_MULTICHOICERATED_TYPE_SEP.$presentation;
        return $item;
    }
}
