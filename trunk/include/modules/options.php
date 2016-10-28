<?php


	class hw_options{

		/** @var hw_option[] */
		private $options = array();
		/** @var hw_options_page[] */
		private $pages = array();


		/**
		 * @param $slug
		 * @return hw_options_page
		 */
		public function page( $slug ){
			if( !$this->page_exists( $slug ) ){
				$this->pages[ $slug ] = new hw_options_page( $slug );
			}
			return $this->pages[ $slug ];
		}


		/**
		 * @param $slug
		 * @return bool
		 */
		public function page_exists( $slug ){
			return array_key_exists( $slug, $this->pages );
		}


		/**
		 * Возвращает опцию, по необходимости создавая ее
		 * @param string $id
		 * @param string $type
		 * @return hw_option
		 */
		public function get( $id, $type = 'text' ){
			if( !$this->is_exist( $id ) ){
				$this->options[ $id ] = new hw_option( $id, $type );
			}
			return $this->options[ $id ];
		}


		/**
		 * Возвращает TRUE, если опция существует
		 * @param $id
		 * @return bool
		 */
		public function is_exist( $id ){
			$id = sanitize_file_name(strtolower($id));
			return array_key_exists( $id, $this->options );
		}

	}


	class hw_options_page{

		private $slug;
		private $menu_title;
		private $page_title;
		private $show_in = 'admin_menu';
		private $show_in_arg_1 = '';
		private $show_in_arg_2 = '';
		/** @var  hw_option[] */
		private $options = array();


		public function __construct( $slug ){
			$this->slug = sanitize_file_name( strtolower( $slug ) );
			$this->page_title = $slug;
			$this->menu_title = $slug;
			add_action( '_admin_menu', array( $this, 'add_action_admin_init' ) );
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'add_action_admin_init':
					switch( $this->show_in ){
						case 'admin_menu':
							$page = hiweb()->admin()->menu()->add_page( $this->slug )->menu_title( $this->menu_title )->page_title( $this->page_title )->function_echo( array( $this, 'the_page' ) );
							$page->icon_url( $this->show_in_arg_1 );
							$page->position( $this->show_in_arg_2 );
							break;
						case 'admin_submenu':
							hiweb()->admin()->menu()->add_sub_page( $this->slug, $this->show_in_arg_1 )->menu_title( $this->menu_title )->page_title( $this->page_title )->function_echo( array( $this, 'the_page' ) );
							break;
						case 'admin_options':
							hiweb()->admin()->menu()->add_options_page( $this->slug )->menu_title( $this->menu_title )->page_title( $this->page_title )->function_echo( array( $this, 'the_page' ) );
							break;
						case 'admin_theme':
							hiweb()->admin()->menu()->add_theme_page( $this->slug )->menu_title( $this->menu_title )->page_title( $this->page_title )->function_echo( array( $this, 'the_page' ) );
							break;
					}

					break;
				case 'the_page':
					$this->the_page();
					break;
			}
		}


		public function slug(){
			return $this->slug;
		}


		public function group(){
			return $this->slug . '-group';
		}


		/**
		 * @param null|string $icon_url
		 * @return hw_options_page
		 */
		public function show_in_admin_menu( $icon_url = null, $position = null ){
			$this->show_in = 'admin_menu';
			$this->show_in_arg_1 = $icon_url;
			$this->show_in_arg_2 = $position;
			return $this;
		}


		/**
		 * @param $parent
		 * @return hw_options_page
		 */
		public function show_in_admin_submenu( $parent ){
			$this->show_in_arg_1 = $parent;
			$this->show_in = 'admin_submenu';
			return $this;
		}


		/**
		 * @return hw_options_page
		 */
		public function show_in_admin_options(){
			$this->show_in = 'admin_options';
			return $this;
		}


		/**
		 * @return hw_options_page
		 */
		public function show_in_admin_theme(){
			$this->show_in = 'admin_theme';
			return $this;
		}


		/**
		 * @param null $set
		 * @return hw_options_page
		 */
		public function menu_title( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return hw_options_page
		 */
		public function page_title( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 *
		 */
		protected function the_page(){
			?>
			<div class="wrap">
				<form action="options.php" method="post" class="hw-admin-menu-options">

					<h1><?php echo $this->page_title ?></h1>

					<?php
						if( !$this->have_options() ){
							?><h4>This Options page is empty!</h4><?php
						}else{
							settings_fields( $this->group() );
							do_settings_sections( $this->group() );
							/**
							 * @var  $id
							 * @var hw_option $option
							 */
							foreach( $this->options as $id => $option ) : ?>
								<div class="hw-admin-menu-options-field">
									<p><strong><?php echo $option->title() ?></strong></p>
									<?php $option->the() ?>
									<?php echo $option->description() != '' ? '<p class="description">' . $option->description() . '</p>' : '' ?>
								</div>
							<?php endforeach; ?>
							<?php submit_button();
						}

					?>
				</form>
			</div>
			<?php
		}


		/**
		 * @param string|array $idOrOptions - ID опции, либо массив опций
		 * @param string $type
		 * @return hw_option
		 */
		public function add_option( $idOrOptions, $type = 'text' ){
			if( is_array( $idOrOptions ) ){
				foreach( $idOrOptions as $option ){
					if( $option instanceof hw_option ){
						$this->options[ $option->id() ] = $option;
					}else hiweb()->console()->warn( 'hiweb()→options()→page()→add_option() error: in idOrOptions array once item is not hw_option!', true );
				}
			}else{
				if( !$this->option_exists( $idOrOptions ) ){
					$this->options[ $idOrOptions ] = hiweb()->options()->get( $idOrOptions, $type );
					$this->options[ $idOrOptions ]->parent( $this );
				}
				return $this->options[ $idOrOptions ];
			}
			return reset( $idOrOptions );
		}


		/**
		 * @param $id
		 * @return bool
		 */
		public function option_exists( $id ){
			return array_key_exists( $id, $this->options );
		}


		/**
		 * @return bool
		 */
		public function have_options(){
			return ( is_array( $this->options ) && count( $this->options ) > 0 );
		}


		public function get_options(){
			return $this->options;
		}

	}


	class hw_option{

		private $id;
		private $input;
		private $title;
		private $description;
		private $hw_options_page;


		public function __construct( $id, $type = 'text' ){
			$this->id = sanitize_file_name( strtolower( $id ) );
			$this->title = $id;
			$this->input = hiweb()->input( $this->id, $type );
			if( function_exists( 'get_option' ) ){
				$this->input->value( get_option( $this->id, null ) );
			}
			add_action( 'admin_init', array( $this, 'register_setting' ) );
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'parent':
					if( $arguments[0] instanceof hw_options_page )
						$this->hw_options_page = $arguments[0];else hiweb()->console()->warn( 'hiweb()→option() warn: parent() not hw_admin_menu_page!', true );
					break;
				case 'register_setting':
					if( $this->hw_options_page instanceof hw_options_page ){
						register_setting( $this->hw_options_page->group(), $this->id );
					}else{
						register_setting( 'hw_admin_menu_options', $this->id );
					}
					break;
			}
		}


		/**
		 * @param null $set
		 * @return string|hw_option
		 */
		public function title( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return string|hw_option
		 */
		public function description( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		public function id(){
			return $this->id;
		}


		/**
		 * @return hw_input|hw_input_checkbox|hw_input_repeat|hw_input_text
		 */
		public function input(){
			return $this->input;
		}


		/**
		 * @return bool|hw_input|string
		 */
		public function get_value(){
			return $this->input->value();
		}


		/**
		 * @return string
		 */
		public function get(){
			return $this->input->get();
		}


		/**
		 * @return string
		 */
		public function the(){
			return $this->input->the();
		}


		/**
		 * @return string
		 */
		public function get_content(){
			return $this->input->get_content();
		}


		/**
		 * @return string
		 */
		public function the_content(){
			return $this->input->the_content();
		}


		public function have_rows(){
			return $this->input->have_rows();
		}


	}