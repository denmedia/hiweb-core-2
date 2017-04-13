<?php


	class hw_fields_static{

		static $hook_template = array(
			'edit_form_top' => 'default',
			'edit_form_before_permalink' => 'postbox',
			'edit_form_after_title' => 'postbox',
			'edit_form_after_editor' => 'postbox',
			'submitpage_box' => 'postbox',
			'submitpost_box' => 'postbox',
			'edit_page_form' => 'postbox',
			'edit_form_advanced' => 'postbox',
			'_add_form_fields' => 'add-term',
			'_edit_form' => 'term',
			'options' => 'default'
		);


		/**
		 * @param string $hook
		 * @return mixed|string
		 */
		static function get_form_template_from_hook( $hook = '' ){
			$template = 'default';
			static $success = array();
			if( isset( $success[ $hook ] ) ){
				$template = $success[ $hook ];
			} elseif( isset( self::$hook_template[ $hook ] ) ) {
				$success[ $hook ] = self::$hook_template[ $hook ];
				$template = $success[ $hook ];
			} else {
				foreach( self::$hook_template as $hook_name => $template_name ){
					if( strpos( $hook, $hook_name ) !== false ){
						$success[ $hook ] = $template_name;
						$template = $template_name;
						break;
					}
				}
			}
			return $template;
		}


		static function get_value_by_context( $fieldId, $contextId = null ){
			if( ( is_null( $contextId ) || is_bool( $contextId ) ) && function_exists( 'get_queried_object' ) ){
				$contextId = get_queried_object();
			}
			switch( gettype( $contextId ) ){
				case 'object':
					switch( get_class( $contextId ) ){
						case 'WP_Post':
							return get_post_meta( $contextId->ID, $fieldId, true );
							break;
						case 'WP_Term':
							return get_term_meta( $contextId->term_id, $fieldId, true );
							break;
						case 'WP_User':
							return get_user_meta( $contextId->ID, $fieldId, true );
							break;
						default:
							hiweb()->console()->warn( sprintf( __( 'Unable to determine the context for emergency-type object [%s]', 'hw-core-2' ), get_class( $contextId ) ), true );
							break;
					}
					break;
				case 'integer':
					$test_post = get_post( $contextId );
					if( $test_post instanceof WP_Post ){
						return get_post_meta( $test_post->ID, $fieldId, true );
					} else {
						hiweb()->console()->warn( 'Error: 894651', true );
					}
					break;
				case 'string':
					$test_value = get_option( $fieldId, null );
					if( is_null( $test_value ) ){
						$test_value = get_option( $contextId . '-' . $fieldId, null );
					}
					if( !is_null( $test_value ) ){
						return $test_value;
					} else {
						hiweb()->console()->warn( 'Error: 3640127', true );
					}
					break;
				default:
					hiweb()->console()->warn( sprintf( __( 'Unable to determine the context for emergency-type variable [%s]', 'hw-core-2' ), gettype( $contextId ) ), true );
					break;
			}
			return null;
		}

	}