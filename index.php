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
      $this->smarty = new Smarty();

      $this->smarty->template_dir = BASE_DIR . '/templates/templates/';
      $this->smarty->compile_dir  = BASE_DIR . '/templates/templates_c/';
      $this->smarty->config_dir   = BASE_DIR . '/templates/configs/';
      $this->smarty->cache_dir    = BASE_DIR . '/templates/cache/';
      
      // assign vars we need on every page
      $this->smarty->assign("username", USERNAME);
      $this->smarty->assign("root_url", ROOT_URL);
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

/*
* URL routes
*/
$site = new ToroApplication(Array(
  Array('/', 'string', 'IndexHandler'),
  Array('^\/student\/([a-zA-Z0-9_ ]+)\/?$', 'regex', 'StudentHandler'),
  Array('^\/code\/([a-zA-Z0-9_ -]+)\/([a-zA-Z0-9_]+)\/?$', 'regex', 'CodeHandler'),
  Array('^\/assignment\/([a-zA-Z0-9_ ]+)\/?$', 'regex', 'AssignmentHandler')
));

if(isset($_REQUEST['path']))
  $_SERVER['PATH_INFO'] = $_REQUEST['path'];

$site->serve();
