<?php // $Id: incl_pupil.php,v 1.1.1.1 2009/10/06 09:33:13 Shtifanov Exp $
/// This file to be included so we can assume config.php has already been included.

    require_once("../../../config.php");
    require_once("$CFG->libdir/gdlib.php");
    require_once('../../monitoring/lib.php');
    require_once('../../mou_ege/lib_ege.php');

    $mode = required_param('mode', PARAM_INT);        // Mode: 0, 1, 2, 3, 4, 9, 99 Can(or can't) show groups
    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $sid = required_param('sid', PARAM_INT);       // School id
    $yid = required_param('yid', PARAM_INT);       // Year id
    $gid = required_param('gid', PARAM_INT);          // Group id
    $uid = optional_param('uid', 0, PARAM_INT);       // User id

	if ($yid == 0)	{
    	$yid = get_current_edu_year_id();;
    }

	require_login();

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
	}

	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && $school_operator_is)  {
	      if ($school = get_record_sql ("SELECT id, uniqueconstcode FROM {$CFG->prefix}monit_school
	                                     WHERE rayonid=$rid AND uniqueconstcode=$sid AND yearid=$yid"))	{
	     		$sid = $school->id;
	      }
	}


    $strtitle = get_string('pupil','block_mou_school');
    $strclasses = get_string('classes','block_mou_school');
	$strclass = get_string('class','block_mou_school');
	$strpupils = get_string('pupils', 'block_mou_school');

	$breadcrumbs = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/index.php?rid=$rid&amp;yid=$yid&amp;sid=$sid\">".get_string('title_mi','block_mou_ege').'</a>';
	$breadcrumbs .= "-> <a href=\"{$CFG->wwwroot}/blocks/mou_mi/class/classlist.php?rid=$rid&amp;sid=$sid&amp;yid=$yid\">$strclasses</a>";
	$breadcrumbs .= "-> <a href=\"{$CFG->wwwroot}/blocks/mou_mi/class/classpupils.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid\">$strpupils</a>";
	$breadcrumbs .= "-> $strtitle";
    print_header("$SITE->shortname: $strtitle", $SITE->fullname, $breadcrumbs);


    if ($admin_is  || $region_operator_is) {  // || $rayon_operator_is)  {

		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("pupil.php?mode=1&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;rid=", $rid);
		listbox_schools("pupil.php?mode=2&amp;rid=$rid&amp;yid=$yid&amp;gid=$gid&amp;sid=", $rid, $sid, $yid);
		echo '</table>';

	} else  if ($rayon_operator_is)  {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_schools("pupil.php?mode=2&amp;rid=$rid&amp;yid=$yid&amp;gid=$gid&amp;sid=", $rid, $sid, $yid);
		echo '</table>';
	} 
	
	if ($rid != 0 && $sid != 0 && $yid != 0)  {
		print_tabs_years_link("pupil.php?mode=$mode&amp;gid=$gid", $rid, $sid, $yid);
		
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	    listbox_class("pupil.php?mode=3&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=", $rid, $sid, $yid, $gid);
	    listbox_pupils("pupil.php?mode=4&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid=", $rid, $sid, $yid, $gid, $uid);
		echo '</table>';
	}	

	if ($mode != 4 || $gid == 0 || $uid == 0 )  {
	    print_footer();
		exit;
	}


	 $profile->fields = array('pol', 'birthday');
	 $profile->type 	 = array('bool', 'date');
	 $profile->numericfield = array();

	$rayon = get_record('monit_rayon', 'id', $rid);

	$school = get_record('monit_school', 'id', $sid);

	$class = get_record('monit_school_class', 'id', $gid);

    $pupil = get_record('monit_school_pupil_card', 'userid', $uid, 'yearid', $yid);


    if (!$user1 = get_record('user', 'id', $uid) ) {
        error('No such pupil in this class!', '..\index.php');
	}

   	$fullname = fullname($user1);

?>

