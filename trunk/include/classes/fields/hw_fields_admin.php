<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 16.04.2017
	 * Time: 1:34
	 */
	class hw_fields_admin{

		private $form_template_for_hooks = array(
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


		use hw_hidden_methods_props;


		public function __construct(){

			if( hiweb()->context()->is_backend_page() ){
				///POSTS BACKEND
				add_action( 'edit_form_top', [ $this, 'edit_form_top' ] );
				add_action( 'edit_form_before_permalink', [ $this, 'edit_form_before_permalink' ] );
				add_action( 'edit_form_after_title', [ $this, 'edit_form_after_title' ] );
				add_action( 'edit_form_after_editor', [ $this, 'edit_form_after_editor' ] );
				add_action( 'submitpost_box', [ $this, 'submitpost_box' ] );
				add_action( 'submitpage_box', [ $this, 'submitpage_box' ] );
				add_action( 'edit_form_advanced', [ $this, 'edit_form_advanced' ] );
				add_action( 'edit_page_form', [ $this, 'edit_page_form' ] );
				add_action( 'dbx_post_sidebar', [ $this, 'dbx_post_sidebar' ] );
				///POSTS SAVE
				add_action( 'save_post', [ $this, 'save_post' ], 10, 3 );
				////////
				///TAXONOMIES BACKEND
				add_action( 'init', function(){
					if( function_exists( 'get_taxonomies' ) && is_array( get_taxonomies() ) )
						foreach( get_taxonomies() as $taxonomy_name ){
							//add
							add_action( $taxonomy_name . '_add_form_fields', [ $this, 'taxonomy_add_form_fields' ] );
							//edit
							add_action( $taxonomy_name . '_edit_form', [ $this, 'taxonomy_edit_form' ], 10, 2 );
						}
				} );
				///TAXONOMY SAVE
				add_action( 'create_term', [ $this, 'edited_term' ], 10, 3 );
				add_action( 'edited_term', [ $this, 'edited_term' ], 10, 3 );
				///OPTIONS FIELDS
				add_action( 'admin_init', [ $this, 'options_page_add_fields' ], 999999 );
				///ADMIN MENU FIELDS
				add_action( 'current_screen', [ $this, 'admin_menu_fields' ], 999999 );
			}
		}


		private function get_form_template_from_hook( $hook = '' ){
			$template = 'default';
			static $success = array();
			if( isset( $success[ $hook ] ) ){
				$template = $success[ $hook ];
			} elseif( isset( $this->form_template_for_hooks[ $hook ] ) ) {
				$success[ $hook ] = $this->form_template_for_hooks[ $hook ];
				$template = $success[ $hook ];
			} else {
				foreach( $this->form_template_for_hooks as $hook_name => $template_name ){
					if( strpos( $hook, $hook_name ) !== false ){
						$success[ $hook ] = $template_name;
						$template = $template_name;
						break;
					}
				}
			}
			return $template;
		}


		///////////////////ADMIN MENU PAGE
		public function admin_menu_fields(){
			if( function_exists( 'get_current_screen' ) ){
				foreach( hiweb()->admin()->menu()->get_pages() as $slug => $page ){
					$current_screen_id = get_current_screen()->id;
					//
					if( ( get_class( $page ) == 'hw_admin_submenu_page' && preg_match( '/^(?>\w+_page_' . $slug . ')$/i', $current_screen_id ) > 0 ) || get_class( $page ) == 'hw_admin_menu_page' && preg_match( '/^(?>toplevel_page_' . $slug . ')$/i', $current_screen_id ) > 0 ){
						add_action( 'hw_admin_menu_page_content_' . $slug, function( $admin_page ){
							if( $admin_page instanceof hw_admin_menu_abstract ){
								$fields = hiweb()->fields()->locations()->get_fields_by( 'admin_menu', [ 'slug' => $admin_page->menu_slug() ] );
								foreach( $fields as $field ){
									$field_option_name = hiweb()->fields()->get_options_field_id($admin_page->menu_slug(),$field->get_id());// 'hiweb-' . $admin_page->menu_slug() . '-' . $field->get_id();
									$field->value( get_option( $field_option_name, null ) );
									$field->input()->name = $field_option_name;
								}
								hiweb()->form( __FUNCTION__ )->add_fields( $fields )->the_noform( __FUNCTION__ );
							}
						} );
					}
				}
			}
		}


		///////////////////OPTIONS PAGE
		public function options_page_add_fields(){
			$locations = hiweb()->fields()->locations()->get_by( 'options_page' );
			$pages = [];
			foreach( $locations as $location ){
				if( !isset( $location->rules['options_page']['slug'] ) || !is_array( $location->rules['options_page']['slug'] ) || count( $location->rules['options_page']['slug'] ) == 0 )
					continue;
				if( reset( $location->rules['options_page']['section_title'] ) != '' )
					$pages[ $location->rules['options_page']['slug'][0] ]['title'] = is_array( $location->rules['options_page']['section_title'] ) ? reset( $location->rules['options_page']['section_title'] ) : $location->rules['options_page']['section_title'];
				$pages[ $location->rules['options_page']['slug'][0] ]['fields'][] = $location->get_field();
			}
			///Register Section and Fields
			foreach( $pages as $page => $data ){
				$current_options_page_update = ( count( $_POST ) > 0 && hiweb()->arrays()->get_byKey( $_POST, 'action' ) == 'update' && hiweb()->arrays()->get_byKey( $_POST, 'option_page' ) == $page );
				add_settings_section( 'hiweb-' . $page, $data['title'], '', $page );
				foreach( $data['fields'] as $field ){
					if( $field instanceof hw_field ){
						$field_options_name = hiweb()->fields()->get_options_field_id($page, $field->get_id()); //'hiweb-' . $page . '-' . $field->get_id();
						if( $current_options_page_update && !isset( $_POST[ $field_options_name ] ) ){
							delete_option( $field_options_name );
						} else {
							$field->value( get_option( $field_options_name, null ) );
							$field->input()->name = $field_options_name;
							add_settings_field( $field_options_name, $field->name(), [ $field->input(), 'the' ], $page, 'hiweb-' . $page );
							register_setting( $page, $field_options_name );
						}
					}
				}
				///

			}
		}


		///////////////////TAXONOMY
		private function get_fields_by_taxonomy( $taxonomy ){
			return hiweb()->fields()->locations()->get_fields_by( 'taxonomy', [ 'name' => $taxonomy ] );
		}


		public function taxonomy_add_form_fields( $taxonomy ){
			$fields = $this->get_fields_by_taxonomy( $taxonomy );
			if( is_array( $fields ) && count( $fields ) > 0 )
				hiweb()->form( __FUNCTION__ )->add_fields( $fields )->the_noform( __FUNCTION__ );
		}


		public function taxonomy_edit_form( $term, $taxonomy ){
			$fields = $this->get_fields_by_taxonomy( $taxonomy );
			if( is_array( $fields ) && count( $fields ) > 0 ){
				if( $term instanceof WP_Term )
					foreach( $fields as $field ){
						$field->value( get_term_meta( $term->term_id, $field->get_id(), true ) );
					}
				hiweb()->form( __FUNCTION__ )->add_fields( $fields )->the_noform( __FUNCTION__ );
			}
		}


		/**
		 * @param null $term_id
		 * @param null $tt_id
		 * @param null $taxonomy
		 */
		public function edited_term( $term_id = null, $tt_id = null, $taxonomy = null ){
			if( intval( $term_id ) > 0 ){
				$fields = $this->get_fields_by_taxonomy( $taxonomy );
				$R = [];
				if( is_array( $fields ) )
					foreach( $fields as $field ){
						$R[] = $field->get_id();
						if( array_key_exists( $field->get_id(), $_POST ) ){
							update_term_meta( $term_id, $field->get_id(), $_POST[ $field->get_id() ] );
						} elseif( array_key_exists( $field->get_id(), $_GET ) ) {
							update_term_meta( $term_id, $field->get_id(), $_GET[ $field->get_id() ] );
						} else {
							update_term_meta( $term_id, $field->get_id(), null );
						}
					}
			}
		}

		///////////////////POST TYPE
		/**
		 * @param null $post
		 * @param int  $position
		 * @return hw_field[]
		 */
		private function get_fields_by_post_type_position( $post = null, $position = 3 ){
			$R = [];
			if( function_exists( 'get_current_screen' ) && is_object( get_current_screen() ) ){
				///GET LOCATIONS by CONTEXT
				/** @var hw_field[] $R */
				$args = [];
				if( is_int( $position ) )
					$args['position'] = $position;
				$args['post_type'] = get_current_screen()->id;

				//Front Page Fields
				if( $post instanceof WP_Post ){
					$args['front_page'] = intval( $post->ID ) == intval( get_option( 'page_on_front' ) );
					$args['ID'] = $post->ID;
				}
				$R = hiweb()->fields()->locations()->get_fields_by( 'post_type', $args );
				if( $post instanceof WP_Post ){
					foreach( $R as $field ){
						hiweb()->console( [ $field->get_id(), get_post_meta( $post->ID, $field->get_id(), true ) ] ); //todo-
						$field->value( get_post_meta( $post->ID, $field->get_id(), true ) );
					}
				}
			}
			return $R;
		}


		/**
		 * @param null $post
		 * @param int  $position
		 */
		private function the_form_post( $post = null, $position = 3 ){
			$fields = $this->get_fields_by_post_type_position( $post, $position );
			if( is_array( $fields ) && count( $fields ) > 0 )
				hiweb()->form( __FUNCTION__ )->add_fields( $fields )->the_noform( __FUNCTION__ );
		}


		//Post Type, Position 0
		public function edit_form_top( $post = null ){
			$this->the_form_post( $post, 0 );
		}


		//Post Type, Position 1
		public function edit_form_before_permalink( $post = null ){
			$this->the_form_post( $post, 1 );
		}


		//Post Type, Position 2
		public function edit_form_after_title( $post = null ){
			$this->the_form_post( $post, 2 );
		}


		//Post Type, Position 3
		public function edit_form_after_editor( $post = null ){
			$this->the_form_post( $post, 3 );
		}


		//Post Type, Position 4
		public function submitpost_box( $post = null ){
			$this->the_form_post( $post, 4 );
		}


		//Post Type:PAGE, Position 4
		public function submitpage_box( $post = null ){
			$this->the_form_post( $post, 4 );
		}


		//Post Type, Position 5
		public function edit_form_advanced( $post = null ){
			$this->the_form_post( $post, 5 );
		}


		//Post Type: PAGE, Position 5
		public function edit_page_form( $post = null ){
			$this->the_form_post( $post, 5 );
		}


		//Post Type: PAGE, Position 6
		public function dbx_post_sidebar( $post = null ){
			$this->the_form_post( $post, 6 );
		}


		//Save POST
		public function save_post( $post_id = null, $post = null, $update = false ){
			if( $post instanceof WP_Post ){
				$fields = $this->get_fields_by_post_type_position( $post, false );
				$R = [];
				if( is_array( $fields ) )
					foreach( $fields as $field ){
						$R[] = $field->get_id();
						if( array_key_exists( $field->get_id(), $_POST ) ){
							update_post_meta( $post->ID, $field->get_id(), $_POST[ $field->get_id() ] );
						} elseif( array_key_exists( $field->get_id(), $_GET ) ) {
							update_post_meta( $post->ID, $field->get_id(), $_GET[ $field->get_id() ] );
						} else {
							update_post_meta( $post->ID, $field->get_id(), null );
						}
					}
			}
		}

	}


	new hw_fields_admin();