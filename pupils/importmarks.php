<?php // $Id: importmarks.php,v 1.8 2010/02/05 10:35:43 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../monitoring/lib.php');
    require_once('../../mou_att/lib_att.php');
    require_once('../../mou_ege/lib_ege.php');
	require_once($CFG->dirroot.'/lib/uploadlib.php');

	define('DELTADAY', 1);
	define('STARTHOUR', 15);

    $did = optional_param('did', 0, PARAM_INT);       // Discipline id
    $yid = optional_param('yid', 0, PARAM_INT);       // Year id
  	$action = optional_param('action', '');       // action
    $rid = $sid = $gid = 0;
    
	$dt = optional_param('dt', 0, PARAM_INT);       // Year id    

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
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

    $strimport = get_string('importmarks', 'block_mou_ege');

	$breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/mou_mi/index.php">'.get_string('title_mi','block_mou_ege').'</a>';
	$breadcrumbs .= "-> $strimport";
    print_header("$SITE->shortname: $strimport", $SITE->fullname, $breadcrumbs);

	print_tabs_years($yid, "importmarks.php?yid=");

    $currenttab = 'importmarks';
    include('tabsmark.php');

	if ($dt != 0)	{
		$disciplines_mi = get_records('monit_school_discipline_mi', 'yearid', $yid);
		foreach($disciplines_mi as $discipline_mi)	{
			set_field('monit_school_discipline_mi', 'timepublish', $discipline_mi->timeload+$dt*HOURSECS, 'id', $discipline_mi->id);			
		}
	}


   /*
	echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	listbox_discipline_mi("importmarks.php?yid=$yid&amp;did=", $rid, $sid, $yid, $did);
	echo '</table>';
*/

    if ($did != 0)	{
	    if (!$discipline_mi = get_record('monit_school_discipline_mi', 'id', $did))	{
	    	error('Discipline not found!');
	    }
    	$codepredmet = $discipline_mi->codepredmet;
    }

	if ($action == 'clear' && $did != 0) 	{
		if (delete_records('monit_mi_results', 'yearid', $yid , 'codepredmet', $codepredmet))  {
		     $discipline_mi->timeload = 0;
		     $discipline_mi->timepublish = 0;
		     update_record('monit_school_discipline_mi', $discipline_mi);
        } else {
             error("Could not delete records.", "importmarks.php");
        }
	}


		if ($action == 'upload')	{
	          echo '<hr />';
	          $dir = '1/appeal/marks_mi';
	          $um = new upload_manager('newfile'.$codepredmet, true, false, false, false, 32097152);
	          if ($um->process_file_uploads($dir))  {
	              notify(get_string('uploadedfile'), 'green', 'center');
		          $newfile_name = $CFG->dataroot.'/'.$dir.'/'.$um->get_new_filename();
		          $newfile_name = addslashes($newfile_name);
		          // echo $newfile_name . '<hr>';
		          // print_r($um);
	          } else {
		          error(get_string("uploaderror", "assignment"), "importmarks.php"); //submitting not allowed!
	          }

	          if (!unzip_file($newfile_name, '', false)) {
	              error(get_string("unzipfileserror","error"));
	          }

	          $newfile_name = $CFG->dataroot.'/'.$dir.'/results.csv';
	          $newfile_name = addslashes($newfile_name);

			  if (!file_exists($newfile_name)) {
		             error("File '$newfile_name' not found!", "importmarks.php");
			  }

	          if (!execute_sql("TRUNCATE TABLE {$CFG->prefix}monit_gia_results_temp", false))	{
	              error('Can not TRUNCATE TABLE!', "importmarks.php");
	          }

			  // LOAD DATA INFILE 'C:\\usr\\wwwroot\\moudata\\1\\appeal\\marks\\results.csv' INTO TABLE  mdl_monit_gia_results
			  // FIELDS TERMINATED BY ';' IGNORE 1 LINES (yearid,rayonid,schoolid,classid,userid,pp,audit,codepredmet,variant,sidea,sideb,sidec,ball,ocenka)

              // SERVER SERVER SERVER SERVER SERVER SERVER SERVER SERVER SERVER
	   		  //$strsql = "LOAD DATA LOCAL INFILE '$newfile_name' INTO TABLE  {$CFG->prefix}monit_gia_results_temp ";

	   		  // LOCALHOST LOCALHOST LOCALHOST LOCALHOST LOCALHOST LOCALHOST LOCALHOST
	   		  // $strsql = "LOAD DATA INFILE '$newfile_name' INTO TABLE  {$CFG->prefix}monit_gia_results_temp ";

	   		  if ($CFG->wwwroot  == 'http://cdoc06/mou')	{
	   		  		// LOCALHOST LOCALHOST LOCALHOST LOCALHOST LOCALHOST LOCALHOST LOCALHOST
	   		  		$strsql = "LOAD DATA INFILE '$newfile_name' INTO TABLE  {$CFG->prefix}monit_gia_results_temp ";	
	   		  } else {
              		// SERVER SERVER SERVER SERVER SERVER SERVER SERVER SERVER SERVER	   		  	
					$strsql = "LOAD DATA LOCAL INFILE '$newfile_name' INTO TABLE  {$CFG->prefix}monit_gia_results_temp ";	
	   		  }


	  		  $strsql .= " FIELDS TERMINATED BY ';' IGNORE 1 LINES ";
			  $strsql .= "(yearid,rayonid,schoolid,classid,userid,pp,audit,codepredmet,variant,sidea,sideb,sidec,ball,ocenka);";
	          if (!execute_sql($strsql, false))	{
	              error('FATAL ERROR !!! :'.$strsql, "importmarks.php");
	          }
		   	  notify(get_string('temploadsuccess', 'block_mou_ege'), 'green', 'center');
	          echo '<hr />';
              // error('!!!!', "importmarks.php");

			  $usercount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
											WHERE yearid=$yid AND codepredmet=$codepredmet");

			  $tusercount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_gia_results_temp
											WHERE yearid=$yid AND codepredmet=$codepredmet");

			  if ($tusercount == 0)	{
  	              error(get_string('resulthisnotfound', 'block_mou_ege'), "importmarks.php");
			  }

			  if ($usercount == 0 || $usercount <= $tusercount)	{
			  	    delete_records('monit_gia_results', 'yearid', $yid, 'codepredmet', $codepredmet);

					/*
					insert into `mou`.`mdl_monit_gia_results_temp` (yearid, rayonid, schoolid, classid, userid, pp, audit, codepredmet, variant, sidea, sideb, sidec, ball, ocenka)
					SELECT yearid, rayonid, schoolid, classid, userid, pp, audit, codepredmet, variant, sidea, sideb, sidec, ball, ocenka
					FROM `mou`.`mdl_monit_gia_results`
					where pp=333
					*/

			  	    $strsql = "INSERT INTO {$CFG->prefix}monit_mi_results (yearid, rayonid, schoolid, classid, userid, pp, audit, codepredmet, variant, sidea, sideb, sidec, ball, ocenka)
			  				   SELECT yearid, rayonid, schoolid, classid, userid, pp, audit, codepredmet, variant, sidea, sideb, sidec, ball, ocenka
							   FROM {$CFG->prefix}monit_gia_results_temp
							   where codepredmet=$codepredmet";


				    if (execute_sql($strsql, false)) {
				    	  $nowtime =  time();
		 	              $startnow = usergetdate($nowtime);
				          list($d, $m, $y) = array(intval($startnow['mday']), intval($startnow['mon']), intval($startnow['year']));
					      $d += DELTADAY;
			  		      $tappeal =  make_timestamp($y, $m, $d, STARTHOUR);

				          set_field('monit_school_discipline_mi', 'timeload', $nowtime, 'codepredmet', $codepredmet, 'yearid', $yid);
				          set_field('monit_school_discipline_mi', 'timepublish', $nowtime+2*HOURSECS, 'codepredmet', $codepredmet, 'yearid', $yid);
					   	  notice('<div align=center>'.get_string('gialoadsuccess', 'block_mou_ege').'</div>', "importmarks.php");
				          echo '<hr />';
				    } else {
			              error($strsql, "importmarks.php");
				    }

			  } else {
				  	error(get_string('recordsnotmatch', 'block_mou_ege'). " ($usercount &bt; $tusercount)", "importmarks.php");
			  }

	    }

	    $strupload = get_string('importmarks', 'block_mou_ege');
	    print_heading_with_help($strupload, 'importmarks', 'mou');


//	    print_simple_box_start('center', '50%', 'white');
	    $CFG->maxbytes = get_max_upload_file_size();
		$struploadafile = "Загрузка ZIP-файла, содержащего оценки учеников, <br> и полученного в результате работы программы 'Автоматизированная система ОРГИА'.";
	    $strmaxsize = get_string("maxsize", "", display_size($CFG->maxbytes));
//		print_simple_box_end();

	    echo "<p align=center>$struploadafile <br>($strmaxsize)</p>";

		$table = table_import_marks($yid);

	    print_color_table($table);
/*
	    upload_print_form_fragment(1,array('newfile'),false,null,0,$CFG->maxbytes,false);
	    echo '<input type="submit" name="save" value="'.get_string('uploadthisfile').'" />';
*/

		for ($i=1; $i<=48; $i++) 	{
				$timemenu[$i] = $i;
		}
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
	    echo '<tr><td>'.get_string('setdeltatime','block_mou_ege').':</td><td>';
  		popup_form("importmarks.php?dt=", $timemenu, "switchtimemenu", $dt, "", "", "", false);
  		echo '</td></tr></table>';
  
		
    print_footer();


function table_import_marks($yid)
{
    global $CFG;

    $table->head  = array ('№',  get_string('disciplinename','block_mou_ege'),
    							 get_string('countsmark','block_mou_ege'),
    							 get_string('timeload','block_mou_ege'),
    							 get_string('publishtimemark','block_mou_ege'),
    							 get_string('loadmark','block_mou_ege'),
    							 get_string('action','block_mou_ege'));
    $table->align = array ('center', 'left',  'center', 'center', 'center', 'center', 'center', 'center', 'center');
    $table->class = 'moutable';
  	$table->width = '60%';
    $table->size = array ('5%', '10%', '10%',  '10%', '10%', '10%', '10%', '30%', '10%');
	$table->columnwidth = array (4, 10, 10, 10, 10, 10, 10, 10, 30, 10);
    $table->titles = array();
    $table->titles[] = get_string('disciplines_mi', 'block_mou_ege');
    $table->worksheetname = get_string('disciplines_mi', 'block_mou_ege');
    $table->titlesrows = array(30);
    $table->downloadfilename = 'publish_date_mi';

//	$currcourse = get_records ('school_discipline', 'curriculumid', $cid);
	$currcourse =  get_records_sql ("SELECT id, yearid, name, codepredmet, timeload, timepublish
									  FROM  {$CFG->prefix}monit_school_discipline_mi
									  WHERE yearid=$yid
									  ORDER BY name");

	$i = 0;
	if ($currcourse)	{
	    $CFG->maxbytes = get_max_upload_file_size();
		foreach ($currcourse as $discipline) {

		    $startnow = $endnow = 0;

			$strtimeload = $strdates = $strtimeh = $strinterval = $strtimefs = $strtimefe = '-';
			if ($discipline->timeload != 0)	{
			   // $strdates =  get_rus_format_date($discipline->timepublish);
   			   $strtimeload =  date ("d.m.Y H:i", $discipline->timeload);

			   $strdates =  date ("d.m.Y H:i", $discipline->timepublish);
			}


		    $usercount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
											WHERE yearid=$yid AND codepredmet={$discipline->codepredmet}");

            $name1 = 'newfile'.$discipline->codepredmet;
            $name2 = 'save'.$discipline->codepredmet;
            $name3 = get_string('uploadgiathispredmet', 'block_mou_ege', $discipline->name);

		    $strload = '<form enctype="multipart/form-data" method="post" action="importmarks.php">';
		    $strload .= '<input type="hidden" name="yid" value="'.$yid.'" />';
		    $strload .= '<input type="hidden" name="action" value="upload" />';
		    $strload .= '<input type="hidden" name="did" value="'.$discipline->id.'" />';
			$strload .= '<input type="hidden" name="MAX_FILE_SIZE" value="'. $CFG->maxbytes .'" />'."\n";
	        $strload .= '<input type="file" size="50" name="'. $name1 .'" alt="'. $name1 .'" />'."\n";
			$strload .= '<input type="submit" name="'. $name2 .'" value="'. $name3 .'" />';
			$strload .= '</form>';

			$title = get_string('deletemark','block_mou_ege', $discipline->name);
	  	 	$strlinkupdate = "<a title=\"$title\" href=\"importmarks.php?action=clear&amp;yid=$yid&amp;did={$discipline->id}\">";
			$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$title\" /></a>&nbsp;";

			$i++;
			$table->data[] = array ($i.'.', $discipline->name, $usercount, $strtimeload, $strdates, $strload, $strlinkupdate);
		}
	}

	return $table;
}

?>