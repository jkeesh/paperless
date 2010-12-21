<?php
class IndexHandler extends ToroHandler {
  
    public function get() {
        // for now this is hard coded .. need to setup DB access
	
        $studentdir = DUMMYDIR;
        //$dirname="/afs/ir.stanford.edu/class/".$classname."/submissions/".$sl."/";
        $dirname = SUBMISSIONS_DIR . "/" . USERNAME ."/";
        
        $assns = $this->getDirEntries($dirname);
        
        // assign template variables
        $this->smarty->assign("assignments", $assns);
        
        // display the template
        $this->smarty->display('index.html');
    }
}
?>