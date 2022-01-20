<?php // $Id: report.php,v 1.22 2009/12/21 14:13:58 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../monitoring/lib.php');
    require_once($CFG->libdir.'/tablelib.php');
    require_once('../../mou_ege/lib_ege.php');
    require_once('../../mou_school/lib_school.php'); 
	require_once("../lib_mi.php");
    
    $rid = optional_param('rid', 0, PARAM_INT);       // Rayon id
    $sid = optional_param('sid', 0, PARAM_INT);       // School id
    $yid = optional_param('yid', 0, PARAM_INT);       // School id
    $rpid = optional_param('rpid', 0, PARAM_INT);       // Report id
    $termid = optional_param('tid', 0, PARAM_INT);		//Term id
    $gid = optional_param('gid', 0, PARAM_INT);			//Class id
    $nyear = optional_param('nyear', 0, PARAM_INT);		// Numberofyear
    $did = optional_param('did', 0, PARAM_INT);       // Discipline id
    $pid = optional_param('pid', 0, PARAM_INT);       // Parallel number 
    $level = optional_param('level', 'school');
    $page    = optional_param('page', 0, PARAM_INT);
    $perpage = optional_param('perpage', 30, PARAM_INT);        // how many per page
    $tab = optional_param('tab', 'ocenka');
       	
    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }
    
  	require_once('../authall.inc.php');    
/*    
	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	if (!$admin_is && !$region_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
	}
   
	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && $rayon_operator_is) {
        $rid = $rayon_operator_is; 
	}  
*/    
    $action   = optional_param('action', '');


	if ($action == 'excel'){
		switch ($rpid){
			case '1':
					$table = table_m ($level, $yid, $did, $rid, $sid, $gid);
			        print_table_to_excel($table);			
			break;
			
			case '2':
			 		$table = table_m ($level, $yid, $did, $rid, $sid);
			        print_table_to_excel($table);			
			break;
			
			case '3':
					$table = table_m ($level, $yid, $did, $rid, 0, 0, $page, $perpage);
					print_table_to_excel($table);	
			break;
			
			case '4':
					$table = table_m ($level, $yid, $did, 0, 0, 0, $page, $perpage);
					print_table_to_excel($table);
			break;
			
			case '5':
					$table = table_pupnomarks ($yid, $did);
					print_table_to_excel($table, 1);
			break;
			
			case '6':
					$table = table_stats($level, $yid, $did, $rid, $tab);
					print_table_to_excel($table);
			break;
			
			case '7':
					$table = table_stats($level, $yid, $did, $rid, $tab);
					print_table_to_excel($table);
			break;
			
			case '8':
					$table = table_stats($level, $yid, $did, 0, $tab);
					print_table_to_excel($table);
			break;
			
			case '9':
					$table = table_stats($level, $yid, $did, 0, $tab);
					print_table_to_excel($table);
			break;
			
			case '10':
					$table = table_diffic($level, $yid, $did, $rid);
					$table2 = table_diffic_sidec($level, $yid, $did, $rid);
					print_table_to_excel($table , 0, $table2);
			break;
			
			case '11':
					$table = table_diffic($level, $yid, $did, $rid);
					print_table_to_excel($table);
					
					$table2 = table_diffic_sidec($level, $yid, $did, $rid);
					print_table_to_excel($table2);
			break;
			
			case '12':
					$table = table_statparallel($level, $yid, $rid, $pid);
					print_table_to_excel($table);
			break;
		}
	}


	$strtitle  = get_string('reports', 'block_mou_school');

	$breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/mou_mi/index.php">'.get_string('title_mi','block_mou_ege').'</a>';
	$breadcrumbs .= "-> $strtitle";
    print_header("$SITE->shortname: $strtitle", $SITE->fullname, $breadcrumbs);

	print_tabs_years_link("report.php?", $rid, $sid, $yid);
	
    if ($admin_is  || $region_operator_is) {  // || $rayon_operator_is)  {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
  		 
		   listbox_reports_mi("report.php?level=class&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=", $yid, $rpid);		
	
	switch ($rpid){
		case '0':
		
		break;
		case '1':
			listbox_rayons("report.php?sid=0&amp;yid=$yid&amp;rpid=$rpid&amp;rid=", $rid);
			listbox_schools("report.php?rid=$rid&amp;yid=$yid&amp;rpid=$rpid&amp;sid=", $rid, $sid, $yid);	
			listbox_class("report.php?level=class&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;rpid=$rpid&amp;gid=", $rid, $sid, $yid, $gid);
			listbox_discipline_mi("report.php?level=class&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;gid=$gid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
								
			if ($rid != 0 && $sid != 0 && $did != 0)  {
				
				$table = table_m ($level, $yid, $did, $rid, $sid, $gid);
				print_color_table($table);
				
		   		$options = array('rid' => $rid, 'did' => $did, 'sid' => $sid, 'yid' => $yid, 'gid' => $gid,  
				  				 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
				echo '<table align="center" border=0><tr><td>';
			    print_single_button("report.php", $options, get_string("downloadexcel"));
				echo '</td></tr></table>';
				}
		break;

		case '2':
			listbox_rayons("report.php?level=school&amp;sid=0&amp;yid=$yid&amp;rpid=$rpid&amp;rid=", $rid);
			listbox_schools("report.php?level=school&amp;rid=$rid&amp;yid=$yid&amp;rpid=$rpid&amp;sid=", $rid, $sid, $yid);	
			listbox_discipline_mi("report.php?level=school&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
			
			if ($rid != 0 && $sid != 0 && $did != 0)  {
					// echo $level . '<br>';
					$table = table_m ($level, $yid, $did, $rid, $sid);
					print_color_table($table);	
					
					$options = array('rid' => $rid, 'did' => $did, 'sid' => $sid, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
					echo '<table align="center" border=0><tr><td>';
				    print_single_button("report.php", $options, get_string("downloadexcel"));
					echo '</td></tr></table>';	
			}
		break;
		
		case '3':
			listbox_rayons("report.php?sid=0&amp;yid=$yid&amp;rpid=$rpid&amp;rid=", $rid);
			listbox_discipline_mi("report.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
			
			if ($rid != 0 && $did != 0)  {
		    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
		    	error('Discipline not found!');
		    }				
			$usercount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
											WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet}");
			if ($usercount/$perpage > 30) 	{
				$perpage = round($usercount/30);
			}
				$table = table_m ($level, $yid, $did, $rid, 0, 0, $page, $perpage);
	
			    print_paging_bar($usercount, $page, $perpage, "report.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;perpage=$perpage&amp;did=$did&amp;");
				print_color_table($table);
			    print_paging_bar($usercount, $page, $perpage, "report.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;perpage=$perpage&amp;did=$did&amp;");


		   		$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
				echo '<table align="center" border=0><tr><td>';
			    print_single_button("report.php", $options, get_string("downloadexcel"));
				echo '</td></tr></table>';
			}
		break;
		
		case '4':
		
			listbox_discipline_mi("report.php?level=region&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);

			if (!$admin_is && !$region_operator_is) {
		        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
			}
			
			if ($did!=0)  {
		    
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    	error('Discipline not found!');
			    }

				$usercount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
												WHERE yearid=$yid AND codepredmet={$discipline_ege->codepredmet}");
				if ($usercount/$perpage > 30) 	{
					$perpage = round($usercount/30);
				}
				
				$table = table_m ($level, $yid, $did, 0, 0, 0, $page, $perpage);
	
			    print_paging_bar($usercount, $page, $perpage, "report.php?level=region&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;perpage=$perpage&amp;did=$did&amp;");
				print_color_table($table);
			    print_paging_bar($usercount, $page, $perpage, "report.php?level=region&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;perpage=$perpage&amp;did=$did&amp;");

		   		$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
				echo '<table align="center" border=0><tr><td>';
			    print_single_button("report.php", $options, get_string("downloadexcel"));
				echo '</td></tr></table>';
			}


		break;
		
		case '5':
		
		listbox_discipline_mi("report.php?rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
			
			if ($did!=0)  {
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
		    	error('Discipline not found!');
	    	}

				$table = table_pupnomarks ($yid, $did);
				print_color_table($table);
				
				$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
				echo '<table align="center" border=0><tr><td>';
			    print_single_button("report.php", $options, get_string("downloadexcel"));
				echo '</td></tr></table>';
			}


		break;
		
		case '6':
		listbox_rayons("report.php?sid=0&amp;yid=$yid&amp;rpid=$rpid&amp;rid=", $rid);		
		listbox_discipline_mi("report.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);			

			if ($rid != 0 && $did != 0)  {		
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    	error('Discipline not found!');
			    }

			    $nowtime = time();
			    if ($nowtime < $discipline_ege->timepublish && !$admin_is && !$region_operator_is)	{
			            $t = date ("d.m.Y H:i", $discipline_ege->timepublish);
					    print_heading(get_string('timewillbepublish','block_mou_ege', $t), 'center', 4);
			    } else {

					$table = table_stats($level, $yid, $did, $rid, $tab);

					print_color_table($table);

			   		$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
					echo '<table align="center" border=0><tr><td>';
				    print_single_button("report.php", $options, get_string("downloadexcel"));
					echo '</td></tr></table>';
				}
			}


		break;
		
		case '7':
		listbox_rayons("report.php?sid=0&amp;yid=$yid&amp;rpid=$rpid&amp;rid=", $rid);				
		listbox_discipline_mi("report.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);

			if ($rid != 0 && $did != 0)  {
				
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    	error('Discipline not found!');
			    }

			    $nowtime = time();
			    if ($nowtime < $discipline_ege->timepublish && !$admin_is && !$region_operator_is)	{
			            $t = date ("d.m.Y H:i", $discipline_ege->timepublish);
					    print_heading(get_string('timewillbepublish','block_mou_ege', $t), 'center', 4);
			    } else {
					$tab = 'ball';
					$table = table_stats($level, $yid, $did, $rid, $tab);

					print_color_table($table);
					
			   		$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
					echo '<table align="center" border=0><tr><td>';
				    print_single_button("report.php", $options, get_string("downloadexcel"));
					echo '</td></tr></table>';
				}
			}
		break;
		
		case '8':
			
		listbox_discipline_mi("report.php?level=region&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);

			if (!$admin_is && !$region_operator_is) {
		        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
			}
		
		if ($did != 0)  {
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    	error('Discipline not found!');
			    }			    
				$table = table_stats($level, $yid, $did, 0, $tab);
				print_color_table($table);
				
				$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
				echo '<table align="center" border=0><tr><td>';
			    print_single_button("report.php", $options, get_string("downloadexcel"));
				echo '</td></tr></table>';
		}
		break;
		
		case '9':
			
		listbox_discipline_mi("report.php?level=region&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);

			if (!$admin_is && !$region_operator_is) {
		        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
			}
		
		if ($did != 0)  {
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    	error('Discipline not found!');
			    }
			    $tab = 'ball';
				$table = table_stats($level, $yid, $did, 0, $tab);
				print_color_table($table);
				
				$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
				echo '<table align="center" border=0><tr><td>';
			    print_single_button("report.php", $options, get_string("downloadexcel"));
				echo '</td></tr></table>';
		}


		break;
		
		case '10':
			listbox_rayons("report.php?sid=0&amp;yid=$yid&amp;rpid=$rpid&amp;rid=", $rid);	
		listbox_discipline_mi("report.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
		if ($rid!=0 && $did != 0)  {				
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    	error('Discipline not found!');
			    }
					$table = table_diffic($level, $yid, $did, $rid);
					print_color_table($table);

					$table = table_diffic_sidec($level = 'region', $yid, $did, $rid);
					echo '<hr>';
                    print_heading(get_string('sidec','block_mou_ege'), 'center', 3);
					print_color_table($table);
					
			   		$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
					echo '<table align="center" border=0><tr><td>';
				    print_single_button("report.php", $options, get_string("downloadexcel"));
					echo '</td></tr></table>';				
		}
		break;
		
		case '11':
			if (!$admin_is && !$region_operator_is) {
		        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
			}
			
		listbox_discipline_mi("report.php?level=region&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
		if ($rid!=0 && $did != 0)  {
				
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    	error('Discipline not found!');
			    }
					$table = table_diffic($level, $yid, $did, $rid);
					print_color_table($table);

					$table = table_diffic_sidec($level = 'region', $yid, $did, $rid);
					echo '<hr>';
                    print_heading(get_string('sidec','block_mou_ege'), 'center', 3);
					print_color_table($table);
					
				    $options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
					echo '<table align="center" border=0><tr><td>';
				    print_single_button("report.php", $options, get_string("downloadexcel"));
					echo '</td></tr></table>';
		}
		break;
		
		case '12':
			if (!$admin_is && !$region_operator_is) {
		        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
			}
		
		listbox_rayons("report.php?sid=0&amp;yid=$yid&amp;rpid=$rpid&amp;rid=", $rid);	
		listbox_parallel_all("report.php?sid=0&amp;yid=$yid&amp;rpid=$rpid&amp;rid=$rid&amp;pid=", $pid);
		if ($rid!=0 && $pid != 0)  {
            // print_heading(get_string('statparallel','block_mou_ege'), 'center', 3);
			$table = table_statparallel($level, $yid, $rid, $pid);
			print_color_table($table);
			
			$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'pid' => $pid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
			echo '<table align="center" border=0><tr><td>';
		    print_single_button("report.php", $options, get_string("downloadexcel"));
			echo '</td></tr></table>';
		}
		break;		
				
	}	
    echo '</table>';
	} else  if ($rayon_operator_is)  {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
  		 listbox_reports_mi("report.php?level=class&amp;rid=$rid&amp;yid=$yid&amp;sid=0&amp;rpid=", $yid, $rpid);		
    	switch ($rpid){
		case '0':
		
		break;
		case '1':
			listbox_schools("report.php?rid=$rid&amp;yid=$yid&amp;rpid=$rpid&amp;sid=", $rid, $sid, $yid);	
			listbox_class("report.php?level=class&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;rpid=$rpid&amp;gid=", $rid, $sid, $yid, $gid);
			listbox_discipline_mi("report.php?level=class&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;gid=$gid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
								
			if ($rid != 0 && $sid != 0 && $did != 0 && $gid != 0)  {
				
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    	error('Discipline not found!');
			    }

			    $nowtime = time();
			    if ($nowtime < $discipline_ege->timepublish && !$admin_is && !$region_operator_is)	{
			            $t = date ("d.m.Y H:i", $discipline_ege->timepublish);
					    print_heading(get_string('timewillbepublish','block_mou_ege', $t), 'center', 4);
			    } else {

				
					$table = table_m ($level, $yid, $did, $rid, $sid, $gid);
					print_color_table($table);
					
					$options = array('rid' => $rid, 'did' => $did, 'sid' => $sid, 'yid' => $yid, 'gid' => $gid, 
					'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
					echo '<table align="center" border=0><tr><td>';
				    print_single_button("report.php", $options, get_string("downloadexcel"));
					echo '</td></tr></table>';
				}
			}
		break;

		case '2':
			listbox_schools("report.php?rid=$rid&amp;yid=$yid&amp;rpid=$rpid&amp;sid=", $rid, $sid, $yid);	
			listbox_discipline_mi("report.php?level=school&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
			
			if ($rid != 0 && $sid != 0 && $did != 0)  {
				
				    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
				    	error('Discipline not found!');
				    }
	
				    $nowtime = time();
				    if ($nowtime < $discipline_ege->timepublish && !$admin_is && !$region_operator_is)	{
				            $t = date ("d.m.Y H:i", $discipline_ege->timepublish);
						    print_heading(get_string('timewillbepublish','block_mou_ege', $t), 'center', 4);
				    } else {
				
						$table = table_m ($level, $yid, $did, $rid, $sid);
						print_color_table($table);	
						
						$options = array('rid' => $rid, 'did' => $did, 'sid' => $sid, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
						echo '<table align="center" border=0><tr><td>';
					    print_single_button("report.php", $options, get_string("downloadexcel"));
						echo '</td></tr></table>';
					}		
			}
		break;
		
		case '3':
			listbox_discipline_mi("report.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
			
			if ($rid != 0 && $did != 0)  {
				
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    	error('Discipline not found!');
			    }	
				
			    $nowtime = time();
			    if ($nowtime < $discipline_ege->timepublish && !$admin_is && !$region_operator_is)	{
			            $t = date ("d.m.Y H:i", $discipline_ege->timepublish);
					    print_heading(get_string('timewillbepublish','block_mou_ege', $t), 'center', 4);
			    } else {
							
					$usercount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
													WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet}");
					if ($usercount/$perpage > 30) 	{
						$perpage = round($usercount/30);
					}
					$table = table_m ($level, $yid, $did, $rid, 0, 0, $page, $perpage);
		
				    print_paging_bar($usercount, $page, $perpage, "report.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;perpage=$perpage&amp;did=$did&amp;");
					print_color_table($table);
				    print_paging_bar($usercount, $page, $perpage, "report.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;perpage=$perpage&amp;did=$did&amp;");
	
					$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
					echo '<table align="center" border=0><tr><td>';
				    print_single_button("report.php", $options, get_string("downloadexcel"));
					echo '</td></tr></table>';
				}	
			}
		break;
		
		case '4':

		break;
		
		case '5':
		
			listbox_discipline_mi("report.php?rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
			
			if ($did!=0)  {
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
		    	error('Discipline not found!');
	    		}

	

			    $nowtime = time();
			    if ($nowtime < $discipline_ege->timepublish && !$admin_is && !$region_operator_is)	{
			            $t = date ("d.m.Y H:i", $discipline_ege->timepublish);
					    print_heading(get_string('timewillbepublish','block_mou_ege', $t), 'center', 4);
			    } else {

					$table = table_pupnomarks ($yid, $did);
					print_color_table($table);
					
					$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
					echo '<table align="center" border=0><tr><td>';
				    print_single_button("report.php", $options, get_string("downloadexcel"));
					echo '</td></tr></table>';
				}	
			}
		break;
		
		case '6':
		
		listbox_discipline_mi("report.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
			
			if ($rid != 0 && $did != 0)  {
				
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    	error('Discipline not found!');
			    }

			    $nowtime = time();
			    if ($nowtime < $discipline_ege->timepublish && !$admin_is && !$region_operator_is)	{
			            $t = date ("d.m.Y H:i", $discipline_ege->timepublish);
					    print_heading(get_string('timewillbepublish','block_mou_ege', $t), 'center', 4);
			    } else {

					$table = table_stats($level, $yid, $did, $rid, $tab);

					print_color_table($table);

			   		$options = array('rid' => $rid, 'sid' => $sid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'action' => 'excel', 'tab' => $tab);
					echo '<table align="center" border=0><tr><td>';
				    print_single_button("report.php", $options, get_string("downloadexcel"));
					echo '</td></tr></table>';
				}
				
				$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
				echo '<table align="center" border=0><tr><td>';
			    print_single_button("report.php", $options, get_string("downloadexcel"));
				echo '</td></tr></table>';
			}


		break;
		
		case '7':
			
		listbox_discipline_mi("report.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=$did", $rid, $sid, $yid, $did);
			
			if ($rid != 0 && $did != 0)  {
				
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    	error('Discipline not found!');
			    }

			    $nowtime = time();
			    if ($nowtime < $discipline_ege->timepublish && !$admin_is && !$region_operator_is)	{
			            $t = date ("d.m.Y H:i", $discipline_ege->timepublish);
					    print_heading(get_string('timewillbepublish','block_mou_ege', $t), 'center', 4);
			    } else {
					$tab = 'ball';
					$table = table_stats($level, $yid, $did, $rid, $tab);

					print_color_table($table);

					$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
					echo '<table align="center" border=0><tr><td>';
				    print_single_button("report.php", $options, get_string("downloadexcel"));
					echo '</td></tr></table>';
				}
			}
		break;
		
		case '8':
		break;
		
		case '9':
		break;
		
		case '10':
			
				listbox_discipline_mi("report.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
				if ($rid!=0 && $did != 0)  {
				
				    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    		error('Discipline not found!');
			 	    }
			 	    
				    $nowtime = time();
				    if ($nowtime < $discipline_ege->timepublish && !$admin_is && !$region_operator_is)	{
				            $t = date ("d.m.Y H:i", $discipline_ege->timepublish);
						    print_heading(get_string('timewillbepublish','block_mou_ege', $t), 'center', 4);
				    } else {
			 	    
						$table = table_diffic($level, $yid, $did, $rid);
						print_color_table($table);
	
						$table = table_diffic_sidec($level, $yid, $did, $rid);
						echo '<hr>';
	                    print_heading(get_string('sidec','block_mou_ege'), 'center', 3);
						print_color_table($table);
						
						$options = array('rid' => $rid, 'did' => $did, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
						echo '<table align="center" border=0><tr><td>';
					    print_single_button("report.php", $options, get_string("downloadexcel"));
						echo '</td></tr></table>';
					}	
			}
		break;
		
		case '11':
		break;
		
	}	
		echo '</table>';
	}  else if ($school_operator_is) {
		print_heading($strtitle.': '.$school->name, "center", 3);
		
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
  		 listbox_reports_mi("report.php?level=class&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=", $yid, $rpid);		
    	switch ($rpid){
		case '0':
		
		break;
		case '1':
			listbox_schools("report.php?rid=$rid&amp;yid=$yid&amp;rpid=$rpid&amp;sid=", $rid, $sid, $yid);	
			listbox_class("report.php?level=class&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;rpid=$rpid&amp;gid=", $rid, $sid, $yid, $gid);
			listbox_discipline_mi("report.php?level=class&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;gid=$gid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
								
			if ($rid != 0 && $sid != 0 && $did != 0 && $gid != 0)  {
				
			    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
			    	error('Discipline not found!');
			    }

			    $nowtime = time();
			    if ($nowtime < $discipline_ege->timepublish && !$admin_is && !$region_operator_is)	{
			            $t = date ("d.m.Y H:i", $discipline_ege->timepublish);
					    print_heading(get_string('timewillbepublish','block_mou_ege', $t), 'center', 4);
			    } else {

				
					$table = table_m ($level, $yid, $did, $rid, $sid, $gid);
					print_color_table($table);
					
					$options = array('rid' => $rid, 'did' => $did, 'sid' => $sid, 'yid' => $yid, 'gid' => $gid, 
					'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
					echo '<table align="center" border=0><tr><td>';
				    print_single_button("report.php", $options, get_string("downloadexcel"));
					echo '</td></tr></table>';
				}
			}
		break;

		case '2':
			listbox_schools("report.php?rid=$rid&amp;yid=$yid&amp;rpid=$rpid&amp;sid=", $rid, $sid, $yid);	
			listbox_discipline_mi("report.php?level=school&amp;rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;rpid=$rpid&amp;did=", $rid, $sid, $yid, $did);
			
			if ($rid != 0 && $sid != 0 && $did != 0)  {
				
				    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
				    	error('Discipline not found!');
				    }
	
				    $nowtime = time();
				    if ($nowtime < $discipline_ege->timepublish && !$admin_is && !$region_operator_is)	{
				            $t = date ("d.m.Y H:i", $discipline_ege->timepublish);
						    print_heading(get_string('timewillbepublish','block_mou_ege', $t), 'center', 4);
				    } else {
				
						$table = table_m ($level, $yid, $did, $rid, $sid);
						print_color_table($table);	
						
						$options = array('rid' => $rid, 'did' => $did, 'sid' => $sid, 'yid' => $yid, 'level' => $level, 'rpid' => $rpid,'action' => 'excel', 'tab' => $tab);
						echo '<table align="center" border=0><tr><td>';
					    print_single_button("report.php", $options, get_string("downloadexcel"));
						echo '</td></tr></table>';
					}		
			}
		break;
		}
		echo '</table>';
		
	}
    print_footer($SITE);
    

    
function table_stats($level, $yid, $did, $rid, $tab = 'ocenka')
{
	global $CFG;

    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
    	error('Discipline not found!');
    }

    switch ($level)	{
		case 'region':
					$strtitle = get_string('rayon', 'block_monitoring');
		break;
		case 'rayon':
					$strtitle = get_string('school', 'block_monitoring');
		break;
    }

    if ($tab == 'ocenka')	{
    	$strocenka = get_string('ocenka', 'block_mou_ege');
    	$strvsegolow = get_string('vsegolow', 'block_mou_ege');
	    $table->head  = array ('N', $strtitle,
							   get_string('kolpupilingia', 'block_mou_ege'), get_string('avgball', 'block_mou_ege'),
							   get_string('avgocenka', 'block_mou_ege'), 
							   $strocenka . ' "5" <br> ' . "($strvsegolow)", $strocenka . ' "5" (%)',
							   $strocenka . ' "4" <br> ' . "($strvsegolow)", $strocenka . ' "4" (%)', 
							   $strocenka . ' "3" <br> ' . "($strvsegolow)", $strocenka . ' "3" (%)',
							   $strocenka . ' "2" <br> ' . "($strvsegolow)", $strocenka . ' "2" (%)');

		$table->align = array ('center', 'left',  'center',   'center',  'center',  'center',   'center',
							   'center', 'center',  'center',  'center',  'center',  'center', 'center');
		$table->columnwidth = array (3, 20, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10);
	    $table->class = 'moutable';
	   	$table->width = '90%';
	    $table->titles = array();
	    $table->titles[] = get_string('resultgiapopredmetu', 'block_mou_ege', $discipline_ege->name);
	    $table->titlesrows = array(30, 30);
	    $table->worksheetname = 'stat';

		$ocenki = array ('5', '4', '3', '2');
	    switch ($level)	{
			case 'region':  $rayons = get_records('monit_rayon');
							$i = 1;
							$allpupilcount = $allsumball = $allsumocenka = 0;
							$allkolocenki  = array ('0', '0', '0', '0');
							$allkolocenkiproc = array ('0', '0', '0', '0');

							foreach ($rayons as $rayon)	{
								$rid = $rayon->id;
								$pupilcount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
																WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet} AND variant <> 0");
								$allpupilcount += $pupilcount;
								$sumball = get_record_sql("select sum(ball) as sumball from  {$CFG->prefix}monit_mi_results
														  where rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet}");
								$allsumball += $sumball->sumball;
								if ($pupilcount == 0)	 {
	                                $avgball = '-';
								} else {
									$avgball = number_format($sumball->sumball/$pupilcount, 3, ',', '');
								}
								$sumocenka = get_record_sql("select sum(ocenka) as sumocenka from  {$CFG->prefix}monit_mi_results
														  where rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet}");
								$allsumocenka += $sumocenka->sumocenka;
								if ($pupilcount == 0)	 {
	                                $avgocenka = '-';
								} else {
									$avgocenka = number_format($sumocenka->sumocenka/$pupilcount, 3, ',', '');
								}

								$kolocenki = array ('0', '0', '0', '0');
								$kolocenkiproc = array ('0', '0', '0', '0');
								foreach ($ocenki as $index => $ocenka)	{
								      $kolocenki[$index] = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
																			  WHERE rayonid=$rid AND yearid=$yid AND
																			  codepredmet={$discipline_ege->codepredmet} and ocenka=$ocenka");
									  $allkolocenki[$index] += $kolocenki[$index];
									  if ($pupilcount == 0)	 {
										  $kolocenkiproc[$index] = '-';
									  } else {
										  $kolocenkiproc[$index] = number_format($kolocenki[$index]/$pupilcount*100, 2, ',', '') . '%';
									  }
								}
					            $table->data[] = array ($i++ . '.', $rayon->name, $pupilcount, $avgball, $avgocenka,
	            										$kolocenki[0], $kolocenkiproc[0], $kolocenki[1], $kolocenkiproc[1],
	            										$kolocenki[2], $kolocenkiproc[2], $kolocenki[3], $kolocenkiproc[3]);
							}


							if ($allpupilcount == 0)	 {
	                           $avgball = '-';
							} else {
							   $avgball = number_format($allsumball/$allpupilcount, 3, ',', '');
							}
							if ($allpupilcount == 0)	 {
	                            $avgocenka = '-';
							} else {
								$avgocenka = number_format($allsumocenka/$allpupilcount, 3, ',', '');
							}
							foreach ($allkolocenki as $index => $ak)	{
								if ($allpupilcount == 0)	{
									  $allkolocenkiproc[$index] = '-';
								} else {
									  $allkolocenkiproc[$index] = number_format($ak/$allpupilcount*100, 2, ',', '') . '%';
								}
							}
				            $table->data[] = array ('', '<b>'. get_string('itogo'.$level, 'block_mou_ege') . '</b>', '<b>'. $allpupilcount . '</b>', '<b>'. $avgball . '</b>', '<b>'. $avgocenka . '</b>',
	            										'<b>'. $allkolocenki[0] . '</b>', '<b>'. $allkolocenkiproc[0] . '</b>', '<b>'. $allkolocenki[1] . '</b>', '<b>'. $allkolocenkiproc[1] . '</b>',
	            										'<b>'. $allkolocenki[2] . '</b>', '<b>'. $allkolocenkiproc[2] . '</b>', '<b>'. $allkolocenki[3] . '</b>', '<b>'. $allkolocenkiproc[3] . '</b>');

							$table->titles[] = get_string('nameregion', 'block_mou_ege');
							$table->downloadfilename = 'statsmarksregion';
			break;
			case 'rayon':   $rayon = get_record('monit_rayon', 'id', $rid);
							$schools =  get_records_sql("SELECT *  FROM {$CFG->prefix}monit_school
						  				   				WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid
						     							ORDER BY number");
							$allpupilcount = $allsumball = $allsumocenka = 0;
							$allkolocenki  = array ('0', '0', '0', '0');
							$allkolocenkiproc = array ('0', '0', '0', '0');

							$i = 1;
							foreach ($schools as $school)	{
								$sid = $school->id;
								$pupilcount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
																WHERE schoolid=$sid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet} AND variant <> 0");
								$allpupilcount += $pupilcount;
								$sumball = get_record_sql("select sum(ball) as sumball from  {$CFG->prefix}monit_mi_results
														  where schoolid=$sid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet}");
								$allsumball += $sumball->sumball;
								if ($pupilcount == 0)	 {
	                                $avgball = '-';
								} else {
									$avgball = number_format($sumball->sumball/$pupilcount, 3, ',', '');
								}
								$sumocenka = get_record_sql("select sum(ocenka) as sumocenka from  {$CFG->prefix}monit_mi_results
														  where schoolid=$sid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet}");
								$allsumocenka += $sumocenka->sumocenka;
								if ($pupilcount == 0)	 {
	                                $avgocenka = '-';
								} else {
									$avgocenka = number_format($sumocenka->sumocenka/$pupilcount, 3, ',', '');
								}

								$kolocenki = array ('0', '0', '0', '0');
								$kolocenkiproc = array ('0', '0', '0', '0');
								foreach ($ocenki as $index => $ocenka)	{
								      $kolocenki[$index] = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
																			  WHERE schoolid=$sid AND yearid=$yid AND
																			  codepredmet={$discipline_ege->codepredmet} and ocenka=$ocenka");
									  $allkolocenki[$index] += $kolocenki[$index];
									  if ($pupilcount == 0)	 {
										  $kolocenkiproc[$index] = '-';
									  } else {
										  $kolocenkiproc[$index] = number_format($kolocenki[$index]/$pupilcount*100, 2, ',', '') . '%';
									  }
								}
					            $table->data[] = array ($i++ . '.', $school->name, $pupilcount, $avgball, $avgocenka,
	            										$kolocenki[0], $kolocenkiproc[0], $kolocenki[1], $kolocenkiproc[1],
	            										$kolocenki[2], $kolocenkiproc[2], $kolocenki[3], $kolocenkiproc[3]);

							}

							if ($allpupilcount == 0)	 {
	                           $avgball = '-';
							} else {
							   $avgball = number_format($allsumball/$allpupilcount, 3, ',', '');
							}
							if ($allpupilcount == 0)	 {
	                            $avgocenka = '-';
							} else {
								$avgocenka = number_format($allsumocenka/$allpupilcount, 3, ',', '');
							}
							foreach ($allkolocenki as $index => $ak)	{
								if ($allpupilcount == 0)	{
									  $allkolocenkiproc[$index] = '-';
								} else {
									  $allkolocenkiproc[$index] = number_format($ak/$allpupilcount*100, 2, ',', '') . '%';
								}
							}
				            $table->data[] = array ('', '<b>'. get_string('itogo'.$level, 'block_mou_ege') . '</b>', '<b>'. $allpupilcount . '</b>', '<b>'. $avgball . '</b>', '<b>'. $avgocenka . '</b>',
	            										'<b>'. $allkolocenki[0] . '</b>', '<b>'. $allkolocenkiproc[0] . '</b>', '<b>'. $allkolocenki[1] . '</b>', '<b>'. $allkolocenkiproc[1] . '</b>',
	            										'<b>'. $allkolocenki[2] . '</b>', '<b>'. $allkolocenkiproc[2] . '</b>', '<b>'. $allkolocenki[3] . '</b>', '<b>'. $allkolocenkiproc[3] . '</b>');

							$table->titles[] = $rayon->name;
							$table->downloadfilename = 'statsmarks_rayon_'.$rid;
			break;
	    }
	} else if ($tab == 'ball')	{
	    $table->head  = array ('N', get_string('ball', 'block_mou_ege'), get_string('numberpupils', 'block_mou_ege'), get_string('persentpupils', 'block_mou_ege')); 

		$table->align = array ('center', 'center',  'center',   'center');
		$table->columnwidth = array (3, 10, 10, 10);
	    $table->class = 'moutable';
	   	$table->width = '50%';
	    $table->size = array ('5%', '10%', '10%', '10%');
	    $table->titles = array();
	    $table->titles[] = get_string('resultgiapopredmetu', 'block_mou_ege', $discipline_ege->name);
	    $table->titlesrows = array(30, 30);
	    $table->worksheetname = 'statballs';
	    switch ($level)	{
			case 'region': 
							$i = 1;
							$allpupilcount = 0;
							if ($balls = get_records_sql("SELECT DISTINCT ball FROM {$CFG->prefix}monit_mi_results
													WHERE yearid=$yid AND codepredmet={$discipline_ege->codepredmet} AND variant <> 0
													ORDER by ball"))	{

								$allkolballs  = array ();
								$allkolballsproc = array ();
								foreach ($balls as $index => $ball)		{
									$allkolballs[$index] = 0;
									$allkolballsproc[$index] = 0;
								}

								$pupilcount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
															WHERE yearid=$yid AND codepredmet={$discipline_ege->codepredmet} AND variant <> 0");
								$allpupilcount += $pupilcount;

								foreach ($balls as $index => $ball)	{
								      $kolballs[$index] = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
																			  WHERE yearid=$yid AND codepredmet={$discipline_ege->codepredmet} and ball={$ball->ball}");
									  $allkolballs[$index] += $kolballs[$index];
									  if ($pupilcount == 0)	 {
										  $kolballsproc[$index] = '-';
									  } else {
										  $kolballsproc[$index] = number_format($kolballs[$index]/$pupilcount*100, 2, ',', '') . '%';
									  }
		 				              $table->data[] = array ($i++ . '.', $ball->ball, $kolballs[$index], $kolballsproc[$index]);
	            				}

							}

							$table->titles[] = get_string('nameregion', 'block_mou_ege');
							$table->downloadfilename = 'statsballsregion';
			break;
			case 'rayon':   $rayon = get_record('monit_rayon', 'id', $rid);

							$i = 1;
							$allpupilcount = 0;
							if ($balls = get_records_sql("SELECT DISTINCT ball FROM {$CFG->prefix}monit_mi_results
													WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet} AND variant <> 0
													ORDER by ball"))	{

								$allkolballs  = array ();
								$allkolballsproc = array ();
								foreach ($balls as $index => $ball)		{
									$allkolballs[$index] = 0;
									$allkolballsproc[$index] = 0;
								}

								$pupilcount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
															WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet} AND variant <> 0");
								$allpupilcount += $pupilcount;

								foreach ($balls as $index => $ball)	{
								      $kolballs[$index] = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_mi_results
																			  WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet} and ball={$ball->ball}");
									  $allkolballs[$index] += $kolballs[$index];
									  if ($pupilcount == 0)	 {
										  $kolballsproc[$index] = '-';
									  } else {
										  $kolballsproc[$index] = number_format($kolballs[$index]/$pupilcount*100, 2, ',', '') . '%';
									  }
		 				              $table->data[] = array ($i++ . '.', $ball->ball, $kolballs[$index], $kolballsproc[$index]);
	            				}

							}

				            $table->data[] = array ('', '<b>'. get_string('itogo'.$level, 'block_mou_ege') . '</b>',
				            							'<b>'. $allpupilcount . '</b>', '<b>100%</b>');

							$table->titles[] = $rayon->name;
							$table->downloadfilename = 'statsballs_rayon_'.$rid;
			break;
	    }


	}


    return $table;
}


function table_pupnomarks ($yid, $did)
{
	global $CFG, $USER, $PUPILCOUNT;

    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
    	error('Discipline not found!');
    }

    $strdisciplines = get_string('disciplines_ege', 'block_mou_ege');
    $straction = get_string('action', 'block_monitoring');
    $strschool = get_string('school', 'block_monitoring');

    $table->head  = array ($strschool, '', get_string('fullname'), get_string('username'),
   						   $strdisciplines,  $straction);
    $table->align = array ('left', 'center', 'left', 'center', 'center', 'center');
	$table->class = 'moutable';


	$table->columnwidth = array (36, 1, 32, 12, 25, 14);
    $table->class = 'moutable';
   	$table->width = '90%';
    $table->titles = array();
    $table->titles[] = get_string('pupilnomarks', 'block_mou_ege');
	$table->titles[] = get_string('nameregion', 'block_mou_ege');
	$table->titles[] = $discipline_ege->codepredmet . ' - ' . $discipline_ege->name;
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
	$strsqlresults = "SELECT id, yearid, rayonid, schoolid, classid, userid, pp, codepredmet, ocenka
					  FROM {$CFG->prefix}monit_mi_results
					 WHERE yearid=$yid AND codepredmet={$discipline_ege->codepredmet}
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
		        if (!isset($grarray[$egeid->userid]))	{
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
		                    $mesto = "<a href=\"{$CFG->wwwroot}/blocks/mou_ege/class/classlist.php?rid=$rid&amp;yid=$yid&amp;sid=$sid\">". $school->name . '(' . $mesto . ')</a>';
						}

						$list_disc = get_list_discipline($listegeids, $egeid->egeids);

						$title = get_string('editprofilepupil','block_mou_ege');
						$strlinkupdate = "<a href=\"{$CFG->wwwroot}/blocks/mou_ege/pupils/pupilcard.php?mode=4&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid={$student->id}\">";
						$strlinkupdate .= "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";

						$title = get_string('deleteprofilepupil','block_mou_ege');
					    $strlinkupdate .= "<a title=\"$title\" href=\"{$CFG->wwwroot}/blocks/mou_ege/pupils/delpupil.php?rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid={$student->id}&amp;sesskey=$USER->sesskey\">";
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
    return $table;
}


function table_administrative ($yid, $rid, $sid, $gid)
	{
		global $CFG, $admin_is, $region_operator_is, $rayon_operator_is, $school_operator_is, $rayon, $GLDAY, $GLDATESTART;

		$table->head  = array (get_string('ordernumber','block_mou_school'), get_string('pupilfio','block_mou_school'), 
								get_string('pol','block_mou_school'), get_string('birthday','block_mou_school'), get_string('phonenumber','block_mou_school'));
		$table->align = array ('center', 'left', 'center', 'center', 'center');
	    $table->size = array ('3%', '20%', '5%', '10%', '10%');
		$table->columnwidth = array (7, 30, 7, 15, 15);
	    $table->class = 'moutable';
	   	$table->width = '90%';
    	$table->titlesrows = array(30);
	    $table->titles = array();
	    $table->titles[] = get_string('administratives', 'block_mou_school');
	    $table->downloadfilename = 'administrative';
	    $table->worksheetname = 'administrative';
       
	    $studentsql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email, u.picture, 
							  u.phone1, u.phone2, m.classid, m.pol, m.birthday, m.pswtxt
                            FROM {$CFG->prefix}user u
                       LEFT JOIN {$CFG->prefix}monit_school_pupil_card m ON m.userid = u.id 
					   WHERE classid = $gid AND u.deleted = 0 AND u.confirmed = 1
					   ORDER BY u.lastname";		
		
        if($students = get_records_sql($studentsql)) {
        	$i=1;
			foreach ($students as $student){	
				$tabledata = array($i);
				$tabledata[] = "<div align=left><strong><a href=\"{$CFG->wwwroot}/blocks/mou_school/class/pupilcard.php?mode=4&amp;rid=$rid&amp;sid=$sid&amp;yid=$yid&amp;gid=$gid&amp;uid={$student->id}\">".fullname($student)."</a></strong></div>";	
			
				if (!empty($student->pol))	{
             			$strsex = get_string ('sympol'.$student->pol, 'block_mou_school');
             		}
  				$tabledata[] = $strsex;
  				$tabledata[] = $student->birthday;
  				$tabledata[] = $student->phone1;
			$i++;
			$table->data[] = $tabledata;		
			}       	
		
		}
	
							
	    return $table;
}

function listbox_years($scriptname, $rid, $sid, $yid, $nyear)
{	
global $CFG;
 	$yearmenu = array();
 	$yearmenu[0] = get_string('selectyear', 'block_mou_school') . '...';
	if ($rid != 0 && $sid!= 0 && $yid!= 0)  {
        $yearmenu[2] = '2008/2009'.get_string('g','block_mou_school');
        $yearmenu[3] = '2009/2010'.get_string('g','block_mou_school');      
	
	echo '<tr><td>'.get_string('year', 'block_mou_school').':</td><td>';
	popup_form($scriptname, $yearmenu, "switchyear", $nyear, "", "", "", false);
	echo '</td></tr>';	
	return 1;
	}
}

function table_filling ($yid, $rid, $sid, $nyear)
	{
		global $CFG, $admin_is, $region_operator_is, $rayon_operator_is, $school_operator_is, $rayon, $GLDAY, $GLDATESTART;

		$table->head  = array (get_string('class','block_mou_school'), get_string('numofpupils','block_mou_school'), 
								get_string('mediumfilling','block_mou_school'));
		$table->align = array ('left', 'center', 'center');
	    $table->size = array ('60%', '15%', '15%');
		$table->columnwidth = array (20, 20, 20);
	    $table->class = 'moutable';
	   	$table->width = '50%';
    	$table->titlesrows = array(30);
	    $table->titles = array();
	    $table->titles[] = get_string('fillingofclasses', 'block_mou_school');
	    $table->downloadfilename = 'filling';
	    $table->worksheetname = 'fillingofclasses';
		$tabledata = array();
		
		for($i=1;$i<=11;$i++){
			$couninschool=0;
			if ($classes = get_records_sql("SELECT id, name, parallelnum  FROM {$CFG->prefix}monit_school_class
	 								  WHERE yearid=$nyear AND schoolid=$sid AND parallelnum=$i
									  ORDER BY parallelnum, name")) 	{
	  		$countparallel = $countclass = 0;
	  		foreach($classes as $class){
	  			$countclass++;
	  			$quantity  =  count_records('monit_school_pupil_card', 'classid', $class->id);
	  			$countparallel += $quantity;
			   
				$table->data[] = array($class->name, $quantity , '');
			}	
			$table->data[] = array(get_string('byparallel','block_mou_school'), $countparallel, $countparallel/$countclass);
			}
		}		
						
	    return $table;
}

function table_m ($level, $yid, $did, $rid = 0, $sid = 0, $gid = 0, $page = '', $perpage = '')
{
	global $CFG;

    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
    	error('Discipline not found!');
    }

    $table->head  = array (get_string('number','block_monitoring'), get_string('code_ou', 'block_mou_ege'), get_string('class', 'block_mou_ege'),
    					   get_string('code_pp', 'block_mou_ege'), get_string('auditoria', 'block_mou_ege'),
    					   get_string('lastname'), get_string('firstname'), get_string('numvariant', 'block_mou_ege'),
    					   get_string('sidea', 'block_mou_ege'), get_string('sideb', 'block_mou_ege'), get_string('sidec', 'block_mou_ege'),
    					   get_string('ball', 'block_mou_ege'), get_string('ocenka', 'block_mou_ege'));

	$table->align = array ('center', 'center', 'center', 'center', 'center', 'left', 'left', 'center', 'left', 'left', 'left', 'center', 'center');
	$table->columnwidth = array (7, 7, 7, 8, 9, 14, 25, 14, 27, 27, 27, 8, 7);
    $table->class = 'moutable';
   	$table->width = '90%';
    $table->titles = array();
    $table->titles[] = get_string('protokolproverki', 'block_mou_ege');
    $table->worksheetname = $level;

	$strsqlresults = "SELECT *  FROM {$CFG->prefix}monit_mi_results ";
	$strsqlschools = "SELECT id, code  FROM {$CFG->prefix}monit_school ";
	$strsqlclasses = "SELECT id, name FROM {$CFG->prefix}monit_school_class	";

    switch ($level)	{
		case 'region':
						$strsqlresults .= " WHERE yearid=$yid AND codepredmet={$discipline_ege->codepredmet}
										 	ORDER BY rayonid, schoolid";
						$strsqlschools .= " WHERE isclosing=0 AND yearid=$yid ";
						$strsqlclasses .= " WHERE yearid=$yid ";

						$table->titles[] = get_string('nameregion', 'block_mou_ege');
						$table->downloadfilename = 'results_region';
		break;
		case 'rayon':
						$strsqlresults .= " WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet}
											ORDER BY schoolid, classid";
						$strsqlschools .= " WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid ";
						$strsqlclasses .= " WHERE yearid=$yid ";

					    $rayon = get_record('monit_rayon', 'id', $rid);
						$table->titles[] = $rayon->name;
						$table->downloadfilename = 'results_rayon_'.$rid;
		break;
		case 'school':
						$strsqlresults .= " WHERE rayonid=$rid AND yearid=$yid AND schoolid=$sid AND codepredmet={$discipline_ege->codepredmet}
									 		ORDER BY classid, userid";
						$strsqlschools .= " WHERE id=$sid ";
						$strsqlclasses .= " WHERE schoolid=$sid AND yearid=$yid ";

					    $rayon = get_record('monit_rayon', 'id', $rid);
					    $school = get_record('monit_school', 'id', $sid);
	                	$table->titles[] = $school->name . " ({$rayon->name})";
						$table->downloadfilename = 'results_school_'.$sid;
		break;
		case 'class':
						$strsqlresults .= " WHERE rayonid=$rid AND yearid=$yid AND schoolid=$sid  AND classid=$gid AND codepredmet={$discipline_ege->codepredmet}
									 		ORDER BY userid";
						$strsqlschools .= " WHERE id=$sid ";
						$strsqlclasses .= " WHERE schoolid=$sid AND yearid=$yid ";

					    $rayon = get_record('monit_rayon', 'id', $rid);
					    $school = get_record('monit_school', 'id', $sid);
					    $class = get_record('monit_school_class', 'id', $gid);
	                	$table->titles[] = $school->name . " ({$rayon->name})";
	                	$table->titles[] = get_string('class', 'block_mou_ege') . ': '. $class->name;
						$table->downloadfilename = 'results_class_'.$gid;
		break;
    }

	$discipline = get_record("monit_school_discipline_mi", 'id', $did);
	$table->titles[] = $discipline->codepredmet . ' - ' . $discipline->name;
    $table->titlesrows = array(30, 30, 30, 30);

	$schoolsarray = array();
 	if ($schools = get_records_sql($strsqlschools))	{
	    foreach ($schools as $sa)  {
	        $schoolsarray[$sa->id] = $sa->code;
	    }
	}

    $classesarray = array();
 	if ($classes = get_records_sql($strsqlclasses))	{
	    foreach ($classes as $class)  {
	        $classesarray[$class->id] = $class->name;
	    }
	}
	// echo $strsqlresults . '<br>';
 	if ($gia_results = get_records_sql($strsqlresults, $page*$perpage, $perpage))	{
 		$i = $page*$perpage + 1;
        foreach ($gia_results as $gia)	{
            $user = get_record_sql("SELECT id, lastname, firstname FROM  {$CFG->prefix}user WHERE id = {$gia->userid}");

            $fieldsname = array ('pp', 'audit', 'variant', 'sidea', 'sideb', 'sidec', 'ball', 'ocenka');
            $fieldsvalue = array ('-', '-', '-', '-', '-', '-', '-', '-');
            foreach ($fieldsname as $fldindex => $fldname)	{
	            if (!empty($gia->{$fldname}))	{
	                $fieldsvalue[$fldindex] = $gia->{$fldname};
	            }
	        }

            $table->data[] = array ($i++ . '.', $schoolsarray[$gia->schoolid], $classesarray[$gia->classid],
            					$fieldsvalue[0], $fieldsvalue[1], $user->lastname, $user->firstname, $fieldsvalue[2],
            					$fieldsvalue[3], $fieldsvalue[4], $fieldsvalue[5], $fieldsvalue[6], $fieldsvalue[7]);
        }

    }  else 	{
    	$table->data[] = array ();
    }
    return $table;
}

function table_diffic ($level, $yid, $did, $rid)
{
	global $CFG;

    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
    	error('Discipline not found!');
    }

    switch ($level)	{
		case 'region':
					$strtitle = get_string('rayon', 'block_monitoring');
		break;
		case 'rayon':
					$strtitle = get_string('school', 'block_monitoring');
		break;
    }

    $table->head  = array (get_string('numbertask','block_mou_ege'), get_string('kolpupilingia', 'block_mou_ege'), get_string('copewithtask', 'block_mou_ege'),
						   get_string('notcopewithtask', 'block_mou_ege'), get_string('notcopewithtaskproc', 'block_mou_ege'));

	$table->align = array ('left',  'center',   'center',  'center',  'center');
    $table->size = array ('30%', '20%', '20%', '20%');
	$table->columnwidth = array (20, 12, 12, 12, 12);
    $table->class = 'moutable';
   	$table->width = '90%';
    $table->titles = array();
    $table->worksheetname = get_string('difficulty', 'block_mou_ege');
    $table->titles[] = $table->worksheetname;
    $table->titlesrows = array(30, 30);


    switch ($level)	{
		case 'region':
						if ($pupils = get_records_sql("SELECT id, sidea, sideb, sidec FROM {$CFG->prefix}monit_mi_results
												       WHERE  yearid=$yid AND codepredmet={$discipline_ege->codepredmet} AND variant <> 0"))	{
							 calc_diffic($pupils, $table);
          				}
						$table->titles[] = get_string('nameregion', 'block_mou_ege');
						$table->downloadfilename = 'difficulty_region';

		break;
		case 'rayon':   $rayon = get_record('monit_rayon', 'id', $rid);
						$i = 1;
						$rid = $rayon->id;
						if ($pupils = get_records_sql("SELECT id, sidea, sideb, sidec FROM {$CFG->prefix}monit_mi_results
												       WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet} AND variant <> 0"))	{
							 calc_diffic($pupils, $table);
          				}
						$table->titles[] = $rayon->name;
						$table->downloadfilename = 'difficulty_rayon_'.$rid;
		break;

    }


    return $table;
}

function table_diffic_sidec($level, $yid, $did, $rid)
{
	global $CFG;

    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
    	error('Discipline not found!');
    }

    switch ($level)	{
		case 'region':
					$strtitle = get_string('rayon', 'block_monitoring');
		break;
		case 'rayon':
					$strtitle = get_string('school', 'block_monitoring');
		break;
    }

	$stringrusballov = get_string('rusballov', 'block_mou_ege');
	$stringrusball = get_string('rusball', 'block_mou_ege');
	$stringrusballa = get_string('rusballa', 'block_mou_ege');
	$strvsegolow = get_string('vsegolow', 'block_mou_ege');


    $table->head  = array (get_string('numbertask','block_mou_ege'), 
	get_string('kolpupilingia', 'block_mou_ege'),
    '"0" ' .  $stringrusballov . "<br>($strvsegolow)", '"0" ' . $stringrusballov . '(%)',
    '"1" ' .  $stringrusball . "<br>($strvsegolow)", '"1" ' . $stringrusball . '(%)',
    '"2" ' .  $stringrusballa . "<br>($strvsegolow)", '"2" ' . $stringrusballa  . '(%)',
    '"3" ' .  $stringrusballa . "<br>($strvsegolow)", '"3" ' . $stringrusballa . '(%)',
    '"4" ' .  $stringrusballa . "<br>($strvsegolow)", '"4" ' . $stringrusballa . '(%)',
    '"5" ' .  $stringrusballa . "<br>($strvsegolow)", '"5" ' . $stringrusballa . '(%)');

	$table->align = array ( 'left',  'center',
						   'center',  'center',  'center',   'center',  'center',  'center',
						   'center', 'center',  'center',  'center',  'center',  'center');
	$table->columnwidth = array (20, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10);
    $table->class = 'moutable';
   	$table->width = '90%';
    $table->titles = array();
    $table->worksheetname = get_string('sidec', 'block_mou_ege');
    $table->titles[] = $table->worksheetname;
    $table->titlesrows = array(30, 30);

    switch ($level)	{
		case 'region':
						if ($pupils = get_records_sql("SELECT id, sidea, sideb, sidec FROM {$CFG->prefix}monit_mi_results
												       WHERE  yearid=$yid AND codepredmet={$discipline_ege->codepredmet} AND variant <> 0"))	{
							 calc_diffic_sidec($pupils, $table);
          				}
						$table->titles[] = get_string('nameregion', 'block_mou_ege');
						$table->downloadfilename = 'difficulty_region_sidec';

		break;
		case 'rayon':   $rayon = get_record('monit_rayon', 'id', $rid);
						$i = 1;
						$rid = $rayon->id;
						if ($pupils = get_records_sql("SELECT id, sidea, sideb, sidec FROM {$CFG->prefix}monit_mi_results
												       WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->codepredmet} AND variant <> 0"))	{
							 calc_diffic_sidec($pupils, $table);
          				}
						$table->titles[] = $rayon->name;
						$table->downloadfilename = 'difficulty_rayon_sidec_'.$rid;
		break;

    }

    return $table;
}

function calc_diffic($pupils, &$table)
{
		$pupilcount = count($pupils);
		$sidenames  = array ('sidea', 'sideb');
		$pupil = current($pupils);
		$countasksidea = $countasksideb = 0;
		if ($tasksidea = explode(',', $pupil->sidea))	{
			$countasksidea = count($tasksidea);
			if ($countasksidea == 1 && empty($tasksidea[0]))	{
				$countasksidea = 0;
			}
		}
		if ($tasksideb = explode(',', $pupil->sideb))	{
			$countasksideb = count($tasksideb);
			if ($countasksideb == 1 && empty($tasksideb[0]))	{
				$countasksideb = 0;
			}
		}

		$sidecounts = array ($countasksidea, $countasksideb);
		$copewithtask = array();
		$notcopewithtask = array();
		foreach ($sidecounts as $key => $sidecount)	{
			for ($i = 0; $i < $sidecount; $i++)	{
				$copewithtask[$sidenames[$key]][$i] = 0;
				$notcopewithtask[$sidenames[$key]][$i] = 0;
			}
		}

		foreach ($sidenames as $index => $side)	{
            $table->data[] = array ('<b>'.get_string($side, 'block_mou_ege').'</b>', '<hr>', '<hr>', '<hr>', '<hr>');
			foreach ($pupils as $pupil)	{
				$taskside = explode(',', $pupil->{$side});
				for ($i = 0; $i < $sidecounts[$index]; $i++)	{
                     if ($taskside[$i] == 0) {
                     	$notcopewithtask[$side][$i]++;
                     } else {
                     	$copewithtask[$side][$i]++;
                     }
				}
			}
			for ($i = 0; $i < $sidecounts[$index]; $i++)	{
				$proc = number_format($notcopewithtask[$side][$i]/$pupilcount*100, 2, ',', '') . '%';
     		    $table->data[] = array (get_string('tasnumber', 'block_mou_ege', ($i+1)),
     		      						$pupilcount, $copewithtask[$side][$i], $notcopewithtask[$side][$i],
     		      						$proc);
 	        }
	    }

}

function calc_diffic_sidec($pupils, &$table)
{
		$pupilcount = count($pupils);
		$pupil = current($pupils);
		$countasksidec = 0;
		if ($tasksidec = explode(',', $pupil->sidec))	{
			$countasksidec = count($tasksidec);
			if ($countasksidec == 1 && empty($tasksidec[0]))	{
				$countasksidec = 0;
			}
		}
		$ocenki = array();
		for ($i = 0; $i < $countasksidec; $i++)	{
			for ($j = 0; $j <= 5; $j++)	{
				$ocenki[$i][$j] = 0;
			}
		}
		foreach ($pupils as $pupil)	{
			$taskside = explode(',', $pupil->sidec);
			for ($i = 0; $i < $countasksidec; $i++)	{
				$ocenki[$i][$taskside[$i]]++;
			}
		}
		for ($i = 0; $i < $countasksidec; $i++)	{
			$data = array();
			$data[] = get_string('tasnumber', 'block_mou_ege', ($i+1));
			$data[] = $pupilcount;
			for ($j = 0; $j <= 5; $j++)	{
				$data[] = $ocenki[$i][$j];
				$data[] = number_format($ocenki[$i][$j]/$pupilcount*100, 2, ',', '') . '%';
			}
			$table->data[] = $data;
        }
}


function table_statparallel ($level, $yid, $rid, $pid)
{
	global $CFG, $rayon;

    $table->head  = array (get_string('number','block_monitoring'),  get_string('school', 'block_monitoring'),
    						get_string('classes','block_mou_ege'),  get_string('numberpupils', 'block_mou_ege'));
    
	$table->align = array ("left", "left", 'center', 'center');
   	$table->columnwidth = array (5, 58, 9, 13);
    $table->class = 'moutable';
   	$table->width = '70%';
    $table->size = array ('5%', '45%', '10%', '10%');

    $table->titles[] = get_string('statparallel', 'block_mou_ege');
    $table->titlesrows = array(30);
    $table->worksheetname = 'statparallel';
	$table->downloadfilename = 'statparallel_'.$rid .'_'.$pid;

	 
	$strsql =  "SELECT id, rayonid, name, number  FROM {$CFG->prefix}monit_school
	   				WHERE rayonid = $rid AND isclosing=0 AND yearid=$yid
	   				ORDER BY number";
/*
 	if ($schools = get_records_sql($strsql))	{
 			$i = 1;
			foreach ($schools as $school)  {
				$strlasses = $strcount = '';
				$classes = get_records_sql ("SELECT * FROM {$CFG->prefix}monit_school_class
											  WHERE schoolid={$school->id} AND yearid=$yid  AND name like '$pid%'
											  ORDER BY name");
				if ($classes)	{
					foreach ($classes as $class)  {
						$strlasses .= $class->name. '<br>';
						$pupils = get_records_sql ("SELECT id 
												  	FROM {$CFG->prefix}monit_school_pupil_card
												  	WHERE classid={$class->id} AND schoolid={$school->id} AND deleted=0");
						    if ($pupils)	{
						    	$strcount .= count($pupils) . '<br>';
						    }
						}
				}
				$table->data[] = array ($i++.'.', $school->name, $strlasses, $strcount);				
			}  	

	}	
*/

 	if ($schools = get_records_sql($strsql))	{
 			$i = 1;
			foreach ($schools as $school)  {
				$flag = true;
				$strlasses = $strcount = '';
				$classes = get_records_sql ("SELECT * FROM {$CFG->prefix}monit_school_class
											  WHERE schoolid={$school->id} AND yearid=$yid  AND name like '$pid%'
											  ORDER BY name");
				if ($classes)	{
					
					foreach ($classes as $class)  {
						$strlasses = $class->name;//. '<br>';
						$pupils = get_records_sql ("SELECT id 
												  	FROM {$CFG->prefix}monit_school_pupil_card
												  	WHERE classid={$class->id} AND schoolid={$school->id} AND deleted=0");
													  	
					
						if ($pupils)	{
					    	$strcount = count($pupils);
					    }	
					
						if (!$flag) $school->name = '';
						
						$table->data[] = array ($i++.'.', $school->name, $strlasses, $strcount);	
							
						$flag = false;
					}
					
				} else {
					$table->data[] = array ($i++.'.', $school->name, $strlasses, $strcount);
				}
								
			}  	

	}
	
	return $table;
}


function listbox_reports_mi($scriptname, $yid, $rpid)
{
	global $CFG, $admin_is,	$region_operator_is, $rayon_operator_is, $school_operator_is;
 	$reportmenu = array();
 	$reportmenu[0] = get_string('selecttypeofreport', 'block_mou_school') . '...';
	 	 
	if ($admin_is || $region_operator_is)  {
        $reportmenu[1] = get_string('resultclassmi', 'block_mou_ege');
        $reportmenu[2] = get_string('resultschoolmi', 'block_mou_ege');      
        $reportmenu[3] = get_string('resultrayonmi', 'block_mou_ege');
        $reportmenu[4] = get_string('resultregionmi', 'block_mou_ege');      
        $reportmenu[5] = get_string('pupilnomarks', 'block_mou_ege');
        $reportmenu[6] = get_string('statmarkbyrayonsmi', 'block_mou_ege');     
        $reportmenu[7] = get_string('statballbyrayonsmi', 'block_mou_ege');
        $reportmenu[8] = get_string('statmarkbyoblmi', 'block_mou_ege');   
        $reportmenu[9] = get_string('statballbyoblmi', 'block_mou_ege');  
        $reportmenu[10] = get_string('difficultyrayon', 'block_mou_ege');
        $reportmenu[11] = get_string('difficultyregion', 'block_mou_ege');
        $reportmenu[12] = get_string('statparallel', 'block_mou_ege');
	} else if ($rayon_operator_is){
		$reportmenu[1] = get_string('resultclassmi', 'block_mou_ege');
        $reportmenu[2] = get_string('resultschoolmi', 'block_mou_ege');      
        $reportmenu[3] = get_string('resultrayonmi', 'block_mou_ege');  
        $reportmenu[5] = get_string('pupilnomarks', 'block_mou_ege');
        $reportmenu[6] = get_string('statmarkbyrayonsmi', 'block_mou_ege');     
        $reportmenu[7] = get_string('statballbyrayonsmi', 'block_mou_ege');
        $reportmenu[10] = get_string('difficultyrayon', 'block_mou_ege');
	} else if ($school_operator_is) {
		$reportmenu[1] = get_string('resultclassmi', 'block_mou_ege');
        $reportmenu[2] = get_string('resultschoolmi', 'block_mou_ege');      
	} 
		

	echo '<tr><td>'.get_string('typeofreport', 'block_mou_school').':</td><td>';
	popup_form($scriptname, $reportmenu, "switchreport", $rpid, "", "", "", false);
	echo '</td></tr>';	
	return 1;
}	

?>