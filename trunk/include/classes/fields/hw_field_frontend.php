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
				hiweb()->console()->warn( sprintf( __( 'Field [%s] not exists', 'hw-core-2' ), $fieldId ), true );
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
			///
			$GROUP = 'options';
			$ARGS = [];
			$value = null;
			///GROUP TEST
			if( is_string( $this->contextId ) ){ //Контекст является опцией
				$GROUP = 'options';
				$value = get_option( hiweb()->fields()->get_options_field_id( $this->contextId, $this->id() ), null ); //get_option( 'hiweb-' . $this->contextId . '-' . $this->id(), null );
			} else { //Контекст задан объектом
				///
				if( !is_object( $this->contextId ) && did_action( 'wp' ) )
					$this->contextId = get_queried_object(); elseif( is_numeric( $this->contextId ) ) {
					$temp_contenxt = get_queried_object();
					if( $temp_contenxt instanceof WP_Post ){
						$this->contextId = get_post( $this->contextId );
					} elseif( $temp_contenxt instanceof WP_Term ) {
						$this->contextId = get_term( $this->contextId, $temp_contenxt->taxonomy );
					} elseif( $temp_contenxt instanceof WP_User ) {
						$this->contextId = get_user_by( 'user_login', $this->contextId );
					} else {
						hiweb()->console()->warn( sprintf( __( 'It is not possible to define the context for the field: [%s] by data [' . $this->contextId . '].' ), $this->field->get_id() ), true );
					}
				}
				///
				if( $this->contextId instanceof WP_Post ){
					$GROUP = 'post_type';
					$ARGS[] = [ 'ID' => $this->contextId->ID ];
					$ARGS[] = [ 'front_page' => ( $this->contextId->ID == get_option( 'page_on_front' ) ) ];
					$value = get_post_meta( $this->contextId->ID, $this->id(), true );
				} elseif( $this->contextId instanceof WP_Term ) {
					$GROUP = 'taxonomy';
					$ARGS[] = [ 'term_id' => $this->contextId->term_id ];
					$value = get_term_meta( $this->contextId->term_id, $this->id(), true );
				} elseif( $this->contextId instanceof WP_User ) {
					$GROUP = 'users';
					$ARGS[] = [ 'term_id' => $this->contextId->user_id ];
					$value = get_user_meta( $this->contextId->ID, $this->id(), true );
				} else {
					hiweb()->console()->warn( sprintf( __( 'It is not possible to define the context for the field: [%s], since the action has not yet done.' ), $this->field->get_id() ), true );
				}
			}
			//
			///
			$fields = hiweb()->fields()->locations()->get_fields_by( $GROUP, $ARGS );
			if( !array_key_exists( $this->id(), $fields ) || !$fields[ $this->id() ] instanceof hw_field )
				return null;
			$this->field = $fields[ $this->id() ];
			$this->field->value( $value );
			///
			return $value;
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