<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

  $i = 1;
  $link = get_config('ucla_links', "link$i");
  
  // This probably doesn't have to be a do-while loop, but I did it for the sake of running it at least once for testing purposes.
  do {
    $link = get_config('ucla_links', "link$i");
    if($link == '') {
      break;
    }
    else {
    $settings->add(new admin_setting_configtext("ucla_links/link$i", get_string('ucla_link', 'block_ucla_links'), get_string('ucla_links_exist', 'block_ucla_links')));
    $settings->add(new admin_setting_configtext("ucla_links/link".$i."_name", get_string('ucla_link_name', 'block_ucla_links'), get_string('ucla_links_help', 'block_ucla_links')));     
    $i++;
    }    
  } while(TRUE);
  $settings->add(new admin_setting_configtext("ucla_links/link$i", get_string('ucla_link', 'block_ucla_links'), get_string('ucla_links_add', 'block_ucla_links'))); 
  $settings->add(new admin_setting_configtext("ucla_links/link".$i."_name", get_string('ucla_link_name', 'block_ucla_links'), get_string('ucla_links_help', 'block_ucla_links')));     
}

