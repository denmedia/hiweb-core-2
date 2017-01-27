<?php


	trait hw_input_rows{

		private $rows;


		/**
		 * Возвращает TRUE, если есть ряды
		 * @return bool
		 */
		public function is_rows(){
			return ( is_array( $this->value() ) );
		}


		/**
		 * Возвращает TRUE, если рядов нет (пустые)
		 * @return bool
		 */
		public function is_empty_rows(){
			return !( is_array( $this->value() ) && count( $this->value() ) > 0 );
		}


		/**
		 * Возвращает TURE, если есть ряды
		 * @return bool
		 */
		public function have_rows(){
			if( $this->is_rows() ){
				if( !is_array( $this->rows ) )
					$this->rows = $this->value();
			}
			return ( is_array( $this->rows ) && count( $this->rows ) > 0 );
		}


		/**
		 * Возвращает TRUE, если поле с указанным индексом (ключем) существует
		 * @param int $row_index
		 * @return bool
		 */
		public function have_row( $row_index = 0 ){
			return ( $this->have_rows() && array_key_exists( $row_index, $this->value() ) );
		}


		/**
		 * Возвращает следующий ряд
		 * @return mixed
		 */
		public function the_row(){
			return next( $this->rows );
		}


		/**
		 * Сбрасывает луп ряда
		 * @return mixed
		 */
		public function reset_row(){
			return reset( $this->rows );
		}
	}