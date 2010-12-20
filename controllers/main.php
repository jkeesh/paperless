<?php
class IndexHandler extends ToroHandler {
  
    public function get() {
        // for now this is hard coded .. need to setup DB access
	
        $studentdir = DUMMYDIR;
        //$dirname="/afs/ir.stanford.edu/class/".$classname."/submissions/".$sl."/";
        $dirname = SUBMISSIONS_DIR . "/" . USERNAME ."/";
        
        $users = $this->getDirEntries($dirname . $studentdir);
        $assns = $this->getDirEntries($dirname);
        
        // assign template variables
        $this->smarty->assign("users", $users);
        $this->smarty->assign("assignments", $assns);

 		$student_suids = array();
		$student_names = array();
		foreach($students as $student){
			$student_suids[] = $student['SUNetID'];
			$student_names[] = $student['DisplayName'];
		}
		
		$this->smarty->assign("student_suids", $student_suids);
		$this->smarty->assign("student_names", $student_names);
        
        // display the template
        $this->smarty->display('index.html');
    }
}
?>