<?php


	/**
	 * Created by PhpStorm.
	 * User: hiweb
	 * Date: 30.06.2016
	 * Time: 16:23
	 */
	class hw_string{


		/**
		 * Разделение строки на символы в массив
		 * @param $string
		 * @return array
		 */
		public function explode_by_symbol( $string ){ return preg_split( '//u', $string, - 1, PREG_SPLIT_NO_EMPTY ); }


		/**
		 * Возвращает набор слуайных символов
		 * @param int  $return_col
		 * @param bool $in_use_latin
		 * @param bool $in_use_number
		 * @param bool $useReg
		 * @return string
		 */
		public function rand( $return_col = 20, $in_use_latin = true, $in_use_number = true, $useReg = false ){
			$symb_arr = array();
			$symb_only_latin_arr = array();
			if( $in_use_latin ){
				for( $list_n = ord( 'a' ); $list_n < ord( 'z' ); $list_n ++ ){
					array_push( $symb_arr, $list_n );
					array_push( $symb_only_latin_arr, $list_n );
				}
			}
			if( $in_use_latin and $useReg ){
				for( $list_n = ord( 'A' ); $list_n < ord( 'Z' ); $list_n ++ ){
					array_push( $symb_arr, $list_n );
				}
			}
			if( $in_use_number ){
				for( $list_n = ord( '0' ); $list_n < ord( '9' ); $list_n ++ ){
					array_push( $symb_arr, $list_n );
				}
			}
			$return_key = '';
			for( $list_n = 0; $list_n < $return_col; $list_n ++ ){
				if( $in_use_latin and $list_n == 0 ){
					$return_key .= chr( $symb_only_latin_arr[ rand( 0, count( $symb_only_latin_arr ) - 1 ) ] );
				} else {
					$return_key .= chr( $symb_arr[ rand( 0, count( $symb_arr ) - 1 ) ] );
				}
			}

			return $return_key;
		}


		/**
		 * Конвертировать utf-8 в cp1251
		 * @param string $utf8
		 * @return string
		 */
		public function utf8_to_ansii( $utf8 = '' ){
			if( function_exists( 'iconv' ) ){
				$returnStr = @iconv( 'UTF-8', 'windows-1251//IGNORE', $utf8 );
			} else {
				$returnStr = strtr( $utf8, array(
					"Р°" => "а",
					"Р±" => "б",
					"РІ" => "в",
					"Рі" => "г",
					"Рґ" => "д",
					"Рµ" => "е",
					"С‘" => "ё",
					"Р¶" => "ж",
					"Р·" => "з",
					"Рё" => "и",
					"Р№" => "й",
					"Рє" => "к",
					"Р»" => "л",
					"Рј" => "м",
					"РЅ" => "н",
					"Рѕ" => "о",
					"Рї" => "п",
					"СЂ" => "р",
					"СЃ" => "с",
					"С‚" => "т",
					"Сѓ" => "у",
					"С„" => "ф",
					"С…" => "х",
					"С†" => "ц",
					"С‡" => "ч",
					"С€" => "ш",
					"С‰" => "щ",
					"СЉ" => "ъ",
					"С‹" => "ы",
					"СЊ" => "ь",
					"СЌ" => "э",
					"СЋ" => "ю",
					"СЏ" => "я",
					"Рђ" => "А",
					"Р‘" => "Б",
					"Р’" => "В",
					"Р“" => "Г",
					"Р”" => "Д",
					"Р•" => "Е",
					"РЃ" => "Ё",
					"Р–" => "Ж",
					"Р—" => "З",
					"Р?" => "И",
					"Р™" => "Й",
					"Рљ" => "К",
					"Р›" => "Л",
					"Рњ" => "М",
					"Рќ" => "Н",
					"Рћ" => "О",
					"Рџ" => "П",
					"Р " => "Р",
					"РЎ" => "С",
					"Рў" => "Т",
					"РЈ" => "У",
					"Р¤" => "Ф",
					"РҐ" => "Х",
					"Р¦" => "Ц",
					"Р§" => "Ч",
					"РЁ" => "Ш",
					"Р©" => "Щ",
					"РЄ" => "Ъ",
					"Р«" => "Ы",
					"Р¬" => "Ь",
					"Р­" => "Э",
					"Р®" => "Ю",
					"С–" => "і",
					"Р†" => "І",
					"С—" => "ї",
					"Р‡" => "Ї",
					"С”" => "є",
					"Р„" => "Є",
					"Т‘" => "ґ",
					"Тђ" => "Ґ",
				) );
			}

			return $returnStr;
		}


		/**
		 * Конвертировать cp1251 в utf-8
		 * @param string $ansii
		 * @return string
		 */
		public function ansii_to_utf8( $ansii = '' ){
			if( function_exists( 'iconv' ) ){
				return iconv( 'windows-1251//IGNORE', 'UTF-8', $ansii );
			} else {
				return strtr( $ansii, array_flip( array(
					"Р°" => "а",
					"Р±" => "б",
					"РІ" => "в",
					"Рі" => "г",
					"Рґ" => "д",
					"Рµ" => "е",
					"С‘" => "ё",
					"Р¶" => "ж",
					"Р·" => "з",
					"Рё" => "и",
					"Р№" => "й",
					"Рє" => "к",
					"Р»" => "л",
					"Рј" => "м",
					"РЅ" => "н",
					"Рѕ" => "о",
					"Рї" => "п",
					"СЂ" => "р",
					"СЃ" => "с",
					"С‚" => "т",
					"Сѓ" => "у",
					"С„" => "ф",
					"С…" => "х",
					"С†" => "ц",
					"С‡" => "ч",
					"С€" => "ш",
					"С‰" => "щ",
					"СЉ" => "ъ",
					"С‹" => "ы",
					"СЊ" => "ь",
					"СЌ" => "э",
					"СЋ" => "ю",
					"СЏ" => "я",
					"Рђ" => "А",
					"Р‘" => "Б",
					"Р’" => "В",
					"Р“" => "Г",
					"Р”" => "Д",
					"Р•" => "Е",
					"РЃ" => "Ё",
					"Р–" => "Ж",
					"Р—" => "З",
					"Р?" => "И",
					"Р™" => "Й",
					"Рљ" => "К",
					"Р›" => "Л",
					"Рњ" => "М",
					"Рќ" => "Н",
					"Рћ" => "О",
					"Рџ" => "П",
					"Р " => "Р",
					"РЎ" => "С",
					"Рў" => "Т",
					"РЈ" => "У",
					"Р¤" => "Ф",
					"РҐ" => "Х",
					"Р¦" => "Ц",
					"Р§" => "Ч",
					"РЁ" => "Ш",
					"Р©" => "Щ",
					"РЄ" => "Ъ",
					"Р«" => "Ы",
					"Р¬" => "Ь",
					"Р­" => "Э",
					"Р®" => "Ю",
					"С–" => "і",
					"Р†" => "І",
					"С—" => "ї",
					"Р‡" => "Ї",
					"С”" => "є",
					"Р„" => "Є",
					"Т‘" => "ґ",
					"Тђ" => "Ґ",
				) ) );
			}
		}


		/**
		 * Возвращает массив, парсинг строки на символы и цифры отдельно
		 * Пример, ab12cdEf3Gh456 =>> array( 'ab', '12', 'cdEf', '3', 'Gh', '456' )
		 * @param $parseStr
		 * @return array
		 */
		public function explode_to_string_numeric( $parseStr ){
			$r = array();
			foreach( $this->explode_by_symbol( $parseStr ) as $s ){
				end( $r );
				$lastVal = current( $r );
				$lastKey = key( $r );
				if( $lastVal === false ){
					$r[] = $s;
				} else {
					$lastNum = is_numeric( $lastVal );
					if( is_numeric( $s ) && $lastNum ){
						$r[ $lastKey ] .= $s;
					} else {
						$r[] = $s;
					}
				}
			}

			return $r;
		}


		/**
		 * Возвращает форматированный JSON
		 * @param string|mixed $json - строка JSON, лио что-то кроме строки будет переведено в JSON автоматически
		 * @return string
		 * @version 1.1
		 */
		public function json_format( $json ){
			if( !is_string( $json ) ){
				$json = json_encode( $json );
			}
			$result = '';
			$pos = 0;
			$strLen = strlen( $json );
			$indentStr = '  ';
			$newLine = "\n";
			$prevChar = '';
			$outOfQuotes = true;
			for( $i = 0; $i <= $strLen; $i ++ ){
				$char = substr( $json, $i, 1 );
				if( $char == '"' && $prevChar != '\\' ){
					$outOfQuotes = !$outOfQuotes;
				} else if( ( $char == '}' || $char == ']' ) && $outOfQuotes ){
					$result .= $newLine;
					$pos --;
					for( $j = 0; $j < $pos; $j ++ ){
						$result .= $indentStr;
					}
				}
				$result .= $char;
				if( ( $char == ',' || $char == '{' || $char == '[' ) && $outOfQuotes ){
					$result .= $newLine;
					if( $char == '{' || $char == '[' ){
						$pos ++;
					}
					for( $j = 0; $j < $pos; $j ++ ){
						$result .= $indentStr;
					}
				}
				$prevChar = $char;
			}

			return $result;
		}


		/**
		 * Вернет TRUE, если $testStr являеться regex выражением
		 * @param $testStr
		 * @return int
		 */
		public function isRegex( $testStr ){ return preg_match( "/^\/[\s\S]+\/$/", $testStr ) > 0; }


		/**
		 * Возвращает TRUE, если используется JSON
		 * @param string     $haystack
		 * @param bool|mixed $returnIfFalse      - вернуть данное значение, в случае неудачи
		 * @param bool       $returnDecodeIfJson - вернуть конвертировнный JSON
		 * @internal param mixed $returnIfNotJSON - вернуть это значение, если haystack не JSON
		 * @return bool|mixed
		 */
		public function is_json( $haystack, $returnIfFalse = false, $returnDecodeIfJson = true ){
			if( !is_string( $haystack ) || empty( $haystack ) ){
				return $returnIfFalse;
			}
			$decode = json_decode( $haystack, true );

			return ( json_last_error() == JSON_ERROR_NONE ) ? ( $returnDecodeIfJson ? $decode : true ) : $returnIfFalse;
		}


		/**
		 * Возвращает TRUE, если значение пустое
		 * @param $str
		 * @return bool
		 * @alias   hiweb()->string()->isEmpty()
		 * @version 1.1
		 */
		public function is_empty( $str ){
			return ( !is_array( $str ) && ( is_null( $str ) || $str === false || trim( (string)$str ) == '' ) ) ? true : false;
		}


		/**
		 * Возвращает дефолтное значение, если 4str пустое
		 * @param        $str
		 * @param string $ifEmpty
		 * @return string
		 */
		public function str_if_empty( $str, $ifEmpty = '' ){
			return $this->is_empty( $str ) ? $ifEmpty : $str;
		}


	}