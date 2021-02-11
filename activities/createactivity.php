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
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once (dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) . '/config.php');
global $PAGE, $DB, $USER, $CFG, $OUTPUT;

require_once ('forms/create_activity_basic.php');
require_once ('forms/create_activity_instructions.php');
require_once ('forms/create_activity_teaching.php');
require ($CFG->dirroot. '/mod/emarking/activities/generos.php');
require_once ('locallib.php');
require_login ();
$step = optional_param ( 'step',1 ,PARAM_INT );
$activityid = optional_param ( 'id',0 ,PARAM_INT );
$PAGE->set_pagelayout ( 'embedded' );
$context = context_system::instance ();
$PAGE->set_context ( $context );
$url = new moodle_url ( $CFG->wwwroot . '/mod/emarking/activities/createactivity.php' );
$PAGE->set_url ( $url );
$PAGE->set_title ( 'escribiendo' );

echo $OUTPUT->header ();

if (empty ( $area->id )) {
	$area = new stdClass ();
	$area->id = 0;
}
$draftid_editor = file_get_submitted_draft_itemid ( 'instructions' );
file_prepare_draft_area ( $draftid_editor, $context->id, 'mod_emarking', 'instructions', $area->id, null );

// print the header
?>

<div class="container">
	<div class="row">
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
		<h2>Crear una actividad</h2>

		<div class="col-md-12">
		
<?php
$basic = new mod_emarking_activities_create_activity_basic ();
$instructions = new mod_emarking_activities_create_activity_instructions ();
$teaching = new mod_emarking_activities_create_activity_teaching ();
if($step == 1){
	if($activityid!=0){
		$activity=$DB->get_record('emarking_activities',array('id'=>$activityid));
		$keyofgenre = array_search($activity->genre, $generos) + 1;
		$area->title 						= $activity->title;
		$area->description 					= $activity->description;
		$area->comunicativepurpose 			= $activity->comunicativepurpose;
		$area->genre 						= $keyofgenre;
		$area->audience 					= $activity->audience;
		$area->estimatedtime 				= $activity->estimatedtime;
		$area->id							= $activityid;
		$area->editing						= 1;
		$basic->set_data ( $area );
		
	}
	$area->estimatedtime 				= $generos;
	$basic->set_data ( $area );
	$basic->display ();
}elseif($step == 2){
	if ($fromformbasic = $basic->get_data ()) {
	//if is creating or editing a rubric
	if($fromformbasic->editing==0){
	$activityid=add_new_activity_basic ( $fromformbasic);
	}else{
		edit_activity_basic ( $fromformbasic,$activityid);
	}
	$activity=$DB->get_record('emarking_activities',array('id'=>$activityid));
	
	$area->instructions = array (
			'text' => $activity->instructions,
			'',
			'itemid' => $draftid_editor
	);
	$area->planification = array (
			'text' => $activity->planification,
			'',
			'itemid' => $draftid_editor
	);
	$area->writing = array (
			'text' => $activity->writing,
			'',
			'itemid' => $draftid_editor
	);
	$area->editing = array (
			'text' => $activity->editing,
			'',
			'itemid' => $draftid_editor
	);
	$area->id 	= $activityid;
	$instructions->set_data ( $area );
	$instructions->display ();
	}
}elseif ($step == 3){
	if ($fromforminstructions = $instructions->get_data ()) {
	add_new_activity_instructions ( $fromforminstructions,$activityid,$context );
	$activity=$DB->get_record('emarking_activities',array('id'=>$activityid));
	$area->teaching = array (
			'text' => $activity->teaching,
			'',
			'itemid' => $draftid_editor
	);
	$area->languageresources = array (
			'text' => $activity->languageresources,
			'',
			'itemid' => $draftid_editor
	);
	$area->id 	= $activityid;
	$teaching->set_data ( $area );
	$teaching->display();
	}	
}
elseif ($step == 4){
	if ($fromformteaching = $teaching->get_data ()) {
		add_new_activity_teaching ( $fromformteaching,$activityid,$context );
		$url = new moodle_url ( $CFG->wwwroot . '/mod/emarking/activities/activity.php', array (
			'id' => $activityid 
				) );
			redirect ( $url, 0 );
			
	}

}
echo $OUTPUT->footer ();
echo " 	</div>
	</div>";
include 'views/headermoodle.php';

include 'views/footer.html';
