<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 27.05.2017
	 * Time: 18:38
	 */
	class hw_input{

		use hw_hidden_methods_props;
		use hw_input_attributes;
		use hw_input_value;
		use hw_input_tags;
		use hw_input_axis;

		/** @var string */
		private $id;
		/** @var string */
		private $name;
		/** @var string */
		protected $type;


		///

		public function __construct( $id = false, $type = 'text' ){
			$this->set_id( $id );
			$this->_init();
			$this->type = $type;
		}


		public function __clone(){
			if( is_array( $this->cols ) )
				foreach( $this->cols as $col_id => $col ){
					$this->cols[ $col_id ] = clone $col;
					$this->cols[ $col_id ]->set_parent_input( $this );
				}
		}


		protected function set_id( $id, $force_set_name = true ){
			if( !is_string( $id ) && !is_int( $id ) ){
				$this->id = hiweb()->string()->rand( 8, true, true, false );
			} else $this->id = strtolower( $id );
			if( $force_set_name || is_null( $this->name ) )
				$this->name = $this->id;
			//Prepend input id
			$this->id = 'hw_input_' . $this->id;
		}


		/**
		 * @param null $set
		 * @return string|hw_input
		 */
		public function id( $set = null ){
			if( is_string( $set ) ){
				$this->set_id( $set );
				return $this;
			} else {
				return $this->id;
			}
		}


		/**
		 * @param null|string $set
		 * @return string|hw_input
		 */
		public function name( $set = null ){
			if( is_null( $set ) ){
				return $this->{__FUNCTION__};
			} else {
				$this->{__FUNCTION__} = $set;
				return $this;
			}
		}


		protected function _init(){
		}


		/**
		 * Возвращает HTML
		 * @return string
		 */
		public function html(){
			if( $this->have_rows() ){
				$R = '';
				if( $this->have_cols() ){
					foreach( array_keys( $this->value() ) as $row_key ){
						foreach( array_keys( $this->get_cols() ) as $col_key ){
							//TODO!!!
						}
					}
					$R = '';
				} else {
					foreach( $this->value[0] as $row_key => $row_val ){
						if( is_array( $row_val ) || is_object( $row_val ) ){
							hiweb()->console()->warn( [
								'В строке вывода инпута попался массив или объект',
								$row_val
							], true );
						} else {
							$R .= '<p><input ' . $this->tags_html( $row_key ) . ' value="' . htmlentities( $row_val, ENT_QUOTES, 'utf-8' ) . '"/></p>';
						}
					}
				}
			} else {
				$R = '<input ' . $this->tags_html() . ' value="' . htmlentities( $this->value(), ENT_QUOTES, 'UTF-8' ) . '"/>';
			}
			return $R;
		}


		/**
		 * @return bool
		 */
		public function have_rows(){
			return ( is_array( $this->value() ) && count( $this->value() ) > 0 );
		}


		/**
		 * Выводит HTML
		 * @param null $arguments - Дополнительные аргументы
		 * @return string
		 */
		public function the(){
			$html = $this->html();
			echo $html;
			return $html;
		}


		/**
		 * @return string
		 */
		public function type(){
			return $this->type;
		}


	}