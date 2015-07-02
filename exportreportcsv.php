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
 * Local plugin "ktreeuserdedicationreport" - Settings
 *
 * @package    report_ktreeuserdedicationreport
 * @copyright   2015 KTree Computer Solutions <opensource@ktree.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $OUTPUT, $PAGE, $CFG, $USER;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot . '/report/ktreeuserdedicationreport/dedication_lib.php');
$starttime = $_SESSION['start'];
$maxtime = $_SESSION['end'];
date_default_timezone_set(ini_get('date.timezone'));
define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);
if (isset($_GET['selectuser']) && $_GET['selectuser'] == 0) {
    $querygetmodules1 ="SELECT id FROM {user} where deleted = 0 and username not in ('guest','admin') order by firstname ASC";
} else {
    $querygetmodules1 ="SELECT id FROM {user} where deleted = 0 and username not in ('guest','admin') and id=".$_GET['selectuser']." order by firstname ASC";
}
$coursesmodules1 = $DB->get_records_sql($querygetmodules1);
$iterate = 0;
$allusers = array();
foreach ($coursesmodules1 as $uin => $udet) {
    $allusers[$iterate] = $udet->id;
    $iterate++;
    }
$table = '';
$table = get_string('sno', 'report_ktreeuserdedicationreport').",".get_string('username', 'report_ktreeuserdedicationreport').",".get_string('userfullname', 'report_ktreeuserdedicationreport').",". get_string('firstaccess', 'report_ktreeuserdedicationreport').",".get_string('lastlogin', 'report_ktreeuserdedicationreport').",".get_string('lastaccess', 'report_ktreeuserdedicationreport').",".get_string('timespent', 'report_ktreeuserdedicationreport')."\n";
$i=1;
$query1 = "select id, fullname as sn from {course} where category != 0";
$courses1 = $DB->get_records_sql($query1);
foreach ($courses1 as $uin => $udet) { 
    $courseshortname[$udet->id] = $udet->sn;
}
$countervalue = 0;
while ($countervalue<count($allusers)) {
    $querygetmodules1 = "SELECT email AS mail FROM mdl_user WHERE id=$allusers[$countervalue]";
    $coursesmodules1 = $DB->get_records_sql($querygetmodules1);
    foreach ($coursesmodules1 as $cin1 => $cval1) {
        $email = $cval1->mail;
    }
    $coursequery = "SELECT `courseid` FROM {enrol} WHERE `id` in (SELECT `enrolid` FROM `mdl_user_enrolments` WHERE `userid`=4 and ( `timeend`=0 or `timeend`>=now()))";
    $coursequeryresult = $DB->get_records_sql($coursequery);
    $enrolledcourses = array();
    foreach ($coursequeryresult as $cin2 => $cval2) {
        $enrolledcourses[] = $cval2->courseid;
    }
    $enrolledcourses = implode(",",$enrolledcourses);
    $sql = "select * from {logstore_standard_log} where  userid = " . $allusers[ $countervalue] . " and timecreated  >='". $starttime ."' and timecreated  <='". $maxtime ."' order by timecreated ASC";
    $logs = $DB->get_records_sql($sql);
    $limit = 60;
    $dedicationtime='';
    if ($logs != null) {
        $previouslog = array_shift($logs);
        $previouslogtime = $previouslog->timecreated;
        $sessionstart = $previouslogtime;
        $dedication = 0;
        foreach ($logs as $log) {
            if (($log->timecreated - $previouslogtime) > $limit) {
                $dedication += $previouslogtime - $sessionstart;
                $sessionstart = $log->timecreated;
            }
            $previouslogtime = $log->timecreated;
        }
		$dedication += $previouslogtime - $sessionstart;
        $dedicationtime = block_dedication_manager::format_dedication($dedication);
    }		
    $querygetmodules1 = "SELECT firstname, lastname, username, firstaccess, lastaccess, lastlogin, currentlogin, institution FROM mdl_user WHERE id=$allusers[$countervalue]";
    $coursesmodules1 = $DB->get_records_sql($querygetmodules1);
    if ($coursesmodules1 != null) 
    foreach ($coursesmodules1 as $cin1 => $cval1) {
        $username = $cval1->username;
        $fname = $cval1->firstname;
        $lname = $cval1->lastname;
        $firstaccess = $cval1->firstaccess;
        $lastaccess = $cval1->lastaccess;
        $lastlogin = $cval1->lastlogin;
        $currentlogin = $cval1->currentlogin;
        $institution = $cval1->institution;
    }
    $name = $fname.' '.$lname;
    $logintime = $DB->get_record_sql("SELECT * FROM `{logstore_standard_log}` where userid='".$allusers[$countervalue]."' and action='loggedin' and (timecreated  >='". $starttime ."' and timecreated  <='". $maxtime ."') order by id limit 0,1");			
    if ($logintime == null) {
        $firstaccess = '_';
    } else {
        $firstaccess = date('m-d-Y h:i:s a',$logintime->timecreated);
    }
    $lastlogintime = $DB->get_record_sql("SELECT * FROM `{logstore_standard_log}` where userid='".$allusers[$countervalue]."' and action='loggedin' and (timecreated  >='". $starttime ."' and timecreated  <='". $maxtime ."') order by id desc limit 0,1");
    if ($lastlogintime == null) {
        $lastlogin = '_';
    } else {
        $lastlogin = date('m-d-Y h:i:s a',$lastlogintime->timecreated);
    }
    $logouttime = $DB->get_record_sql("SELECT * FROM `{logstore_standard_log}` where userid='".$allusers[$countervalue]."' and action='loggedout' and (timecreated  >='". $starttime ."' and timecreated  <='". $maxtime ."') order by id desc limit 0,1");
    if ($logouttime == null) {
        $lastaccess = '_';
    } else {
        $lastaccess = date('m-d-Y h:i:s a',$logouttime->timecreated);
    }
    if ($dedicationtime != '') {
        $dedicationtime = $dedicationtime;
    } else {
        $dedicationtime="never";
    }
    $table.= $i.",".$username.",".$name.",".$firstaccess.",".$lastlogin.",".$lastaccess.",".$dedicationtime."\n";
    $i++;
    $dedicationtime = '';
    $countervalue++;
}
$csvname ='KTree_User_Dedication_Report.csv';
header('Content-Type: text/x-comma-separated-values');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header('Content-Disposition: attachment; filename="' . $csvname . '"');
print $table;exit();
