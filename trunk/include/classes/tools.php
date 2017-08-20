<?php


	class hw_tools{

		use hw_hidden_methods_props;

		private $complete_remove_post = [];

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


		/**
		 * @param $post_id
		 * @return hw_tool_comlete_remove_post
		 */
		public function complete_remove_post( $post_id ){
			if(!isset($this->complete_remove_post[$post_id])) $this->complete_remove_post[$post_id] = new hw_tool_comlete_remove_post( $post_id );
			return $this->complete_remove_post[$post_id];
		}

	}