<?php // $Id: index.php,v 1.2 2009/10/30 12:26:06 Shtifanov Exp $

    require_once('../../config.php');
    require_once('../monitoring/lib.php');

    if (!$site = get_site()) {
        redirect('index.php');
    }

    $strmonit = get_string('title_mi', 'block_mou_ege');

    print_header_mou("$site->shortname: $strmonit", $site->fullname, $strmonit);

    print_heading($strmonit);

    $table->align = array ('right', 'left');
    // $table->class = 'moutable';

	require_login();

	$admin_is = isadmin();
	// $staff_operator_is = ismonitoperator('staff');
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if  (!$admin_is && !$region_operator_is && $rayon_operator_is) 	{
		$rid = $rayon_operator_is;
	}	else {
		$rid = 0;
	}
	$sid = ismonitoperator('school', 0, 0, 0, true);
	$college_operator_is = ismonitoperator('college', 0, 0, 0, true);

	// $staffview_operator = isstaffviewoperator();

	if ($admin_is  || $region_operator_is || $rayon_operator_is)	 {
	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/mi/disciplines_mi.php?mode=1&amp;rid=$rid&amp;sid=0&amp;cid=0\">".get_string('disciplines_mi','block_mou_ege').'</a></strong>',
 	                          get_string('description_disciplines_mi','block_mou_ege'));


	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/class/classlist.php?mode=1&amp;rid=$rid&amp;sid=0&amp;cid=0\">".get_string('classes_mi','block_mou_ege').'</a></strong>',
 	                          get_string('description_classes_mi','block_mou_ege'));
/*
	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/class/classpupils.php?mode=1&amp;rid=$rid&amp;sid=0&amp;cid=0&amp;gid=0\">".get_string('class','block_mou_ege').'</a></strong>',
 	                          get_string('description_class','block_mou_ege'));

	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/pupil.php?mode=1&amp;rid=$rid&amp;sid=0&amp;cid=0&amp;gid=0&amp;yid=0\">".get_string('pupil','block_mou_ege').'</a></strong>',
 	                          get_string('description_pupil','block_mou_ege'));

	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot.'/blocks/mou_mi/pupils/searchpupil.php">'.get_string('searchpupil', 'block_mou_ege').'</a></strong>',
 	                          get_string('description_searchpupil','block_mou_ege'));

	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/markspupil.php?rid=$rid&amp;sid=0\">".get_string('markspupil', 'block_mou_ege').'</a></strong>',
 	                          get_string('description_markspupil','block_mou_ege'));

	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/statsmarkspupil.php?rid=$rid&amp;sid=0\">".get_string('statsmarkspupil', 'block_mou_ege').'</a></strong>',
 	                          get_string('description_statsmarkspupil','block_mou_ege'));

	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/difficulty.php?rid=$rid&amp;sid=0\">".get_string('difficulty', 'block_mou_ege').'</a></strong>',
 	                          get_string('description_difficulty','block_mou_ege'));
*/

	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/textbook/textbook.php?rid=$rid&amp;sid=0\">".get_string('textbooks', 'block_mou_ege').'</a></strong>',
 	                          get_string('description_textbook','block_mou_ege'));

			if ($admin_is  || $region_operator_is)	{
	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/importmarks.php?rid=$rid&amp;sid=0\">".get_string('markspupil', 'block_mou_ege').'</a></strong>',
 	                          get_string('importmarks','block_mou_ege'));
 	        }                  

	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/mi/report.php?rid=$rid&amp;sid=0\">".get_string('report', 'block_mou_ege').'</a></strong>', 
 	                          get_string('description_markspupil','block_mou_ege'));


	}


	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && $sid) {
	       if ($school = get_record('monit_school', 'id', $sid)) {
			    $rid = $school->rayonid;

		    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/mi/disciplines_mi.php?mode=1&amp;rid=$rid&amp;sid=$sid&amp;cid=0\">".get_string('disciplines_mi','block_mou_ege').'</a></strong>',
 	                          get_string('description_disciplines_mi','block_mou_ege'));
			    
		    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/class/classlist.php?mode=1&amp;rid=$rid&amp;sid=$sid&amp;cid=0\">".get_string('classes_mi','block_mou_ege').'</a></strong>',
	 	                          get_string('description_classes_mi','block_mou_ege'));

	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/textbook/textbook.php?rid=$rid&amp;sid=$sid\">".get_string('textbooks', 'block_mou_ege').'</a></strong>',
 	                          get_string('description_textbook','block_mou_ege'));
	 	                          
/*
		    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/class/classpupils.php?mode=1&amp;rid=$rid&amp;sid=$sid&amp;cid=0&amp;gid=0\">".get_string('class','block_mou_ege').'</a></strong>',
	 	                          get_string('description_class','block_mou_ege'));

		    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/pupil.php?mode=1&amp;rid=$rid&amp;sid=$sid&amp;cid=0&amp;gid=0&amp;yid=0\">".get_string('pupil','block_mou_ege').'</a></strong>',
	 	                          get_string('description_pupil','block_mou_ege'));
		    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/markspupil.php?rid=$rid&amp;sid=$sid\">".get_string('markspupil', 'block_mou_ege').'</a></strong>',
	 	                          get_string('description_markspupil','block_mou_ege'));
*/

		   }
	}

	if ($admin_is  || $region_operator_is)	 {
	    	$table->data[] = array('<strong><a href="'.$CFG->wwwroot.'/blocks/mou_mi/mi/exportxml.php?backup_name=1&amp;backup_unique_code=1">'.get_string('xmlexport', 'block_mou_ege').'</a></strong>',
 	                          get_string('description_xmlexport','block_mou_ege'));
	}

    print_table($table);

    print_footer($site);


?>