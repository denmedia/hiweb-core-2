<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 26.02.2017
	 * Time: 21:42
	 */
	class hw_field_frontend{

		private $id;
		private $global_id;
		/** @var null|hw_field */
		private $field;
		private $contextId;


		public function __construct( $fieldId, $global_id, $contextId = null ){
			$this->id = $fieldId;
			$this->contextId = $contextId;
			if( $this->is_exists() ){
				$this->field = hiweb()->fields()->get( $fieldId );
				$this->field->value( $this->value() );
			} else {
				hiweb()->console()->warn( sprintf( __( 'Filed [%s] not exists', 'hw-core-2' ), $fieldId ), true );
			}
		}


		/**
		 * @return mixed
		 */
		public function id(){
			return $this->id;
		}


		/**
		 * @return bool
		 */
		public function is_exists(){
			return hiweb()->fields()->is_exists( $this->id );
		}


		/**
		 * @return mixed|null
		 */
		public function value(){
			if( !$this->is_exists() )
				return null;
			return hw_fields_static::get_value_by_context( $this->id, $this->contextId );
		}


		/**
		 * @return mixed
		 */
		public function reset_row(){
			return $this->field->input()->reset_row();
		}


		/**
		 * @return bool
		 */
		public function have_rows(){
			if( !$this->is_exists() )
				return false;
			hiweb()->fields()->loop_rows_field = $this->field;
			return $this->field->input()->have_rows();
		}


		public function get_sub_field( $subFieldId ){
			//todo
		}


	}