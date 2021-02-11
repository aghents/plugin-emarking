<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package mod_emarking
 * @copyright 2017 Francisco Ralph fco.ralph@gmail.com
 * @copyright 2017 Hans Jeria (hansjeria@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once (dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . '/config.php');
require_once ('locallib.php');
global $PAGE, $DB, $USER, $CFG;

$id = optional_param ( 'id',0 ,PARAM_INT );
$activityid = required_param ( 'activityid', PARAM_INT );

$PAGE->set_context ( context_system::instance () );
$url = new moodle_url ( $CFG->wwwroot . '/mod/emarking/activities/rubric.php' );
$PAGE->set_url ( $url );
$PAGE->set_title ( 'escribiendo' );
// If not logged in, redirect to login/index.php
if (!isloggedin ()) {
	redirect(new moodle_url($CFG->wwwroot.'/login/index.php'), 0);
	die();
}

$activity = $DB->get_record('emarking_activities', array('id' => $activityid));
if ($activity->userid != $USER->id) {
	$backUrl = new moodle_url($CFG->wwwroot.'/mod/emarking/activities/activity.php', array('id' => $activityid));
	redirect($backUrl, 0);	
}
$id = $activity->rubricid;

if (isset ( $_POST['submit'])) {
	$data = $_POST;
	if(isset($id) && $id != null){
		update_rubric($id,$data);
	}else{
		insert_rubric($data,$activityid);
	}
}

$rubricname = "";
$rubricdescription = "";
if(isset($id) && $id != null){
	$rubricdetails = $DB->get_record('emarking_rubrics', array('id' => $id));
	$rubricname = $rubricdetails->name;
	$rubricdescription = $rubricdetails->description;
	$sql = "SELECT rc.id 
		FROM {emarking_rubrics_criteria} rc 
		INNER JOIN {emarking_rubrics} r ON (r.id = rc.rubricid )
		where r.id = ?";
	$rubric = $DB->get_records_sql($sql, array($id));
}
$sql='SELECT rl.*, rc.description as criteria, r.id as rubricid, i.max
	FROM {emarking_rubrics_levels} as rl
	INNER JOIN {emarking_rubrics_criteria} rc ON (rc.id = rl.criterionid )
	INNER JOIN {emarking_rubrics} r ON (r.id = rc.rubricid )
	LEFT JOIN (select criterionid, max(score) as max 
			FROM {emarking_rubrics_levels} as rl group by criterionid) as i on (i.criterionid = rl.criterionid)
	ORDER BY rl.criterionid ASC, rl.score DESC';
$levels = $DB->get_records_sql($sql);

// print the header
include 'views/header.php';

// print the main page
include 'views/rubric.php';

// print the footer
include 'views/footer.html';