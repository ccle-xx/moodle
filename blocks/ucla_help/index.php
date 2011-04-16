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
require_once($CFG->libdir . '/jira.php');

// form to process help request
require_once(dirname(__FILE__) . '/help_form.php' );

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
    echo format_text($block_ucla_help->config->helpbox_text['text'], FORMAT_HTML);
}
echo '</div>';

echo '<h3>' . get_string('helpform_header', 'block_ucla_help') . '</h3>';
echo '<div>' . get_string('helpform_text', 'block_ucla_help') . '</div>';

$mform = new help_form();
//default 'action' for form is strip_querystring(qualified_me())
if ($fromform = $mform->get_data()) {

    echo $OUTPUT->box_start('generalbox', 'notice');
    
    // get email body
    $body = create_help_message($fromform);    
    
    // check if need to send message via email
    if ('email' == $block_ucla_help->config->send_to) {
        
        $mail = get_mailer();
        
        if(!empty($fromform->ucla_help_email)) {
            $mail->From = $fromform->ucla_help_email;
        } else if (!empty($USER->email)) {
            $mail->From = $USER->email;
        } else {
            $mail->From = $CFG->noreplyaddress;
        }             
        
        $mail->AddAddress($block_ucla_help->config->email);        
        $mail->Subject = 'Moodle feedback from ' . $mail->From;
        $mail->Body = $body;
        
        // just going to use php's built-in email functionality. Moodle provides
        // a function called "email_to_user", but it requires a user in the 
        // database to exist
        if ($mail->Send()) {
            echo $OUTPUT->notification(get_string('success_sending_email', 'block_ucla_help'), 'notifysuccess');
        } else {
            echo $OUTPUT->error_text(get_string('error_sending_email', 'block_ucla_help'));
        }
        
    } elseif ('jira' == $block_ucla_help->config->send_to) {
    
    } else {
        
    }    
    
    if ($COURSE->id == 1) {
        $url = $CFG->wwwroot;
    } else {
        $url = $CFG->wwwroot . '/course/view.php?id=' . $COURSE->id;
    }
    echo sprintf('<a href="%s">%s</a>', $url, get_string('continue'));
    echo $OUTPUT->box_end();    
    
} else {
    $mform->display();    
}

echo '</div>';
echo $OUTPUT->footer();

/**
 * Constructs body of email that will be sent when user submits help form.
 * 
 * @param mixed $fromform   Form data submitted by user. Passed by reference. 
 * 
 * @return string           Returns 
 */
function create_help_message(&$fromform)
{
    global $COURSE, $CFG, $DB, $SESSION, $USER;
    
    // If user is not logged in, then a majority of these values will raise PHP 
    // notices, so supress them with @    
    
    // setup description array
    $description['maildisplay'][0] = '0 - '.get_string('emaildisplayno');
    $description['maildisplay'][1] = '1 - '.get_string('emaildisplayyes');
    $description['maildisplay'][2] = '2 - '.get_string('emaildisplaycourse');
    $description['autosubscribe'][0] = '0 - '.get_string('autosubscribeno');
    $description['autosubscribe'][1] = '1 - '.get_string('autosubscribeyes');
    $description['emailstop'][0] = '0 - '.get_string('emailenable');
    $description['emailstop'][1] = '1 - '.get_string('emaildisable');
    $description['htmleditor'][0] = '0 - '.get_string('texteditor');
    $description['htmleditor'][1] = '1 - '.get_string('htmleditor');
    $description['trackforums'][0] = '0 - '.get_string('trackforumsno');
    $description['trackforums'][1] = '1 - '.get_string('trackforumsyes');
    $description['screenreader'][0] = '0 - '.get_string('screenreaderno');
    $description['screenreader'][1] = '1 - '.get_string('screenreaderyes');
    $description['ajax'][0] = '0 - '.get_string('ajaxno');
    $description['ajax'][1] = '1 - '.get_string('ajaxyes');
    
    if (isset($USER->currentcourseaccess[$COURSE->id])) {
        $accesstime = date('r' , $USER->currentcourseaccess[$COURSE->id]);
    } else {
        @$accesstime = date('r' , $USER->lastaccess);
    }

    // Needs stripslashes after obtaining information that has been escaped for security reasons    
    $body = stripslashes($fromform->ucla_help_name) . " wrote: \n\n" . 
            stripslashes($fromform->ucla_help_description) . "\n
    Name: " . stripslashes($fromform->ucla_help_name) . "
    UCLA ID: " . @$USER->idnumber . "
    Email: " . stripslashes($fromform->ucla_help_email) . "
    Server: $_SERVER[SERVER_NAME]
    User_Agent: $_SERVER[HTTP_USER_AGENT]
    Host: $_SERVER[REMOTE_ADDR]
    Referer: $_SERVER[HTTP_REFERER]
    Course Shortname: $COURSE->shortname
    Access Time: $accesstime
    User Profile: $CFG->wwwroot/user/view.php?id=$USER->id
    SESSION_fromdiscussion   = " . @$SESSION->fromdiscussion . "
    USER_id                  = $USER->id
    USER_auth                = " . @$USER->auth . "
    USER_username            = " . @$USER->username . "
    USER_institution         = " . @$USER->institution . "
    USER_firstname           = " . @$USER->firstname . "
    USER_lastname            = " . @$USER->lastname . "
    USER_email               = " . @$USER->email . "
    USER_emailstop           = " . @$description['emailstop'][$USER->emailstop] . "
    USER_lastaccess          = " . @date('r' , $USER->lastaccess) . "
    USER_lastlogin           = " . @date('r' , $USER->lastlogin) . "
    USER_lastip              = " . @$USER->lastip . "
    USER_maildisplay         = " . @$description['maildisplay'][$USER->maildisplay] . "
    USER_htmleditor          = " . @$description['htmleditor'][$USER->htmleditor] . "
    USER_ajax (AJAX and Javascript) = " . @$description['ajax'][$USER->ajax] . "
    USER_autosubscribe       = " . @$description['autosubscribe'][$USER->autosubscribe] . "
    USER_trackforums         = " . @$description['trackforums'][$USER->trackforums] . "
    USER_timemodified        = " . @date('r' , $USER->timemodified) . "
    USER_screenreader        = " . @$description['screenreader'][$USER->screenreader];
    $body .= "\n";
    
    // get logging records
    $log_records = $DB->get_records('log', array('userid' => $USER->id), 'time DESC', '*', 0, 10);        
    if (empty($log_records)) {
        $body .= "No log entries\n";
    } else {
        $body .= print_ascii_table($log_records);
    }
        
    $body .= 'This message was generated by ' . __FILE__;    
    
    return $body;
}

/**
 * Copied from CCLE 1.9 feedback code.
 * @param type $stuff
 * @return string 
 */
function print_ascii_table($stuff)
{
    $formatted_table = array();
    $formatted_string = "";

    // Parse through once to get proper formatting length
    $line_count = 0;
    foreach ($stuff as $line) {
        $line_count++;
        foreach (get_object_vars($line) as $key => $data) {
            unset($test_string);

            // Make the testing string
            $test_string = ' ';
            if ($key == 'time') {
                $test_string .= date('r', $data);
            } else {
                $test_string .= $data;
            }
            $test_string .= ' ';

            // Get length
            $string_length = strlen($test_string);

            // Get max length
            if (!isset($formatted_table[$key])) {
                $formatted_table[$key] = $string_length;
            } else if ($formatted_table[$key] < $string_length) {
                $formatted_table[$key] = $string_length;
            }

            if ($formatted_table[$key] < strlen(" " . $key . " ")) {
                $formatted_table[$key] = strlen(" " . $key . " ");
            }
        }
    }

    $formatted_table['KINDEX'] = 0;
    while ($line_count >= 1) {
        $line_count = $line_count / 10;
        $formatted_table['KINDEX']++;
    }

    $line_count = 0;
    $formatted_string .= "\n";

    // Print field names
    $formatted_line = "| ";
    while (strlen($formatted_line) - 2 < $formatted_table['KINDEX']) {
        $formatted_line .= "-";
    }
    $formatted_line .= " |";
    $formatted_set = strlen($formatted_line);

    $sampleline = $stuff[array_rand($stuff)];
    foreach (get_object_vars($sampleline) as $key => $data) {
        $formatted_line .= " ";
        $formatted_line .= $key;

        while (strlen($formatted_line) - $formatted_set < $formatted_table[$key]) {
            $formatted_line .= " ";
        }
        $formatted_line .= "|";
        $formatted_set = strlen($formatted_line);
    }
    $formatted_string .= $formatted_line . "\n";

    for ($i = 0; $i < $formatted_set; $i++) {
        $formatted_string .= "-";
    }
    $formatted_string .= "\n";

    foreach ($stuff as $line) {
        $line_count++;
        $formatted_line = "| " . $line_count;
        while (strlen($formatted_line) - 3 < $formatted_table['KINDEX']) {
            $formatted_line .= " ";
        }
        $formatted_line .= "|";
        $formatted_set = strlen($formatted_line);

        foreach (get_object_vars($line) as $key => $data) {
            $formatted_line .= " ";
            if ($key == 'time') {
                $formatted_line .= date('r', $data);
            } else {
                $formatted_line .= $data;
            }

            while (strlen($formatted_line) - $formatted_set < $formatted_table[$key]) {
                $formatted_line .= " ";
            }
            $formatted_line .= "|";
            $formatted_set = strlen($formatted_line);
        }
        $formatted_string .= $formatted_line . "\n";
    }
    return $formatted_string;
}
?>
