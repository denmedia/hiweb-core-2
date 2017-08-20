<?php
	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 16.08.2017
	 * Time: 17:14
	 */

	if( !class_exists( 'hw_tool_comlete_remove_post' ) ){

		class hw_tool_comlete_remove_post{

			public $post_id = 0;


			public function __construct( $post_id ){
				$this->post_id = $post_id;
			}


			/**
			 * @return array|null|WP_Post
			 */
			public function get_post(){
				return get_post( $this->post_id );
			}


			/**
			 * @return bool
			 */
			public function is_exists(){
				return $this->get_post() instanceof WP_Post;
			}


			/**
			 * @return array
			 */
			public function get_attachment_ids(){
				$R = [];
				if( $this->is_exists() ){
					if( has_post_thumbnail( $this->post_id ) ){
						$R[] = get_post_thumbnail_id( $this->post_id );
					}
					$content = $this->get_post()->post_content;
					$doc = new DOMDocument();
					@$doc->loadHTML( $content );
					$tags = $doc->getElementsByTagName( 'img' );
					foreach( $tags as $tag ){
						$R[] = hiweb()->path()->get_attachment_id( $tag->getAttribute( 'src' ) );
					}
				}
				return $R;
			}


		}
	}

