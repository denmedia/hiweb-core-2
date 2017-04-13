<?php


	class hw_wp_add_link_to_admin_bar{


		protected $parent;
		protected $id;
		protected $title;
		protected $href;
		protected $meta;


		public function __construct( $id ){
			$this->id = $id;
			$this->title = $id;
			add_action( 'admin_bar_menu', array( $this, 'add_link_to_admin_bar' ), 999, 2 );
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'add_link_to_admin_bar':
					$this->add_link_to_admin_bar( reset( $arguments ) );
					break;
			}
		}


		/**
		 * @param $wp_admin_bar
		 */
		private function add_link_to_admin_bar( WP_Admin_Bar $wp_admin_bar ){
			// adds a child node to site name parent node
			$wp_admin_bar->add_node( array(
				'parent' => $this->parent,
				'id' => $this->id,
				'title' => $this->title,
				'href' => esc_url( admin_url( 'upload.php' ) ),
				'meta' => false
			) );
		}


		/**
		 * @return string|null
		 */
		public function id(){
			return $this->id;
		}


		/**
		 * @param null $set
		 * @return $this|string|null
		 */
		public function parent( $set = null ){
			if( !is_null( $set ) ){
				$this->parent = $set;
				return $this;
			}
			return $this->parent;
		}


		/**
		 * @param null $set
		 * @return $this|string|null
		 */
		public function title( $set = null ){
			if( !is_null( $set ) ){
				$this->title = $set;
				return $this;
			}
			return $this->title;
		}


		/**
		 * @param null $set
		 * @return $this|string|null
		 */
		public function href( $set = null ){
			if( !is_null( $set ) ){
				$this->href = $set;
				return $this;
			}
			return $this->href;
		}


		/**
		 * @param null $set
		 * @return $this|string|null
		 */
		public function meta( $set = null ){
			if( !is_null( $set ) ){
				$this->meta = $set;
				return $this;
			}
			return $this->meta;
		}
	}