<?php
	/**
	 * Created by PhpStorm.
	 * User: d9251
	 * Date: 31.08.2016
	 * Time: 17:36
	 */


	/**
	 * Класс для работы с WordPress
	 * Class hiweb_wp
	 */
	class hiweb_wp {

		/** @var hiweb_wp_post[] */
		private $posts = array();

		/** @var hiweb_wp_taxonomy[] */
		private $taxonomies = array();

		/** @var hiweb_wp_theme[] */
		private $themes = array();

		/**
		 * Возвращает hiweb_wp_post
		 *
		 * @param int|WP_post $postOrId
		 *
		 * @return hiweb_wp_post
		 */
		public function post( $postOrId ) {
			if ( $postOrId instanceof WP_Post ) {
				$postId = $postOrId->ID;
			} else {
				$postId = $postOrId;
			}
			///
			if ( ! isset( $this->posts[ $postId ] ) ) {
				$this->posts[ $postId ] = new hiweb_wp_post( $postOrId );
			}

			return $this->posts[ $postId ];
		}

		/**
		 * @param int|WP_Post $postOrId
		 * @param null $key
		 * @param null $use_regex_index
		 *
		 * @return hiweb_wp_meta|mixed|null
		 */
		public function meta( $postOrId, $key = null, $use_regex_index = null ) {
			return $this->post( $postOrId )->meta( $key, $use_regex_index );
		}


		public function taxonomy( $taxonomy = null ) {
			if ( ! array_key_exists( $taxonomy, $this->taxonomies ) ) {
				$this->taxonomies[ $taxonomy ] = new hiweb_wp_taxonomy( $taxonomy );
			}

			return $this->taxonomies[ $taxonomy ];
		}

		/**
		 * @param $theme - слуг темы
		 *
		 * @return hiweb_wp_theme
		 */
		public function theme( $theme = null ) {
			if ( ! is_string( $theme ) || trim( $theme ) == '' ) {
				$theme = get_option( 'template' );
			}
			if ( ! array_key_exists( $theme, $this->themes ) ) {
				$this->themes[ $theme ] = new hiweb_wp_theme( $theme );
			}

			return $this->themes[ $theme ];
		}
	}


	/**
	 * Класс для работы с одной записью
	 * Class hiweb_wp_post
	 */
	class hiweb_wp_post {

		/**
		 * @var array|null|WP_Post
		 */
		private $object;

		/**
		 * @var hiweb_wp_meta
		 */
		private $meta;

		private $taxonomy_exist = array();
		/** @var hiweb_wp_taxonomy[] */
		private $taxonomies = array();

		public function __construct( $postOrId = 0 ) {
			$this->object = get_post( $postOrId );
		}

		/**
		 * Возвращает TRUE, если пост существует
		 * @return bool
		 */
		public function exist() {
			return ( $this->object instanceof WP_Post );
		}

		/**
		 * Возвращает текущий объект WP_Post, либо NULL
		 * @return array|null|WP_Post
		 */
		public function object() {
			return $this->object;
		}

		/**
		 * Возвращает ID записи
		 * @return int|null
		 */
		public function id() {
			return ( $this->object instanceof WP_Post ) ? $this->object->ID : null;
		}

		/**
		 * Возвращает класс для работы с мета записи
		 *
		 * @param null|string $key - вернуть значение ключа мета, либо regex-паттерн (обязатиельно указать INT $use_regex_index), либо объект класса для работы с мета даннойзаписи
		 * @param null $use_regex_index - если $key является паттерном regex, то вернуть значение по индексу найденного ключа
		 *
		 * @return hiweb_wp_meta|mixed|null
		 */
		public function meta( $key = null, $use_regex_index = null ) {
			if ( ! $this->meta instanceof hiweb_wp_meta ) {
				$this->meta = new hiweb_wp_meta( $this->object );
			}
			if ( is_null( $key ) ) {
				return $this->meta;
			}

			return $this->meta->get( $key, $use_regex_index );
		}

		/**
		 * Возвращает TRUE, если таксономия принадлежит типу записи
		 *
		 * @param $taxonomy
		 *
		 * @return bool
		 */
		public function taxonomy_exist( $taxonomy ) {
			if ( ! array_key_exists( $taxonomy, $this->taxonomy_exist ) ) {
				$taxonomies                        = get_post_taxonomies( $this->object );
				$this->taxonomy_exist[ $taxonomy ] = array_key_exists( $taxonomy, array_flip( $taxonomies ) );
			}

			return $this->taxonomy_exist[ $taxonomy ];
		}

		/**
		 * Возвращает класс таксономии hiweb_wp_taxonomy
		 *
		 * @param $taxonomy
		 *
		 * @return hiweb_wp_taxonomy|bool
		 */
		public function taxonomy( $taxonomy ) {
			if ( ! isset( $this->taxonomies[ $taxonomy ] ) ) {
				if ( $this->taxonomy_exist( $taxonomy ) ) {
					$this->taxonomies[ $taxonomy ] = false;
				}
				$this->taxonomies[ $taxonomy ] = hiweb()->wp()->taxonomy( $taxonomy );
			}

			return $this->taxonomies[ $taxonomy ];
		}

		/**
		 * @return hiweb_wp_taxonomy[]
		 */
		public function taxonomies() {
			$taxonomies = get_post_taxonomies( $this->object );
			$R          = array();
			if ( is_array( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					$tax = $this->taxonomy( $taxonomy );
					if ( $tax->exist() ) {
						$R[ $tax->name() ] = $tax;
					}
				}
			}

			return $R;
		}

		/**
		 * Возвращает массив терминов данного поста
		 *
		 * @param      $taxonomy - если не указать таксономию (указав 0, null, false), то будет вернут массив, сгрупированный по таксономиям
		 * @param null $only_field - если указать ключь термина, например name, то вместо WP_Term вернеться расгруппированный объект по ключу
		 *
		 * @return array
		 */
		public function terms( $taxonomy = null, $only_field = null ) {
			$R = array();
			if ( ! is_string( $taxonomy ) || trim( $taxonomy ) == '' ) {
				$taxonomies = $this->taxonomies();
				foreach ( $taxonomies as $tax ) {
					$R[ $tax->name() ] = $this->terms( $tax->name(), $only_field );
				}
			} elseif ( $this->taxonomy_exist( $taxonomy ) ) {
				$terms = get_the_terms( $this->id(), $taxonomy );
				if ( is_array( $terms ) ) {
					foreach ( $terms as $term ) {
						if ( is_string( $only_field ) && trim( $only_field ) != '' ) {
							$R[ $term->term_id ] = property_exists( $term, $only_field ) ? $term->{$only_field} : $term;
						} else {
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
	 * Class hiweb_wp_meta
	 */
	class hiweb_wp_meta {

		/**
		 * @return hiweb_wp_post
		 */
		private $post;

		/**
		 * @var array
		 */
		private $meta;

		public function __construct( $postOrId ) {
			$this->post = get_post( $postOrId );
			if ( $this->post instanceof WP_Post ) {
				$meta = get_post_meta( $this->post->ID );
				if ( is_array( $meta ) ) {
					foreach ( $meta as $key => $val ) {
						$this->meta[ $key ] = is_array( $val ) ? reset( $val ) : $val;
					}
				}
			}
		}

		/**
		 * Возвращает массив значений мета
		 *
		 * @param null $key_regex_pattern - regex-паттер для поиска нужных ключей, если не указать, будут вернуты все ключи
		 *
		 * @return array
		 */
		public function arr( $key_regex_pattern = null ) {
			if ( is_null( $key_regex_pattern ) ) {
				return $this->meta;
			}
			////
			$R = array();
			foreach ( $this->meta as $key => $val ) {
				if ( preg_match( $key_regex_pattern, $key ) > 0 ) {
					$R[ $key ] = $val;
				}
			}

			return $R;
		}

		/**
		 * Возвращает значение ключа
		 *
		 * @param null $key - ключь, либо regex-паттерн (обязатиельно указать INT $use_regex_index)
		 * @param null $use_regex_index - если $key является паттерном regex, то вернуть значение по индексу найденного ключа
		 *
		 * @return mixed|null
		 */
		public function get( $key = null, $use_regex_index = null ) {
			if ( is_null( $key ) ) {
				return null;
			}
			if ( ! is_int( $use_regex_index ) ) {
				return $this->exist( $key ) ? $this->meta[ $key ] : null;
			} else {
				$arr = $this->arr( $key );

				return array_key_exists( $use_regex_index, $arr ) ? $arr[ $use_regex_index ] : null;
			}
		}

		/**
		 * Возвращает TRUE, если ключ существует
		 *
		 * @param $key
		 *
		 * @return bool
		 */
		public function exist( $key ) {
			return array_key_exists( $key, $this->meta );
		}

	}


	/**
	 * Класс для работы с таксономией и постами
	 * Class hiweb_wp_taxonomy
	 */
	class hiweb_wp_taxonomy {

		/** @var string */
		private $name;

		/** @var array */
		private $terms = array();

		private $object;

		public function __construct( $taxonomy ) {
			$this->name   = $taxonomy;
			$this->object = get_taxonomy( $taxonomy );
		}


		public function object() {
			return $this->object;
		}

		/**
		 * Возвращает значение ключа таксономии, либо NULL, если ключа нет
		 *
		 * @param null $key - название ключа
		 * @param null $secondKey - поиск значения ключа во вложенном массиве
		 *
		 * @return null
		 */
		public function get( $key, $secondKey = null ) {
			if ( array_key_exists( $key, (array) $this->object ) ) {
				if ( is_null( $secondKey ) ) {
					return $this->object->{$key};
				} else {
					$val = (array) $this->object->{$key};

					return $val[ $secondKey ];
				}
			} else {
				return null;
			}
		}

		/**
		 * Возвращает TRUE, если таксономия существует
		 * @return bool
		 */
		public function exist() {
			return taxonomy_exists( $this->name );
		}

		/**
		 * Возвращает все термины таксономии
		 * @return WP_Term[]|int|WP_Error
		 */
		public function terms( $returnKeyGoup = '', $orderby = 'name', $hide_empty = false, $number = 0, $offset = 0, $field = 'all' ) {
			$taxonomy   = $this->name;
			$args       = get_defined_vars();
			$argsString = md5( json_encode( $args ) );
			if ( ! array_key_exists( $argsString, $this->terms ) ) {
				$this->terms[ $argsString ] = array();
				$terms                      = get_terms( $args );
				/** @var WP_Term[] $terms */
				if ( is_array( $terms ) ) {
					if ( is_string( $returnKeyGoup ) && trim( $returnKeyGoup != '' ) ) {
						foreach ( $terms as $term ) {
							if ( property_exists( $term, $returnKeyGoup ) ) {
								$this->terms[ $argsString ][ $term->{$returnKeyGoup} ][] = $term;
							}
						}
					} else {
						foreach ( $terms as $term ) {
							$this->terms[ $argsString ][ $term->term_id ] = $term;
						}
					}
				} else {
					$this->terms[ $argsString ] = array();
				}
			}

			return $this->terms[ $argsString ];
		}

		/**
		 * Возвращает SLUG таксономии
		 * @return string
		 */
		public function name() {
			return $this->name;
		}

	}


	class hiweb_wp_theme {

		/** @var  string */
		private $theme;

		/** @var hiweb_wp_location[] */
		private $locations = array();

		public function __construct( $theme ) {
			$this->theme = $theme;
		}


		public function exist() {

		}


		/**
		 * Возвращает массив локаций
		 * @return array
		 */
		public function locations() {
			$R    = array();
			$mods = get_option( 'theme_mods_' . $this->theme );
			if ( isset( $mods['nav_menu_locations'] ) ) {
				$R = $mods['nav_menu_locations'];
			}

			return $R;
		}

		/**
		 * @param $location
		 *
		 * @return hiweb_wp_location
		 */
		public function location( $location = null ) {
			if ( ! array_key_exists( $location, $this->locations ) ) {
				$this->locations[ $location ] = new hiweb_wp_location( $location );
			}

			return $this->locations[ $location ];
		}


		/**
		 * Возвращает массив с массивами элементов
		 *
		 * @param $location
		 *
		 * @return array|false
		 */
		public function menu_items( $location ) {
			$R              = array();
			$menus          = wp_get_nav_menus();
			$menu_locations = $this->locations();
			if ( isset( $menu_locations[ $location ] ) ) {
				foreach ( $menus as $menu ) {
					if ( $menu->term_id == $menu_locations[ $location ] ) {
						return wp_get_nav_menu_items( $menu );
					}
				}
			}

			return $R;
		}

	}


	class hiweb_wp_location {

		private $location;


		public function __construct( $location ) {
			$this->location = $location;
		}

	}