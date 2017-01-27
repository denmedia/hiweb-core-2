<?php


	/**
	 * Трейт для управления множества инпутов
	 * Class hw_inputs_home_multi_functions
	 */
	trait hw_inputs_home_multi_functions{

		/** @var null|hw_inputs_home */
		private $inputs_home_parent;

		/** @var array|hw_inputs_home[] */
		private $inputs_homes = array();
		private $inputs_homes_multi_path = array();


		/**
		 * @param $path - массив пути
		 */
		private function inputs_home_make( $path ){
			if( !is_array( $path ) )
				$path = array( $path );
			$this->inputs_homes_multi_path = $path;
			$this->inputs_home_parent = hiweb()->inputs()->give_home( $path );
		}


		/**
		 * @param string $position
		 * @return hw_inputs_home
		 */
		private function inputs_home( $position = null, $make_if_not_exists = false ){
			$position = (string)$position;
			if( !isset( $this->inputs_homes[ $position ] ) ){
				$new_home_path = array_merge( $this->inputs_homes_multi_path, array( '=', $position ) );
				if( $make_if_not_exists ){
					$new_home = hiweb()->inputs()->give_home( $new_home_path );
					$this->inputs_homes[ $position ] = $new_home;
				} else {
					$new_home = hiweb()->inputs()->get_home( $new_home_path );
					if( $new_home instanceof hw_inputs_home ){
						$this->inputs_homes[ $position ] = $new_home;
					} else return new hw_inputs_home( '' );
				}
			}
			return $this->inputs_homes[ $position ];
		}


		/**
		 * @param $position
		 * @return bool
		 */
		private function inputs_home_exists( $position ){
			return ( array_key_exists( $position, $this->inputs_homes ) && ( $this->inputs_homes[ $position ] instanceof hw_inputs_home ) );
		}


		/**
		 * @param $idOrName
		 * @param string|hw_input|null $type
		 * @param int $position
		 * @return hw_input
		 */
		public function add_field( $idOrName, $type = 'text', $position = null ){
			$position = (string)$position;
			return $this->inputs_home( $position, true )->add_input( $idOrName, $type );
		}


		/**
		 * @param $fields
		 * @param null $position
		 * @return array|hw_input[]
		 */
		public function add_fields( $fields, $position = null ){
			$position = (string)$position;
			if( $fields instanceof hw_input )
				$fields = array( $fields );
			if( is_array( $fields ) )
				/** @var hw_input $field */
				foreach( $fields as $field ){
					$this->add_field( $field, null, $position );
				}
			return $this->get_fields();
		}


		/**
		 * @param null $position
		 * @param bool|int $return_children
		 * @param bool|int $return_parent
		 * @return array|hw_input[]
		 */
		public function get_fields( $position = null, $return_children = false, $return_parent = false ){
			$R = array();
			if( is_null( $position ) || is_bool( $position ) ){
				foreach( $this->inputs_homes as $home ){
					$R = array_merge( $R, $home->get_inputs( true, $return_children === true ? 99 : $return_children, $return_parent === true ? 99 : $return_parent ) );
				}
			} else {
				$position = (string)$position;
				if( $this->inputs_home_exists( $position ) )
					$R = $this->inputs_home( $position )->get_inputs( true, $return_children === true ? 99 : $return_children, $return_parent === true ? 99 : $return_parent );
			}
			///Children and Parent fields
			if( $return_children !== false ){
				$R = array_merge( $R, $this->inputs_home_parent->get_inputs( false, $return_children, $return_parent ) );
			}
			return $R;
		}


		/**
		 * @param null $position
		 * @param bool $return_children
		 * @param bool $return_parent
		 * @return bool
		 */
		public function have_fields( $position = null, $return_children = false, $return_parent = false ){
			$inputs = $this->get_fields( $position, $return_children, $return_parent );
			return ( is_array( $inputs ) && count( $inputs ) > 0 );
		}


	}