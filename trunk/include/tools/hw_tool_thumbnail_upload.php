<?php

	if( !class_exists( 'hw_tool_thumbnail_upload' ) ){

		class hw_tool_thumbnail_upload{

			use hw_hidden_methods_props;

			private $post_types = [];
			private $taxonomies = [];

			public $default_preview_size = [ 200, 300 ];


			public function __construct(){
				///Print Thumbnails to Columns
				add_action( 'admin_init', function(){
					if( is_array( $this->post_types() ) ) foreach( $this->post_types() as $post_type ){
						if( $post_type == 'post' ){
							add_action( 'manage_posts_custom_column', [ $this, 'manage_posts_custom_column' ], 10, 2 );
							add_filter( 'manage_posts_columns', [ $this, 'manage_posts_columns' ] );
						} elseif( $post_type == 'page' ) {
							add_action( 'manage_pages_custom_column', [ $this, 'manage_posts_custom_column' ], 10, 2 );
							add_filter( 'manage_pages_columns', [ $this, 'manage_posts_columns' ] );
						} else {
							add_action( 'manage_' . $post_type . '_posts_custom_column', [ $this, 'manage_posts_custom_column' ], 10, 2 );
							add_filter( 'manage_' . $post_type . '_posts_columns', [ $this, 'manage_posts_columns' ] );
						}
					}
				} );
				///Thumbnail Upload
				add_action( 'current_screen', function(){
					if( is_array( $this->post_types() ) ) foreach( $this->post_types() as $post_type ){
						if( get_current_screen()->post_type == $post_type ){
							hiweb()->css( hiweb()->url_css . '/tool-thumbnail-upload.css' );
							$script_id = hiweb()->js( hiweb()->url_js . '/hw_Dropzone.js', [ 'jquery' ] );
							hiweb()->js( hiweb()->url_js . '/tool-thumbnail-upload.js', [ 'jquery', $script_id ], true );
						}
					}
				} );


			}


			protected function manage_posts_custom_column( $column_name = '', $post_id = 0 ){
				if( intval( $post_id ) > 0 && trim( $column_name ) == 'hw_tool_thumbnail' ){
					$P = get_post( $post_id );
					if( $this->post_type_exists( $P->post_type ) ){
						?>
						<div id="thumb_hw_upload_zone-<?= $P->ID ?>" class="thumb_hw_upload_zone" data-is-process="0" data-has-thumbnail="<?= has_post_thumbnail( $P ) ? '1' : '0' ?>">
							<div data-ctrl="noimg">
								<div class="left" data-ctrl-btn="upload" title="Загрузить миниатюру"><i class="dashicons dashicons-upload"></i></div>
								<div class="right" data-ctrl-btn="media" title="Выбрать из библиотеки"><i class="dashicons dashicons-admin-media"></i></div>
							</div>
							<div data-ctrl="img">
								<div class="left" data-ctrl-btn="upload" title="Загрузить миниатюру"><i class="dashicons dashicons-upload"></i></div>
								<div class="right" data-ctrl-btn="remove" title="Удалить миниатюру"><i class="dashicons dashicons-no"></i></div>
							</div>
							<div class="img noimg"></div>
							<div class="img" data-img <?php
								if( has_post_thumbnail( $P ) ){
							?>style="background-image: url(<?= get_the_post_thumbnail_url( $P, $this->default_preview_size ) ?>)"<?php
								}
							?>></div>
							<div data-drop-logo>
								<i class="dashicons dashicons-upload"></i>
							</div>
							<div data-loader>
								<i class="dashicons dashicons-update"></i>
							</div>
						</div>
						<?php
					}
				}
			}


			protected function manage_posts_columns( $posts_columns, $post_type ){
				wp_enqueue_media();
				$posts_columns = hiweb()->arrays()->push( $posts_columns, ' ', 0, 'hw_tool_thumbnail' );
				return $posts_columns;
			}


			/**
			 * @param string $post_type
			 * @return array
			 */
			public function post_type( $post_type = 'post' ){
				$this->post_types[] = $post_type;
				$this->post_types = array_unique( $this->post_types );
				return $this->post_types;
			}


			/**
			 * @return array
			 */
			public function post_types(){
				return $this->post_types;
			}


			/**
			 * @param string $post_type
			 * @return array
			 */
			public function remove_post_type( $post_type = 'post' ){
				$flip = array_flip( $this->post_types );
				if( array_key_exists( $post_type, $flip ) ){
					unset( $flip[ $post_type ] );
					$this->post_types = array_keys( $flip );
				}
				return $this->post_types;
			}


			/**
			 * @param $post_type
			 * @return bool
			 */
			public function post_type_exists( $post_type ){
				$flip = array_flip( $this->post_types );
				return array_key_exists( $post_type, $flip );
			}


			/**
			 * @param string $tax_name
			 * @return array
			 */
			public function taxonomy( $tax_name = 'category' ){
				//TODO
				return $this->taxonomies;
			}


			/**
			 * @return array
			 */
			public function taxonomies(){
				//TODO
				return $this->taxonomies;
			}


			/**
			 * @param string $tax_name
			 * @return array
			 */
			public function remove_taxonomy( $tax_name = 'category' ){
				//TODO
				return $this->taxonomies;
			}


			/**
			 * @param $post_type
			 * @return bool
			 */
			public function taxonomy_exists( $post_type ){
				$flip = array_flip( $this->post_types );
				return array_key_exists( $post_type, $flip );
			}


			/**
			 * Передайте массив одного файла для загрузки
			 * @param $_file
			 * @return int|WP_Error
			 */
			public function upload( $_file ){
				if( !isset( $_file['tmp_name'] ) ){
					return 0;
				}
				///
				ini_set( 'upload_max_filesize', '128M' );
				ini_set( 'post_max_size', '128M' );
				ini_set( 'max_input_time', 300 );
				ini_set( 'max_execution_time', 300 );
				///
				$tmp_name = $_file['tmp_name'];
				$fileName = $_file['name'];
				if( !is_readable( $tmp_name ) ){
					return - 1;
				}
				///File Upload
				$wp_filetype = wp_check_filetype( $fileName, null );
				$wp_upload_dir = wp_upload_dir();
				$newPath = $wp_upload_dir['path'] . '/' . sanitize_file_name( $fileName );
				if( !copy( $tmp_name, $newPath ) ){
					return - 2;
				}
				$attachment = [ 'guid' => $wp_upload_dir['url'] . '/' . $fileName, 'post_mime_type' => $wp_filetype['type'], 'post_title' => preg_replace( '/\.[^.]+$/', '', $fileName ), 'post_content' => '', 'post_status' => 'inherit' ];
				$attachment_id = wp_insert_attachment( $attachment, $newPath );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $newPath );
				wp_update_attachment_metadata( $attachment_id, $attachment_data );
				return $attachment_id;
			}

		}
	}
