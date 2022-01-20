<?php // $Id: classpupils.php,v 1.7 2010/02/18 13:07:58 Shtifanov Exp $

    require_once("../../../config.php");
    require_once($CFG->libdir.'/tablelib.php');
    require_once('../../monitoring/lib.php');
    require_once('../../mou_att/lib_att.php');
    require_once('../../mou_ege/lib_ege.php');
    require_once('../lib_mi.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $sid = required_param('sid', PARAM_INT);       // School id
    $gid = required_param('gid', PARAM_INT);          // Class id
    $yid = optional_param('yid', 4, PARAM_INT);       // Year id
    $newuser = optional_param('newuser', false);  // Add new user

    $curryearid = get_current_edu_year_id();
    if ($yid == 0)	{
    	$yid = $curryearid;
    }
   
    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();;
    }


	$action   = optional_param('action', '');
    if ($action == 'excel') {
		$table = table_classpupils ($yid, $rid, $sid, $gid);
    	// print_r($table);
        print_table_to_excel($table, 1);

        exit();
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

    if ($gid != 0)	{
    	
		$class = get_record('monit_school_class', 'id', $gid);

	    if ($newuser and confirm_sesskey())   {           // Create a new user

		    $rayon = get_record('monit_rayon', 'id', $rid);
		    // $currtime = time();

		    // $code = '-'.$rid . '-' . $sid . '-' . $gid . '-' . $USER->id;
		    $code = get_pupil_username($rid, $sid, $class);


	        $user->auth      = "manual";
	        $user->firstname = "Имя Отчество";
	        $user->lastname  = "Фамилия";
	        $user->username  = $code;
	        $pswtxt = gen_psw($user->username);
	        $user->password = hash_internal_user_password($pswtxt);
	        $user->email     = $code.'@temp.ru';    // time()
	        $user->city      = $rayon->name;
	        $user->country   = 'RU';
	        $user->lang      = 'ru_utf8';
	        // $user->lang      = $CFG->lang;
	        $user->icq 		 = '';
	        $user->skype	 = '';
	        $user->yahoo 	 = '';
	        $user->msn       = '';
	        $user->display   = 1;
	        $user->mnethostid 	 = $CFG->mnet_localhost_id;
	        $user->mailformat    = 1;
	        $user->maildigest    = 0;
	        $user->autosubscribe = 1;
	        $user->htmleditor    = 1;
	        $user->emailstop     = 0;
	        $user->trackforums   = 1;

	        $user->confirmed = 1;
	        $user->timemodified = time();
	        $user->description = '';

	        // print_r($user);
	        // if (!$user = get_record("user", "username", "teacher"))	 {
		        if (!$user->id = insert_record("user", $user)) 	{
	 	           if (!$user = get_record("user", "username", $code)) 	{   // half finished user from another time
	  	              error("Could not start a new user!");
	   		       }
	     	    }
	     	// }

	        $uid = $user->id;
	        unset($user);
	        if (!$pupil = get_record('monit_school_pupil_card', 'userid', $uid))	{
	            $pupil->yearid 			= $yid;
	            $pupil->pswtxt 			= $pswtxt;
	            $pupil->userid 			= $uid;
	            $pupil->rayonid 		= $rid;
	            $pupil->schoolid 		= $sid;
	            $pupil->classid 		= $gid;
	            $pupil->timemodified 	= time();

			    if (record_exists('monit_school_pupil_card', 'userid', $pupil->userid))	 {
			    	$u = get_record('user', 'id', $pupil->userid);
					notice(get_string('existpupil', 'block_mou_school', fullname($u)), "classlist.php?rid=$rid&amp;yid=$yid&amp;sid=$sid");
				}

			    if (record_exists('user', 'id', $pupil->userid))	 {
					if (insert_record('monit_school_pupil_card', $pupil))	{
						// add_to_log(1, 'monitoring', 'operator added', '/blocks/monitoring/users/operators.php?level=$levelmonit&amp;sid=$sid&amp;rid=$rid', $USER->lastname.' '.$USER->firstname);
					} else  {
						error(get_string('errorinaddingpupil','block_mou_school'), "classlist.php?rid=$rid&amp;yid=$yid&amp;sid=$sid");
	                	// error("--> Can not add <b>teacher</b> in staff: $user->username ($user->lastname $user->firstname)"); //TODO: localize
					}
			    }
	          	unset($pupil);
	        }
	        // exit();
	        redirect($CFG->wwwroot."/blocks/mou_mi/pupils/pupilcard.php?mode=4&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid=$uid", 0);
	    }
    }


    $strtitle = get_string('pupils','block_mou_school');
    $strclasses = get_string('classes','block_mou_ege');

	$breadcrumbs = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/index.php?rid=$rid&amp;yid=$yid&amp;sid=$sid\">".get_string('title_mi','block_mou_ege').'</a>';
	$breadcrumbs .= "-> <a href=\"{$CFG->wwwroot}/blocks/mou_mi/class/classlist.php?rid=$rid&amp;sid=$sid&amp;yid=$yid\">$strclasses</a>";
	$breadcrumbs .= "-> $strtitle";
    print_header("$SITE->shortname: $strtitle", $SITE->fullname, $breadcrumbs);

    if ($admin_is  || $region_operator_is) {  // || $rayon_operator_is)  {

		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("classpupils.php?sid=0&amp;yid=$yid&amp;gid=0&amp;rid=", $rid);
		listbox_schools("classpupils.php?rid=$rid&amp;yid=$yid&amp;gid=0&amp;sid=", $rid, $sid, $yid);
		echo '</table>';

	} else  if ($rayon_operator_is)  {

		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_schools("classpupils.php?rid=$rid&amp;yid=$yid&amp;gid=0&amp;sid=", $rid, $sid, $yid);
		echo '</table>';

	}  else if ($school_operator_is) {
		$school = get_record('monit_school', 'id', $sid, 'yearid', $yid);
		print_heading($strtitle.': '.$school->name, "center", 3);
	}

	if ($rid != 0 && $sid != 0 && $yid != 0)  {
		
		print_tabs_years_link("classpupils.php?gid=0", $rid, $sid, $yid);
	
	    $currenttab = 'listclass';
	    include('tabsclasses.php');
	
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	    listbox_class("classpupils.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=", $rid, $sid, $yid, $gid);
		echo '</table>';

	
		if ($gid != 0)	{
	    	$table = table_classpupils ($yid, $rid, $sid, $gid);
	    	echo '<div align=center>';
			// $table->print_html();
			print_color_table($table);
	       	echo '</div>';
	
			?>
			<table align="center">
			<tr>
	
			<?php
			/*
			// if ($admin_is || $region_operator_is) {
	
			?>
			 <td>
			  <form name="adduser" method="post" action="classpupils.php">
				    <div align="center">
					<input type="hidden" name="rid" value="<?php echo $rid ?>" />
					<input type="hidden" name="sid" value="<?php echo $sid ?>" />
					<input type="hidden" name="yid" value="<?php echo $yid ?>" />
					<input type="hidden" name="gid" value="<?php echo $gid ?>" />
				    <input type="hidden" name="newuser" value="true" />
					<input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>">
					<input type="submit" name="addteacher" value="<?php print_string('addpupil','block_mou_ege')?>">
				    </div>
			  </form>
			  </td>
			<?php
			// }
			*/
			?>
	
				<td>
				<form name="download" method="post" action="classpupils.php">
				    <div align="center">
					<input type="hidden" name="rid" value="<?php echo $rid ?>" />
					<input type="hidden" name="sid" value="<?php echo $sid ?>" />
					<input type="hidden" name="yid" value="<?php echo $yid ?>" />
					<input type="hidden" name="gid" value="<?php echo $gid ?>" />
					<input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>">
					<input type="hidden" name="action" value="excel" />
					<input type="submit" name="downloadexcel" value="<?php print_string('downloadexcel_class', 'block_mou_ege')?>">
				    </div>
			  </form>
				</td>
	 		  </tr>
			  </table>
			  <p>
				<?php
		}	
	}
	
	print_string('remarkmuppupil', 'block_mou_ege');
	
    print_footer();
    
    
    
function table_classpupils ($yid, $rid, $sid, $gid)
{
		global $SITE, $USER, $CFG, $admin_is, $region_operator_is, $rayon_operator_is, $school_operator_is, $rayon;

		$curryearid = get_current_edu_year_id();
		
		$arr_group = get_records_sql ("SELECT id, name  FROM {$CFG->prefix}monit_school_class
	 								  WHERE yearid=$yid AND schoolid=$sid AND id=$gid
									  ORDER BY name");
									  
		$table->head  = array ('', get_string('fullname'), get_string('pol', 'block_mou_school'),
								get_string('birthday', 'block_mou_school'),
							   get_string('username') . '/<br>' . get_string('startpassword', 'block_mou_school'),
							   get_string('disciplines_mi', 'block_mou_ege'), get_string('action'));
							   
		$table->align = array ('center', 'left', 'center', 'center', 'center', 'left', 'center');
	    $table->size = array ('5%', '20%', '7%', '10%', '10%', '18%', '7%');
		$table->columnwidth = array (0, 35, 10, 15, 20,20,0);
	    // $table->datatype = array ('char', 'char');
	    $table->class = 'moutable';
	   	$table->width = '90%';
	    // $table->size = array ('10%', '10%');
	    $table->titles = array();
	    $table->titles[] = get_string('listclass', 'block_mou_school');
	    $table->titlesrows = array(30);
	    $table->worksheetname = 'listclass';
	    $table->downloadfilename = 'class_'.$gid;

		$disciplines =  get_records_sql ("SELECT id, yearid, parallelnum, name  FROM  {$CFG->prefix}monit_school_discipline_mi
										  WHERE yearid=$yid ORDER BY name");
		if ($disciplines)	{
			$listmiids = array();
			foreach ($disciplines as $discipline) 	{
				$listmiids [$discipline->id] = $discipline->name . ' (' . $discipline->parallelnum . ')';
			}
		}


        $studentsql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email, u.picture, 
							  m.classid, m.pol, m.birthday, m.pswtxt, m.listmiids
                            FROM {$CFG->prefix}user u
                       LEFT JOIN {$CFG->prefix}monit_school_pupil_card m ON m.userid = u.id 
					   WHERE classid = $gid AND u.deleted = 0 AND u.confirmed = 1
					   ORDER BY u.lastname";

        // print_r($studentsql); echo '<hr>';

        if($students = get_records_sql($studentsql)) {
        	
             foreach ($students as $student) {
             		$stremail = $strsex = $strbd = '-';
             		
             		if (!empty($student->pol))	{
             			$strsex = get_string ('sympol'.$student->pol, 'block_mou_school');
             		}

             		if ($student->birthday != '0000-00-00')	{
             			$strbd = convert_date($student->birthday, 'en', 'ru');
             		}

             		if (!empty($student->email))	{
             			$stremail = $student->email;
             		}

					$strlinkupdate = '-';
					if ($yid == $curryearid)  {
						$title = get_string('editprofilepupil','block_mou_ege');
						$strlinkupdate = "<a href=\"{$CFG->wwwroot}/blocks/mou_mi/pupils/pupilcard.php?mode=4&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid={$student->id}\">";
						$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";
	/*
						$title = get_string('pupilleaveschool','block_mou_ege');
					    $strlinkupdate .= "<a title=\"$title\" href=\"{$CFG->wwwroot}/blocks/mou_mi/pupils/leaveschool.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid={$student->id}&amp;sesskey=$USER->sesskey\">";
						$strlinkupdate .= "<img src=\"{$CFG->wwwroot}/blocks/mou_mi/i/leave.png\" alt=\"$title\" /></a>&nbsp;";
	
						$title = get_string('pupilmoveschool','block_mou_ege');
					    $strlinkupdate .= "<a title=\"$title\" href=\"{$CFG->wwwroot}/blocks/mou_mi/pupils/movepupil.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid={$student->id}&amp;sesskey=$USER->sesskey\">";
						$strlinkupdate .= "<img src=\"{$CFG->wwwroot}/blocks/mou_mi/i/btn_move.png\" alt=\"$title\" /></a>&nbsp;";
	
	
						$title = get_string('deleteprofilepupil','block_mou_ege');
					    $strlinkupdate .= "<a title=\"$title\" href=\"{$CFG->wwwroot}/blocks/mou_mi/pupils/delpupil.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid={$student->id}&amp;sesskey=$USER->sesskey\">";
						$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$title\" /></a>&nbsp;";
				*/
					}	


					$list_disc = get_list_discipline_mi($listmiids, $student->listmiids);
					
                    $table->data[] = array (print_user_picture($student->id, 1, $student->picture, false, true),
								    "<div align=left><strong><a href=\"{$CFG->wwwroot}/blocks/mou_mi/pupils/pupilcard.php?mode=4&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid={$student->id}\">".fullname($student)."</a></strong></div>",
								    $strsex, $strbd,
									"$student->username/<br>$student->pswtxt", 
									$list_disc, $strlinkupdate);

			}

		}
        return $table;
}

?>