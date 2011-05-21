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
	require_once('controllers/utils.php');
	
	/*
	 * Each controller extends the following class.
	 */
	class ToroHandler {
		protected $smarty;
		
		public function __construct() {
			$is_sl = Model::isSectionLeader(USERNAME);
			$is_student = Model::isStudent(USERNAME);
			$is_student_only = $is_student && !$is_sl;
			//$is_admin = true;
			$is_admin = USERNAME == "sahami";
			
			define('IS_ADMIN', $is_admin);
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
			$this->smarty->assign("is_admin", $is_admin);
			
			if(usingIE()){
				$this->smarty->assign("ie", 1);
			}
			
			//if($is_sl) $sectionLeader = USERNAME;
			//else $sectionLeader = Model::getSectionLeaderForStudent(USERNAME);
			
			//define('SECTION_LEADER', $sectionLeader);
			
			$this->smarty->assign("FACEBOOK_APP_ID", FACEBOOK_APP_ID);
			
			$userClasses = Model::getClass(USERNAME);
			if(count($userClasses) > 1){
				$this->smarty->assign("multiple_classes", 1);				
			}
			//$this->smarty->assign("section_leader", $sectionLeader);	
			
			$this->smarty->assign("version", 1.2.0.1);	// May 21, 2011	
		}
		
		public function __call($name, $arguments) {			
			$this->smarty->display("error.html");
			exit;
		}
		
		function starts_with_lower($str) {
			$chr = mb_substr ($str, 0, 1, "UTF-8");
			return ctype_lower($chr);
		}
		
		function isValidDirectory($entry){
			///Note: this matches for assignment directories which start with lowercase
			///and also for student submission directories of the form sunetid_# .... however
			///it seems like a sunetid can have a '-' or '.' but I have never seen one. And I think dashes
			///mess up the url.... 
			return (preg_match("/^([a-z])([a-zA-z\d_]+)(_\d+)?$/", $entry) > 0) ? true : false;
		}
		
		/*
		 * Gets information from the directories found in a path.
		 */
		protected function getDirEntries($dirname) {
			$entries = array();
			if(!is_dir($dirname)) return false;
			$dir = opendir($dirname);
			while($entry = readdir($dir)) {
				if($this->isValidDirectory($entry))
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
	require_once('controllers/admin.php');
	require_once('controllers/submit.php');		// submit 
	require_once('controllers/ddsubmit.php');	// drag drop submit
	require_once('controllers/upload.php');		// upload
	require_once('controllers/ddupload.php');	// drag drop upload
	require_once('controllers/select.php'); // if a user has multiple classes with different roles, allow them to select the class
	require_once('controllers/setuser.php');
	require_once('controllers/error.php');
	require_once('controllers/manage.php');
	require_once('controllers/download.php');
		
	/*
	 * URL routes
	 */
	$site = new ToroApplication(Array(
		 							  Array('/select', 'string', 'SelectHandler'),
		 							  Array('^\/user\/([a-zA-Z0-9_ \-]+)\/?$', 'regex', 'SetUser'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/student\/([a-zA-Z0-9_ \-]+)\/?$', 'regex', 'StudentHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/code\/([a-zA-Z0-9_ -]+)\/([a-zA-Z0-9_]+)(\/print)?$', 'regex', 'CodeHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/download\/([a-zA-Z0-9_ -]+)\/([a-zA-Z0-9_]+)\/?(.*)?$', 'regex', 'DownloadHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/assignment\/([a-zA-Z0-9_ -]+)\/([a-zA-Z0-9_ ]+)\/?$', 'regex', 'AssignmentHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/sectionleader\/([a-zA-Z0-9_ \-]+)\/?$', 'regex', 'SectionLeaderHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/admin\/?$', 'regex', 'AdminHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/manage\/?$', 'regex', 'ManageHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/submit\/?$', 'regex', 'SubmitHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/ddsubmit\/?$', 'regex', 'DragDropSubmitHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/upload\/?$', 'regex', 'UploadHandler'),
									  Array('^\/([a-zA-Z0-9_ \-]+)\/ddupload\/?$', 'regex', 'DragDropUploadHandler'),
									  Array('^\/([a-zA-Z0-9_]*)\/?$', 'regex', 'IndexHandler'),
									  Array('(.*)', 'regex', 'ErrorHandler'),
									  ));
	
	if(isset($_REQUEST['path']))
	$_SERVER['PATH_INFO'] = $_REQUEST['path'];
	
	$site->serve();
