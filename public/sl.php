<?php

//echo $sunetid;

require_once("../models/Model.php");



$sunetid = $_GET['student'];
$class= "cs106a";
$sl = Model::getSectionLeaderForStudent($sunetid, $class);

echo $sl;
?>
