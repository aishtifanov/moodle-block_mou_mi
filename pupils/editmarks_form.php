<?php  // $Id: editmarks_form.php,v 1.4 2009/07/06 11:29:27 Shtifanov Exp $

require_once($CFG->libdir.'/formslib.php');

class editmarks_form extends moodleform {
    function definition() {

        global $yid, $rid, $sid, $did, $gid, $uid;

        $mform =& $this->_form;

        $mform->addElement('header','', get_string('resultpupil', 'block_mou_ege'));

        $mform->addElement('text', 'pp', get_string('point', 'block_mou_ege'), 'maxlength="4" size="3"');
        $mform->addRule('pp', get_string('missingname'), 'required', null, 'client');
        $mform->setType('pp', PARAM_TEXT);

        $mform->addElement('text', 'audit',  get_string('auditoria', 'block_mou_ege'), 'maxlength="5" size="5"');
        $mform->addRule('audit', get_string('missingname'), 'required', null, 'client');
        $mform->setType('audit', PARAM_INT);

        $mform->addElement('text', 'variant',  get_string('numvariant', 'block_mou_ege'), 'maxlength="5" size="5"');
        $mform->addRule('variant', get_string('missingname'), 'required', null, 'client');
        $mform->setType('variant', PARAM_INT);

        $mform->addElement('text', 'sidea',  get_string('sidea', 'block_mou_ege'), 'maxlength="100" size="100"');
        $mform->addRule('sidea', get_string('missingname'), 'required', null, 'client');
        $mform->setType('sidea', PARAM_TEXT);

        $mform->addElement('text', 'sideb',  get_string('sideb', 'block_mou_ege'), 'maxlength="100" size="100"');
        $mform->addRule('sideb', get_string('missingname'), 'required', null, 'client');
        $mform->setType('sideb', PARAM_TEXT);

        $mform->addElement('text', 'sidec',  get_string('sidec', 'block_mou_ege'), 'maxlength="100" size="100"');
        $mform->addRule('sidec', get_string('missingname'), 'required', null, 'client');
        $mform->setType('sidec', PARAM_TEXT);

        $mform->addElement('text', 'ball',  get_string('ball', 'block_mou_ege'), 'maxlength="5" size="5"');
        $mform->addRule('ball', get_string('missingname'), 'required', null, 'client');
        $mform->setType('ball', PARAM_INT);

        $mform->addElement('text', 'ocenka',  get_string('ocenka', 'block_mou_ege'), 'maxlength="5" size="5"');
        $mform->addRule('ocenka', get_string('missingname'), 'required', null, 'client');
        $mform->setType('ocenka', PARAM_INT);

		$mform->addElement('hidden', 'yid', $yid);  $mform->setType('yid', PARAM_INT);
		$mform->addElement('hidden', 'rid', $rid);  $mform->setType('rid', PARAM_INT);
		$mform->addElement('hidden', 'sid', $sid);  $mform->setType('sid', PARAM_INT);
		$mform->addElement('hidden', 'did', $did);  $mform->setType('did', PARAM_INT);
		$mform->addElement('hidden', 'gid', $gid);  $mform->setType('gid', PARAM_INT);
		$mform->addElement('hidden', 'uid', $uid);  $mform->setType('uid', PARAM_INT);

        $this->add_action_buttons();
    }

    function validation($data) {
        $errors = array();

        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }

    }

}
?>
