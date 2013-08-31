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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * launches the experience with the requested registration 
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
include 'locallib.php';

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // tincanlaunch instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('tincanlaunch', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $tincanlaunch  = $DB->get_record('tincanlaunch', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $tincanlaunch  = $DB->get_record('tincanlaunch', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $tincanlaunch->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('tincanlaunch', $tincanlaunch->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'tincanlaunch', 'launch', "launch.php?id={$cm->id}", $tincanlaunch->name, $cm->id);

//get the registration id
$registrationid = $_POST["launchform_registration"];

//Save a record of this registration to the LRS state API
//TODO:Get the existing data so we can append this registration rather than overwriting whatever might be there already. 

$datenow = date("c");

$registrationdata = array(
    "id" => $registrationid,
    "created" => $datenow,
    "lastlaunched" => $datenow
);

tincanlaunch_get_global_parameters_and_save_state($registrationdata,"http://tincanapi.co.uk/stateapikeys/registrations");

//launch the experience
header("Location: ". tincanlaunch_get_launch_url($registrationid));
exit;

 
 ?>