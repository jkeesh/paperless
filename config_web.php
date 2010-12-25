<?php
	//This is a configuration file for running paperless locally
	
	//$sunetid = $_ENV['WEBAUTH_USER'];
	//$username = strtolower($username);
	define('USERNAME', 'jkeeshin');
	
	define('BASE_DIR', dirname(__FILE__));
	
	define('POSITION_STUDENT', 1);
	define('POSITION_APPLICANT', 2);
	define('POSITION_COURSE_HELPER', 3);
	define('POSITION_SECTION_LEADER', 4);
	define('POSITION_TEACHING_ASSISTANT', 5);
	define('POSITION_LECTURER', 6);
	define('POSITION_COORDINATOR', 7);
		
	//we will look for submissions in directories like
	// SUBMISSIONS_PREFIX/class/SUBMISSIONS_DIR/sl/student/codefiles
	define('SUBMISSIONS_PREFIX', '/afs/ir/class');
	define('SUBMISSIONS_DIR', 'submissions');
	
	define('ROOT_URL', 'http://paperless.stanford.edu/');
	
	define('MYSQL_HOST', 'mysql-user.stanford.edu');
	define('MYSQL_USERNAME', 'ccs198paperless');
	define('MYSQL_PASSWORD', 'chauweif');
	define('MYSQL_DATABASE', 'c_cs198_paperless');
	
	define('ASSIGNMENT_COMMENT_TABLE', "AssignmentComments");
	define('ASSIGNMENT_FILE_TABLE', 'AssignmentFiles');
	
	?>