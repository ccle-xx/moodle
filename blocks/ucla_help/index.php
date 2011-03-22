<?php
/**
 * Script to let users view help information or send feedback. If being called
 * to serve as a modal window, will just output form field & help links.
 * 
 * Else, can be called displayed in a site or course context.
 * 
 * @package ucla
 * @copyright 2011 UC Regents
 * @author Rex Lorenzo <rex@seas.ucla.edu>
 * 
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/blocklib.php');

// form to process help request
require_once(dirname(__FILE__).'/help_form.php' );

// determine context of page request
// can be modal/course/site
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$is_modal = optional_param('modal', 0, PARAM_BOOL);

// setup page context
if (!$is_modal) {
    if ($courseid == SITEID) {
        $courseid = 0;
    }
    if ($courseid) {
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        $PAGE->set_course($course);
        $context = $PAGE->context;
        $PAGE->set_pagelayout('course');
    } else {
        $context = get_context_instance(CONTEXT_SYSTEM);
        $PAGE->set_context($context);
    }
} else {
    // if showing up as a modal window, then don't show real headers/footers
    $PAGE->set_pagelayout('popup');
}

// set page title/url
$struclahelp = get_string('pluginname', 'block_ucla_help');
$PAGE->set_title($struclahelp);
$PAGE->set_heading($struclahelp);
$url = new moodle_url('/blocks/ucla_help/index.php');
$PAGE->set_url($url);

// using core renderer
echo $OUTPUT->header();

echo '<h3>' . get_string('helpbox_header', 'block_ucla_help') . '</h3>';

// To get configuration settings, need to create block instance
// for reference, please read (code example was updated for Moodle 2.0): 
// "How do I access a blocks config data from outside the block?"
// http://moodle.org/mod/forum/discuss.php?d=129799

//$instance = $DB->get_record('block_instances', array('id' => $blockinfo->id), '*', MUST_EXIST); 
$instance = $DB->get_record('block_instances', array('blockname' => 'ucla_help'), '*', MUST_EXIST); 
$block_ucla_help = block_instance('ucla_help', $instance);

// now show specific text for helpbox (should be set in configuration page
echo '<div>';
if (empty($block_ucla_help->config->helpbox_text)) {
    // no text set, so use default text
    echo get_string('helpbox_text_default', 'block_ucla_help');
} else {
    echo $block_ucla_help->config->helpbox_text['text'];
}
echo '</div>';

echo '<h3>' . get_string('helpform_header', 'block_ucla_help') . '</h3>';
echo '<div>' . get_string('helpform_text', 'block_ucla_help') . '</div>';

$mform = new help_form();
//default 'action' for form is strip_querystring(qualified_me())
if ($fromform = $mform->get_data()) {
    //this branch is where you process validated data.
} else {
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
    //setup strings for heading
//    print_header_simple($streditinga, '', "<a href=\"$CFG->wwwroot/mod/$module->name/index.php?id=$course->id\">$strmodulenameplural</a> ->
//     $strnav $streditinga", $mform->focus(), "", false);
    //notice use of $mform->focus() above which puts the cursor 
    //in the first form field or the first field with an error.
    //call to print_heading_with_help or print_heading? then :
    //put data you want to fill out in the form into array $toform here then :

    //$mform->set_data($toform);
    $mform->display();
}

echo '</div>';


echo $OUTPUT->footer();
?>
