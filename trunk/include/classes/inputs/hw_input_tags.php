<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 19.05.2017
	 * Time: 22:39
	 */
	trait hw_input_tags{

		/** @var array */
		protected $tags = array();


		public function tag_add( $name, $value ){
			$this->tags[ $name ] = $value;
		}


		/**
		 * @param $name
		 * @return null|mixed
		 */
		public function tag_get( $name ){
			return array_key_exists( $name, $this->tags ) ? $this->tags[ $name ] : null;
		}


		/**
		 * @return array
		 */
		public function tags(){
			$tags = $this->tags;
			$add_tag_keys = array(
				'id',
				'name',
				'type'
			);
			foreach( $add_tag_keys as $add_tag_key ){
				if( !isset( $tags[ $add_tag_key ] ) ){
					$tags[ $add_tag_key ] = $this->{$add_tag_key};
				}
			}
			///
			return $tags;
		}


		/**
		 * Возвращает строку тэгов
		 * @param bool|string|integer $index
		 * @return array|string
		 */
		public function tags_html( $index = false ){
			$tags = $this->tags();
			///
			foreach( $tags as $key => $val ){
				if( is_null( $val ) )
					continue;
				if( is_string( $index ) && $this->have_rows() && array_key_exists( $key, array_flip( [
						'value',
						'name'
					] ) )
				){
					if( array_key_exists( $index, $this->value() ) ){
						switch( $key ){
							case 'name':
								$R[] = 'name="' . sanitize_file_name( $val ) . '[' . $index . ']"';
								break;
							case 'id':
								$R[] = 'id="' . sanitize_file_name( $val ) . '-' . sanitize_file_name( $index ) . '"';
								break;
							case 'value':
								$R[] = 'value="' . htmlentities( $val[ $index ], ENT_QUOTES, 'utf-8' ) . '"';
								break;
						}
					}
				} elseif( !is_array( $val ) && !is_object( $val ) ) {
					if( $val != false )
						$R[] = $key . '="' . htmlentities( $val, ENT_QUOTES, 'utf-8' ) . '"';
				} else {
					$complexTag = array();
					foreach( $val as $subKey => $subVal ){
						if( is_array( $subVal ) || is_object( $subVal ) ){
							$complexTag[] = $subKey . ':' . json_encode( $subVal ) . '';
						} else {
							$complexTag[] = $subKey . ':' . htmlentities( $subVal, ENT_QUOTES, 'utf-8' ) . '';
						}
					}
					$R[] = $key . '="' . implode( ';', $complexTag ) . '"';
				}
			}
			return implode( ' ', $R );
		}

	}