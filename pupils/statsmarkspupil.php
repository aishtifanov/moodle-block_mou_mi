<?PHP // $Id: statsmarkspupil.php,v 1.7 2009/06/17 07:43:59 Shtifanov Exp $

    require_once('../../../config.php');
    require_once('../../monitoring/lib.php');
    require_once('../../mou_ege/lib_ege.php');

    $yid = optional_param('yid', 0, PARAM_INT);       // Year id
	$rid = optional_param('rid', 0, PARAM_INT);
	$sid = optional_param('sid', 0, PARAM_INT);
	$gid = optional_param('gid', 0, PARAM_INT);
    $did = optional_param('did', 0, PARAM_INT);       // Discipline id
	$uid = optional_param('uid', 0, PARAM_INT);
   	$level = optional_param('level', 'rayon');
   	$tab = optional_param('tab', 'ocenka');
	$action   = optional_param('action', '');

    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }


    switch ($level)	{
		case 'region':
		break;
		case 'rayon': $rid = required_param('rid', PARAM_INT);       // Rayon id
		break;
    }

	$action   = optional_param('action', '');
    if ($action == 'excel') {
		$table = table_statsmarkspupil ($level, $yid, $did, $rid, $tab);
    	// print_r($table);
        print_table_to_excel($table);
        exit();
	}

	if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

	require_login();

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
	}

	// $strclasses = get_string('classes','block_mou_ege');
	$strclasses = get_string('school','block_monitoring');
	$strmarks = get_string('statsmarkspupil','block_mou_ege');

	$breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/mou_mi/index.php">'.get_string('title_mi','block_mou_ege').'</a>';
	$breadcrumbs .= " -> $strmarks";
    print_header_mou("$site->shortname: $strmarks", $site->fullname, $breadcrumbs);

	print_tabs_years($yid, "statsmarkspupil.php?rid=$rid&amp;yid=");

    $currenttab = 'statsmarks'.$level;
    $toprow = array();
    $toprow[] = new tabobject('statsmarksrayon', "statsmarkspupil.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;tab=$tab",
	               get_string('statsmarksrayon', 'block_mou_ege'));
	if ($admin_is || $region_operator_is) {
	    $toprow[] = new tabobject('statsmarksregion', "statsmarkspupil.php?level=region&amp;yid=$yid&amp;tab=$tab",
 	               get_string('statsmarksregion', 'block_mou_ege'));
	}
    $tabs = array($toprow);
    print_tabs($tabs, $currenttab, NULL, NULL);

    $currenttab = 'statsmarks'.$tab;
    $toprow = array();
    $toprow[] = new tabobject('statsmarksocenka', "statsmarkspupil.php?level=$level&amp;rid=$rid&amp;yid=$yid&amp;tab=ocenka",
	               get_string('statsmarksocenka', 'block_mou_ege'));
    $toprow[] = new tabobject('statsmarksball', "statsmarkspupil.php?level=$level&amp;rid=$rid&amp;yid=$yid&amp;tab=ball",
 	               get_string('statsmarksball', 'block_mou_ege'));
    $tabs = array($toprow);
    print_tabs($tabs, $currenttab, NULL, NULL);


    switch ($level)	{
		case 'region':  if ($admin_is || $region_operator_is) 	{
      						echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
							listbox_discipline_ege("statsmarkspupil.php?level=region&amp;yid=$yid&amp;tab=$tab&amp;did=", $rid, 0, $yid, $did);
							echo '</table>';

						 	if ($did != 0)  {

							    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
							    	error('Discipline not found!');
							    }

								$table = table_statsmarkspupil($level, $yid, $did, 0, $tab);

								print_color_table($table);

						   		$options = array('rid' => $rid, 'sid' => 0, 'did' => $did, 'yid' => $yid, 'level' => 'region', 'action' => 'excel', 'tab' => $tab);
								echo '<table align="center" border=0><tr><td>';
							    print_single_button("statsmarkspupil.php", $options, get_string("downloadexcel"));
	   							echo '</td></tr></table>';
						    }
						}
		break;

		case 'rayon':   if ($admin_is || $region_operator_is) 	{
							echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
							listbox_rayons("statsmarkspupil.php?level=rayon&amp;yid=$yid&amp;tab=$tab&amp;rid=", $rid);
							listbox_discipline_ege("statsmarkspupil.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;tab=$tab&amp;did=", $rid, $sid, $yid, $did);
							echo '</table>';
						} else  if ($rayon_operator_is)  {
							echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
							listbox_discipline_ege("statsmarkspupil.php?level=rayon&amp;rid=$rid&amp;yid=$yid&amp;tab=$tab&amp;did=", $rid, $sid, $yid, $did);
							echo '</table>';
						}


					 	if ($rid != 0 && $did != 0)  {

						    if (!$discipline_ege = get_record('monit_school_discipline_mi', 'id', $did))	{
						    	error('Discipline not found!');
						    }

						    $nowtime = time();
						    if ($nowtime < $discipline_ege->timepublish && !$admin_is && !$region_operator_is)	{
						            $t = date ("d.m.Y H:i", $discipline_ege->timepublish);
	  							    print_heading(get_string('timewillbepublish','block_mou_ege', $t), 'center', 4);
						    } else {

								$table = table_statsmarkspupil($level, $yid, $did, $rid, $tab);

								print_color_table($table);

						   		$options = array('rid' => $rid, 'sid' => $sid, 'did' => $did, 'yid' => $yid, 'level' => 'rayon', 'action' => 'excel', 'tab' => $tab);
								echo '<table align="center" border=0><tr><td>';
							    print_single_button("statsmarkspupil.php", $options, get_string("downloadexcel"));
	   							echo '</td></tr></table>';
	   						}
					    }
		break;
    }

	print_footer();


function table_statsmarkspupil($level, $yid, $did, $rid, $tab = 'ocenka')
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
	    $table->head  = array ('№', $strtitle,
							   get_string('kolpupilingia', 'block_mou_ege'), get_string('avgball', 'block_mou_ege'),
							   get_string('avgocenka', 'block_mou_ege'), 'Оценка "5" <br>(всего)', 'Оценка "5" (%)',
							   'Оценка "4" <br>(всего)', 'Оценка "4" (%)', 'Оценка "3"<br> (всего)', 'Оценка "3" (%)',
							   'Оценка "2"<br> (всего)', 'Оценка "2" (%)');

		$table->align = array ('center', 'left',  'center',   'center',  'center',  'center',   'center',
							   'center', 'center',  'center',  'center',  'center',  'center', 'center');
		$table->columnwidth = array (3, 20, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10);
	    // $table->datatype = array ('char', 'char');
	    $table->class = 'moutable';
	   	$table->width = '90%';
	    // $table->size = array ('10%', '10%');
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
								$pupilcount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_gia_results
																WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->code} AND variant <> 0");
								$allpupilcount += $pupilcount;
								$sumball = get_record_sql("select sum(ball) as sumball from  {$CFG->prefix}monit_gia_results
														  where rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->code}");
								$allsumball += $sumball->sumball;
								if ($pupilcount == 0)	 {
	                                $avgball = '-';
								} else {
									$avgball = number_format($sumball->sumball/$pupilcount, 3, ',', '');
								}
								$sumocenka = get_record_sql("select sum(ocenka) as sumocenka from  {$CFG->prefix}monit_gia_results
														  where rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->code}");
								$allsumocenka += $sumocenka->sumocenka;
								if ($pupilcount == 0)	 {
	                                $avgocenka = '-';
								} else {
									$avgocenka = number_format($sumocenka->sumocenka/$pupilcount, 3, ',', '');
								}

								$kolocenki = array ('0', '0', '0', '0');
								$kolocenkiproc = array ('0', '0', '0', '0');
								foreach ($ocenki as $index => $ocenka)	{
								      $kolocenki[$index] = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_gia_results
																			  WHERE rayonid=$rid AND yearid=$yid AND
																			  codepredmet={$discipline_ege->code} and ocenka=$ocenka");
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
								$pupilcount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_gia_results
																WHERE schoolid=$sid AND yearid=$yid AND codepredmet={$discipline_ege->code} AND variant <> 0");
								$allpupilcount += $pupilcount;
								$sumball = get_record_sql("select sum(ball) as sumball from  {$CFG->prefix}monit_gia_results
														  where schoolid=$sid AND yearid=$yid AND codepredmet={$discipline_ege->code}");
								$allsumball += $sumball->sumball;
								if ($pupilcount == 0)	 {
	                                $avgball = '-';
								} else {
									$avgball = number_format($sumball->sumball/$pupilcount, 3, ',', '');
								}
								$sumocenka = get_record_sql("select sum(ocenka) as sumocenka from  {$CFG->prefix}monit_gia_results
														  where schoolid=$sid AND yearid=$yid AND codepredmet={$discipline_ege->code}");
								$allsumocenka += $sumocenka->sumocenka;
								if ($pupilcount == 0)	 {
	                                $avgocenka = '-';
								} else {
									$avgocenka = number_format($sumocenka->sumocenka/$pupilcount, 3, ',', '');
								}

								$kolocenki = array ('0', '0', '0', '0');
								$kolocenkiproc = array ('0', '0', '0', '0');
								foreach ($ocenki as $index => $ocenka)	{
								      $kolocenki[$index] = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_gia_results
																			  WHERE schoolid=$sid AND yearid=$yid AND
																			  codepredmet={$discipline_ege->code} and ocenka=$ocenka");
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
	    $table->head  = array ('№', get_string('ball', 'block_mou_ege'), 'Количество учеников', 'Процент от общего количества');

		$table->align = array ('center', 'center',  'center',   'center');
		$table->columnwidth = array (3, 10, 10, 10);
	    // $table->datatype = array ('char', 'char');
	    $table->class = 'moutable';
	   	$table->width = '50%';
	    $table->size = array ('5%', '10%', '10%', '10%');
	    $table->titles = array();
	    $table->titles[] = get_string('resultgiapopredmetu', 'block_mou_ege', $discipline_ege->name);
	    $table->titlesrows = array(30, 30);
	    $table->worksheetname = 'statballs';

		// $balls = array ('5', '4', '3', '2');
	    switch ($level)	{
			case 'region':  // $rayons = get_records('monit_rayon');
							$i = 1;
							$allpupilcount = 0;
							if ($balls = get_records_sql("SELECT DISTINCT ball FROM {$CFG->prefix}monit_gia_results
													WHERE yearid=$yid AND codepredmet={$discipline_ege->code} AND variant <> 0
													ORDER by ball"))	{

								$allkolballs  = array ();
								$allkolballsproc = array ();
								foreach ($balls as $index => $ball)		{
									$allkolballs[$index] = 0;
									$allkolballsproc[$index] = 0;
								}
        /*
								foreach ($rayons as $rayon)	{
									$rid = $rayon->id;
									$pupilcount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_gia_results
																WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->code} AND variant <> 0");
									$allpupilcount += $pupilcount;

									$kolballs = array ();
									$kolballsproc = array ();
									foreach ($balls as $index => $ball)	{
									      $kolballs[$index] = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_gia_results
																				  WHERE rayonid=$rid AND yearid=$yid AND
																				  codepredmet={$discipline_ege->code} and ball={$ball->ball}");
										  $allkolballs[$index] += $kolballs[$index];
										  if ($pupilcount == 0)	 {
											  $kolballsproc[$index] = '-';
										  } else {
											  $kolballsproc[$index] = number_format($kolballs[$index]/$pupilcount*100, 2, ',', '') . '%';
										  }
			 				              $table->data[] = array ($i++ . '.', $ball->ball, $kolballs[$index], $kolballsproc[$index]);
		            				}
								}
*/
								$pupilcount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_gia_results
															WHERE yearid=$yid AND codepredmet={$discipline_ege->code} AND variant <> 0");
								$allpupilcount += $pupilcount;

								foreach ($balls as $index => $ball)	{
								      $kolballs[$index] = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_gia_results
																			  WHERE yearid=$yid AND codepredmet={$discipline_ege->code} and ball={$ball->ball}");
									  $allkolballs[$index] += $kolballs[$index];
									  if ($pupilcount == 0)	 {
										  $kolballsproc[$index] = '-';
									  } else {
										  $kolballsproc[$index] = number_format($kolballs[$index]/$pupilcount*100, 2, ',', '') . '%';
									  }
		 				              $table->data[] = array ($i++ . '.', $ball->ball, $kolballs[$index], $kolballsproc[$index]);
	            				}

							}

                            /*
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
								if ($pupilcount == 0)	{
									  $allkolocenkiproc[$index] = '-';
								} else {
									  $allkolocenkiproc[$index] = number_format($ak/$allpupilcount*100, 2, ',', '') . '%';
								}
							}
				            $table->data[] = array ('', '<b>'. get_string('itogo'.$level, 'block_mou_ege') . '</b>', '<b>'. $allpupilcount . '</b>', '<b>'. $avgball . '</b>', '<b>'. $avgocenka . '</b>',
	            										'<b>'. $allkolocenki[0] . '</b>', '<b>'. $allkolocenkiproc[0] . '</b>', '<b>'. $allkolocenki[1] . '</b>', '<b>'. $allkolocenkiproc[1] . '</b>',
	            										'<b>'. $allkolocenki[2] . '</b>', '<b>'. $allkolocenkiproc[2] . '</b>', '<b>'. $allkolocenki[3] . '</b>', '<b>'. $allkolocenkiproc[3] . '</b>');
                            */
							$table->titles[] = get_string('nameregion', 'block_mou_ege');
							$table->downloadfilename = 'statsballsregion';
			break;
			case 'rayon':   $rayon = get_record('monit_rayon', 'id', $rid);

							$i = 1;
							$allpupilcount = 0;
							if ($balls = get_records_sql("SELECT DISTINCT ball FROM {$CFG->prefix}monit_gia_results
													WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->code} AND variant <> 0
													ORDER by ball"))	{

								$allkolballs  = array ();
								$allkolballsproc = array ();
								foreach ($balls as $index => $ball)		{
									$allkolballs[$index] = 0;
									$allkolballsproc[$index] = 0;
								}

								$pupilcount = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_gia_results
															WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->code} AND variant <> 0");
								$allpupilcount += $pupilcount;

								foreach ($balls as $index => $ball)	{
								      $kolballs[$index] = count_records_sql("SELECT COUNT(*) FROM {$CFG->prefix}monit_gia_results
																			  WHERE rayonid=$rid AND yearid=$yid AND codepredmet={$discipline_ege->code} and ball={$ball->ball}");
									  $allkolballs[$index] += $kolballs[$index];
									  if ($pupilcount == 0)	 {
										  $kolballsproc[$index] = '-';
									  } else {
										  $kolballsproc[$index] = number_format($kolballs[$index]/$pupilcount*100, 2, ',', '') . '%';
									  }
		 				              $table->data[] = array ($i++ . '.', $ball->ball, $kolballs[$index], $kolballsproc[$index]);
	            				}

							}
/*
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
								if ($pupilcount == 0)	{
									  $allkolocenkiproc[$index] = '-';
								} else {
									  $allkolocenkiproc[$index] = number_format($ak/$allpupilcount*100, 2, ',', '') . '%';
								}
							}
*/
				            $table->data[] = array ('', '<b>'. get_string('itogo'.$level, 'block_mou_ege') . '</b>',
				            							'<b>'. $allpupilcount . '</b>', '<b>100%</b>');

							$table->titles[] = $rayon->name;
							$table->downloadfilename = 'statsballs_rayon_'.$rid;
			break;
	    }


	}


    return $table;
}



?>