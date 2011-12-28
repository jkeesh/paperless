<?php
require_once("../models/SectionLeader.php");
require_once("../models/Student.php");
require_once("../models/Course.php");
require_once("../models/PaperlessAssignment.php");
require_once("../models/Quarter.php");

$quarter = Quarter::current();
$class = "cs106a";
$course = Course::from_name_and_quarter_id($class, $quarter->id);

$sunetid = $_GET['student'];

$the_student = new Student;
$the_student->from_sunetid_and_course($sunetid, $course);
$sl = $the_student->get_section_leader();

echo $sl->sunetid;
// $class= "cs106a";
// $sl = Model::getSectionLeaderForStudent($sunetid, $class);

//echo $sl;
?>
