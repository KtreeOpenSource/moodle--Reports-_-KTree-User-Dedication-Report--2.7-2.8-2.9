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
require_once($CFG->dirroot.'/report/ktreeuserdedicationreport/dedication_lib.php');
require_once('function.php');
$title = 'KTree\'s User Dedication Report ';
$url = new moodle_url('/blocks/ktreeuserdedicationreport/adminreport.php');
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('frontpage');
require_login();
date_default_timezone_set(ini_get('date.timezone'));
define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();
$PAGE->set_heading($title);
if (isset($_GET['selectuser']) && !empty($_GET['selectuser'])) {
    $selecteduser = $_GET['selectuser'];
    $_SESSION['selecteduser'] = $selecteduser;
}
if (isset($_GET['date1']) && !empty($_GET['date1'])) {
    $date1 = $_GET['date1'];
    $_SESSION['date1'] = $date1;
}
if (isset($_GET['date2']) && !empty($_GET['date2'])) {
    $date2 = $_GET['date2'];
    $_SESSION['date2'] = $date2;
}
$date1 = $_SESSION['date1'];
$date2 = $_SESSION['date2'];
?>
<link rel="stylesheet" type="text/css" href="cssstyles.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script type="text/javascript" src="js/script.js"></script>
<div align="center">
<form action="<?php echo $CFG->wwwroot.'/report/ktreeuserdedicationreport/adminreport.php';?>" name="frmSample" method="get" onSubmit="return ValidateForm()">
<p class="plugin-title"><b><?php echo  get_string('report_title','report_ktreeuserdedicationreport');?></b></p>
<?php echo '<p class="admin-label1"><label>Start Date:</label> <input type="text" id="datepicker1"  name="date1" value="'.$date1.'"></p>';
echo '<p class="admin-label2"><label>&nbsp; &nbsp;End Date:&nbsp;&nbsp;</label><input type="text" id="datepicker2" name="date2" value="'.$date2.'" ></p>';
?>
<p class="admin-label2">
<label >Users : </label><select class="selectalign" name="selectuser"  onchange="this.form.submit();">
<?php 
if ($selecteduser == 0) {
    echo "<option value=0 selected='selected' align='center'>ALL Users</option>";
}
$userdetails  = $DB->get_records_sql("SELECT * FROM {user} where deleted = 0 and username not in ('guest','admin') order by firstname ASC");
foreach ($userdetails as $sind => $sval) {
    if ($sind == $selecteduser) {
    ?>
    <option value="<?php echo $sval->id;?>" selected='selected'><?php echo $sval->firstname.' - '.$sval->username;?></option>
    <?php
    } else {
    ?>
    <option value="<?php echo $sval->id;?>"><?php  echo $sval->firstname.' - '.$sval->username;?></option>
    <?php
    }
}
?>
</select></p>
&nbsp;&nbsp;<input type="submit" class="Go_align" value="Go" name="submitbtn" />
</form>
</div>
<?php
if (isset($_GET['limit']) || isset($_GET['submitbtn']) || isset($_GET['selectuser'])) {
    $d1 = $date1.' 00:00:00';
    $d2 = $date2.' 23:59:59';
    $starttime = strtotime($d1);
    $maxtime = strtotime($d2);
    $_SESSION['start'] = $starttime;
    $_SESSION['end'] = $maxtime;
    $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    $recsarray = array(10, 25, 50, 100, 500);
    if (isset($_GET['limit']) && ($_GET['limit'] != null)) {
        if (in_array($_GET['limit'],$recsarray)) {
            $limit = $_GET['limit'];
        } else {
        $limit = 10;
        }
    } else {
    $limit = 10;
    }
    $startpoint = ($page * $limit) - $limit;
    if (isset($_GET['selectuser']) && $_GET['selectuser'] != 0) {
        $statement = "{user} where deleted = 0 and username not in ('guest','admin') and id=".$_GET['selectuser']." order by firstname ASC";
    } else {
        $statement = "{user} where deleted = 0 and username not in ('guest','admin') order by firstname ASC";
    }
    $querygetmodules1 = "SELECT * FROM {$statement} LIMIT {$startpoint} , {$limit}";
    $coursesmodules1 = $DB->get_records_sql($querygetmodules1);
    $iterate = 0;
    $allusers = array();
    foreach ($coursesmodules1 as $uin => $udet) {
        $allusers[$iterate] = $udet->id;
        $iterate++;
    }
?><html>
</head>
<body>
<div class="module-selection">
<form action="<?php echo $CFG->wwwroot?>/report/ktreeuserdedicationreport/index.php" action="get">
<div ><a align='right' href='<?php echo $CFG->wwwroot;?>/report/ktreeuserdedicationreport/exportreportcsv.php?<?php echo $_SERVER['QUERY_STRING'];?>'>
<input type='button' id="btnPrint" class="btnAlign" value='<?php echo  get_string('exportcontent', 'report_ktreeuserdedicationreport');?>' /></a></div>
<input type="hidden" name="user" id="user" value="<?php echo $USER->id;?>"/>
<b><h3 class="PdCredit_title"><?php echo  get_string('duration', 'report_ktreeuserdedicationreport');?>&nbsp; ' <?php echo $date1;?> ' &nbsp; to  &nbsp; ' <?php echo $date2;?> '</h3></b>
<table align="center" class="gridtable">
<thead><tr><th ><?php echo  get_string('sno', 'report_ktreeuserdedicationreport');?></th><th ><?php echo  get_string('username', 'report_ktreeuserdedicationreport');?></th><th><?php echo  get_string('userfullname','report_ktreeuserdedicationreport');?></th>
<th><?php echo  get_string('firstaccess', 'report_ktreeuserdedicationreport');?></th>
<th><?php echo  get_string('lastlogin', 'report_ktreeuserdedicationreport');?></th><th><?php echo  get_string('lastaccess', 'report_ktreeuserdedicationreport');?></th>
<th><?php echo  get_string('timespent', 'report_ktreeuserdedicationreport');?></th></tr></thead><tbody>
<?php
    if ($allusers == null) {
        echo "<tr ><td colspan='9' align='center'> <b>Sorry no user records found in database<b></td></tr></tbody></table>";
    }
    $i = 1;
    $query1 = "select id, fullname as sn from {course} where category != 0";
    $courses1 = $DB->get_records_sql($query1);
    foreach ($courses1 as $uin => $udet) {
    $courseshortname[$udet->id]=$udet->sn;
    }
	$i = $startpoint + 1;
	$countervalue = 0;
	while ($countervalue<count($allusers)) {
        $querygetmodules1 = "SELECT email AS mail FROM mdl_user WHERE id=$allusers[$countervalue]";
        $coursesmodules1 = $DB->get_records_sql($querygetmodules1);
        foreach ($coursesmodules1 as $cin1 => $cval1) {
        $email=$cval1->mail;
		}
        $coursequery="SELECT `courseid` FROM {enrol} WHERE `id` in (SELECT `enrolid` FROM `mdl_user_enrolments` WHERE `userid`=4 and ( `timeend`=0 or `timeend`>=now()))";
        $coursequeryresult = $DB->get_records_sql($coursequery);
        $enrolledcourses = array();
        foreach ($coursequeryresult as $cin2 => $cval2) {
        $enrolledcourses[]=$cval2->courseid;
        }
        $enrolledcourses = implode(",", $enrolledcourses);
        $sql = "select * from {logstore_standard_log} where  userid = " . $allusers[ $countervalue] . " and timecreated  >='". $starttime ."' and timecreated  <='". $maxtime ."' order by timecreated ASC";
        $logs = $DB->get_records_sql($sql);
        $dedicationtime = '';
        $limit1 = 60;
        if ($logs != null) {
            $previouslog = array_shift($logs);
            $previouslogtime = $previouslog->timecreated;
            $sessionstart = $previouslogtime;
            $dedication = 0;
            foreach ($logs as $log) {
                if (($log->timecreated - $previouslogtime) > $limit1) {
                    $dedication += $previouslogtime - $sessionstart;
                    $sessionstart = $log->timecreated;
                }
                $previouslogtime = $log->timecreated;
            }
	        $dedication += $previouslogtime - $sessionstart;
	        $dedicationtime = block_dedication_manager::format_dedication($dedication);
        }
        $querygetmodules1 ="SELECT firstname, lastname, username, firstaccess, lastaccess, lastlogin, currentlogin, institution FROM mdl_user WHERE id=$allusers[$countervalue]";
        $coursesmodules1 = $DB->get_records_sql($querygetmodules1);
        if ($coursesmodules1 != null) 
        foreach ($coursesmodules1 as $cin1 => $cval1) {
            $username=$cval1->username;
            $fname=$cval1->firstname;
            $lname=$cval1->lastname;
            $firstaccess=$cval1->firstaccess;
            $lastaccess=$cval1->lastaccess;
            $lastlogin=$cval1->lastlogin;
            $currentlogin=$cval1->currentlogin;
            $institution=$cval1->institution;
        }
		$name = $fname.' '.$lname;
        $logintime = $DB->get_record_sql("SELECT * FROM `{logstore_standard_log}` where userid='".$allusers[$countervalue]."' and action='loggedin' and (timecreated  >='". $starttime ."' and timecreated  <='". $maxtime ."') order by id limit 0,1");
        if ($logintime == null) {
            $firstaccess = '-';
        } else {
            $firstaccess = date('m-d-Y h:i:s a',$logintime->timecreated);
        }
        $lastlogintime = $DB->get_record_sql("SELECT * FROM `{logstore_standard_log}` where userid='".$allusers[$countervalue]."' and action='loggedin' and (timecreated  >='". $starttime ."' and timecreated  <='". $maxtime ."') order by id desc limit 0,1");			
        if ($lastlogintime == null) {
            $lastlogin = '-';
        } else {
            $lastlogin = date('m-d-Y h:i:s a',$lastlogintime->timecreated);
        }
        $logouttime = $DB->get_record_sql("SELECT * FROM `{logstore_standard_log}` where userid='".$allusers[$countervalue]."' and action='loggedout' and (timecreated  >='". $starttime ."' and timecreated  <='". $maxtime ."') order by id desc limit 0,1");
        if ($logouttime == null) {
        $lastaccess = '-';
        } else {
        $lastaccess = date('m-d-Y h:i:s a',$logouttime->timecreated);
        }
?>
<tr>
<td align="center"><?php echo $i?></td>
<td align="center"><a href="<?php echo $CFG->wwwroot;?>/user/profile.php?id=<?php echo $allusers[ $countervalue];?>"><?php echo $username;?></a></td>
<td align="left"><a href="<?php echo $CFG->wwwroot;?>/user/profile.php?id=<?php echo $allusers[ $countervalue];?>"><?php echo $name;?></a></td>
<td align="center"><?php echo $firstaccess;?></td>
<td align="center"><?php echo $lastlogin;?></td>
<td align="center"><?php echo $lastaccess;?></td>
<td align="center">
<?php
        if ($dedicationtime !='' ) {
            echo $dedicationtime;
        } else {
            echo "Never";
        }
?></td>
</tr><?php
        $i++;
        $dedicationtime = '';
        $coursetable = '';
        $countervalue++;
	}
?>
</tbody>
<tfoot>
</tfoot>
</table>
<center></form>
<form action="<?php echo $CFG->wwwroot?>/report/ktreeuserdedicationreport/index.php" action="get">
<div></div></form></center>
</div>
<?php
    if (isset($_GET['selectuser']) && $_GET['selectuser'] == 0 || isset($_GET['limit']) && ($_GET['limit'] != null)) {
        echo "<form action='#' method='get'><div class='pagination-div'>Records per page: ";
        echo "<div class='check_style'><select name='limit' id='limit' onchange='this.form.submit();'>";
        foreach ($recsarray as $reci => $recv) {
            if ($recv == $limit) {
                echo "<option value='".$recv."' selected>".$recv."</option>";
            } else {
                echo "<option value='".$recv."'>".$recv."</option>";
            }
        }
        echo "</select></div>";
        $url = '?limit='.$limit.'&'.'selectuser='.$_GET['selectuser='].'&';
        echo pagination($statement, $limit, $page, $url)."</div></form>";
    }
?>
</body>
</html>
<?php
}
echo $OUTPUT->footer();
?>
<link href="css/pagination.css" rel="stylesheet" type="text/css" />
<link href="css/B_blue.css" rel="stylesheet" type="text/css" />
<link href="css/styles.css" rel="stylesheet" type="text/css" />
