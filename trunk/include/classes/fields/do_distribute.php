<?php

	/**
	 * Логика распределения полей по страницам админпанели
	 * @var $this hw_fields
	 */

	if( !function_exists( 'get_current_screen' ) || !get_current_screen() instanceof WP_Screen ){
		hiweb()->console()->warn( __( 'Object [WP_Screen] not found or them not object' ), true );
		return false;
	}
	$current_screen = (array)get_current_screen();
	///
	///Collect fields to hooks
	foreach( $this->get_hooks() as $hook ){
		///
		if( !is_array( $hook->rules ) ){
			hiweb()->console()->warn( __( 'The rules are not an array' ), true );
			continue;
		}
		///
		$field = $hook->get_field();

		foreach( $hook->rules as $screen_name => $rule_data ){

			if( !is_string( $screen_name ) || trim( $screen_name ) == '' ){
				hiweb()->console()->warn( __( 'Screen name is not defined' ), true );
				continue;
			}

			if( !is_array( $rule_data ) ){
				hiweb()->console()->warn( __( 'Rule data is not array' ), true );
				continue;
			}
			/////////
			switch( $screen_name ){
				case 'post_types':
					hiweb()->console()->warn( 'todo 1!', true );//todo!
					break;
				case 'post_type':
					foreach( $rule_data as $key => $val ){
						if( $current_screen['base'] == 'post' && $current_screen['post_type'] == $key ){
							if( $field->form_template() == '' ){
								$field->form_template( 'postbox' );
							}
							if( isset( $_GET['post'] ) ){
								$field->value( get_post_meta( $_GET['post'], $field->input()->name, true ) );
							}
							switch( $val ){
								case 0:
									$this->hook_fields['edit_form_top'][] = $field;
									break;
								case 1:
									$this->hook_fields['edit_form_before_permalink'][] = $field;
									break;
								case 2:
									$this->hook_fields['edit_form_after_title'][] = $field;
									break;
								case 4:
									$this->hook_fields[ $current_screen['post_type'] != 'page' ? 'submitpost_box' : 'submitpage_box' ][] = $field;
									break;
								case 5:
									$this->hook_fields[ $current_screen['post_type'] != 'page' ? 'edit_form_advanced' : 'edit_page_form' ][] = $field;
									break;
								case 6:
									$this->hook_fields['dbx_post_sidebar'][] = $field;
									break;
								default:
									$this->hook_fields['edit_form_after_editor'][] = $field;
									break;
							}
						}
					}
					break;
				case 'taxonomy':
					foreach( $rule_data as $key => $val ){
						if( ( $current_screen['base'] == 'edit-tags' || $current_screen['base'] == 'term' ) && $current_screen['taxonomy'] == $val ){
							if( isset( $_GET['tag_ID'] ) ){
								$field->value( get_term_meta( $_GET['tag_ID'], $field->input()->name, true ) );
							}
							$this->hook_fields[ $val . '_add_form_fields' ][] = $field;
							$this->hook_fields[ $val . '_edit_form' ][] = $field;
						}
					}
					break;
				case 'users':
					if( $field->form_template() == '' ){
						$field->form_template( 'user-edit' );
					}
					if( isset( $_GET['user_id'] ) || $current_screen['base'] == 'profile' ){
						$user_id = $current_screen['base'] == 'profile' ? get_current_user_id() : $_GET['user_id'];
						hiweb()->console( get_user_meta( $user_id, $field->input()->name, true ) );
						$field->value( get_user_meta( $user_id, $field->input()->name, true ) );
					}
					foreach( $rule_data as $key => $val ){
						if( $current_screen['base'] == 'user-edit' ){
							switch( $val ){
								case 2:
									$this->hook_fields['personal_options'][] = $field;
									break;
								case 1:
									$this->hook_fields['admin_color_scheme_picker'][] = $field;
									break;
								default:
									$this->hook_fields['edit_user_profile'][] = $field;
									break;
							}
						}
						if( $current_screen['base'] == 'profile' ){
							switch( $val ){
								case 1:
									$this->hook_fields['admin_color_scheme_picker'][] = $field;
									break;
								case 2:
									$this->hook_fields['profile_personal_options'][] = $field;
									break;
								default:
									$this->hook_fields['show_user_profile'][] = $field;
									break;
							}
						}
						if( $current_screen['base'] == 'user' ){
							$this->hook_fields['user_new_form'][] = $field;
						}
					}
					break;
				case 'admin_menu':
					foreach( $rule_data as $key => $val ){
						if( $current_screen['base'] == 'toplevel_page_' . $val ){
							$options_value = get_option( $field->input()->name );
							if( !hiweb()->string()->is_empty( $options_value ) || !hiweb()->arrays()->is_empty( $options_value ) )
								$field->value( $options_value );
							$this->hook_fields[ 'hw_admin_menu_page_' . $val ][] = $field;
							hiweb()->admin()->menu()->get( $val )->add_field( $field );
						}
					}
					///
					break;
				case 'options':
					foreach( $rule_data as $key => $val ){
						$filename = preg_replace( '/.php$/', '', $key );
						$page_name = preg_replace( '/^options-/', '', $filename );
						register_setting( $page_name, $field->input()->name );
						if( strpos( $current_screen['base'], 'options-' ) === 0 && $current_screen['base'] == $filename ){
							$field->value( get_option( $field->input()->name ) );
							$sections_position = array(
								'general' => [ 'default' ],
								'writing' => [ 'default' ],
								'reading' => [ 'default' ],
								'discussion' => [
									'default',
									'avatars'
								],
								'media' => [
									'default',
									'embeds',
									'uploads'
								],
								'permalink' => [ 'optional' ]
							);
							$section_id = 'default';
							if( isset( $sections_position[ $page_name ][ $val ] ) ){
								$section_id = $sections_position[ $page_name ][ $val ];
							}
							add_settings_field( 'hw-section-' . $field->input()->name, '<label for="extra_blog_desc_id">' . $field->name() . '</label>', array(
								$field,
								'the'
							), $page_name, $section_id );
						}
					}
					break;
				default:
					hiweb()->console()->warn( sprintf( __( 'Screen name [%s] not found' ), $screen_name ), true );
					break;
			}
		}
	}
	///Render hooks
	foreach( $this->hook_fields as $hook => $fields ){
		add_action( $hook, function( $attr = null ){
			$hook = hiweb()->backtrace()->get_args( 4, 0 );
			if( array_key_exists( $hook, hiweb()->fields()->hook_fields ) ){
				hiweb()->form( $hook )->template( hw_fields_static::get_form_template_from_hook( $hook ) )->add_fields( hiweb()->fields()->hook_fields[ $hook ] )->the_noform($hook);
			} elseif( trim( $hook ) != '' ) {
				hiweb()->console()->warn( sprintf( __( 'Hook [%s] does not found in hiweb()→hook_fields[]' ), $hook ), true );
			}
		} );
	}