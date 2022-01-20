<?php // $Id: enrolclass_mi.php,v 1.7 2010/11/11 08:46:06 Shtifanov Exp $

    require_once("../../../config.php");
    require_once($CFG->libdir.'/tablelib.php');
    require_once('../../monitoring/lib.php');
    require_once('../../mou_ege/lib_ege.php');

    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $sid = required_param('sid', PARAM_INT);       // School id
    $gid = required_param('gid', PARAM_INT);          // Class id
    $yid = optional_param('yid', 0, PARAM_INT);       // Year id
	$ishowall = optional_param('iall', 0, PARAM_INT);		// Show all course
	$modecheck = optional_param('check', 0, PARAM_INT);		// Synchronise enrol/unenrol
	$midate = optional_param('midate', '-');		// Synchronise enrol/unenrol


    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
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


	$class = get_record('monit_school_class', 'id', $gid);

	$strclasses = get_string('classes','block_mou_ege');
	$strclass = get_string('class','block_mou_ege');
	$strpupils = get_string('pupils', 'block_mou_ege');

	$breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/mou_mi/index.php">'.get_string('title_mi','block_mou_ege').'</a>';
	$breadcrumbs .= "-> <a href=\"{$CFG->wwwroot}/blocks/mou_mi/class/classlist.php?rid=$rid&amp;sid=$sid&amp;yid=$yid\">$strclasses</a>";
	$breadcrumbs .= "-> $strpupils";
    print_header("$SITE->shortname: $strpupils", $SITE->fullname, $breadcrumbs);

    if ($admin_is  || $region_operator_is) {  // || $rayon_operator_is)  {

		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("enrolclass_mi.php?sid=0&amp;yid=$yid&amp;gid=0&amp;rid=", $rid);
		listbox_schools("enrolclass_mi.php?rid=$rid&amp;yid=$yid&amp;gid=0&amp;sid=", $rid, $sid, $yid);
		echo '</table>';

	} else  if ($rayon_operator_is)  {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_schools("enrolclass_mi.php?rid=$rid&amp;yid=$yid&amp;gid=0&amp;sid=", $rid, $sid, $yid);
		echo '</table>';

	}  else if ($school_operator_is) {
		$school = get_record('monit_school', 'id', $sid, 'yearid', $yid);
		print_heading($strclasses.': '.$school->name, "center", 3);
	}


	if ($rid != 0 && $sid != 0 && $yid != 0)	{

		print_tabs_years_link("enrolclass_mi.php?gid=0", $rid, $sid, $yid);
	
	    $currenttab = 'enrolclass';
	    include('tabsclasses.php');

		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	    listbox_class("enrolclass_mi.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=", $rid, $sid, $yid, $gid);
	    // listbox_mi_date("enrolclass_mi.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;midate=", $rid, $sid, $yid, $midate);
		echo '</table>';

		if ($gid != 0)	{

			/// A form was submitted so process the input
	    	if ($recs = data_submitted())   {
	    		// print_r($frm); exit();
				$redirlink = "enrolclass_mi.php?sid=$sid&amp;yid=$yid&amp;rid=$rid&amp;gid=$gid";
				    		
				$disciplines =  get_records_sql ("SELECT id, yearid, name  FROM  {$CFG->prefix}monit_school_discipline_mi
												  WHERE yearid=$yid ORDER BY name");
												  
				$listmiids = $listmidatesids = '';
				$umk = array();
				foreach ($disciplines as $discipline) 	{
					$pf = 'dis_'.$discipline->id;
					if (isset($recs->{$pf}) && $recs->{$pf} == 1)  {
						$umk[$discipline->id]->discmiid = $discipline->id;
						$listmiids .= $discipline->id . ',';
						if ($midate =  get_record('monit_school_gia_dates', 'yearid', $yid, 'discmiid', $discipline->id))  {
							$listmidatesids .= $midate->id . ','; 
						}	
					} else {
						$umk[$discipline->id]->discmiid = 0;
					}
				}
				$listmiids .= '0';
				$listmidatesids .= '0'; 
				
				set_field('monit_school_class', 'listmiids', $listmiids, 'id', $class->id);
				set_field('monit_school_class', 'listmidatesids', $listmidatesids, 'id', $class->id);
				$class->listmiids = $listmiids;
				$class->listmidatesids = $listmidatesids;
				
			    $strsql = "SELECT id, userid, classid, schoolid, listmiids
		    		   		FROM {$CFG->prefix}monit_school_pupil_card
					   		WHERE classid = $gid AND deleted=0";
			    $pupils = get_records_sql ($strsql);
			    if ($pupils)  {
				   	   foreach ($pupils as $astud)	  {
				   	   		set_field('monit_school_pupil_card', 'listmiids', $listmiids, 'id', $astud->id);
				   	   		set_field('monit_school_pupil_card', 'listmidatesids', $listmidatesids, 'id', $astud->id);				   	   		
					   }
				}

				foreach($recs as $fieldname => $value)	{
				    $mask = substr($fieldname, 0, 2);
				    switch ($mask)  {
						case 'h_': 	$ids = explode('_', $fieldname);
				            		$umk[$ids[1]]->hours = $value;
		  				break;
						case 'l_': 	$ids = explode('_', $fieldname);
				            		$umk[$ids[1]]->leveledu = $value;
		  				break;
						case 't_': 	$ids = explode('_', $fieldname);
				            		$umk[$ids[1]]->textbookid = $value;
		  				break;
		  			}
		  		}
				  
				// print_r($umk);  
				foreach ($umk as $u)	{
					if ($oldumk = get_record_select('monit_school_umk', "yearid = $yid AND schoolid = $sid AND classid = $gid AND discmiid={$u->discmiid}"))	{
						$newrec->id 	= $oldumk->id;
	       				$newrec->hours 	= $u->hours;
	       				$newrec->leveledu 	= $u->leveledu;
	       				$newrec->textbookid = $u->textbookid;
		                if (!update_record('monit_school_umk', $newrec))	{
		                    print_r($newrec);
			                error("Could not update the monit_school_umk record.");
		                }
					} else {
						if ($u->discmiid != 0)	{
		       				$newrec->yearid	  = $yid;
		       				$newrec->schoolid = $sid;
		       				$newrec->classid  = $gid;
		       				$newrec->discmiid = $u->discmiid;
		       				$newrec->hours 	  = $u->hours;
		       				$newrec->leveledu = $u->leveledu;
		       				$newrec->textbookid = $u->textbookid;						   						   	       				
		       				
			       			if (!insert_record('monit_school_umk', $newrec))	{
			       				print_r($newrec);
								error(get_string('errorininseringumk', 'block_mou_ege'), $redirlink);
				  			}
						}
					}	
				} 
				redirect($redirlink, get_string("changessaved"), 0); 	
			}		

	   $sesskey = !empty($USER->id) ? $USER->sesskey : '';
   
    	// print_simple_box_start("center", '70%', 'white');
		?>

		<table class="formtable">
		<form method="post" name="form" enctype="multipart/form-data" action="enrolclass_mi.php">
		<input type="hidden" name="rid" value="<?php echo $rid ?>" />
		<input type="hidden" name="sid" value="<?php echo $sid ?>" />
		<input type="hidden" name="yid" value="<?php echo $yid ?>" />
		<input type="hidden" name="gid" value="<?php echo $gid ?>" />
		<input type="hidden" name="sesskey" value="<?php echo $sesskey ?>" />

		<?php

		$table = table_set_discipline_mi ($yid, $gid, $sid, $midate);
		print_color_table($table); 

		
		if (!isregionviewoperator() && !israyonviewoperator())  {
			echo '<table align=center border=0>';
			echo '<tr align=center><td align=right><input type="submit" value="' . get_string("savechanges") . '" /></td>';
	  		echo '</form><td align=left>';
			?>
			<form method="post" name="form2" enctype="multipart/form-data" action="classpupils.php">
			<input type="hidden" name="rid" value="<?php echo $rid ?>" />
			<input type="hidden" name="sid" value="<?php echo $sid ?>" />
			<input type="hidden" name="yid" value="<?php echo $yid ?>" />
			<input type="hidden" name="gid" value="<?php echo $gid ?>" />
			<input type="submit" value="<?php print_string("revert")?>" />
			</form>
			
			<?php
	  		echo '</td></tr></table>';
  		}
	}
 }
   print_footer();


function table_set_discipline_mi ($yid, $gid, $sid, $midate)
{
	global $CFG;

	$table->head  = array (get_string('disciplines_mi', 'block_mou_ege'), 
						   get_string('mi_umk','block_mou_ege'));
	$table->align = array ('left', 'left');
    $table->size = array ('30%', '70%');
	$table->columnwidth = array (20, 20);
    // $table->datatype = array ('char', 'char');
    $table->class = 'moutable';
   	$table->width = '80%';
    $table->titles = array();
    $table->titles[] = get_string('mi_umk', 'block_mou_school');
    $table->worksheetname = 'mi_umk';

	$class = get_record('monit_school_class', 'id', $gid);
	
	$pnum = $class->parallelnum;
	
	if ($pnum == 10) $strpnum = '';
	else  if ($pnum == 12) $strpnum = "AND parallelnum=11"; 
	else  $strpnum = "AND parallelnum=$pnum";
	
	$strsql = "SELECT id, yearid, parallelnum, name  FROM  {$CFG->prefix}monit_school_discipline_mi
				WHERE yearid=$yid  $strpnum ORDER BY name";
				
	// echo $strsql . '<br>'; 			
	if ($disciplines =  get_records_sql ($strsql))	 {

			
			
			$tabledata = array();
			
			$arr_miids = array();
			if (isset($class->listmiids))	{
		    	$arr_miids = explode(',', $class->listmiids);
			}

			$strlowclass = get_string('lowclass', 'block_mou_ege');

			foreach ($disciplines as $discipline) {
				
				if ($midate != '-')	{
					$giadate =  get_record("monit_school_gia_dates", 'discmiid', $discipline->id);
					if ($giadate->date_gia != $midate) continue;
				}	
				 
				$name = 'dis_'.$discipline->id;

				if (in_array($discipline->id, $arr_miids))	{
					$tabledata[0] = "<input name=$name type=checkbox checked=checked value=1>";
				} else {
					$tabledata[0] = "<input name=$name type=checkbox value=1>";
				}
				$tabledata[0] .=  ' '.$discipline->name . ' (' . $discipline->parallelnum . " $strlowclass)";
				
				$choice = $hours = $choice2 = 0;
				if ($umk = get_record_select('monit_school_umk', "yearid = $yid AND schoolid = $sid AND classid = $gid AND discmiid={$discipline->id}"))	{
					$hours = $umk->hours;
					$choice = $umk->leveledu;
					$choice2 = $umk->textbookid;
				}	
				
				$tabledata[1] = get_string('mi_hours', 'block_mou_ege') . ': ';
				$tabledata[1] .= "<input type=text name=h_{$discipline->id} size=2 maxlength=2 value=$hours><p>";
				$tabledata[1] .= get_string('mi_leveledu', 'block_mou_ege') . ': ';

				$mi_levels = explode(';', get_string('mi_levels', 'block_mou_ege'));
				$choices = array(get_string('selectmileveledu', 'block_mou_ege') . ' ...');
				foreach ($mi_levels as $mi_level)	{
					$choices[] = $mi_level;
				}
				$tabledata[1] .= choose_from_menu ($choices, 'l_'.$discipline->id, $choice, "" , "", "", true);
				$tabledata[1] .= '<p>';

				$arr_egeids = array();
		        if ($schooltextbooks =  get_record('monit_school_textbook',  'yearid', $yid , 'schoolid', $sid, 'discmiid', $discipline->id))  {
				    $arr_egeids = explode(',', $schooltextbooks->textbooksids);
				}
				$choices2 = array(get_string('selecttextbook', 'block_mou_ege') . ' ...');
				$strtextbooks = '';
			    if (!empty($schooltextbooks->textbooksids))	{
			    	$tbids = explode(',', $schooltextbooks->textbooksids);
			    	foreach ($tbids as $tbid)	{
			    		if ($tbid > 0)	{
			    		    if ($textbook = get_record ('monit_textbook',  'id', $tbid))	{
					    		$strtextbooks = $textbook->authors .' '. $textbook->name .'. - '. $textbook->publisher . ' (' . $textbook->numclass . ' '. $strlowclass . ')<br>';
					    	}
					    	$choices2[$tbid] = $strtextbooks;
				    	}
			    	}
			    }	
				$tabledata[1] .= get_string('textbook', 'block_mou_ege') . ': ';
				$tabledata[1] .= choose_from_menu ($choices2, 't_'.$discipline->id, $choice2, "" , "", "", true);
				$tabledata[1] .= '<p>';

				$table->data[] = array($tabledata[0], $tabledata[1]);
			}
	}
	
	return $table;
}



?>