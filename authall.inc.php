<?php // $Id: authall.inc.php,v 1.2 2009/12/21 14:13:58 Shtifanov Exp $

	require_login();

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	// echo $school_operator_is;
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && $school_operator_is)  {
	      if ($school = get_record_sql ("SELECT id, uniqueconstcode, name FROM {$CFG->prefix}monit_school
	                                     WHERE rayonid=$rid AND uniqueconstcode=$sid AND yearid=$yid"))	{
	     		$sid = $school->id;
	      }
	}

	if (!$admin_is && !$region_operator_is && $rayon_operator_is && $rayon_operator_is != $rid)  {
			add_to_log(1, 'authall.inc.php', 'mou_mi', 'selectownrayon', fullname($USER), '', $USER->id);
			error(get_string('selectownrayon', 'block_monitoring'));
			exit();
	}


?>