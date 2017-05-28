<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 26.05.2017
	 * Time: 12:41
	 */
	class hw_fields_loop{

		static $limit = 99;

		///
		/** @var hw_field_frontend[] */
		private $enqueu = [];
		private $current_row = [];


		/**
		 * @param      $field_id
		 * @param null $context_id
		 * @return bool
		 */
		public function have_rows( $field_id, $context_id = null ){
			$R = false;
			if( self::$limit > 0 ){
				self::$limit --;
				$ffield = hiweb()->fields()->home()->get_frontend( $field_id, $context_id );
				if( !$ffield->is_exists() )
					return false;
				///
				$global_id = $ffield->global_id();
				if( !$this->enqueu_exists( $global_id ) ){
					$this->enqueu[ $global_id ] = $ffield;
				}
				$R = $ffield->rows()->have_rows();
				if( !$R ){
					unset( $this->enqueu[ $global_id ] );
				}
			}
			return $R;
		}


		/**
		 * @param $col_id
		 * @return hw_field_frontend|null
		 */
		private function get_ffield_by_col_id( $col_id ){
			$ffields_r = array_reverse( $this->enqueu );
			/**
			 * @var hw_field_frontend $ffield
			 */
			foreach( $ffields_r as $id => $ffield ){
				if( $ffield instanceof hw_field_frontend ){
					if( $ffield->input()->has_col( $col_id ) ){
						return $ffield;
					}
				}
				if( $ffield instanceof hw_input ){
					if( $ffield->has_col( $col_id ) ){
						return $ffield;
					}
				}
			}
			return null;
		}


		/**
		 * @param $global_id
		 * @return bool
		 */
		private function enqueu_exists( $global_id ){
			return array_key_exists( $global_id, $this->enqueu ) && ( $this->enqueu[ $global_id ] instanceof hw_field_frontend || $this->enqueu[ $global_id ] instanceof hw_input );
		}


		/**
		 * @return mixed|null
		 */
		public function the_row(){
			if( end( $this->enqueu ) instanceof hw_field_frontend || end( $this->enqueu ) instanceof hw_input ){
				$this->current_row = end( $this->enqueu )->rows()->the_row();
				return $this->current_row;
			}
			return null;
		}


		/**
		 * @param null $field_id
		 * @param null $content_id
		 * @return mixed|null
		 */
		public function reset_rows( $field_id = null, $content_id = null ){
			if( is_null( $field_id ) ){
				if( end( $this->enqueu ) instanceof hw_field_frontend || end( $this->enqueu ) instanceof hw_input ){
					return end( $this->enqueu )->reset_rows();
				}
			} else {
				hiweb()->fields()->home()->get_frontend( $field_id, $content_id )->rows()->reset_rows();
			}
			return null;
		}


		/**
		 * @param $col_id
		 * @return mixed|null
		 */
		public function get_sub_field( $col_id ){
			$ffield = $this->get_ffield_by_col_id( $col_id );
			if( $ffield instanceof hw_field_frontend || $ffield instanceof hw_input ){
				return $ffield->rows()->get_sub_input_value( $col_id );
			}
			return null;
		}


		/**
		 * @param      $col_id
		 * @param null $atts
		 * @param null $atts2
		 * @return mixed|null
		 */
		public function get_sub_field_content( $col_id, $atts = null, $atts2 = null ){
			$ffield = $this->get_ffield_by_col_id( $col_id );
			if( $ffield instanceof hw_field_frontend ){
				return $ffield->rows()->get_sub_input_content( $col_id, $atts, $atts2 );
			}
			return null;
		}

	}