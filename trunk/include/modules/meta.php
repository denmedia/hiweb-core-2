<?php


	class hw_meta{

		private $meta = array();


		/**
		 * @param $field_id
		 * @return hw_meta_field
		 */
		public function get( $field_id ){
			if( !array_key_exists( $field_id, $this->meta ) ){
				$this->meta[ $field_id ] = new hw_meta_field( $field_id );
			}
			return $this->meta[ $field_id ];
		}

	}


	class hw_meta_field{

		private $id;
		public $object_type;
		public $object_id;


		public function __construct( $id ){
			$this->id = $id;
			switch( get_class( get_queried_object() ) ){
				case 'WP_Post':
					$this->set_post( get_queried_object_id() );
					break;
				case 'WP_Term':
					$this->set_term( get_queried_object_id() );
					break;
				case 'WP_User':
					$this->set_user( get_queried_object_id() );
					break;
			}
		}


		/**
		 * Возвращает значение поля
		 * @return array|hw_post_meta|mixed|null
		 */
		public function get(){
			if( $this->object_type == 'post' ){
				return get_post_meta( $this->object_id, $this->id, true );
			}
			if( $this->object_type == 'term' ){
				return get_term_meta( $this->object_id, $this->id, true );
			}
			if( $this->object_type == 'user' ){
				return hiweb()->user( $this->object_id )->meta( $this->id );
			}
			return null;
		}
		
		
		/**
		 * Выводит значение поля
		 */
		public function the(){
			echo $this->get();
		}


		public function set_post( $post = null ){
			$this->object_type = 'post';
			if( $post instanceof WP_Post ){
				$this->object_id = $post->ID;
			}elseif( is_numeric( $post ) ){
				$this->object_id = $post;
			}elseif( get_queried_object() instanceof WP_Post ){
				$this->object_id = get_queried_object_id();
			}else{
				hiweb()->console()->warn( 'hiweb()→meta()→set_post() error: post id for [' . $this->id . '] not found!' );
			}
			return $this;
		}


		public function set_term( $term = null ){
			$this->object_type = 'term';
			if( $term instanceof WP_Term ){
				$this->object_id = $term->term_id;
			}elseif( is_numeric( $term ) ){
				$this->object_id = $term;
			}elseif( get_queried_object() instanceof WP_Term ){
				$this->object_id = get_queried_object_id();
			}else{
				hiweb()->console()->warn( 'hiweb()→meta()→set_post() error: term id for [' . $this->id . '] not found!' );
			}
			return $this;
		}


		public function set_user( $user ){
			$this->object_type = 'user';
			if( $user instanceof WP_User ){
				$this->object_id = $user->ID;
			}elseif( is_numeric( $user ) ){
				$this->object_id = $user;
			}elseif( get_queried_object() instanceof WP_User ){
				$this->object_id = get_queried_object_id();
			}else{
				hiweb()->console()->warn( 'hiweb()→meta()→set_post() error: user id for [' . $this->id . '] not found!' );
			}
			return $this;
		}


		public function has_rows(){
			return is_array( $this->get() );
		}
	}