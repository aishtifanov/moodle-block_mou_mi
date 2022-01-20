<?php // $Id: block_mou_mi.php,v 1.3 2009/10/30 12:26:06 Shtifanov Exp $


class block_mou_mi extends block_list {

    function init() {
        $this->title = get_string('title_mi', 'block_mou_ege');
        $this->version = 2008120500;
    }

    function get_content() {
        global $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (empty($this->instance)) {
            $this->content = '';
        } else {
            $this->load_content();
        }

        return $this->content;
        }

    function load_content() {
        global $CFG, $yearmonit;

		$yid = 4; // $yearmonit;        


		$admin_is = isadmin();
		$staff_operator_is = ismonitoperator('staff');
		$region_operator_is = ismonitoperator('region');
		$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
		if  (!$admin_is && !$region_operator_is && $rayon_operator_is) 	{
			$rid = $rayon_operator_is;
		}	else {
			$rid = 0;
		}
		$sid = ismonitoperator('school', 0, 0, 0, true);
		$college_operator_is = ismonitoperator('college', 0, 0, 0, true);

		$staffview_operator = isstaffviewoperator();

		if ($admin_is  || $region_operator_is || $rayon_operator_is)	 {

			$this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/mi/disciplines_mi.php?mode=1&amp;rid=$rid&amp;sid=0&amp;cid=0&amp;yid=$yid\">".get_string('disciplines_mi_short','block_mou_ege').'</a>';
 	        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/ege.gif" height="16" width="16" alt="" />';

			$this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/class/classlist.php?mode=1&amp;rid=$rid&amp;sid=0&amp;cid=0&amp;yid=$yid\">".get_string('classes_mi_short','block_mou_ege').'</a>';
 	        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/classes2.gif" height="16" width="16" alt="" />';
/*
			$this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/class/classpupils.php?mode=1&amp;rid=$rid&amp;sid=0&amp;cid=0&amp;gid=0&amp;yid=$yid\">".get_string('class','block_mou_ege').'</a>';
 	        $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/group.gif" height="16" width="16" alt="" />';

        	$this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/pupil.php?mode=1&amp;rid=$rid&amp;sid=0&amp;cid=0&amp;gid=0&amp;yid=$yid\">".get_string('pupil','block_mou_ege').'</a>';
        	$this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/guest.gif" height="16" width="16" alt="" />';

	        $this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/mou_mi/pupils/searchpupil.php">'.get_string('searchpupil', 'block_mou_ege').'</a>';
	        $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/search.gif" height="16" width="16" alt="" />';

	        $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/markspupil.php?rid=$rid&amp;sid=0&amp;yid=$yid\">".get_string('markspupil', 'block_mou_ege').'</a>';
	        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/journal.gif" height="16" width="16" alt="" />';

	        $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/statsmarkspupil.php?rid=$rid&amp;sid=0&amp;yid=$yid\">".get_string('statsmarkspupil', 'block_mou_ege').'</a>';
	        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/journal.gif" height="16" width="16" alt="" />';

	        $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/difficulty.php?rid=$rid&amp;sid=0&amp;yid=$yid\">".get_string('difficulty', 'block_mou_ege').'</a>';
	        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/journal.gif" height="16" width="16" alt="" />';
*/
	        $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/textbook/textbook.php?rid=$rid&amp;sid=0\">".get_string('textbooks', 'block_mou_ege').'</a>';
	        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/textbooks.gif" height="16" width="16" alt="" />';
	        

			if ($admin_is  || $region_operator_is)	{ 
			        $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/importmarks.php?rid=$rid&amp;sid=0\">".get_string('markspupil', 'block_mou_ege').'</a>';
			        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/journal.gif" height="16" width="16" alt="" />';
			 }
			        
			$this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/mi/report.php?rid=$rid&amp;sid=0\">".get_string('report', 'block_mou_ege').'</a>';
   	    	$this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/journal.gif" height="16" width="16" alt="" />';


		    $this->content->footer = '<a href="'.$CFG->wwwroot.'/blocks/mou_mi/index.php">'.get_string('title_mi', 'block_mou_ege').'</a>'.' ...';
	    }

		if (!$admin_is && !$region_operator_is && !$rayon_operator_is && $sid) {
	       if ($school = get_record('monit_school', 'id', $sid)) {
			    $rid = $school->rayonid;
			    
			$this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/mi/disciplines_mi.php?mode=1&amp;rid=$rid&amp;sid=$sid&amp;cid=0&amp;yid=$yid\">".get_string('disciplines_mi_short','block_mou_ege').'</a>';
 	        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/ege.gif" height="16" width="16" alt="" />';

			$this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/class/classlist.php?mode=1&amp;rid=$rid&amp;sid=$sid&amp;cid=0&amp;yid=$yid\">".get_string('classes_mi_short','block_mou_ege').'</a>';
 	        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/classes2.gif" height="16" width="16" alt="" />';
 	        
	        $this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/textbook/textbook.php?rid=$rid&amp;sid=$sid\">".get_string('textbooks', 'block_mou_ege').'</a>';
	        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/textbooks.gif" height="16" width="16" alt="" />';

			$this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/mi/report.php?rid=$rid&amp;sid=$sid\">".get_string('report', 'block_mou_ege').'</a>';
	        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/textbooks.gif" height="16" width="16" alt="" />';
			    
/*
				$this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/class/classlist.php?mode=1&amp;rid=$rid&amp;sid=$sid&amp;cid=0&amp;yid=$yid\">".get_string('school','block_monitoring').'</a>';
	 	        $this->content->icons[] = '<img src="'.$CFG->wwwroot.'/blocks/mou_mi/i/groups.gif" height="16" width="16" alt="" />';

				$this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/class/classpupils.php?mode=1&amp;rid=$rid&amp;sid=$sid&amp;cid=0&amp;gid=0&amp;yid=$yid\">".get_string('class','block_mou_ege').'</a>';
	 	        $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/group.gif" height="16" width="16" alt="" />';

	        	$this->content->items[] = '<a href="'.$CFG->wwwroot."/blocks/mou_mi/pupils/pupil.php?mode=1&amp;rid=$rid&amp;sid=$sid&amp;cid=0&amp;gid=0&amp;yid=$yid\">".get_string('pupil','block_mou_ege').'</a>';
	        	$this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/guest.gif" height="16" width="16" alt="" />';
*/

			    $this->content->footer = '<a href="'.$CFG->wwwroot.'/blocks/mou_mi/index.php">'.get_string('title_mi', 'block_mou_ege').'</a>'.' ...';
			    
			    
			}
		}

    }
  }
 ?>