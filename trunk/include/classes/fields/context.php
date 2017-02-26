<?php

	/**
	 * @var $this hw_field
	 * @var $contextId
	 */

	$queried_object = false;

	if( ( is_null( $contextId ) || is_bool( $contextId ) ) && function_exists( 'get_queried_object' ) ){
		$contextId = get_queried_object();
	}

	switch( gettype( $contextId ) ){
		case 'object':
			switch( get_class( $contextId ) ){
				case 'WP_Post':
					return get_post_meta( $contextId->ID, $this->input()->name, true );
					break;
				case 'WP_Term':
					return get_term_meta( $contextId->term_id, $this->input()->name, true );
					break;
				case 'WP_User':
					return get_user_meta( $contextId->user_id, $this->input()->name, true );
					break;
				default:
					hiweb()->console()->warn( sprintf( __( 'Unable to determine the context for emergency-type object [%s]', 'hw-core-2' ), get_class( $contextId ) ), true );
					break;
			}
			break;
		case 'integer':
			$test_post = get_post( $contextId );
			if( $test_post instanceof WP_Post ){
				return get_post_meta( $test_post->ID, $this->input()->name, true );
			} else {
				hiweb()->console()->warn( 'Error: 894651',true );
			}
			break;
		case 'string':
			$test_value = get_option( $this->input()->name, null );
			if( is_null( $test_value ) ){
				$test_value = get_option( $contextId . '-' . $this->input()->name, null );
			}
			if( !is_null( $test_value ) ){
				return $test_value;
			} else {
				hiweb()->console()->warn('Error: 3640127',true);
			}
			break;
		default:
			hiweb()->console()->warn( sprintf( __( 'Unable to determine the context for emergency-type variable [%s]', 'hw-core-2' ), gettype( $contextId ) ), true );
			break;
	}