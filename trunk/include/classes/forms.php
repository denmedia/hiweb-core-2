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

		/** @var hw_field[] */
		private $fields = array();


		/**
		 * Add field or fields to the form
		 * @param hw_field[] $field_or_fields
		 * @return hw_form
		 */
		public function add_fields( $field_or_fields = array() ){
			if( !is_array( $field_or_fields ) )
				$field_or_fields = array( $field_or_fields );
			foreach( $field_or_fields as $field ){
				if( $field instanceof hw_field ){
					$this->fields[ $field->id() ] = $field;
				}
			}
			return $this;
		}


		/**
		 * Add field to the form
		 * @param hw_field $field
		 * @return hw_form
		 */
		public function add_field( $field ){
			$this->add_fields( $field );
			return $this;
		}


		/**
		 * Get form fields
		 * @return hw_field[]
		 */
		public function get_fields(){
			$R = array();
			if( is_array( $this->fields ) ){
				foreach( $this->fields as $field ){
					$R[] = $field;
				}
			}
			return $R;
		}


		/**
		 * Return TRUE, if form has fields
		 * @return bool
		 */
		public function have_fields(){
			return count( $this->get_fields() ) > 0;
		}


		public function __construct( $id = '' ){
			$this->id = $id;
			$this->settings_group = $id;
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
				'class' => 'hw-form',
				'action' => $this->action,
				'method' => $this->method,
				'id' => $this->id
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
		 * @param string $addition_class - Дополнительный класс к форме
		 * @return string
		 */
		public function get_noform( $addition_class = '' ){
			hiweb()->css( hiweb()->url_css . '/forms.css' );
			///
			if( !$this->have_fields() ){
				hiweb()->console()->warn( sprintf( __( 'For form id:[%s] fields not found' ), $this->id ), true );
				return '';
			} else {
				$fields_group_by_template = array();
				$current_template = null;
				///
				$current_number = 0;
				$fields_old = $this->fields;
				foreach( $this->fields as $field ){
					$field_template = $field->form_template();
					if( is_null( $current_template ) ){
						$current_template = $field_template;
					} elseif( $current_template != $field_template ) {
						$current_number ++;
						$current_template = $field_template;
					}
					$fields_group_by_template[ $field_template . ':' . $current_number ][] = $field;
				}
				ob_start();
				///
				foreach( $fields_group_by_template as $template_code => $fields ){
					$template = preg_replace( '/:[\d]*$/', '', $template_code );
					$templatePath = $this->get_template_path( $template );
					$this->fields = $fields;
					include $templatePath;
				}
				$R = ob_get_clean();
				$this->fields = $fields_old;
				///
				return $R;
			}
		}


		private function get_template_path( $template_name = 'default' ){
			if( !is_string( $template_name ) )
				$template_name = $this->template;
			$templatePath = hiweb()->dir_views . '/form-template-' . $template_name . '.php';
			if( !file_exists( $templatePath ) ){
				if( trim( $template_name ) != '' )
					hiweb()->console()->warn( sprintf( __( 'Template [%s] for form not found' ), $templatePath ), true );
				$templatePath = hiweb()->dir_views . '/form-template-default.php';
			}
			return $templatePath;
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
		 * @param string $addition_class
		 * @return string - Дополнительный класс к форме
		 */
		public function the_noform( $addition_class = '' ){
			$content = $this->get_noform( $addition_class );
			echo $content;
			return $content;
		}

	}
	
	
