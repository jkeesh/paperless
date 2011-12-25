<?php
	//This is a configuration file for running paperless locally
	
	// Turn off all error reporting
	error_reporting(0);
	
	$sunetid = $_ENV['WEBAUTH_USER'];
	$username = strtolower($username);
	define('USERNAME', $username);
	define('BASE_DIR', dirname(__FILE__));
	
	define('POSITION_STUDENT', 1);
	define('POSITION_APPLICANT', 2);
	define('POSITION_COURSE_HELPER', 3);
	define('POSITION_SECTION_LEADER', 4);
	define('POSITION_TEACHING_ASSISTANT', 5);
	define('POSITION_LECTURER', 6);
	define('POSITION_COORDINATOR', 7);
	
	define('IS_LOCAL', false);	
	
	//we will look for submissions in directories like
	// SUBMISSIONS_PREFIX/class/SUBMISSIONS_DIR/sl/student/codefiles
	define('SUBMISSIONS_PREFIX', '/afs/ir/class/archive/cs/');
	define('SUBMISSIONS_DIR', 'submissions');
	
	define('CLASS_CONFIG_DIR', '/afs/ir/class/cs198/cgi-bin/paperless/class_configs');
	
	define('ROOT_URL', 'https://www.stanford.edu/class/cs198/cgi-bin/paperless/');
	
	define('ASSIGNMENT_COMMENT_TABLE', "AssignmentComments");
	define('ASSIGNMENT_FILE_TABLE', 'AssignmentFiles');
	
	define('FACEBOOK_APP_ID', '101468079939627');
	
	?>