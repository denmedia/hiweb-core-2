<?php


	class hiweb_date {

		/**
		 * Возвращает форматированное дату и время
		 *
		 * @param int $time - необходимое время в секундах, если не указывать, будет взято текущее время
		 * @param string $format - форматирование времени
		 *
		 * @return bool|string
		 */
		public function dateTime( $time = null, $format = 'Y-m-d H:i:s' ) {
			if ( intval( $time ) < 100 ) {
				$time = time();
			}

			return date( $format, intval( $time ) );
		}


		/**
		 * Возвращает наименование дня недели
		 * @param int $weekNum
		 * @param bool $fullName
		 *
		 * @return bool
		 */
		public function dateWeek( $weekNum = 0, $fullName = true ) {
			$weekNum = intval( $weekNum );
			$a       = array(
				array( 'вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб' ),
				array( 'восресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота' )
			);

			return isset( $a[ $fullName ? 1 : 0 ][ $weekNum ] ) ? $a[ $fullName ? 1 : 0 ][ $weekNum ] : false;
		}

	}