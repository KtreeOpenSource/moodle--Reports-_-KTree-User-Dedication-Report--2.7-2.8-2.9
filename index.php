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

global $OUTPUT, $PAGE, $CFG;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('lib.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
require_once($CFG->libdir.'/formslib.php');
$title='Course Dedication Report';
$PAGE->set_url($CFG->dirroot.'/report/ktreeuserdedicationreport/adminreport.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
require_login();
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();
$PAGE->set_heading($title);
$weburl=$CFG->wwwroot;
echo $OUTPUT->footer();
