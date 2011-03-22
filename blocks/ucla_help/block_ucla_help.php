<?php

class block_ucla_help extends block_base {

    function init()
    {
        $this->title = get_string('pluginname', 'block_ucla_help');
    }

    // The PHP tag and the curly bracket for the class definition
    // will only be closed after there is another function added in the next section.

    function get_content()
    {
        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;        
        $this->content->text = get_string('block_text', 'block_ucla_help');

        return $this->content;
    }

    function instance_allow_config()
    {
        return true;
    }
    
}
?>