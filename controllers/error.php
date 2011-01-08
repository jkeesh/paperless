<?php
	class ErrorHandler extends ToroHandler {
		
		public function get() {
			$this->smarty->display('error.html');
		}
	}
	?>