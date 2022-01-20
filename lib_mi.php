<?php // $Id: lib_mi.php,v 1.2 2009/10/29 11:05:35 Oleg Exp $

// Display list discipline monitorings investigation as popup_form
function listbox_discipline_mi($scriptname, $rid, $sid, $yid, $did, $except = '0')
{
  global $CFG;

  $strtitle = get_string('selectdiscipline_mi', 'block_mou_ege') . '...';
  $disciplinemenu = array();

  $disciplinemenu[0] = $strtitle;

  if ($yid != 0)  {

		$disciplines =  get_records_sql ("SELECT id, yearid, name  FROM  {$CFG->prefix}monit_school_discipline_mi
										  WHERE yearid=$yid AND id NOT IN ($except)
										  ORDER BY name");
		if ($disciplines)	{
			foreach ($disciplines as $discipline) 	{
				$disciplinemenu[$discipline->id] = $discipline->name;
			}
		}
  }

  echo '<tr><td>'.get_string('discipline_mi','block_mou_ege').':</td><td>';
  popup_form($scriptname, $disciplinemenu, "switchdiscege", $did, "", "", "", false);
  echo '</td></tr>';
  return 1;
}

function get_list_discipline_mi($arrayegeids, $stregeids)
{
   	$list_disc = '';
    if (!empty($stregeids))	{
    	$pli = explode(',', $stregeids);
    	foreach ($pli as $pli1)	{
    		if ($pli1 > 0)	{
	    		$list_disc .= $arrayegeids[$pli1] . ', ';
	    	}
    	}
    	if ($list_disc != '')  {
    		$list_disc = substr($list_disc, 0, strlen($list_disc)- 2);
    	}

    }
    if ($list_disc == '')  $list_disc = '-';

	return $list_disc;
}



?>