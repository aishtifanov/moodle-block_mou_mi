<?php  // $Id: tabsclasses.php,v 1.7 2009/04/21 11:23:36 Shtifanov Exp $
/// This file to be included so we can assume config.php has already been included.
/// We also assume that $user, $course, $currenttab have been set


    if (empty($currenttab)) {
        error('You cannot call this script in that way');
    }

    $toprow = array();
    $toprow[] = new tabobject('listclasses', $CFG->wwwroot."/blocks/mou_mi/class/classlist.php?rid=$rid&amp;yid=$yid&amp;sid=$sid",
                get_string('listclasses', 'block_mou_ege'));
                
    $toprow[] = new tabobject('listclass', $CFG->wwwroot."/blocks/mou_mi/class/classpupils.php?rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;gid=$gid",
                get_string('classpupils', 'block_mou_ege'));

    $toprow[] = new tabobject('enrolclass', $CFG->wwwroot."/blocks/mou_mi/class/enrolclass_mi.php?rid=$rid&amp;yid=$yid&amp;sid=$sid&amp;gid=$gid",
                get_string('enrolclass_mi', 'block_mou_ege'));
                

    $toprow[] = new tabobject('mi_teachers', $CFG->wwwroot."/blocks/mou_mi/class/gia_teachers.php?sid=$sid&amp;rid=$rid&amp;yid=$yid",
    	            get_string('mi_teachers', 'block_mou_ege'));

    $toprow[] = new tabobject('mi_textbook', $CFG->wwwroot."/blocks/mou_mi/class/gia_textbooks.php?sid=$sid&amp;rid=$rid&amp;yid=$yid",
    	            get_string('mi_textbook', 'block_mou_ege'));

    $toprow[] = new tabobject('importclass', $CFG->wwwroot."/blocks/mou_mi/class/importclasses.php?sid=$sid&amp;rid=$rid&amp;yid=$yid",
	   	            get_string('importclass0', 'block_mou_ege'));


    $tabs = array($toprow);

    print_tabs($tabs, $currenttab, NULL, NULL);

?>