<?php

require_once("../models/PaperlessAssignment.php");
$class = "cs106a";

$class = $_GET['class'];


$assns = PaperlessAssignment::loadForClass($class);
//echo $class;
//print_r($assns);

echo json_encode($assns);

?>
