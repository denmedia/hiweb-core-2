<?php


	class hw_posts{

		/** @var hw_post[] */
		private $posts = array();


		/**
		 * Возвращает пост
		 * @param $postOrId
		 * @return hw_post
		 */
		public function get( $postOrId ){
			if( $postOrId instanceof WP_Post ){
				$id = $postOrId->ID;
			}else{
				$id = $postOrId;
			}
			///
			if( !array_key_exists( $id, $this->posts ) ){
				$this->posts[ $id ] = new hw_post( $postOrId );
			}
			return $this->posts[ $id ];
		}

	}


	/**
	 * Класс для работы с одной записью
	 * Class hiweb_wp_post
	 */
	class hw_post{

		/**
		 * @var array|null|WP_Post
		 */
		private $object;

		/**
		 * @var hw_wp_post_meta
		 */
		private $meta;

		private $taxonomy_exist = array();
		/** @var hw_wp_taxonomy[] */
		private $taxonomies = array();


		public function __construct( $postOrId = 0 ){
			$this->object = get_post( $postOrId );
		}


		/**
		 * Возвращает TRUE, если пост существует
		 * @return bool
		 */
		public function exist(){
			return ( $this->object instanceof WP_Post );
		}


		/**
		 * Возвращает текущий объект WP_Post, либо NULL
		 * @return array|null|WP_Post
		 */
		public function object(){
			return $this->object;
		}


		/**
		 * Возвращает ID записи
		 * @return int|null
		 */
		public function id(){
			return ( $this->object instanceof WP_Post ) ? $this->object->ID : null;
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function name( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * Возвращает класс для работы с мета записи
		 * @param null|string $key - вернуть значение ключа мета, либо regex-паттерн (обязатиельно указать INT $use_regex_index), либо объект класса для работы с мета даннойзаписи
		 * @param null $use_regex_index - если $key является паттерном regex, то вернуть значение по индексу найденного ключа
		 * @return hw_wp_post_meta|mixed|null
		 */
		public function meta( $key = null, $use_regex_index = null ){
			if( !$this->meta instanceof hw_wp_post_meta ){
				$this->meta = new hw_wp_post_meta( $this->object );
			}
			if( is_null( $key ) ){
				return $this->meta;
			}

			return $this->meta->get( $key, $use_regex_index );
		}


		/**
		 * Возвращает TRUE, если таксономия принадлежит типу записи
		 * @param $taxonomy
		 * @return bool
		 */
		public function taxonomy_exist( $taxonomy ){
			if( !array_key_exists( $taxonomy, $this->taxonomy_exist ) ){
				$taxonomies = get_post_taxonomies( $this->object );
				$this->taxonomy_exist[ $taxonomy ] = array_key_exists( $taxonomy, array_flip( $taxonomies ) );
			}

			return $this->taxonomy_exist[ $taxonomy ];
		}


		/**
		 * Возвращает класс таксономии hiweb_wp_taxonomy
		 * @param $taxonomy
		 * @return hw_wp_taxonomy|bool
		 */
		public function taxonomy( $taxonomy ){
			if( !isset( $this->taxonomies[ $taxonomy ] ) ){
				if( $this->taxonomy_exist( $taxonomy ) ){
					$this->taxonomies[ $taxonomy ] = false;
				}
				$this->taxonomies[ $taxonomy ] = hiweb()->wp()->taxonomy( $taxonomy );
			}

			return $this->taxonomies[ $taxonomy ];
		}


		/**
		 * @return hw_wp_taxonomy[]
		 */
		public function taxonomies(){
			$taxonomies = get_post_taxonomies( $this->object );
			$R = array();
			if( is_array( $taxonomies ) ){
				foreach( $taxonomies as $taxonomy ){
					$tax = $this->taxonomy( $taxonomy );
					if( $tax->exist() ){
						$R[ $tax->name() ] = $tax;
					}
				}
			}

			return $R;
		}


		/**
		 * Возвращает массив терминов данного поста
		 * @param      $taxonomy - если не указать таксономию (указав 0, null, false), то будет вернут массив, сгрупированный по таксономиям
		 * @param null $only_field - если указать ключь термина, например name, то вместо WP_Term вернеться расгруппированный объект по ключу
		 * @return array
		 */
		public function terms( $taxonomy = null, $only_field = null ){
			$R = array();
			if( !is_string( $taxonomy ) || trim( $taxonomy ) == '' ){
				$taxonomies = $this->taxonomies();
				foreach( $taxonomies as $tax ){
					$R[ $tax->name() ] = $this->terms( $tax->name(), $only_field );
				}
			}elseif( $this->taxonomy_exist( $taxonomy ) ){
				$terms = get_the_terms( $this->id(), $taxonomy );
				if( is_array( $terms ) ){
					foreach( $terms as $term ){
						if( is_string( $only_field ) && trim( $only_field ) != '' ){
							$R[ $term->term_id ] = property_exists( $term, $only_field ) ? $term->{$only_field} : $term;
						}else{
							$R[ $term->term_id ] = $term;
						}
					}
				}
			}

			return $R;
		}

	}


	/**
	 * Класс для работы с мета-данными одной записи
	 * Class hw_wp_post_meta
	 */
	class hw_wp_post_meta{

		/**
		 * @return hw_wp_post
		 */
		private $post;

		/**
		 * @var array
		 */
		private $meta;


		public function __construct( $postOrId ){
			$this->post = get_post( $postOrId );
			if( $this->post instanceof WP_Post ){
				$meta = get_post_meta( $this->post->ID );
				if( is_array( $meta ) ){
					foreach( $meta as $key => $val ){
						$this->meta[ $key ] = get_post_meta( $this->post->ID, $key, true );
					}
				}
			}
		}


		/**
		 * Возвращает массив значений мета
		 * @param null $key_regex_pattern - regex-паттер для поиска нужных ключей, если не указать, будут вернуты все ключи
		 * @return array
		 */
		public function arr( $key_regex_pattern = null ){
			if( is_null( $key_regex_pattern ) ){
				return $this->meta;
			}
			////
			$R = array();
			foreach( $this->meta as $key => $val ){
				if( preg_match( $key_regex_pattern, $key ) > 0 ){
					$R[ $key ] = $val;
				}
			}

			return $R;
		}


		/**
		 * Возвращает значение ключа
		 * @param null $key - ключь, либо regex-паттерн (обязатиельно указать INT $use_regex_index)
		 * @param null $use_regex_index - если $key является паттерном regex, то вернуть значение по индексу найденного ключа
		 * @return mixed|null
		 */
		public function get( $key = null, $use_regex_index = null ){
			if( is_null( $key ) ){
				return null;
			}
			if( !is_int( $use_regex_index ) ){
				return $this->exist( $key ) ? $this->meta[ $key ] : null;
			}else{
				$arr = $this->arr( $key );

				return array_key_exists( $use_regex_index, $arr ) ? $arr[ $use_regex_index ] : null;
			}
		}


		/**
		 * Возвращает TRUE, если ключ существует
		 * @param $key
		 * @return bool
		 */
		public function exist( $key ){
			return array_key_exists( $key, $this->meta );
		}

	}