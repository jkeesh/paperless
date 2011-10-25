<?php
	class ErrorHandler extends ToroHandler {
		
		public function get() {
            $this->smarty->assign('errorMsg', "Unknown error");
			$this->smarty->display('error.html');
		}
	}
	?>
