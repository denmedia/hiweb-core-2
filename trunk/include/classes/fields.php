<?php

	include_once 'fields/hw_fields_locations.php';
	include_once 'fields/hw_fields_home.php';
	include_once 'fields/hw_field.php';
	include_once 'fields/hw_field_frontend.php';
	include_once 'fields/hw_fields_backend.php';
	include_once 'fields/hw_fields_loop.php';


	class hw_fields{


		use hw_hidden_methods_props;


		/**
		 * @return hw_fields_home
		 */
		public function home(){
			static $class;
			if( !$class instanceof hw_fields_home )
				$class = new hw_fields_home();
			return $class;
		}


		/**
		 * @return hw_fields_loop
		 */
		public function loop(){
			static $class;
			if( !$class instanceof hw_fields_loop )
				$class = new hw_fields_loop();
			return $class;
		}


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
		 * @param null $context_id
		 * @return array
		 */
		public function context_to_array( $context_id = null ){
			$GROUP = 'options';
			$ARGS = [];
			$value = null;
			///GROUP TEST
			if( is_string( $context_id ) ){ //Контекст является опцией
				$GROUP = 'options';
				$ARGS[] = [ 'slug' => $context_id ]; //todo? было закомментированно
			} else { //Контекст задан объектом
				///
				if( !is_object( $context_id ) && did_action( 'wp' ) )
					$context_id = get_queried_object(); elseif( is_numeric( $context_id ) ) {
					$temp_contenxt = get_queried_object();
					if( $temp_contenxt instanceof WP_Post ){
						$context_id = get_post( $context_id );
					} elseif( $temp_contenxt instanceof WP_Term ) {
						$context_id = get_term( $context_id, $temp_contenxt->taxonomy );
					} elseif( $temp_contenxt instanceof WP_User ) {
						$context_id = get_user_by( 'user_login', $context_id );
					} else {
						hiweb()->console()->warn( sprintf( __( 'It is not possible to define the context for the field: [%s] by data [' . $context_id . '].' ), $this->field->id() ), true );
					}
				}
				///
				if( $context_id instanceof WP_Post ){
					$GROUP = 'post_type';
					$ARGS[] = [ 'ID' => $context_id->ID ];
					$ARGS[] = [ 'front_page' => ( $context_id->ID == get_option( 'page_on_front' ) ) ];
					$ARGS[] = [ 'post_name' => $context_id->post_name ];
				} elseif( $context_id instanceof WP_Term ) {
					$GROUP = 'taxonomy';
					$ARGS[] = [ 'term_id' => $context_id->term_id ];
				} elseif( $context_id instanceof WP_User ) {
					$GROUP = 'users';
					$ARGS[] = [ 'term_id' => $context_id->user_id ];
				} else {
					hiweb()->console()->warn( sprintf( __( 'It is not possible to define the context and convert to global_id.' ) ), true );
				}
			}
			return [
				$GROUP,
				$ARGS,
				$context_id
			];
		}


		/**
		 * @param string $field_id
		 * @param null   $content_id
		 * @param bool   $md5
		 * @return string
		 */
		public function context_to_id( $field_id, $content_id = null, $md5 = true ){
			$R = json_encode( [
				$field_id,
				$this->context_to_array( $content_id )
			] );
			return $md5 ? md5( $R ) : $R;
		}


	}