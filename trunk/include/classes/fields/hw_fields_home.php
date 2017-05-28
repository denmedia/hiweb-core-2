<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 20.05.2017
	 * Time: 15:39
	 */
	class hw_fields_home{


		/** @var hw_field[] */
		public $fields = array();
		/** @var hw_field_frontend[] */
		public $fields_byContext = array();
		/** @var array */
		private $fieldId_globalId = array();
		/** @var array */
		private $globalId_fieldId = array();


		/**
		 * @return hw_field[]
		 */
		public function fields(){
			return $this->fields;
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
			$field->label( $fieldName );
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
			if( isset( $this->fieldId_globalId[ $field->id() ] ) && is_array( $this->fieldId_globalId[ $field->id() ] ) )
				foreach( $this->fieldId_globalId[ $field->id() ] as $index => $globalIds ){
					if( $globalIds == $oldGlobalId ){
						$this->fieldId_globalId[ $field->id() ][ $index ] = $newGlobalId;
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
		public function get_frontend( $fieldId, $contextId = null ){
			$global_id = hiweb()->fields()->context_to_id($fieldId, $contextId);
			if( !array_key_exists( $global_id, $this->fields_byContext ) ){
				$this->fields_byContext[ $global_id ] = new hw_field_frontend( $fieldId, $contextId, $global_id );
			}
			return $this->fields_byContext[ $global_id ];
		}


	}