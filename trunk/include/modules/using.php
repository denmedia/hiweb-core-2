<?php
	
	
	class hw_using {
		
		private $paths = array();
	
	
		public function __construct($path = null){
			hiweb()->path()->mkdir($path);
			$debug = debug_backtrace();
			hiweb()->console($path);
			hiweb()->console($debug);
			add_action('shutdown',function(){
				
			});
		}
		
		
	}