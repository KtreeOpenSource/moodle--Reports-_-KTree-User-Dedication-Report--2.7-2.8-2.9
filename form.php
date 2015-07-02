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
require_once($CFG->dirroot.'/report/ktreeuserdedicationreport/lib.php');
require_login();
$title = 'Course Dedication';
$PAGE->set_url($CFG->dirroot.'/report/ktreeuserdedicationreport/adminreport.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('frontpage');
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();
$PAGE->set_heading($title);
?>
<html>
<head>
<script src='jquery12.js'></script>
<script type="text/javascript">
$(document).ready(function(){
$('#addmore').click(function() {
$len= $("#more").children("div").length;
alert($len);

$content=$('#form2').html();
$('#more').append("<div><label>Title</label><br><input type='text' name='title["+$len+"]'><br><label>Description:</label><br><textarea name='description["+$len+"]'></textarea><br><br></div>");
});
});
</script>
</head>
<form id='form' action='feedsave.php' method='post'>
<div id='form'>
Title:
<br>
<select name='titlee'>
<option>Quiz</option>
<option>test</option>
</select>
<br>
Description:
<br><textarea name='descriptione'></textarea>
<br>
<br>
</div>
<div id='more'></div>
<div id='form2' style='display:none'>
</div>
<input id='addmore' type='button' name='addmore' value='AddMore' />
<br>
<br>
<input id='save' type='submit' name='save' value='Save' />
</html>
<?php
echo $OUTPUT->footer();
