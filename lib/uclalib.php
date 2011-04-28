<?php

//This file is required to map roles coming in from the registrar view and stored procedures to the Moodle specific roles
//roles are mapped to moodle roles after applying basic rules (specific to a subj area) to the incoming prof codes for the instructors
//A role mapping file overrides any existing entries in the database to apply more specific role mapping.

function role_mapping ($profcode, array $other_roles, $subject_area){

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
   // echo " 0 ";echo $hasrole[0];echo " 1 ";echo $hasrole[1];echo " 2 ";echo $hasrole[2];echo " 3 ";echo $hasrole[3];echo "  ";
	
    switch ($profcode){
	    case 1:
			return "instructor";
		case 2:
			if($hasrole[2] == 'true') {
			  return "ta";
			}else{
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
	$moodle_roleid = $moodleroleobject->$moodlerole;
	
	if (file_exists($rolemappingfile))
	{
		include($rolemappingfile);
		if($moodleroleid = $DB->get_record('role', array('shortname'=>$role[$pseudorole][$subject_area]))){
			$moodle_roleid = $moodleroleid->id;
		}
	}
	return $moodle_roleid;
}


?>