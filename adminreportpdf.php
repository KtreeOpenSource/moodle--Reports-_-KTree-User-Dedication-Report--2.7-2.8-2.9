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

ob_start();
global $OUTPUT, $PAGE, $CFG, $USER;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('lib.php');
require_once($CFG->libdir.'/tcpdf/tcpdf.php');
require_once($CFG->dirroot.'/report/ktreeuserdedicationreport/dedication_lib.php');
if (isset($_GET['date1']) && !empty($_GET['date1'])) {
    $date1 = $_GET['date1'];
}
if (isset($_GET['date2']) && !empty($_GET['date2'])) {
    $date2 = $_GET['date2'];
}
$querygetmodules1 = "SELECT * FROM {user} where deleted = 0 and username not in ('guest','admin') ";
$coursesmodules1 = $DB->get_records_sql($querygetmodules1);
$iterate = 0;
$allusers=array();
foreach ($coursesmodules1 as $uin => $udet) {
    $allusers[$iterate] = $udet->id;
    $iterate++;
}
$d1 = $date1.' 00:00:00';
$d2 = $date2.' 23:59:59';
$starttime = strtotime($d1);
$maxtime = strtotime($d2);
$table="<table ><thead><tr><td class='sno'>S.No</td><td>User Name</td><td>Total Time Spent</td><td>Email</td></tr></thead><tbody>";
$i = 1;
$query1 = "select id, fullname as sn from {course} where category != 0";
$courses1 = $DB->get_records_sql($query1);
foreach ($courses1 as $uin => $udet) {
    $courseshortname[$udet->id] = $udet->sn;
}
$i = 1;
$countervalue = 0;
while ($countervalue<count($allusers)) {
    $querygetmodules1 ="SELECT email AS mail FROM mdl_user WHERE id=$allusers[$countervalue]";
    $coursesmodules1 = $DB->get_records_sql($querygetmodules1);
    foreach ($coursesmodules1 as $cin1 => $cval1){
        $email=$cval1->mail;
	}
	$sql = "select * from {log} where  userid = " . $allusers[ $countervalue] . " and time  >='". $starttime ."' and time  <='". $maxtime ."' order by time ASC";
	$logs = $DB->get_records_sql($sql);
	$dedicationtime='';
	$limit1 = 60;
	if ($logs != null) {
		$previouslog = array_shift($logs);
		$previouslogtime = $previouslog->time;
		$sessionstart = $previouslogtime;
		$dedication = 0;
		foreach ($logs as $log) {
		    if (($log->time - $previouslogtime) > $limit1) {
		        $dedication += $previouslogtime - $sessionstart;
		        $sessionstart = $log->time;
		    }
		    $previouslogtime = $log->time;
		}
		$dedication += $previouslogtime - $sessionstart;
		$dedicationtime = block_dedication_manager::format_dedication($dedication);
	}
    $querygetmodules1 ="SELECT firstname,lastname,username FROM mdl_user WHERE id=$allusers[$countervalue]";
    $coursesmodules1 = $DB->get_records_sql($querygetmodules1);
    if ($coursesmodules1 != null) 
    foreach ($coursesmodules1 as $cin1 => $cval1) {
		$name = $cval1->username;
		$fname = $cval1->firstname;
		$lname = $cval1->lastname;
    }
    $name=$fname.' '.$lname;  	     
    if ($dedicationtime !='' ) {
        $dedicationtime = $dedicationtime;
    } else {
        $dedicationtime = "Never";
    }
    $table.= "<tr><td align='center'>". $i."</td>";
    $table.= "<td align='center'>".$name."</td>";
    $table.= "<td align='center'>".$dedicationtime."</td>";
    $table.= "<td align='center'>".$email."</td></tr>";$i++;
    $dedicationtime = '';
    $coursetable = '';
    $countervalue++;
}
$table.="</tbody><tfoot></tfoot></table>";
?>	
<style type='text/css'>
table, th{
   border: 1px solid black;
   text-align: center;
   table-layout: auto;
} 

td {
 border: 1px solid black;
text-align: left;

}
</style>
<?php
echo $table;
$pagepdf = ob_get_contents();
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('course dedication');
$pdf->SetTitle('course dedication pdf file');
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(4, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->setPrintHeader(false);
$pdf->SetFont('helveticai', '', 7);
$pdf->AddPage();
$pdf->WriteHTML($pagepdf,true, false, false, false, '');
$pdf->lastPage();
ob_end_clean();
$pdf->Output('Dedicationreport.pdf', 'D');
ob_end_flush();
redirect($CFG->wwwroot.'report/ktreeuserdedicationreport/adminreport.php');
