<?php

require_once("../models/Course.php");
require_once("../models/PaperlessAssignment.php");
require_once("../models/Quarter.php");

$quarter = Quarter::current();
$class = $_GET['class'];
$course = Course::from_name_and_quarter_id($class, $quarter->id);
$assns = PaperlessAssignment::load_for_course($course);
echo json_encode($assns);

?>
