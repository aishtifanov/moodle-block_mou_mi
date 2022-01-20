<?PHP // $Id: searchpupil.php,v 1.7 2009/06/11 09:40:38 Shtifanov Exp $

    require_once('../../../config.php');
    require_once('../../monitoring/lib.php');
    require_once('../../mou_ege/lib_ege.php');

    $rid = optional_param('rid', '0', PARAM_INT);       // Rayon id
    $sid = optional_param('sid', '0', PARAM_INT);       // School id
    $namestudent = optional_param('namestudent', '');		// pupil lastname
    $loginstudent = optional_param('loginstudent', '');		// pupil login
   	$action = optional_param('action', '');
    $yid = optional_param('yid', '0', PARAM_INT);       // Year id

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }

	if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	require_login();

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	// $school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
	}

	$strstudent  = get_string('pupil', 'block_mou_ege');
	$strstudents = get_string('pupils', 'block_mou_ege');
    $strsearchstudent = get_string('searchpupil', 'block_mou_ege');
    $strsearch = get_string("search");
    $strsearchresults  = get_string("searchresults");
    $searchtext1 = '';
    $searchtext2 = '';

    $breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/mou_mi/index.php">'.get_string('title_mi','block_mou_ege').'</a>';
	$breadcrumbs .= " -> $strsearchstudent";
    print_header_mou("$site->shortname: $strsearchstudent", $site->fullname, $breadcrumbs);


    if (isset($action) && !empty($action)) 	{

	    if (isset($namestudent) && !empty($namestudent)) 	{
		     $searchtext1 = $namestudent;
	         $studentsql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email, u.city, u.picture,
        			  			 t.userid, t.rayonid, t.schoolid, t.classid, t.listegeids
 	                        FROM {$CFG->prefix}user u, {$CFG->prefix}monit_school_pupil_card t
	                        WHERE (t.userid = u.id) AND (u.lastname LIKE '$namestudent%') AND (u.deleted = 0) AND (u.confirmed = 1)";
			 if (!$admin_is && !$region_operator_is && $rayon_operator_is) {
				 $studentsql .= " AND (t.rayonid = $rayon_operator_is) ";
			 }
	   	 	 $studentsql .= "ORDER BY u.lastname";

	         $students = get_records_sql($studentsql);
	    }
	    else if (isset($loginstudent) && !empty($loginstudent)) 	{
			 $searchtext2 = $loginstudent;

	         $studentsql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email, u.city, u.picture,
        			  			 t.userid, t.rayonid, t.schoolid, t.classid, t.listegeids
 	                        FROM {$CFG->prefix}user u, {$CFG->prefix}monit_school_pupil_card t
	                        WHERE (t.userid = u.id) AND (u.username LIKE '$loginstudent%') AND (u.deleted = 0) AND (u.confirmed = 1)";
			 if (!$admin_is && !$region_operator_is && $rayon_operator_is) {
				 $studentsql .= " AND (t.rayonid = $rayon_operator_is) ";
			 }
	   	 	 $studentsql .= "ORDER BY u.lastname";

	         $students = get_records_sql($studentsql);
	    }

        if(!empty($students)) {

         	if (count($students) > 200)  {
         		error(get_string('errorverybigcount', 'block_mou_ege'), 'searchpupil.php');
         	}

			$disciplines =  get_records_sql ("SELECT id, yearid, name  FROM  {$CFG->prefix}monit_school_discipline_mi
											  WHERE yearid=$yid ORDER BY name");
			if ($disciplines)	{
				$listegeids = array();
				foreach ($disciplines as $discipline) 	{
					$listegeids [$discipline->id] = $discipline->name;
				}
			}

	        $strdisciplines = get_string('disciplines_mi', 'block_mou_ege');
		    $straction = get_string('action', 'block_monitoring');
		    $strschool = get_string('school', 'block_monitoring');

       	    $table->head  = array ($strschool, '', get_string('fullname'), get_string('username'),
	    						   $strdisciplines,  $straction);
		    $table->align = array ('left', 'center', 'left', 'center', 'center', 'center');
	 		$table->class = 'moutable';

            foreach ($students as $student) {

                $rid 	= $student->rayonid;
                $sid 	= $student->schoolid;
                $gid	= $student->classid;
                $mesto	= $student->city;

				if ($school = get_record_sql("SELECT id, name FROM {$CFG->prefix}monit_school WHERE id=$sid")) 	{
                    $mesto = "<a href=\"{$CFG->wwwroot}/blocks/mou_mi/class/classlist.php?rid=$rid&amp;yid=$yid&amp;sid=$sid\">". $school->name . '(' . $mesto . ')</a>';
				}

				$list_disc = get_list_discipline($listegeids, $student->listegeids);

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

           	print_heading(get_string('resultsearchpupil', 'block_mou_ege'), 'center', 3);
			print_color_table($table);
		}
		else {
			notify(get_string('pupilnotfound','block_mou_ege'));
			echo '<hr>';
		}

	}

	print_heading($strsearchstudent, 'center', 2);

	print_heading(get_string('searchstudentlastname', 'block_mou_ege'), 'center', 3);
    print_simple_box_start('center', '50%', 'white');
	echo '<center><form name="studentform1" id="studentform1" method="post" action="searchpupil.php?action=lastname">'.
		 get_string('lastname'). '&nbsp&nbsp'.
		 '<input type="text" name="namestudent" size="20" value="' . $searchtext1. '" />'.
	     '<input name="search" id="search" type="submit" value="' . $strsearch . '" />'.
		 '</form></center>';
    print_simple_box_end();

	print_heading(get_string('searchstudentlogin', 'block_mou_ege'), 'center', 3);
    print_simple_box_start('center', '50%', 'white');
	echo '<center><form name="studentform2" id="studentform2" method="post" action="searchpupil.php?action=login">'.
		 get_string('username'). '&nbsp&nbsp'.
		 '<input type="text" name="loginstudent" size="20" value="' . $searchtext2. '" />'.
	     '<input name="search" id="search" type="submit" value="' . $strsearch . '" />'.
		 '</form></center>';
    print_simple_box_end();

	print_heading(get_string('searchstudentfio', 'block_mou_ege'), 'center', 3);
    print_simple_box_start('center', '50%', 'white');
	echo '<center><form name="studentform3" id="studentform3" method="post" action="namesake.php">'.
	     '<input name="search" id="search" type="submit" value="' . get_string('namesake', 'block_mou_ege') . '" />'.
		 '</form></center>';
    print_simple_box_end();

    print_footer();

?>