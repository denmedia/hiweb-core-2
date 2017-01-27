<?php


	/**
	 * Трейт для добавления инпутов
	 * Class hw_inputs_home_functions
	 */
	trait hw_inputs_home_functions{

		/** @var null|hw_inputs_home */
		private $inputs_home;
		private $inputs_name_prepend;


		/**
		 * @param $path
		 * @param bool $multi_home - создать
		 * @return hw_inputs_home
		 */
		private function inputs_home_make( $path ){
			$this->inputs_home = hiweb()->inputs()->give_home( $path );
			return $this->inputs_home;
		}


		private function inputs_name_prepend( $set ){
			$this->inputs_name_prepend = $set;
		}


		/**
		 * @return bool
		 */
		private function inputs_home_exists(){
			return $this->inputs_home instanceof hw_inputs_home;
		}


		/**
		 * @param $idOrName
		 * @param string|hw_input|null $type
		 * @return hw_input
		 */
		public function add_field( $idOrName, $type = 'text' ){
			if( !$this->inputs_home_exists() ){
				hiweb()->console()->warn( 'Попытка добавить поле в несуществующий дом', true );
				$input = hiweb()->inputs()->make( $idOrName, $type );
			} else {
				$input = $this->inputs_home->add_input( $idOrName, $type );
			}
			$input->name( $this->inputs_name_prepend . $input->name() );
			return $input;
		}


		/**
		 * @param $fields
		 * @return array|hw_input[]
		 */
		public function add_fields( $fields ){
			if( !$this->inputs_home_exists() ){
				hiweb()->console()->warn( 'Попытка добавить поля в несуществующий дом', true );
				return array();
			} else {
				if( $fields instanceof hw_input )
					$fields = array( $fields );
				if( is_array( $fields ) )
					/** @var hw_input $field */
					foreach( $fields as $field ){
						$this->add_field( $field );
					}
				return $this->inputs_home->get_inputs( true, 0, 0 );
			}
		}


		/**
		 * @param bool $return_children
		 * @return array|hw_input[]
		 */
		public function get_fields( $return_children = false ){
			if( !$this->inputs_home_exists() ){
				hiweb()->console()->warn( 'Попытка получить поля из несуществующего дома', true );
				return array();
			} else {
				return $this->inputs_home->get_inputs( true, $return_children === true ? 99 : $return_children );
			}
		}


		/**
		 * @param $id
		 * @return bool
		 */
		public function field_exists( $id ){
			return $this->inputs_home->input_exists( $id );
		}


		/**
		 * @param bool $return_children
		 * @return bool
		 */
		public function have_fields( $return_children = false ){
			$inputs = $this->get_fields( $return_children );
			return ( is_array( $inputs ) && count( $inputs ) > 0 );
		}


	}