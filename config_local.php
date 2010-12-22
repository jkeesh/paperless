<?php
//This is a configuration file for running paperless locally

define('BASE_DIR', dirname(__FILE__));

define('DUMMYDIR', 'karel');
define('DUMMYDIR_106A', 'karel');
define('DUMMYDIR_106B', '2_ADTS');
define('DUMMYDIR_106L', '2_ADTS');
define('DUMMYDIR_106X', '2_ADTS');

define('POSITION_STUDENT', 1);
define('POSITION_APPLICANT', 2);
define('POSITION_COURSE_HELPER', 3);
define('POSITION_SECTION_LEADER', 4);
define('POSITION_TEACHING_ASSISTANT', 5);
define('POSITION_LECTURER', 6);
define('POSITION_COORDINATOR', 7);

	
define('USERNAME', 'jkeeshin');

//TODO remove classname and submissions directory from config file
define('CLASSNAME', 'cs106a');
define('SUBMISSIONS_DIR', 'submissions');

define('ROOT_URL', 'http://localhost:8888/paperless/'); 
define('MYSQL_HOST', 'localhost');
define('MYSQL_USERNAME', 'root');
define('MYSQL_PASSWORD', 'root');
define('MYSQL_DATABASE', 'paperless');
define('ASSIGNMENT_COMMENT_TABLE', "AssignmentComments");
define('ASSIGNMENT_FILE_TABLE', 'AssignmentFiles');

?>