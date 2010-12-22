<?php
	/*
	 * Constants
	 */
	require_once('config.php');
	
	/*
	 * Third-party libraries
	 */
	require_once('lib/smarty/Smarty.class.php');
	require_once('lib/toro.php');
	
	/*
	 * Each controller extends the following class.
	 */
	class ToroHandler {
		protected $smarty;
		
		public function __construct() {
			$is_sl = Model::isSectionLeader(USERNAME);
			$is_student = Model::isStudent(USERNAME);
			$is_student_only = $is_student && !$is_sl;
			
			define('IS_STUDENT', $is_student);
			define('IS_STUDENT_ONLY', $is_student_only);
			define('IS_SECTION_LEADER', $is_sl);
			
			$this->smarty = new Smarty();
			
			$this->smarty->template_dir = BASE_DIR . '/views/templates/';
			$this->smarty->compile_dir  = BASE_DIR . '/views/templates_c/';
			$this->smarty->config_dir   = BASE_DIR . '/views/configs/';
			$this->smarty->cache_dir    = BASE_DIR . '/views/cache/';
			
			// assign vars we need on every page
			$this->smarty->assign("username", USERNAME);
			$this->smarty->assign("root_url", ROOT_URL);
			$this->smarty->assign("quarter_name", Model::getQuarterName());
			$this->smarty->assign("display_name", Model::getDisplayName(USERNAME));
			
			$this->smarty->assign("is_section_leader", $is_sl);
			$this->smarty->assign("is_student", $is_student);
			// if($is_sl && $is_student){
			// 		 echo "sl and student";
			// 		 return;
			//  	  }
			
			if($is_sl) $sectionLeader = USERNAME;
			else $sectionLeader = Model::getSectionLeaderForStudent(USERNAME);
			
			define('SECTION_LEADER', $sectionLeader);
			
			$this->smarty->assign("section_leader", $sectionLeader);
			$this->smarty->assign("students", Model::getStudentsForSectionLeader(USERNAME));
		}
		
		public function __call($name, $arguments) {
			header("HTTP/1.0 404 Not Found");
			echo "404 Not Found";
			exit;
		}
		
		/*
		 * Gets information from the directories found in a path.
		 */
		protected function getDirEntries($dirname) {
			$entries = array();
			$dir = opendir($dirname);
			while($entry = readdir($dir)) {
				if(strpos($entry, ".") === false)
					$entries[] = $entry;
			}
			return $entries;
		}
		
	}
	
	/*
	 * Controllers
	 */
	require_once('controllers/main.php');       // builds index page (lists assignments and students)
	require_once('controllers/student.php');    // lists the students for a given assignment
	require_once('controllers/code.php');       // shows the code view
	require_once('controllers/assignment.php'); // lists the assignments for a given student
	require_once('controllers/sectionleader.php'); // lists the assignments and students for a sectionleader
	
	/*
	 * URL routes
	 */
	$site = new ToroApplication(Array(
									  Array('^\/([a-zA-Z0-9_]*)\/?$', 'regex', 'IndexHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/student\/([a-zA-Z0-9_ \-]+)\/?$', 'regex', 'StudentHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/code\/([a-zA-Z0-9_ -]+)\/([a-zA-Z0-9_]+)\/?$', 'regex', 'CodeHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/assignment\/([a-zA-Z0-9_ ]+)\/?$', 'regex', 'AssignmentHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/sectionleader\/([a-zA-Z0-9_ \-]+)\/?$', 'regex', 'SectionLeaderHandler'),
									  ));
	
	if(isset($_REQUEST['path']))
	$_SERVER['PATH_INFO'] = $_REQUEST['path'];
	
	$site->serve();
