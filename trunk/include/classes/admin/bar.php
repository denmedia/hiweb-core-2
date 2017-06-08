<?php


	class hw_wp_admin_bar{


		protected $parent;
		protected $id;
		protected $title;
		protected $href;
		protected $meta;
		/** @var hw_wp_admin_bar_add_menu[]|hw_wp_admin_bar_add_node[]|hw_wp_admin_bar_add_group[] */
		private $_node = array();


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
			if( is_array( $this->_node ) )
				foreach( $this->_node as $id => $node ){
					if( $node instanceof hw_wp_admin_bar_add_menu )
						$wp_admin_bar->add_menu( array( 'parent' => $node->parent(), 'id' => $node->id(), 'title' => $node->title(), 'href' => esc_url( $node->href() ), 'meta' => $node->meta() ) );
					if( $node instanceof hw_wp_admin_bar_add_node )
						$wp_admin_bar->add_node( array( 'parent' => $node->parent(), 'id' => $node->id(), 'title' => $node->title(), 'href' => esc_url( $node->href() ), 'meta' => $node->meta() ) );
					if( $node instanceof hw_wp_admin_bar_add_group )
						$wp_admin_bar->add_group( array( 'parent' => $node->parent(), 'id' => $node->id(), 'meta' => $node->meta() ) );
				}
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


		/**
		 * @param $id
		 * @return hw_wp_admin_bar_add_menu
		 */
		public function add_menu( $id ){
			if( !array_key_exists( $id, $this->_node ) ){
				$this->_node[ $id ] = new hw_wp_admin_bar_add_menu( $id );
			}
			return $this->_node[ $id ];
		}


		/**
		 * @param $id
		 * @return hw_wp_admin_bar_add_menu
		 */
		public function add_node( $id ){
			if( !array_key_exists( $id, $this->_node ) ){
				$this->_node[ $id ] = new hw_wp_admin_bar_add_node( $id );
			}
			return $this->_node[ $id ];
		}


		/**
		 * @param $id
		 * @return hw_wp_admin_bar_add_group
		 */
		public function add_group( $id ){
			if( !array_key_exists( $id, $this->_node ) ){
				$this->_node[ $id ] = new hw_wp_admin_bar_add_group( $id );
			}
			return $this->_node[ $id ];
		}

	}


	class hw_wp_admin_bar_add_menu{

		/** @var string The ID of the node. */
		protected $id;
		/** @var string|false The text that will be visible in the Toolbar. Including html tags is allowed. */
		protected $title = false;
		/** @var string|false The ID of the parent node. */
		protected $parent = false;
		/** @var string|false The 'href' attribute for the link. If 'href' is not set the node will be a text node. */
		protected $href = false;
		/** @var array|false An array of meta data for the node. */
		protected $group = false;
		/**
		 * * 'html' - The html used for the node.
		 * * 'class' - The class attribute for the list item containing the link or text node.
		 * * 'rel' - The rel attribute.
		 * * 'onclick' - The onclick attribute for the link. This will only be set if the 'href' argument is present.
		 * * 'target' - The target attribute for the link. This will only be set if the 'href' argument is present.
		 * * 'title' - The title attribute. Will be set to the link or to a div containing a text node.
		 * * 'tabindex' - The tabindex attribute. Will be set to the link or to a div containing a text node.
		 * @var array
		 */
		protected $meta = array();


		public function __construct( $id ){
			$this->id = $id;
			$this->title = $id;
		}


		/**
		 * @return string
		 */
		public function id(){
			return $this->id;
		}


		/**
		 * @param null $set
		 * @return $this|false|string
		 */
		public function title( $set = null ){
			if( is_string( $set ) ){
				$this->title = $set;
				return $this;
			}
			return $this->title;
		}


		/**
		 * @param null $set
		 * @return $this|false|string
		 */
		public function parent( $set = null ){
			if( is_string( $set ) ){
				$this->parent = $set;
				return $this;
			}
			return $this->parent;
		}


		/**
		 * @param null $set
		 * @return $this|false|string
		 */
		public function href( $set = null ){
			if( is_string( $set ) ){
				$this->href = $set;
				return $this;
			}
			return $this->href;
		}


		/**
		 * @param null $set
		 * @return $this|false|string
		 */
		public function group( $set = null ){
			if( is_string( $set ) ){
				$this->group = $set;
				return $this;
			}
			return $this->group;
		}


		/**
		 * * 'html' - The html used for the node.
		 * * 'class' - The class attribute for the list item containing the link or text node.
		 * * 'rel' - The rel attribute.
		 * * 'onclick' - The onclick attribute for the link. This will only be set if the 'href' argument is present.
		 * * 'target' - The target attribute for the link. This will only be set if the 'href' argument is present.
		 * * 'title' - The title attribute. Will be set to the link or to a div containing a text node.
		 * * 'tabindex' - The tabindex attribute. Will be set to the link or to a div containing a text node.
		 * @param null $set
		 * @return $this|false|string
		 */
		public function meta( $set = null ){
			if( is_array( $set ) ){
				$this->meta = $set;
				return $this;
			}
			return $this->meta;
		}

	}


	class hw_wp_admin_bar_add_node extends hw_wp_admin_bar_add_menu{

	}


	class hw_wp_admin_bar_add_group{

		/**
		 * The ID of the group (node).
		 * @var string|bool
		 */
		private $id = false;
		/**
		 * The ID of the parent node.
		 * @var string|bool
		 */
		private $parent = false;
		/**
		 * 'class' - The class attribute for the unordered list containing the child nodes.
		 * @var array
		 */
		private $meta = array();


		public function __construct( $id ){
			$this->id = $id;
		}


		/**
		 * The ID of the group (node).
		 * @param null $set
		 * @return $this|bool|string
		 */
		public function id( $set = null ){
			if( is_string( $set ) ){
				$this->id = $set;
				return $this;
			}
			return $this->id;
		}


		/**
		 * The ID of the parent node.
		 * @param null $set
		 * @return $this|hw_wp_admin_bar_add_group
		 */
		public function parent( $set = null ){
			if( is_string( $set ) ){
				$this->parent = $set;
				return $this;
			}
			return $this->parent;
		}


		/**
		 * An array of meta data for the group (node).
		 * 'class' - The class attribute for the unordered list containing the child nodes.
		 * @param null $set
		 * @return $this|array
		 */
		public function meta( $set = null ){
			if( is_array( $set ) ){
				$this->meta = $set;
				return $this;
			}
			return $this->meta;
		}

	}