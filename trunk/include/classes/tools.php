<?php


	class hw_tools{

		use hw_hidden_methods_props;


		public function __construct(){
			hiweb()->path()->include_dir( hiweb()->dir_tools );
		}


		/**
		 * @return hw_tool_thumbnail_upload
		 */
		public function thumbnail_upload(){
			static $class;
			if( !$class instanceof hw_tool_thumbnail_upload ) $class = new hw_tool_thumbnail_upload();
			return $class;
		}

	}