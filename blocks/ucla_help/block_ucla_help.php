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
        $this->content->text = $this->config->text;
        $this->content->footer = '';

        return $this->content;
    }

    function instance_allow_config()
    {
        return true;
    }

    function specialization()
    {
        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        } else {
            $this->config->title = 'Some title...';
        }
        if (empty($this->config->text)) {
            $this->config->text = 'Some text...';
        }
    }

//function hide_header() {
//  return true;
//}
//    function  is_empty()
//    {
//        return false;
//    }
}

// Here's the closing curly bracket for the class definition
// and here's the closing PHP tag from the section above.
?>