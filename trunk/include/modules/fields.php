<?php
	
	
	class hw_fields{
		
		private $fields = array();
		
		
		public function give( $fieldId, $contextId = null, $contextType = null ){
			if( is_null( $contextId ) ){
				if( function_exists( 'get_queried_object' ) ){
					$contextId = get_queried_object();
				}
			}
			///
			if( $contextId instanceof WP_Post ){
				$contextId = $contextId->ID;
				$contextType = 'post';
			}elseif( $contextId instanceof WP_Term ){
				$contextId = $contextId->term_id;
				$contextType = 'term';
			}elseif( $contextId instanceof WP_User ){
				$contextId = $contextId->ID;
				$contextType = 'user';
			}elseif(is_string($contextId)){
				$contextId = sanitize_file_name(strtolower($contextId));
				$contextType = 'options';
			}elseif(is_integer($contextId)){
				$contextType = 'post';
			}
			///
			if(!isset($this->fields[$contextType][$contextId])){
				$this->fields[$contextType][$contextId] = new hw_field($fieldId, $contextId, $contextType);
			}
			return $this->fields[$contextType][$contextId];
		}
		
	}
	
	
	class hw_field{
		
		private $id;
		
		private $input;
		
		private $contextId;
		private $contextType;
		
		
		public function __construct($fieldId, $contextId = '', $contextType = 'post'){
			$this->id = sanitize_file_name(strtolower($fieldId));
			$this->contextId = $contextId;
			$this->contextType = $contextType;
			switch($this->contextType){
				case 'post':
					$this->input = hiweb()->post($contextId)->get_field($fieldId);
					break;
				case 'term':
					//todo
					break;
				case 'user':
					//todo
					break;
				case 'options':
					$this->input = hiweb()->admin()->menu()->get($contextId)->get_field($this->id);
					break;
			}
		}
		
		
		/**
		 * @return mixed
		 */
		public function id(){
			return $this->id;
		}
		
		
		/**
		 * @return string
		 */
		public function type_id(){
			return $this->contextId;
		}
		
		
		/**
		 * @return string
		 */
		public function type(){
			return $this->contextType;
		}
		
		
		/**
		 * @param null $args
		 * @return mixed
		 */
		public function get($args = null){
			return $this->input->value($args);
		}
		
		
		/**
		 * @param null $args
		 * @return string
		 */
		public function get_content($args = null){
			return $this->input()->get_content($args);
		}
		
		
		/**
		 * @param null $args
		 * @return string
		 */
		public function the_content($args = null){
			return $this->input()->the_content($args);
		}
		
		
		/**
		 * @param null $args
		 */
		public function the($args = null){
			return $this->input->the_value($args);
		}
		
		
		/**
		 * @return bool|int
		 */
		public function have_rows(){
			return ( is_array( $this->input->value() ) ? count( $this->input->value() ) : false );
		}
		
		
		/**
		 * @return hw_input|hw_input_checkbox|hw_input_repeat|hw_input_text
		 */
		public function input(){
			return $this->input;
		}
		
	}