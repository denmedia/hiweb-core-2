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
			$id = sanitize_file_name(strtolower($id));
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
		
		/** @var hw_input[] */
		private $fields = array();
		
		private $submit = false;
		
		private $settings_group;


		public function __construct( $id = '' ){
			$this->id = $id;
		}
		

		/**
		 * @param $idOrField
		 * @param string $type
		 * @return hw_input|hw_input_image|hw_input_repeat|hw_input_images
		 */
		public function field( $idOrField, $type = 'text' ){
			if( !array_key_exists( $idOrField, $this->fields ) ){
				$this->fields[ $idOrField ] = hiweb()->inputs()->give( $idOrField, $type );
			}
			return $this->fields[ $idOrField ];
		}
		
		
		/**
		 * @param array $inputs
		 * @param bool|int $append - укажите 1 или TRUE для добавления в конец массива полей, -1 для добавления в начало массива
		 * @return hw_form
		 */
		public function fields($inputs = array(), $append = true){
			if( is_array($inputs) && count($inputs) > 0) {
				if($append != false) {
					if($append < 0) {
						$this->fields = array_merge($inputs, $this->fields);
					} else {
						$this->fields = array_merge($this->fields,$inputs);
					}
				} else {
					$this->fields = $inputs;
				}
			}
			return $this;
		}
		
		public function get_fields(){
			return $this->fields;
		}
		
		
		/**
		 * @param null $set
		 * @return $this
		 */
		public function submit($set = null){
			if(!is_null($set)){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}
		
		
		/**
		 * @param null $set
		 * @return $this
		 */
		public function settings_group($set = null){
			if(!is_null($set)){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}
		
		
		/**
		 * @param null $set
		 * @return $this
		 */
		public function id($set = null){
			if(!is_null($set)){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}
		
		
		/**
		 * @param null $set
		 * @return $this
		 */
		public function action($set = null){
			if(!is_null($set)){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}
		
		
		/**
		 * @param null $set
		 * @return $this
		 */
		public function method($set = null){
			if(!is_null($set)){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}
		
		
		/**
		 * @param null $set
		 * @return $this
		 */
		public function template($set = null){
			if(!is_null($set)){
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
				}else{
					$formTags[] = $key . '="' . htmlentities( $val, ENT_QUOTES, 'utf-8' ) . '"';
				}
			}
			///
			return '<form ' . implode( ' ', $formTags ) . '>' . $this->get_noform() . ( $this->submit ? '<button type="submit">'.$this->submit.'</button>' : '' ). '</form>';
		}


		/**
		 * Возвращает HTML полей без формы
		 * @return string
		 */
		public function get_noform(){
			hiweb()->css(HIWEB_URL_CSS.'/forms.css');
			///
			$templatePath = HIWEB_DIR_MODULES . '/forms/' . $this->template . '.php';
			if( !file_exists( $templatePath ) )
				$templatePath = HIWEB_DIR_MODULES . '/forms/default.php';
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
	
	
	