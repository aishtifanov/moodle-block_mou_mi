<?PHP // $Id: disciplines_mi.php,v 1.13 2009/09/17 10:02:19 Shtifanov Exp $

    require_once('../../../config.php');
    require_once('../../monitoring/lib.php');
    require_once('../../mou_ege/lib_ege.php');

    $rid = optional_param('rid', '0', PARAM_INT);       // Rayon id
    $yid = optional_param('yid', '0', PARAM_INT);       // Year id
    $sid = optional_param('sid', '0', PARAM_INT);       // School id
    
    if ($yid == 0)	{
    	$yid = get_current_edu_year_id();
    }

	$action   = optional_param('action', '');
    if ($action == 'excel') 	{
		$table = table_discipline_mi($yid);
		print_table_to_excel($table, 1);
        exit();
	}

	if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
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
	      if ($school = get_record_sql ("SELECT id, uniqueconstcode, name FROM {$CFG->prefix}monit_school
	                                     WHERE rayonid=$rid AND uniqueconstcode=$sid AND yearid=$yid"))	{
	     		$sid = $school->id;
	      }
	}

    $strdisciplines = get_string('disciplines_mi', 'block_mou_ege');

	$breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/mou_mi/index.php">'.get_string('title_mi','block_mou_ege').'</a>';
	$breadcrumbs .= "-> $strdisciplines";
	print_header_mou("$site->shortname: $strdisciplines", $site->fullname, $breadcrumbs);

	print_tabs_years_link("disciplines_mi.php?", $rid, 0, $yid);

    $currenttab = 'disciplines_mi';
    include('tabsege.php');

	$table = table_discipline_mi($yid);

	// echo "<hr />";
	// print_heading($strdisciplines, "center", 4);
	// print_heading(get_string("disciplinesterm","block_mou_ege"), "center", 4);
    print_heading($strdisciplines, "center", 4);

    print_color_table($table);

	if 	($admin_is || $region_operator_is && !isregionviewoperator())  {

?>
<table align="center">
	<tr>
	<td>
  <form name="adddiscipl" method="post" action="<?php echo "addiscipline.php?mode=new&amp;yid=$yid"; ?>">
	    <div align="center">
		<input type="submit" name="adddiscipline" value="<?php print_string('addiscipline','block_mou_ege')?>">
	    </div>
  </form>
  </td>
	<td>
	<form name="download" method="post" action="<?php echo "disciplines_mi.php?action=excel&amp;yid=$yid" ?>">
	    <div align="center">
		<input type="submit" name="downloadexcel" value="<?php print_string("downloadexcel")?>">
	    </div>
  </form>
	</td>
	</tr>
  </table>
<?php

	}

    print_footer();



function table_discipline_mi($yid)
{
    global $CFG, $admin_is, $region_operator_is;

    $table->head  = array (get_string('codepredmet','block_mou_ege'),  get_string('disciplinename','block_mou_ege'), get_string('parallel','block_mou_ege'),
	 get_string('midates','block_mou_ege'), get_string('action','block_mou_ege'));
    $table->align = array ("center",  "left", "center", "left", "center");
    $table->class = 'moutable';
  	$table->width = '60%';
    $table->size = array ('10%', '15%', '10%', '20%', '10%');
	$table->columnwidth = array (17, 17, 17,  20, 10);
    $table->titles = array();
    $table->titles[] = get_string('disciplines_mi', 'block_mou_ege');
    $table->worksheetname = get_string('disciplines_mi', 'block_mou_ege');
    $table->titlesrows = array(30);
    $table->downloadfilename = 'disciplines_mi';

//	$currcourse = get_records ('school_discipline', 'curriculumid', $cid);
	$disciplines =  get_records_sql ("SELECT id, yearid, parallelnum, codepredmet, name
									  FROM  {$CFG->prefix}monit_school_discipline_mi
									  WHERE yearid=$yid
									  ORDER BY name");

	$i = 0;
	if ($disciplines)	{
		foreach ($disciplines as $discipline) {

			$strdates = '';

			if ($giadates =  get_records_sql ("SELECT id, date_gia FROM  {$CFG->prefix}monit_school_gia_dates
											  WHERE yearid=$yid AND discmiid={$discipline->id} ORDER BY date_gia"))  {
				foreach ($giadates as $giadate)	{
					 $strdates .= convert_date($giadate->date_gia, 'en', 'ru') . ', ';
				}

				$strdates = substr($strdates, 0, strlen($strdates)- 2);
			}


			if 	($admin_is || $region_operator_is)	 {
				$title = get_string('editdiscipline','block_mou_ege');
				$strlinkupdate = "<a title=\"$title\" href=\"addiscipline.php?mode=edit&amp;yid=$yid&amp;did={$discipline->id}\">";
				$strlinkupdate = $strlinkupdate . "<img src=\"{$CFG->pixpath}/i/edit.gif\" alt=\"$title\" /></a>&nbsp;";
				$title = get_string('deletingdiscipline','block_mou_ege');
		  	 	$strlinkupdate = $strlinkupdate . "<a title=\"$title\" href=\"deldiscipline.php?yid=$yid&amp;did={$discipline->id}\">";
				$strlinkupdate = $strlinkupdate . "<img src=\"{$CFG->pixpath}/i/cross_red_big.gif\" alt=\"$title\" /></a>&nbsp;";
			}
			else	{
				$strlinkupdate = '-';
			}

			$i++;
			$table->data[] = array ($discipline->codepredmet, $discipline->name, $discipline->parallelnum,  $strdates, $strlinkupdate);
		}
	}

	return $table;
}

?>