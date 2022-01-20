<?PHP // $Id: pupilnomarks.php,v 1.3 2009/06/11 09:40:37 Shtifanov Exp $

    require_once('../../../config.php');
    require_once('../../monitoring/lib.php');
    require_once('../../mou_ege/lib_ege.php');

    $yid = optional_param('yid', 0, PARAM_INT);       // Year id
    $did = optional_param('did', 0, PARAM_INT);       // Discipline id
	$rid = 0;
	$sid = 0;
	$gid = 0;

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }


	$action   = optional_param('action', '');
    if ($action == 'excel') {
		$table = table_pupilnomarks($yid, $did);
    	// print_r($table);
        print_table_to_excel($table, 1);
        exit();
	}

	if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	require_login();

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
	}

	if (isregionviewoperator() || israyonviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}


	// $strclasses = get_string('classes','block_mou_ege');
	$strclasses = get_string('school','block_monitoring');
	$strmarks = get_string('pupilnomarks','block_mou_ege');

	$breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/mou_mi/index.php">'.get_string('title_mi','block_mou_ege').'</a>';
	$breadcrumbs .= " -> $strmarks";
    print_header_mou("$site->shortname: $strmarks", $site->fullname, $breadcrumbs);


	print_tabs_years($yid, "pupilnomarks.php?yid=");

    $currenttab = 'pupilnomarks';
    include('tabsmark.php');

	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	listbox_discipline_ege("pupilnomarks.php?yid=$yid&amp;did=", $rid, $sid, $yid, $did);
	echo '</table>';

 	if ($did != 0)  {

	    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
	    	error('Discipline not found!');
	    }

        $PUPILCOUNT = 0;
		$table = table_pupilnomarks ($yid, $did);

		print_color_table($table);

        print_heading(get_string('itogoregion', 'block_mou_ege') . ': ' . $PUPILCOUNT, 'center', 4);
   		$options = array('rid' => $rid, 'sid' => $sid, 'did' => $did, 'yid' => $yid, 'action' => 'excel');
		echo '<table align="center" border=0><tr><td>';
	    print_single_button("pupilnomarks.php", $options, get_string("downloadexcel"));
		echo '</td></tr></table>';
    }


	print_footer();


function table_pupilnomarks ($yid, $did)
{
	global $CFG, $USER, $PUPILCOUNT;

    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
    	error('Discipline not found!');
    }

    $strdisciplines = get_string('disciplines_mi', 'block_mou_ege');
    $straction = get_string('action', 'block_monitoring');
    $strschool = get_string('school', 'block_monitoring');

    $table->head  = array ($strschool, '', get_string('fullname'), get_string('username'),
   						   $strdisciplines,  $straction);
    $table->align = array ('left', 'center', 'left', 'center', 'center', 'center');
	$table->class = 'moutable';


	$table->columnwidth = array (36, 1, 32, 12, 25, 14);
    // $table->datatype = array ('char', 'char');
    $table->class = 'moutable';
   	$table->width = '90%';
    // $table->size = array ('10%', '10%');
    $table->titles = array();
    $table->titles[] = get_string('pupilnomarks', 'block_mou_ege');
	$table->titles[] = get_string('nameregion', 'block_mou_ege');
	$table->titles[] = $discipline_ege->code . ' - ' . $discipline_ege->name;
    $table->titlesrows = array(30, 30, 30, 30);
    $table->worksheetname = 'pupilnomarks';
	$table->downloadfilename = 'pupilnomarks';

	$disciplines =  get_records_sql ("SELECT id, yearid, name  FROM  {$CFG->prefix}monit_school_discipline_mi
									  WHERE yearid=$yid ORDER BY name");
	$listegeids = array();
	if ($disciplines)	{
		foreach ($disciplines as $discipline) 	{
			$listegeids [$discipline->id] = $discipline->name;
		}
	}

	// $strsqlschools = "SELECT id, code  FROM {$CFG->prefix}monit_school  WHERE isclosing=0 AND yearid=$yid ";
	/*
	$schoolsarray = array();
 	if ($schools = get_records_sql($strsqlschools))	{
	    foreach ($schools as $sa)  {
	        $schoolsarray[$sa->id] = $sa->code;
	    }
	}
    */
	$strsqlresults = "SELECT id, yearid, rayonid, schoolid, classid, userid, pp, codepredmet, ocenka
					  FROM {$CFG->prefix}monit_gia_results
					 WHERE yearid=$yid AND codepredmet={$discipline_ege->code}
				 	 ORDER BY rayonid, schoolid";
    $grarray = array();
 	if ($gia_results = get_records_sql($strsqlresults))	  {
        foreach ($gia_results as $gia)	{
       	    $grarray[$gia->userid] = $gia->ocenka;
       	}
 	}


	// SELECT id, userid, concat('0,',listegeids) as egeids FROM mdl_monit_school_pupil_card
	$strsql = "SELECT id, rayonid, userid, schoolid, classid, deleted, concat('0,',listegeids) as egeids
			   FROM  {$CFG->prefix}monit_school_pupil_card
			   WHERE listegeids != '0'";
	$template = ',' . $discipline_ege->id . ',';
    $egeidsarray = array();
 	if ($egeids = get_records_sql($strsql))	{
	    foreach ($egeids as $egeid)   {
			$pos = strpos($egeid->egeids, $template, 1);
			if ($pos) {
			    // echo $egeid->egeids . '<br>';
		        if (!isset($grarray[$egeid->userid]))	{
		         	// find !!!
		         	$PUPILCOUNT++;
		           $studentsql = "SELECT id, username, firstname, lastname, picture, city
	 	                          FROM {$CFG->prefix}user
	                              WHERE (id = $egeid->userid) AND (deleted = 0) AND (confirmed = 1)";
	         		if ($student = get_record_sql($studentsql))	{

		                $rid 	= $egeid->rayonid;
		                $sid 	= $egeid->schoolid;
		                $gid	= $egeid->classid;
		                $mesto	= $student->city;

						if ($school = get_record_sql("SELECT id, name FROM {$CFG->prefix}monit_school WHERE id=$sid")) 	{
		                    $mesto = "<a href=\"{$CFG->wwwroot}/blocks/mou_mi/class/classlist.php?rid=$rid&amp;yid=$yid&amp;sid=$sid\">". $school->name . '(' . $mesto . ')</a>';
						}

						$list_disc = get_list_discipline($listegeids, $egeid->egeids);

						$title = get_string('editprofilepupil','block_mou_ege');
						$strlinkupdate = "<a href=\"{$CFG->wwwroot}/blocks/mou_mi/pupils/pupilcard.php?mode=4&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid={$student->id}\">";
						$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";

						$title = get_string('deleteprofilepupil','block_mou_ege');
					    $strlinkupdate .= "<a title=\"$title\" href=\"{$CFG->wwwroot}/blocks/mou_mi/pupils/delpupil.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid={$student->id}&amp;sesskey=$USER->sesskey\">";
						$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$title\" /></a>&nbsp;";

		                $table->data[] = array ($mesto, print_user_picture($student->id, 1, $student->picture, false, true),
										    "<div align=left><strong><a href=\"pupil.php?mode=4&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid={$student->id}\">".fullname($student)."</a></strong></div>",
		                                    "<strong>$student->username</strong>",
		                                    $list_disc,
											$strlinkupdate);
					}
		        }
		    }
	    }
    }  else 	{
    	$table->data[] = array ();
    }


    // print_r($schoolsarray); echo '<hr>';

    // echo $strsqlresults;

   // print_r($gia_results);

    return $table;
}

?>