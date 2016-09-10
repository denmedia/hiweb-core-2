<?php
	
	/**
	 * Created by PhpStorm.
	 * User: d9251
	 * Date: 31.08.2016
	 * Time: 23:03
	 */
	class hiweb_dump{
		
		
		
		public function __construct($data = null){
			if(!is_null($data)) $this->print_r($data);
		}
		
		
		/**
		 * Выводить структуру заданной переменной
		 * @param      $mixed
		 * @param int  $depth       - установить глубину массивов и объектов
		 * @param bool $showObjects - раскрывать объекты
		 * @return string
		 * @version 1.4
		 */
		public function getHtml_arrayPrint( $mixed, $depth = 4, $showObjects = true ){
			if( $depth < 1 ) return '<div class="hiweb-string-printarr">...</div>';
			$r = '';
			if( in_array( gettype( $mixed ), array( 'array', 'object' ) ) ) $r .= ' <span class="hiweb-string-printarr-gettype">[' . gettype( $mixed ) . ']</span>';
			switch( gettype( $mixed ) ){
				case 'array':
					$r .= '<ul>';
					foreach( $mixed as $k => $v ){
						$r .= '<li><span data-key>' . $k . '</span>: ' . ( $this->getHtml_arrayPrint( $v, $depth - 1, $showObjects ) ) . '</li>';
					}
					$r .= '</ul>';
					break;
				case 'object':
					$r .= '<ul>';
					if( $showObjects ){
						foreach( $mixed as $k => $v ){
							$r .= '<li><span data-key>' . $k . '</span>: ' . ( $this->getHtml_arrayPrint( $v, $depth - 1, $showObjects ) ) . '</li>';
						}
					}
					$r .= '</ul>';
					break;
				case 'boolean':
					$r .= ( $mixed ? 'TRUE' : 'FALSE' );
					break;
				case 'null':
					$r .= 'NULL';
					break;
				default:
					$r .= ( trim( $mixed ) == '' ? '<span class="hiweb-string-printarr-gettype">пусто</span>' : nl2br( htmlentities( $mixed, ENT_COMPAT, 'UTF-8' ) ) );
					break;
			}
			if( !in_array( gettype( $mixed ), array( 'array', 'object' ) ) ) $r .= ' <span class="hiweb-string-printarr-gettype">' . ( gettype( $mixed ) == 'string' && mb_strlen( $mixed ) == 1 ? '[ord:<b>' . ord( $mixed ) . '</b>]' : '' ) . '[' . gettype( $mixed ) . ']</span>';
			return "<div class='hiweb-string-printarr'>$r</div>";
		}
		
		public function print_r( $mixed, $depth = 4, $showObjects = true ){
			echo '<link rel="stylesheet" href="' . HIWEB_URL_BASE . '/css/arrays.css"/>';
			echo $this->getHtml_arrayPrint( $mixed, $depth, $showObjects );
			return $this;
		}
	}