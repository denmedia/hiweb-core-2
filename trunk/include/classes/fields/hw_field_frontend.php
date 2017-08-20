<?php

	require_once hiweb()->dir_classes . '/inputs/hw_input_axis_rows.php';


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

		private $GROUP = 'options';
		private $ARGS = [];
		private $value;
		/** @var hw_input_axis_rows */
		private $rows;


		public function __construct( $fieldId, $contextId = null, $global_id = '' ){
			$this->id = $fieldId;
			$this->contextId = $contextId;
			$this->global_id = $global_id;
			if( $this->is_exists() ){
				$this->field = hiweb()->fields()->home()->get( $fieldId );
				$this->field->value( $this->value() );
				$this->define_values();
			} else {
				hiweb()->console()->warn( sprintf( __( 'Field [%s] not exists', 'hw-core-2' ), $fieldId ), 3 );
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
		public function global_id(){
			return $this->global_id;
		}


		/**
		 * @return bool
		 */
		public function is_exists(){
			return hiweb()->fields()->home()->is_exists( $this->id );
		}


		private function define_values(){
			$this->GROUP = '';
			$this->ARGS = [];
			if( $this->is_exists() ){
				///
				$context_arr = hiweb()->fields()->context_to_array( $this->contextId );
				if( is_array( $context_arr ) ){
					$this->GROUP = $context_arr[0];
					$this->ARGS = $context_arr[1];
					$this->contextId = $context_arr[2];
					switch( $this->GROUP ){
						case 'options':
							$this->value = get_option( hiweb()->fields()->get_options_field_id( $this->contextId, $this->id() ), null );
							break;
						case 'post_type':
							$this->value = get_post_meta( $this->contextId->ID, $this->id(), true );
							break;
						case 'taxonomy':
							$this->value = get_term_meta( $this->contextId->term_id, $this->id(), true );
							break;
						case 'users':
							$this->value = get_user_meta( $this->contextId->ID, $this->id(), true );
							break;
						default:
							hiweb()->console()->warn( sprintf( __( 'It is not possible to define the context for the field: [%s], since the action has not yet done.' ), $this->field->id() ), true );
							break;
					}
				}
			}
		}


		/**
		 * @return mixed
		 */
		public function context_value(){
			return $this->value;
		}


		/**
		 * @return string
		 */
		public function context_group(){
			return $this->GROUP;
		}


		/**
		 * @return array
		 */
		public function context_args(){
			return $this->ARGS;
		}


		/**
		 * @return hw_field
		 */
		public function field(){
			if( !$this->field instanceof hw_field ){
				if( !$this->is_exists() )
					return new hw_field($this->id(),'');
				///
				$fields = hiweb()->fields()->locations()->get_fields_by( $this->context_group(), $this->context_args() );
				if( !array_key_exists( $this->id(), $fields ) || !$fields[ $this->id() ] instanceof hw_field ){
					return new hw_field($this->id(),'');
				}
				$this->field = $fields[ $this->id() ];
			} else {
				$this->field = hiweb()->fields()->home()->get( $this->id() );
			}
			$this->field->value( $this->context_value() );
			return $this->field;
		}


		/**
		 * @return hw_input
		 */
		public function input(){
			return $this->field()->input();
		}


		/**
		 * @return mixed|null
		 */
		public function value(){
			return $this->field()->value();
		}


		public function the(){
			echo $this->value();
		}


		/**
		 * @param null|mixed $args
		 * @param null|mixed $args2
		 * @return mixed
		 */
		public function content( $args = null, $args2 = null ){
			return $this->field()->content( $args, $args2 );
		}


		public function the_content( $args = null, $args2 = null ){
			echo $this->content( $args, $args2 );
		}


		/**
		 * @return hw_input_axis_rows
		 */
		public function rows(){
			if( !$this->rows instanceof hw_input_axis_rows ){
				$this->rows = new hw_input_axis_rows( $this );
			}
			return $this->rows;
		}


	}