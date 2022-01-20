<?PHP // $Id: delpupil.php,v 1.2 2009/10/13 07:08:50 Shtifanov Exp $

    require_once("../../../config.php");
	require_once($CFG->libdir.'/adminlib.php');
    require_once('../../monitoring/lib.php');
    require_once('../../mou_ege/lib_ege.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $sid = required_param('sid', PARAM_INT);          // School id
    $yid = required_param('yid', PARAM_INT);          // Year id
    $gid = required_param('gid', PARAM_INT);          // Group id
    $delete  = required_param('uid', PARAM_INT);
	$confirm = optional_param('confirm');

	require_login();

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
	}

	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && $school_operator_is)  {
	      if ($school = get_record_sql ("SELECT id, uniqueconstcode, name FROM {$CFG->prefix}monit_school
	                                     WHERE rayonid=$rid AND uniqueconstcode=$sid AND yearid=$yid"))	{
	     		$sid = $school->id;
	      }
	}


	if (isregionviewoperator() || israyonviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}


    $pupil = get_record('monit_school_pupil_card', 'userid', $delete, 'yearid', $yid);
    $user = get_record_sql("SELECT id, lastname, firstname FROM {$CFG->prefix}user WHERE id=$delete");
    $fullname = fullname($user);

    $strtitle = get_string('pupil','block_mou_school');
    $strclasses = get_string('classes','block_mou_school');
	$strclass = get_string('class','block_mou_school');
	$strpupils = get_string('pupils', 'block_mou_school');

	$breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/mou_mi/index.php">'.get_string('title_mi','block_mou_ege').'</a>';
	$breadcrumbs .= "-> <a href=\"{$CFG->wwwroot}/blocks/mou_mi/class/classlist.php?rid=$rid&amp;sid=$sid&amp;yid=$yid\">$strclasses</a>";
	$breadcrumbs .= "-> <a href=\"{$CFG->wwwroot}/blocks/mou_mi/class/classpupils.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid\">$strpupils</a>";
	$breadcrumbs .= "-> $strtitle";
    print_header("$SITE->shortname: $strtitle", $SITE->fullname, $breadcrumbs);

	
	if (!$admin_is && !$region_operator_is) {
        // error(get_string('accesstemporarylock', 'block_mou_school'));
        notice(get_string('deletepupil','block_monitoring'), $CFG->wwwroot."/blocks/mou_mi/class/classpupils.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid");
	}

 	if ($delete and confirm_sesskey()) {              // Delete a selected user, after confirmation

        // if (!has_capability('moodle/user:delete', $sitecontext)) {
            // error('You do not have the required permission to delete a user.');
        // }
		$redirlink = "{$CFG->wwwroot}/blocks/mou_mi/class/classpupils.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid";
		
        if (!$user = get_record('user', 'id', $delete)) {
            error("No such user!", '', true);
        }

        $primaryadmin = get_admin();
        if ($user->id == $primaryadmin->id) {
            error("You are not allowed to delete the primary admin user!", '', true);
        }

        if ($confirm != md5($delete)) {
            $fullname = fullname($user, true);
            print_heading(get_string('deleteprofilepupil', 'block_mou_school'));
            $optionsyes = array('rid'=>$rid, 'sid'=>$sid, 'yid'=>$yid, 'gid'=>$gid, 'uid'=>$delete,
            					'confirm'=>md5($delete), 'sesskey'=>sesskey());
	        notice_yesno(get_string('deletecheckfull', 'block_mou_school', "'$fullname'"), 'delpupil.php', $redirlink, $optionsyes, $optionsyes, 'post', 'get');

        } else if (data_submitted() and !$user->deleted) {
        	if (record_exists('monit_school_pupil_card', 'userid', $delete, 'yearid', $yid-1))	{
        		delete_records('monit_school_pupil_card', 'userid', $delete, 'yearid', $yid);
				redirect($redirlink, get_string('deletedactivity', '', fullname($user, true)), 3);        		
        	} else {
            //following code is also used in auth sync scripts
	            $updateuser = new object();
	            $updateuser->id           = $user->id;
	            $updateuser->deleted      = 1;
	            $updateuser->username     = addslashes("$user->email.".time());  // Remember it just in case
	            $updateuser->email        = '';               // Clear this field to free it up
	            $updateuser->idnumber     = '';               // Clear this field to free it up
	            $updateuser->timemodified = time();
	            if (update_record('user', $updateuser)) {
	                // Removing a user may have more requirements than just removing their role assignments.
	                // Use 'role_unassign' to make sure that all necessary actions occur.
	                role_unassign(0, $user->id);
	                // remove all context assigned on this user?
	                // notify(get_string('deletedactivity', '', fullname($user, true)) );
	           		delete_records('monit_school_pupil_card', 'userid', $delete, 'yearid', $yid);
	
			   		redirect($redirlink, get_string('deletedactivity', '', fullname($user, true)), 3);
	
	            } else {
	           		redirect($redirlink, get_string('deletednot', '', fullname($user, true)), 5);
	               // notify(get_string('deletednot', '', fullname($user, true)));
	            }
	         }    
        }
    }

	print_footer();
?>