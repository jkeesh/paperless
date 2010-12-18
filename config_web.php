<?php
//This is a configuration file that is not tracked by git. The difference is that
//there is a different configuration file locally and on the web.

define('BASE_DIR', dirname(__FILE__));

print_r($_COOKIE);
echo $_COOKIE['SignOnDefault'];


$username = $_COOKIE['SignOnDefault'];
if(!$username) $username = 'zhoud';
$username = strtolower($username);

if($username == "zhoud") $classname = "cs106x";
else $classname = "cs106a";

if($classname == "cs106x") $dummy = "2_ADTS";
else $dummy = "breakout";

define('DUMMYDIR', $dummy);
define('USERNAME', $username);
define('CLASSNAME', $classname);

$submissions = '/afs/ir/class/'.$classname.'/submissions';

define('SUBMISSIONS_DIR', $submissions);
define('ROOT_URL', 'http://stanford.edu/class/cs198/cgi-bin/paperless/'); 

?>

