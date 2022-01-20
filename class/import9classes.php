<?php // $Id: import9classes.php,v 1.2 2009/11/12 10:48:45 Shtifanov Exp $

    require_once("../../../config.php");
    require_once('../../monitoring/lib.php');
    require_once('../../mou_att/lib_att.php');
    require_once('../../mou_ege/lib_ege.php');
	require_once($CFG->dirroot.'/lib/uploadlib.php');


    $rid = required_param('rid', PARAM_INT);          // Rayon id
    $sid = required_param('sid', PARAM_INT);       // School id
	// $cid = required_param('cid', PARAM_INT);		  // Curriculum id
    // $yid = required_param('yid', PARAM_INT);       // Year id

	define('ROLE_PUPIL', 5);

    $yid = get_current_edu_year_id();

	require_login();

	$admin_is = isadmin();
	$region_operator_is = ismonitoperator('region');
	$rayon_operator_is  = ismonitoperator('rayon', 0, 0, 0, true);
	$school_operator_is = ismonitoperator('school', 0, $rid, $sid);
	if (!$admin_is && !$region_operator_is && !$rayon_operator_is && !$school_operator_is) {
        error(get_string('adminaccess', 'block_monitoring'), "$CFG->wwwroot/login/index.php");
	}

	if (isregionviewoperator() || israyonviewoperator())  {
        error(get_string('adminaccess', 'block_monitoring'));
	}

	$struser = get_string('user');
    $strpupil = get_string('import9class', 'block_mou_att');

	$strclasses = get_string('school','block_monitoring');
	$strclass = get_string('class','block_mou_ege');
	$strpupils = get_string('pupils', 'block_mou_ege');

	$breadcrumbs = '<a href="'.$CFG->wwwroot.'/blocks/mou_mi/index.php">'.get_string('title_mi','block_mou_ege').'</a>';
	$breadcrumbs .= "-> <a href=\"{$CFG->wwwroot}/blocks/mou_mi/class/classlist.php?rid=$rid&amp;sid=$sid&amp;yid=$yid\">$strclasses</a>";
	$breadcrumbs .= "-> $strpupils";
    print_header("$SITE->shortname: $strpupils", $SITE->fullname, $breadcrumbs);

    print_heading('Страница в стадии разарботки. См. импорт классов в "Электронной школе".', 'center', 3);
	print_footer();
    exit();

//    print_heading('Страница в стадии доработки.', 'center', 3);
//    exit();
    // add_to_log(SITEID, 'monit', 'school view', 'school.php?id='.SITEID, $strschool);

//    $currenttab = 'import';
//    include('tabsoperators.php');

    $csv_delimiter = ';';
    $usersnew = 0;
	$userserrors  = 0;
    $linenum = 1; // since header is line 1


	if (!$admin_is && !$region_operator_is) {
        error(get_string('accesstemporarylock', 'block_mou_ege'));
	}

	/// If a file has been uploaded, then process it

//	if (!empty($frm) ) {
		$um = new upload_manager('userfile',false,false,null,false,0);
		$f = 0;
		if ($um->preprocess_files()) {
			$filename = $um->files['userfile']['tmp_name'];

		    @set_time_limit(0);
		    @raise_memory_limit("192M");
		    if (function_exists('apache_child_terminate')) {
		        @apache_child_terminate();
		    }

			$text = file($filename);
			if($text == FALSE)	{
				error(get_string('errorfile', 'block_monitoring'), "$CFG->wwwroot/blocks/mou_mi/class/import9classes.php?sid=$sid&amp;rid=$rid&amp;yid=$yid");
			}
			$size = sizeof($text);

			$textlib = textlib_get_instance();
  			for($i=0; $i < $size; $i++)  {
				$text[$i] = $textlib->convert($text[$i], 'win1251');
            }
            // unset ($textlib);

		    $required = array("name" => 1, "lastname" => 1, "firstname" => 1, "listegenames" => 1);
		    // "email" => 1, 'phone1' => 1, 'city' => 1, 'description' => 1, 'idschool' => 1);

            // --- get and check header (field names) ---
            $header = split($csv_delimiter, $text[0]);

            // translate_header_pupil($header);

			$string_rus['name']='класс';                              //1
			$string_rus['lastname']='фамилия';                            //2
			$string_rus['firstname']='имя отчество';                       //3
			$string_rus['namedocument']='документ';
			$string_rus['serial']='серия';
			$string_rus['number']='номер';                              //5
			$string_rus['who_hands']='кем выдан';
			$string_rus['when_hands']='когда выдан';
			$string_rus['listegenames']='предметы ЕГЭ';

			$string_lat['name']='name';
			$string_lat['lastname']='lastname';
			$string_lat['firstname']='firstname';
			$string_lat['namedocument']='namedocument';
			$string_lat['serial']='serial';
			$string_lat['number']='number';
			$string_lat['who_hands']='who_hands';
			$string_lat['when_hands']='when_hands';
			$string_lat['listegenames']='listegenames';

		    foreach ($header as $i => $h) {
				$h = trim($h);
				$flag = true;
				foreach ($string_rus as $j => $strrus) {
		       		if ($strrus == $h)  {
		       			$header[$i] = $string_lat[$j];
						$flag = false;
		       			break;
		       		}
		       	}
		       	if ($flag)  {
			         print_r($header); echo '<hr>';
					 error(get_string('errorinnamefield', 'block_mou_ege', $string_rus[$header[$i]]), "import9classes.php?sid=$sid&amp;rid=$rid&amp;yid=$yid");
		       	}
		    }


            // print_r($header);


            // check for valid field names
            /*
            foreach ($header as $i => $h) {
                $h = trim($h);
                $header[$i] = $h;
                if (!isset($required[$h])) {
                    error(get_string('invalidfieldname', 'error', $string_rus[$h]), "$CFG->wwwroot/blocks/mou_mi/class/import9classes.php?sid=$sid&amp;rid=$rid&amp;yid=$yid");
                }
                if (isset($required[$h])) {
                    $required[$h] = 0;
                }
            }
            */


	        $fullnames = array();

			$strsql = "SELECT id, schoolid FROM {$CFG->prefix}monit_school_class
				      WHERE schoolid=$sid AND yearid=$yid";
		 	if ($classes = get_records_sql($strsql))	{
		        $classesarray = array();
			    foreach ($classes as $ca)  {
			        $classesarray[] = $ca->id;
			    }
	 			$classeslist = implode(',', $classesarray);

				$strsql = "SELECT id, classid, userid FROM {$CFG->prefix}monit_school_pupil_card
				 		   WHERE classid in ($classeslist) AND deleted=0 ";
			    if ($pupils = get_records_sql($strsql)) 	{
			        $pupilsarray = array();
				    foreach ($pupils as $pp)  {
				       $pupilsarray[] = $pp->userid;
				    }
				    $pupilslist = implode(',', $pupilsarray);

					$strsql = "SELECT id, lastname, firstname FROM {$CFG->prefix}user
					 		   WHERE id in ($pupilslist)";
				    if ($upupils = get_records_sql($strsql)) 	{
					    foreach ($upupils as $upp)  {
					       $fullnames[] = $upp->lastname . ' ' . $upp->firstname;
					    }
					}
	            }
	        }
            // print_r($fullnames);
            // exit();

			echo 'login;password;lastname;firstname;email<br>';

  			for($i=1; $i < $size; $i++)  {


	            $line = split($csv_delimiter, $text[$i]);
 	  	        foreach ($line as $key => $value) {
  	                $record[$header[$key]] = trim($value);
   	 	        }

                $linenum++;
                if ($linenum > 100)	 {
					error(get_string('verybiglinenum', 'block_mou_ege'), "import9classes.php?sid=$sid&amp;rid=$rid&amp;yid=$yid");
                }
                // print_r($record);
                // add fields to object $user
                foreach ($record as $name => $value) {
                    // check for required values
                    if (isset($required[$name]) and !$value) {
                        error(get_string('missingfield', 'error', $string_rus[$name]). " ".
                              get_string('erroronline', 'error', $linenum),
                              "import9classes.php?sid=$sid&amp;rid=$rid&amp;yid=$yid");
                    }
                    // normal entry
                    else {
                        if ($name == 'lastname' || $name == 'firstname') {
                        	$user->{$name} = addslashes($value);
                        } else {
                        	$pupil->{$name} = addslashes($value);
                        }
                    }
                }

                $fullnames_check = $user->lastname . ' ' . $user->firstname;

                if (in_array($fullnames_check, $fullnames))	 {
                    notify(get_string('pupilnotaddedregistered', 'block_mou_ege', $fullnames_check));
                    $userserrors++;
                    continue;
                }
               // echo $pupil->when_hands.':';

			    $datefield = array('when_hands');

				foreach ($datefield as $df)  {
				     if (isset($pupil->{$df}) && !empty($pupil->{$df}))	{
	                	 if (is_date($pupil->{$df}))	{
			                // echo "$day, $month, $year -- ";
	               		    $pupil->{$df} = convert_date($pupil->{$df});
	  		                // echo $pupil->{$df} . '<br>';
	                	 }
                      }
                }

		     	if (isset($pupil->namedocument) && !empty($pupil->namedocument))	{
		     		$typedocuments1 = get_string('typedocuments1', 'block_mou_ege');
		     		$typedocuments2 = get_string('typedocuments2', 'block_mou_ege');
		     		if ($pupil->namedocument == $typedocuments1)	{
				     	$pupil->typedocuments = 1;
		     		} else 	if ($pupil->namedocument == $typedocuments2)	{
				     	$pupil->typedocuments = 2;
		     		}
		     	}

		     	if (isset($pupil->listegenames) && !empty($pupil->listegenames))	{
					$disciplines =  get_records_sql ("SELECT id, yearid, name
													  FROM  {$CFG->prefix}monit_school_discipline_mi
													  WHERE yearid=$yid
													  ORDER BY name");
					if ($disciplines)	{
		       			$namedisciplines = explode(',', $pupil->listegenames);
		       			foreach($namedisciplines as $key => $value)	{
		       				$namedisciplines[$key] = trim($value);
		       			}
					    $listegeids = '';
						foreach ($disciplines as $discipline) 	{
							if (in_array($discipline->name, $namedisciplines))	{
									$listegeids .= $discipline->id.',';
							}
						}
						$listegeids .= '0';
						$pupil->listegeids = $listegeids;
         			}

		     	}

				  $pupil->name = $textlib->strtoupper($pupil->name);

				  $pupil->name = translit_english_utf8($pupil->name);

				  $symbols = array (' ', '\"', "\'", "`", '-', '#', '*', '+', '_', '=');
				  foreach ($symbols as $sym)	{
					  $pupil->name = str_replace($sym, '', $pupil->name);
				  }

				if(!$class = get_record('monit_school_class', 'schoolid', $sid, 'yearid', $yid, 'name', $pupil->name)) 	 {
						$rec->rayonid = $rid;
						$rec->schoolid = $sid;
						$rec->yearid = $yid;
						$rec->curriculumid = 0;
						$rec->name = $pupil->name;
						$rec->parallelnum = (int)$rec->name;
						$rec->description = "";
						$rec->timecreated = time();

						if ($idnew = insert_record('monit_school_class', $rec))	{
							$class = get_record('monit_school_class', 'id', $idnew);
		                    notify(get_string('classadded','block_mou_ege', $rec->name), 'green', 'left');
						} else {
						    print_r($rec); echo '<hr>';
							error(get_string('errorinaddingclass','block_mou_ege'), "$CFG->wwwroot/blocks/mou_mi/class/import9classes.php?sid=$sid&amp;rid=$rid&amp;yid=$yid");
						}
                    }


			    $code = get_pupil_username($rid, $sid, $class);

				$user->username = $code;

				 if ($olduser = get_record('user', 'username', $user->username))		{
				      if (($olduser->lastname == $user->lastname) && ($olduser->firstname == $user->firstname))	{
                           //Record not added - user is already registered
                           //In this case, output userid from previous registration
                           //This can be used to obtain a list of userids for existing users
                           notify("$olduser->id ".get_string('usernotaddedregistered', 'error', $user->username . ' '. $user->lastname. ' '.  $user->firstname));
                           $userserrors++;
                           continue;
                      }
                 }



/*
				 if ($olduser = get_record('user', 'username', $user->username, 'lastname', $user->lastname, 'firstname', $user->firstname))  {
                           //Full tezka
                           notify("FULL TEZKA: $olduser->id ".get_string('usernotaddedregistered', 'error', $user->username . ' '. $user->lastname. ' '.  $user->firstname));
                           $userserrors++;
                           continue;
                 }
*/

                $j = 1;
                $makecontinue = false;
				while (record_exists('user', 'username', $user->username))  {
					$user->username = $code.$j;
			 		if ($olduser = get_record('user', 'username', $user->username))		{
					    if ($olduser->firstname == $user->firstname)	{
                           notify("$olduser->id ".get_string('usernotaddedregistered', 'error', $user->username . ' '. $user->lastname. ' '.  $user->firstname));
	                       $userserrors++;
	                       $makecontinue = true;
	                       break;
	                    }
	                }
					if ($j++ > 1000) break;
				}

				if ($makecontinue) continue;


				$user->email = $user->username . '@temp.ru';

                // $pupil->pswtxt = gen_psw($user->username);
                $pupil->pswtxt = generate_password2(6);
                // $txtl->convert($strvalue, 'utf-8', 'windows-1251');
                $user->password = hash_internal_user_password($pupil->pswtxt);


		    	$rayon = get_record('monit_rayon', 'id', $rid);
		   	    $user->city = $rayon->name;

                $user->mnethostid = $CFG->mnet_localhost_id;
                $user->confirmed = 1;
                $user->timemodified = time();
                $user->country = 'RU';
                $user->lang = 'ru_utf8';

		    	$school = get_record('monit_school', 'id', $sid);

                $user->description = '';

                // echo '<hr>';
                // print_r($user);
                // print_r($pupil);


                if ($newid = insert_record("user", $user)) {
                    echo "$user->username; $pupil->pswtxt; $user->lastname; $user->firstname; $user->email<br>";
                    $usersnew++;
                    $pupil->userid = $newid;
                } else {
                    // Record not added -- possibly some other error
                    notify(get_string('usernotaddederror', 'error', $user->username));
                    $userserrors++;
                    continue;
	            }
                /*
                $coursecontext = get_context_instance(CONTEXT_COURSE, 1);
                if (!user_can_assign($coursecontext, ROLE_PUPIL)) {
                    notify("--> Can not assign role: $newid = $user->username ($user->lastname $user->firstname)"); //TODO: localize
                }
                $ret = role_assign(ROLE_PUPIL, $newid, 0, $coursecontext->id);
                */
                $pupil->classid 	 = $class->id;
                $pupil->rayonid  	 = $rid;
                $pupil->schoolid	 = $sid;
                $pupil->timemodified = time();

			    if (record_exists('user', 'id', $pupil->userid))	 {
					if (insert_record('monit_school_pupil_card', $pupil))	{
					    $fullnames[] = $user->lastname . ' ' . $user->firstname;
						// add_to_log(1, 'monitoring', 'operator added', '/blocks/monitoring/users/operators.php?level=$levelmonit&amp;sid=$sid&amp;rid=$rid', $USER->lastname.' '.$USER->firstname);
					} else  {
					    print_r($pupil); echo '<hr>';
						error(get_string('errorinaddingpupil','block_mou_ege'), "classlist.php?rid=$rid&amp;yid=$yid&amp;sid=$sid");
	                	// error("--> Can not add <b>teacher</b> in staff: $user->username ($user->lastname $user->firstname)"); //TODO: localize
					}
			    }

                unset($user);
                unset($pupil);
            }
		    $strusersnew = get_string("usersnew");
    	    notify("$strusersnew: $usersnew", 'green', 'center');
            notify(get_string('errors', 'block_mou_ege') . ": $userserrors");
	        echo '<hr />';
       }

//   }
    if ($admin_is  || $region_operator_is)	 {
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_rayons("import9classes.php?sid=0&amp;yid=$yid&amp;rid=", $rid);
		listbox_schools("import9classes.php?rid=$rid&amp;yid=$yid&amp;sid=", $rid, $sid, $yid);
		echo '</table>';
	}  else if ($rayon_operator_is)	 	{
		echo '<table cellspacing="0" cellpadding="10" align="center" class="generaltable generalbox">';
		listbox_schools("import9classes.php?rid=$rid&amp;yid=$yid&amp;sid=", $rid, $sid, $yid);
		echo '</table>';
	}

	if ($rid!=0 && $sid!=0) {

    	$school = get_record('monit_school', 'id', $sid);
   	    $strschool = $school->name;

	    $struploadusers = get_string('import9classpupil', 'block_mou_ege', $strschool);

		// print_heading($strclasses, "center", 3);

		// print_tabs_years($yid, "classlist.php?rid=$rid&amp;sid=$sid&amp;yid=");

	    $currenttab = 'gia';
	    include('tabsclasses.php');

	    $currenttab = 'import9class';
	    include('tabsgia.php');

	    print_heading_with_help($struploadusers, 'import9class', 'mou');

		/// Print the form
   	    $struploadusers = get_string('import9class', 'block_mou_ege');
	    $maxuploadsize = get_max_upload_file_size();
		$strchoose = ''; // get_string("choose"). ':';

	    echo '<center>';
	    echo '<form method="post" enctype="multipart/form-data" action="import9classes.php">'.
	         $strchoose.'<input type="hidden" name="MAX_FILE_SIZE" value="'.$maxuploadsize.'">'.
	         '<input type="hidden" name="rid" value="'.$rid.'">'.
	         '<input type="hidden" name="sid" value="'.$sid.'">'.
	         '<input type="hidden" name="yid" value="'.$yid.'">'.
	         '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'">';


	    echo '<input type="file" name="userfile" size="50">'.
	         '<br><input type="submit" value="'.$struploadusers.'">'.
	         '</form><p>';
	    $output = helpbutton('import9class', 'Как загрузить списки учеников нескольких классов', 'mou', true, true, '', true);
	    echo $output;
	    echo '</center>';
    }

    print_footer();

?>