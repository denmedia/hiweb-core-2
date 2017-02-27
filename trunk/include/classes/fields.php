<?php

	include_once 'fields/hw_field_location.php';
	include_once 'fields/hw_field.php';
	include_once 'fields/hw_fields_static.php';
	include_once 'fields/hw_field_frontend.php';


	class hw_fields{


		use hw_hidden_methods_props;


		private $dir = 'fields';

		/** @var hw_field_location[] */
		private $hooks = array();

		/** @var hw_field[] */
		private $fields = array();
		/** @var hw_field_frontend[] */
		private $fields_byContext = array();

		private $fieldId_globalId = array();
		private $globalId_fieldId = array();

		/**  */
		public $hook_fields = array();

		public $loop_rows_field;
		private $current_row = array();


		public function __construct(){
			$this->dir = hiweb()->dir_classes . '/' . $this->dir;
			///
			add_action( 'current_screen', array(
				$this, 'do_distribute'
			), 999999999999 );
			///
			add_action( 'save_post', array(
				$this, 'save_post'
			) );
			///
			add_action( 'personal_options_update', array(
				$this, 'user_options_update'
			) );
			add_action( 'edit_user_profile_update', array(
				$this, 'user_options_update'
			) );
			///
			add_action( 'init', function(){
				if( function_exists( 'get_taxonomies' ) ){
					foreach( get_taxonomies() as $taxonomy ){
						//Save Term
						add_action( 'create_term', array(
							$this, 'save_taxonomy'
						), 99 );
						add_action( 'edited_' . $taxonomy, array(
							$this, 'save_taxonomy'
						), 99 );
					}
				}
			} );
		}


		/**
		 * Добавить поле
		 * @param        $fieldId
		 * @param string $type
		 * @param null   $fieldName
		 * @return hw_field
		 */
		public function make( $fieldId, $type = 'text', $fieldName = null ){
			$global_id = hiweb()->string()->rand();
			$field = new hw_field( $fieldId, $global_id, $type );
			$field->name( $fieldName );
			$this->fields[ $global_id ] = $field;
			$this->fieldId_globalId[ $fieldId ][] = $field;
			$this->globalId_fieldId[ $global_id ][] = $field;
			return $field;
		}


		/**
		 * Return TRUE, if field exists
		 * @param $fieldOrGlobalId
		 * @return bool
		 */
		public function is_exists( $fieldOrGlobalId ){
			return isset( $this->fieldId_globalId[ $fieldOrGlobalId ] ) || isset( $this->globalId_fieldId[ $fieldOrGlobalId ] );
		}


		/**
		 * Get all exists fields
		 * @return hw_field[]
		 */
		public function get_fields(){
			return $this->fields;
		}


		/**
		 * @param $field_id
		 * @return hw_field
		 */
		public function get( $field_id ){
			if( !isset( $this->fieldId_globalId[ $field_id ] ) ){
				hiweb()->console()->warn( sprintf( __( 'Field id:[%s] not found to display value by context', 'hw-core-2' ), $field_id ) );
				return $this->make( $field_id );
			}
			return end( $this->fieldId_globalId[ $field_id ] );
		}


		/**
		 * @param      $fieldId
		 * @param null $contextId
		 * @return hw_field_frontend
		 */
		public function get_byContext( $fieldId, $contextId = null ){
			$global_id = hiweb()->string()->rand();
			$this->fields_byContext[ $fieldId ] = new hw_field_frontend( $fieldId, $global_id, $contextId );
			return $this->fields_byContext[ $fieldId ];
		}


		/**
		 * Добавление функции в бэк-энд редакторе типов поста, пользователя, таксономий
		 * @param hw_field $hw_field
		 * @return hw_field_location
		 */
		protected function hook( hw_field $hw_field ){
			$edit_hook = new hw_field_location( $hw_field );
			$this->hooks[] = $edit_hook;
			return $edit_hook;
		}


		/**
		 * @return hw_field_location[]
		 */
		protected function get_hooks(){
			$R = array();
			if( is_array( $this->hooks ) )
				foreach( $this->hooks as $hook ){
					if( $hook instanceof hw_field_location )
						$R[] = $hook;
				}
			return $R;
		}


		///////////////////////

		protected function do_distribute(){
			include 'fields/do_distribute.php';
		}


		/**
		 * @param null $post_id
		 * @param null $post
		 */
		private function save_post( $post_id = null, $post = null ){
			/*$update_data = array();
			foreach( $_POST as $key => $value ){
				if( strpos( $key, hiweb()->fields()->input_prefix ) === 0 ){
					$update_data[ $key ] = $value;
				}
			}*/
			/** @var hw_field_location $hook */
			foreach( $this->get_hooks() as $hook ){
				$input_name = $hook->get_field()->input()->name;
				if( array_key_exists( $input_name, $_POST ) ){
					//todo: сделать фильтр, если данного поля на самом деле не должно быть
					update_post_meta( $post_id, $hook->get_field()->input()->name, $_POST[ $input_name ] );
				}
			}
		}


		/**
		 * @param integer $term_id
		 */
		private function save_taxonomy( $term_id ){
			/*$update_data = array();
			foreach( $_POST as $key => $value ){
				if( strpos( $key, hiweb()->fields()->input_prefix ) === 0 ){
					$update_data[ $key ] = $value;
				}
			}*/
			/** @var hw_field_location $hook */
			foreach( $this->get_hooks() as $hook ){
				$input_name = $hook->get_field()->input()->name;
				if( array_key_exists( $input_name, $_POST ) ){
					//todo: сделать фильтр, если данного поля на самом деле не должно быть
					update_term_meta( $term_id, $hook->get_field()->input()->name, $_POST[ $input_name ] );
				}
			}
		}


		/**
		 * User Options Update
		 * @param $user_id
		 */
		private function user_options_update( $user_id ){
			if( !isset( $_POST['user_id'] ) )
				return;
			foreach( $this->get_hooks() as $hook ){
				if( count( $hook->get_rules_by_group( 'users' ) ) > 0 && isset( $_POST[ $hook->get_field()->input()->name ] ) ){
					update_user_meta( $_POST['user_id'], $hook->get_field()->input()->name, $_POST[ $hook->get_field()->input()->name ] );
				}
			}
		}


		/**
		 * @return mixed
		 */
		public function the_row(){
			if( $this->loop_rows_field instanceof hw_field ){
				$this->current_row = $this->loop_rows_field->input()->the_row();
				return $this->current_row;
			}
			return null;
		}


		public function get_sub_field( $sub_field_id ){
			if( is_array( $this->current_row ) && array_key_exists( $sub_field_id, $this->current_row ) )
				return $this->current_row[ $sub_field_id ]; else return null;
		}

	}