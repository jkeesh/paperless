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

		/*
		 * This method handles all the basic setup that we want in all of the pages.
		 * This includes, setting up the course, the role, and role string.
		 */
		public function basic_setup(){
			$args = func_get_args();
			$args = $args[0];
			
			$this->current_quarter = Quarter::current();
			$this->smarty->assign("current_quarter", $this->current_quarter);

			$this->settings = Settings::get_for_user($this->user);		
			$this->smarty->assign("settings", $this->settings);
	
			if(count($args) < 2){
				$this->smarty->assign("role", 0);							
			}else{
				$qid = $args[0];
				$class = $args[1];

				$this->course = Course::from_name_and_quarter_id($class, $qid);
				$this->smarty->assign("course", $this->course);		
				$this->smarty->assign("quarter_id", $qid);	
				
				$this->user->set_course($this->course);
				$this->role = $this->user->get_role();
				$this->smarty->assign("role", $this->role);	
				$this->smarty->assign("role_string", $this->user->get_role_string());		
			}
		}
		
		public function __construct() {
			// We creat a user that can be accessed by all of the controllers. This contains
			// The basic information, like, name, sunetid, etc.
			$this->user = new User;
			$this->user->from_sunetid(USERNAME);
			
			$this->smarty = new Smarty();
			$this->smarty->assign("user", $this->user);
			
			
			$this->smarty->template_dir = BASE_DIR . '/views/templates/';
			$this->smarty->compile_dir  = BASE_DIR . '/views/templates_c/';
			$this->smarty->config_dir   = BASE_DIR . '/views/configs/';
			$this->smarty->cache_dir    = BASE_DIR . '/views/cache/';
			
			if(IS_LOCAL){
				$this->smarty->assign("DEBUG_ON", 'true');				
			}else{
				$this->smarty->assign("DEBUG_ON", 'false');								
			}
			
			// assign vars we need on every page
			$this->smarty->assign("root_url", ROOT_URL);
						
			$this->smarty->assign("POSITION_TEACHING_ASSISTANT", POSITION_TEACHING_ASSISTANT);
			$this->smarty->assign("POSITION_SECTION_LEADER", POSITION_SECTION_LEADER);
			$this->smarty->assign("POSITION_COURSE_HELPER", POSITION_COURSE_HELPER);
			$this->smarty->assign("POSITION_STUDENT", POSITION_STUDENT);
			
			if(usingIE()){
				$this->smarty->assign("ie", 1);
			}
			
			$this->smarty->assign("version", "1.2.0.4");	// April 25, 2012, 2:10am
		}
		
		public function __call($name, $arguments) {			
            $this->smarty->assign('errorMsg', 'Tried to __call controller.');
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
	require_once('controllers/student.php');    // lists the students for a given assignment
	require_once('controllers/code.php');       // shows the code view
	require_once('controllers/assignment.php'); // lists the assignments for a given student
	require_once('controllers/sectionleader.php'); // lists the assignments and students for a sectionleader
	require_once('controllers/admin.php');
	require_once('controllers/ddsubmit.php');	// drag drop submit
	require_once('controllers/ddupload.php');	// drag drop upload
	require_once('controllers/setuser.php');
	require_once('controllers/error.php');
	require_once('controllers/manage.php');
	require_once('controllers/download.php');
	require_once('controllers/router.php');
	require_once('controllers/settings.php');
	require_once('controllers/ajax.php');
	require_once('controllers/downloadassignment.php');



	// This regex represents the url for a course. It contains first
	// the quarter id as an integer, and then the class name
	$course_regex = '^\/([0-9]+)\/([a-zA-Z0-9_ \-]+)\/';
	$sunet_regex = '([a-zA-Z0-9_ -]+)';
	$assn_regex = '([a-zA-Z0-9_ -]+)';
		
	/*
	 * URL routes
	 */
	$site = new ToroApplication(Array(
		 							  Array('^\/user\/'.$sunet_regex.'\/?$', 'regex', 'SetUser'),
		 							  Array('^\/ajax\/?$', 'regex', 'AjaxHandler'),
		 							  Array('^\/settings\/?$', 'regex', 'SettingsHandler'),
									  Array($course_regex. 'student\/'.$sunet_regex.'\/?$', 'regex', 'StudentHandler'),
									  Array($course_regex. 'code\/'.$assn_regex.'\/'.$sunet_regex.'(\/print)?$', 'regex', 'CodeHandler'),
									  Array($course_regex. 'download\/'.$sunet_regex.'\/'.$assn_regex.'\/?$', 'regex', 'DownloadHandler'),
									  Array($course_regex. 'assignment\/'.$sunet_regex.'\/'.$assn_regex.'\/?$', 'regex', 'AssignmentHandler'),
									  Array($course_regex. 'sectionleader\/'.$sunet_regex.'\/?$', 'regex', 'SectionLeaderHandler'),
									  Array($course_regex. 'admin\/?$', 'regex', 'AdminHandler'),
									  Array($course_regex. 'manage\/?$', 'regex', 'ManageHandler'),
									  Array($course_regex. 'ddsubmit\/?$', 'regex', 'DragDropSubmitHandler'),
									  Array($course_regex. 'ddupload\/?$', 'regex', 'DragDropUploadHandler'),
									  Array($course_regex. 'downloadassignment\/'.$sunet_regex.'\/' . $assn_regex. '\/?$', 'regex', 'DownloadAssignmentHandler'),
									  Array('(.*)', 'regex', 'RouterHandler'),
									  // Array('(.*)', 'regex', 'ErrorHandler'),
									  ));
	
	if(isset($_REQUEST['path']))
	$_SERVER['PATH_INFO'] = $_REQUEST['path'];
	$site->serve();
