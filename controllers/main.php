<?php
class IndexHandler extends ToroHandler {
  
    public function get() {
        // for now this is hard coded .. need to setup DB access
		if(IS_STUDENT_ONLY){
		   echo "<script type='text/javascript'> window.location = '". ROOT_URL ."student/" . USERNAME ."'</script>";
		   return;
		}
	
        $studentdir = DUMMYDIR;
        $dirname = SUBMISSIONS_DIR . "/" . USERNAME ."/";
        
        $assns = $this->getDirEntries($dirname);
        
        // assign template variables
        $this->smarty->assign("assignments", $assns);
        
        // display the template
        $this->smarty->display('index.html');
    }
}
?>