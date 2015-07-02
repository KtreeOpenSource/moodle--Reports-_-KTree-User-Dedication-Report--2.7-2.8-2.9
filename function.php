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
require_once ('../../config.php');
function pagination($query, $perpage = 10, $page = 1, $url = '?') {
    global $DB;
    $query = $DB->get_record_sql("SELECT COUNT(*) as `num` FROM {$query}");
    $total = $query->num;
    $adjacents = "2";
    $page = ($page == 0 ? 1 : $page);
    $start = ($page - 1) * $perpage;
    $prev = $page - 1;
    $next = $page + 1;
    $lastpage = ceil($total/$perpage);
    $lpm1 = $lastpage - 1;
    $pagination = "";
    if ($lastpage > 1) {
        $pagination .= "<ul class='pagination'>";
        $pagination .= "<li class='details'>Page $page of $lastpage</li>";
        if ($lastpage < 7 + ($adjacents * 2)) {
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page) {
                    $pagination.= "<li><a class='current'>$counter</a></li>";
                } else {
                    $pagination.= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                }
            }
        } elseif ($lastpage > 5 + ($adjacents * 2)) {
            if ($page < 1 + ($adjacents * 2)) {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page){
                        $pagination.= "<li><a class='current'>$counter</a></li>";
                    } else {
                        $pagination.= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                    }
			    }
            $pagination.= "<li class='dot'>...</li>";
            $pagination.= "<li><a href='{$url}page=$lpm1'>$lpm1</a></li>";
            $pagination.= "<li><a href='{$url}page=$lastpage'>$lastpage</a></li>";
            } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination.= "<li class='dot'>...</li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page) {
                        $pagination.= "<li><a class='current'>$counter</a></li>";
                    } else {
                        $pagination.= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                    }
                }
                $pagination.= "<li class='dot'>..</li>";
                $pagination.= "<li><a href='{$url}page=$lpm1'>$lpm1</a></li>";
                $pagination.= "<li><a href='{$url}page=$lastpage'>$lastpage</a></li>";
            } else {
                $pagination.= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination.= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination.= "<li class='dot'>..</li>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page) {
                        $pagination.= "<li><a class='current'>$counter</a></li>";
                    } else {
                        $pagination.= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                    }	
                }
            }
        }
        if ($page < $counter - 1) { 
            $pagination.= "<li><a href='{$url}page=$next'>Next</a></li>";
            $pagination.= "<li><a href='{$url}page=$lastpage'>Last</a></li>";
        } else {
            $pagination.= "<li><a class='current'>Next</a></li>";
            $pagination.= "<li><a class='current'>Last</a></li>";
        }
        $pagination.= "</ul>\n";
    }
    return $pagination;
}
