<?php
class IndexHandler extends ToroHandler {
  
    public function get() {
    	print_r($_GET);
	print_r($_POST);
        // for now this is hard coded .. need to setup DB access
        $studentdir = "2_ADTS";
        //$dirname="/afs/ir.stanford.edu/class/".$classname."/submissions/".$sl."/";
        $dirname = SUBMISSIONS_DIR . "/" . USERNAME ."/";
        
        $users = $this->getDirEntries($dirname . $studentdir);
        $assns = $this->getDirEntries($dirname);
        
        // assign template variables
        $this->smarty->assign("users", $users);
        $this->smarty->assign("assignments", $assns);
        
        // display the template
        $this->smarty->display('index.html');
    }
}
?>