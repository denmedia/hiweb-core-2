<?php


	/**
	 * Class hw_forms
	 */
	class hw_forms{

		/** @var hw_form[] */
		private $forms = array();


		/**
		 * @param $id
		 * @return hw_form
		 */
		public function give( $id ){
			$id = sanitize_file_name( strtolower( $id ) );
			if( !array_key_exists( $id, $this->forms ) ){
				$this->forms[ $id ] = new hw_form( $id );
			}
			return $this->forms[ $id ];
		}

	}


	/**
	 * Class hw_form
	 */
	class hw_form{

		protected $id = '';
		protected $action = '';
		protected $method = 'post';
		protected $template = 'default';

		private $submit = false;
		private $settings_group;


		use hw_inputs_home_functions;


		public function __construct( $id = '' ){
			$this->id = $id;
			$this->settings_group = $id;
			$this->inputs_home_make( array( 'forms', $id ) );
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function submit( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function settings_group( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function id( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function action( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function method( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function template( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * Возвращает HTML формы
		 * @return string
		 */
		public function get(){
			///Form Tags
			$formTagsPairs = array(
				'class' => 'hw-form', 'action' => $this->action, 'method' => $this->method, 'id' => $this->id
			);
			$formTags = array();
			foreach( $formTagsPairs as $key => $val ){
				if( trim( $val ) == '' )
					continue;
				if( is_numeric( $key ) ){
					$formTags[] = $val;
				} else {
					$formTags[] = $key . '="' . htmlentities( $val, ENT_QUOTES, 'utf-8' ) . '"';
				}
			}
			///
			return '<form ' . implode( ' ', $formTags ) . '>' . $this->get_noform() . ( $this->submit ? get_submit_button( is_string( $this->submit ) ? $this->submit : '' ) : '' ) . '</form>';
		}


		/**
		 * Возвращает HTML полей без формы
		 * @return string
		 */
		public function get_noform(){
			hiweb()->css( hiweb()->url_css . '/forms.css' );
			///
			$templatePath = hiweb()->dir_classes . '/forms/' . $this->template . '.php';
			if( !file_exists( $templatePath ) )
				$templatePath = hiweb()->dir_classes . '/forms/default.php';
			ob_start();
			include $templatePath;
			$R = ob_get_clean();
			///
			return $R;
		}


		/**
		 * @return string
		 */
		public function the(){
			$content = $this->get();
			echo $content;
			return $content;
		}


		/**
		 * @return string
		 */
		public function the_noform(){
			$content = $this->get_noform();
			echo $content;
			return $content;
		}

	}
	
	
	