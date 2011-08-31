<?php
	//This is a configuration file for running paperless locally

	//This is the only thing you should change	
	define('USERNAME', 'jkeeshin');
	
	define('BASE_DIR', dirname(__FILE__));
	
	define('POSITION_NOT_A_MEMBER', -1);
	define('POSITION_STUDENT', 1);
	define('POSITION_APPLICANT', 2);
	define('POSITION_COURSE_HELPER', 3);
	define('POSITION_SECTION_LEADER', 4);
	define('POSITION_TEACHING_ASSISTANT', 5);
	define('POSITION_LECTURER', 6);
	define('POSITION_COORDINATOR', 7);
		
	//we will look for submissions in directories like
	// SUBMISSIONS_PREFIX/class/SUBMISSIONS_DIR/sl/student/codefiles
	define('SUBMISSIONS_PREFIX', 'submission_files');
	define('SUBMISSIONS_DIR', 'submissions');
	
	define('ROOT_URL', 'http://localhost:8888/paperless/'); 
	
	define('CLASS_CONFIG_DIR', 'class_configs');

	define('MYSQL_HOST', 'localhost');
	define('MYSQL_USERNAME', 'root');
	define('MYSQL_PASSWORD', 'root');
	define('MYSQL_DATABASE', 'paperless');
	define('ASSIGNMENT_COMMENT_TABLE', "AssignmentComments");
	define('ASSIGNMENT_FILE_TABLE', 'AssignmentFiles');
	
	define('FACEBOOK_APP_ID', '120178364723061');
?>