<?php

//This file is required to map roles coming in from the registrar view and stored procedures to the Moodle specific roles
//A role mapping file (role_mapping.php in /enrol/database/) overrides any existing entries in the database table ucla_rolemapping

require_once(dirname(__FILE__) . "/../config.php");

function role_mapping ($profcode, array $other_roles, $subject_area="*SYSTEM*"){

	$pseudorole = get_pseudorole($profcode, $other_roles); //logic to parse profcodes, and return pseudorole
	$moodleroleid = get_moodlerole($pseudorole, $subject_area); //call to the ucla_rolemapping table
	return $moodleroleid;
}

//this mapping definition will be used only for instructors
/* Refer to Jira: CCLE-2320

role InstSet Pseudo Role
01  any 	 instructor
02	01,02	 ta
02	01,02,03 ta
02	02,03	 ta_instructor
03	any	     supervising_instructor
22	any	     student_instructor
*/

function get_pseudorole($profcode, array $other_roles){

	for ($i=0;$i<count($other_roles);$i++)
	{
		$hasrole[$other_roles[$i]]='true';
	}

    switch ($profcode){
	    case 1:
			return "instructor";
		case 2:
			if($hasrole[1] == 'true' && $hasrole[2] == 'true') {
			  return "ta";
			}elseif($hasrole[1] != 'true' && $hasrole[2] == 'true' && $hasrole[3] == 'true' ){
			  return "ta_instructor";
			}
		case 3:
			return "supervising_instructor";
		case 22:
			return "student_instructor";
	}
}

function get_moodlerole($pseudorole, $subject_area) //call to the ucla_rolemapping table
{
	global $CFG,$DB;
		
	$rolemappingfile = $CFG->dirroot."/enrol/database/role_mappings.php";
	$moodleroleobject = $DB->get_record('ucla_rolemapping',array('pseudo_role'=>$pseudorole, 'subject_area'=>$subject_area));
	$moodle_roleid = $moodleroleobject->moodle_roleid;
	
	if (file_exists($rolemappingfile))
	{
		require_once($rolemappingfile);
		if($moodlerole = $DB->get_record('role', array('shortname'=>$role[$pseudorole][$subject_area]))){
			$moodle_roleid = $moodlerole->id;
		}
	}
	return $moodle_roleid;

}

?>

