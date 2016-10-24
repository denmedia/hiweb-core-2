<?php


	class hw_screen_logic{

		private $chain = array();
		/** @var hw_screen_logic_operators */
		private $operator;
		/** @var  hw_screen_logic_detect */
		private $detect;

		private $or_block_index = 0;


		public function __construct(){
			$this->detect = new hw_screen_logic_detect( $this );
			return $this->detect;
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'chain':
					return $this->chain();
					break;
				case 'detect':
					return $this->detect();
					break;
				case 'or_block_next':
					return $this->or_block_next();
					break;
			}
			return false;
		}


		private function or_block_next(){
			$this->or_block_index ++;
		}


		private function chain(){
			return $this->chain;
		}


		/**
		 * @return hw_screen_logic_detect
		 */
		private function detect(){
			return $this->detect;
		}


		/**
		 * @param $post_type - укажите тип поста, в котором показывать метабокс. Если не указывать, то мета бокс будет доступен во всех типах.
		 * @return hw_screen_logic_operators
		 */
		public function post_type( $post_type = null ){
			$this->chain[ $this->or_block_index ][] = array( 'post_type' => $post_type );
			return $this->operator();
		}


		/**
		 * @param $post_id
		 * @return hw_screen_logic_operators
		 */
		public function post_id( $post_id ){
			$this->chain[ $this->or_block_index ][] = array( 'post_id' => $post_id );
			return $this->operator();
		}


		/**
		 * @param $post_slug
		 * @return hw_screen_logic_operators
		 */
		public function post_slug( $post_slug ){
			$this->chain[ $this->or_block_index ][] = array( 'post_slug' => $post_slug );
			return $this->operator();
		}


		/**
		 * @return hw_screen_logic_operators
		 */
		public function taxonomies(){
			$this->chain[ $this->or_block_index ][] = array( 'taxonomies' => '' );
			return $this->operator();
		}


		/**
		 * @param $taxonomy_name
		 * @return hw_screen_logic_operators
		 */
		public function taxonomy( $taxonomy_name ){
			$this->chain[ $this->or_block_index ][] = array( 'taxonomy' => $taxonomy_name );
			return $this->operator();
		}


		/**
		 * Показывать на странице профиля (редактирования собственных данных)
		 * @return hw_screen_logic_operators
		 */
		public function user_profile(){
			$this->chain[ $this->or_block_index ][] = array( 'user_profile' => '' );
			return $this->operator();
		}


		/**
		 * Показывать на странице редактирования пользователя
		 * @return hw_screen_logic_operators
		 */
		public function user_edit(){
			$this->chain[ $this->or_block_index ][] = array( 'user_edit' => '' );
			return $this->operator();
		}


		/**
		 * @param $user_login
		 * @return hw_screen_logic_operators
		 */
		public function user_login( $user_login ){
			$this->chain[ $this->or_block_index ][] = array( 'user_login' => $user_login );
			return $this->operator();
		}


		/**
		 * @param $user_id
		 * @return hw_screen_logic_operators
		 */
		public function user_id( $user_id ){
			$this->chain[ $this->or_block_index ][] = array( 'user_id' => $user_id );
			return $this->operator();
		}


		/**
		 * @param $user_role
		 * @return hw_screen_logic_operators
		 */
		public function user_role( $user_role ){
			$this->chain[ $this->or_block_index ][] = array( 'user_role' => $user_role );
			return $this->operator();
		}


		/**
		 * @return hw_screen_logic_operators
		 */
		private function operator(){
			if( !$this->operator instanceof hw_screen_logic_operators ){
				$this->operator = new hw_screen_logic_operators( $this );
			}
			return $this->operator;
		}


	}


	class hw_screen_logic_operators{

		/** @var hw_screen_logic */
		private $screen;


		public function __construct( hw_screen_logic $hw_screen_logic ){
			$this->screen = $hw_screen_logic;
		}


		/**
		 * @return hw_screen_logic
		 */
		public function or_in(){
			$this->screen->or_block_next();
			return $this->screen;
		}


		/**
		 * @return hw_screen_logic
		 */
		public function and_in(){
			return $this->screen;
		}

	}


	class hw_screen_logic_detect{

		/** @var  hw_screen_logic */
		private $screen_logic;


		public function __construct( $screen_logic ){
			$this->screen_logic = $screen_logic;
		}


		public function is_edit_post(){
			$current = get_current_screen();
			return $current->base == 'post';
		}


		public function is_edit_user(){
			$current = get_current_screen();
			return ( $current->base == 'profile' || $current->base == 'user-edit' );
		}


		public function is_edit_taxonomy(){
			$current = get_current_screen();
			return $current->taxonomy != '';
		}


		public function get_taxonomies_from_chain(){
			$chain = $this->screen_logic->chain();
			$taxonomies = array();
			$allTaxonomies = false;
			foreach( $chain as $ors ){
				foreach( $ors as $ands ){
					foreach( $ands as $type => $value ){
						if( $type == 'taxonomy' ){
							$taxonomies[] = $value;
						}
						if($type == 'taxonomies'){
							$taxonomies = array_keys(get_taxonomies());
							return $taxonomies;
						}
					}
				}
			}
			$taxonomies = array_unique( $taxonomies );
			return $taxonomies;
		}


		public function detect(){
			$chain = $this->screen_logic->chain();
			$current = get_current_screen();
			$math = 0;
			foreach( $chain as $ors ){
				$math = 1;
				foreach( $ors as $ands ){
					foreach( $ands as $type => $value ){
						switch( $type ){
							case 'post_type':
								$math = $math && ( $current->base == 'post' && ($current->post_type == $value || trim($value) == '') );
								break;
							case 'post_id':
								$math = $math && ( $current->base == 'post' && isset( $_GET['post'] ) && $_GET['post'] == $value );
								break;
							case 'post_slug':
								$math = $math && ( $current->base == 'post' && isset( $_GET['post'] ) && hiweb()->post( $_GET['post'] )->name() == $value );
								break;
							case 'taxonomies':
								$math = $math && $current->taxonomy != '';
								break;
							case 'taxonomy':
								$math = $math && $current->taxonomy == $value;
								break;
							case 'user_profile':
								$math = $math && ( $current->base == 'profile' );
								break;
							case 'user_edit':
								$math = $math && ( $current->base == 'profile' || $current->base == 'user-edit' || $current->base == 'user' || ( $current->base == 'base' && $current->action == 'add' ) );
								break;
							case 'user_login':
								$math = $math && ( ( $current->base == 'user-edit' && hiweb()->user( isset( $_GET['user_id'] ) ? $_GET['user_id'] : '' )->login() == $value ) || ( $current->base == 'profile' && hiweb()->user()->login() == $value ) );
								break;
							case 'user_id':
								$math = $math && ( ( $current->base == 'user-edit' && hiweb()->user( isset( $_GET['user_id'] ) ? $_GET['user_id'] : '' )->id() == $value ) || ( $current->base == 'profile' && hiweb()->user()->id() == $value ) );
								break;
							case 'user_role':
								$math = $math && ( ( $current->base == 'user-edit' && hiweb()->user( isset( $_GET['user_id'] ) ? $_GET['user_id'] : '' )->is_role( $value ) ) || ( $current->base == 'profile' && hiweb()->user()->is_role( $value ) ) );
								break;
						}
					}
				}
				if( $math === true )
					break;
			}
			$math = $math === true;
			return $math;
		}

	}