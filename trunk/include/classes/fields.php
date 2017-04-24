<?php

	include_once 'fields/hw_fields_locations.php';
	include_once 'fields/hw_field.php';
	include_once 'fields/hw_field_frontend.php';
	include_once 'fields/hw_fields_admin.php';


	class hw_fields{


		use hw_hidden_methods_props;


		/** @var hw_field[] */
		public $fields = array();
		/** @var hw_field_frontend[] */
		public $fields_byContext = array();

		private $fieldId_globalId = array();
		private $globalId_fieldId = array();

		/**  */
		public $hook_fields = array();

		/** @var hw_field */
		public $loop_rows_field;
		private $current_row = array();


		/**
		 * @param string $field_id
		 * @param string $page_slug
		 * @return string
		 */
		public function get_options_field_id( $page_slug, $field_id ){
			return $page_slug . '-' . $field_id;
		}


		/**
		 * @param $page_slug
		 * @return string
		 */
		public function get_options_group_id( $page_slug ){
			return 'hiweb-options-group-' . $page_slug;
		}


		/**
		 * @param $field_id
		 * @return string
		 */
		public function get_columns_field_id( $field_id ){
			return 'hiweb-column-' . $field_id;
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
		 * Смена глобального ID для поля
		 * @param $oldGlobalId
		 * @param $newGlobalId
		 * @return bool
		 */
		public function change_globalId( $oldGlobalId, $newGlobalId ){
			if( !isset( $this->fields[ $oldGlobalId ] ) )
				return false;
			$field = $this->fields[ $oldGlobalId ];
			unset( $this->fields[ $oldGlobalId ] );
			$this->fields[ $newGlobalId ] = $field;
			if( isset( $this->fieldId_globalId[ $field->get_id() ] ) && is_array( $this->fieldId_globalId[ $field->get_id() ] ) )
				foreach( $this->fieldId_globalId[ $field->get_id() ] as $index => $globalIds ){
					if( $globalIds == $oldGlobalId ){
						$this->fieldId_globalId[ $field->get_id() ][ $index ] = $newGlobalId;
					}
				}
			if( isset( $this->globalId_fieldId[ $oldGlobalId ] ) && is_array( $this->globalId_fieldId[ $oldGlobalId ] ) ){
				$ids = $this->globalId_fieldId[ $oldGlobalId ];
				unset( $this->globalId_fieldId[ $oldGlobalId ] );
				$this->globalId_fieldId[ $newGlobalId ] = $ids;
			}
			return true;
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


		//		/**
		//		 * @return mixed
		//		 */
		//		public function reset_rows(){
		//			return $this->loop_rows_field->input()->reset_row();
		//		}

		/**
		 * @return hw_fields_locations
		 */
		public function locations(){
			static $class;
			if( !$class instanceof hw_fields_locations )
				$class = new hw_fields_locations();
			return $class;
		}


		/**
		 * Зарегистрировать тип инпута
		 * @param string $type
		 * @param        $callable
		 * @param int    $priority - приоритет определяет какой класс откроется
		 * @return void
		 */
		public function register_content_type( $type = 'text', $callable, $priority = 10 ){
			add_filter( 'hiweb-fields-content-type-' . $type, $callable, $priority, 3 );
		}


	}