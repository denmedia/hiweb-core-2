<?php
	
	
	/**
	 * Created by PhpStorm.
	 * User: d9251
	 * Date: 31.08.2016
	 * Time: 23:03
	 */
	class hiweb_dump{
		
		
		public function __construct( $data = null ){
			if( ! is_null( $data ) ){
				$this->print_r( $data );
			}
		}
		
		
		/**
		 * Выводить структуру заданной переменной
		 * @param      $mixed
		 * @param int $depth - установить глубину массивов и объектов
		 * @param bool $showObjects - раскрывать объекты
		 * @return string
		 * @version 1.4
		 */
		public function getHtml_arrayPrint( $mixed, $depth = 4, $showObjects = true ){
			if( $depth < 1 ){
				return '<div class="hiweb-string-printarr">...</div>';
			}
			$r = '';
			if( in_array( gettype( $mixed ), array( 'array', 'object' ) ) ){
				$r .= ' <span class="hiweb-string-printarr-gettype">[' . gettype( $mixed ) . ']</span>';
			}
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
			if( ! in_array( gettype( $mixed ), array( 'array', 'object' ) ) ){
				$r .= ' <span class="hiweb-string-printarr-gettype">' . ( gettype( $mixed ) == 'string' && mb_strlen( $mixed ) == 1 ? '[ord:<b>' . ord( $mixed ) . '</b>]' : '' ) . '[' . gettype( $mixed ) . ']</span>';
			}
			return "<div class='hiweb-string-printarr'>$r</div>";
		}
		
		
		public function print_r( $mixed, $depth = 4, $showObjects = true ){
			echo '<link rel="stylesheet" href="' . HIWEB_URL_BASE . '/css/arrays.css"/>';
			echo $this->getHtml_arrayPrint( $mixed, $depth, $showObjects );
			return $this;
		}
		
		
		/**
		 * Записывает данные `$dataMix` в формате HTML в файл. Это удобно для похоже на собственный лог-файл. Этой функцией можно в течении некоторого времени (установленного параметром `$autoDeleteOldFile`) многократно дозаписать информацию в один и тот же файл для дальнейшего анализа. По умолчанию все записывается в файл `log.html` в корне сайта.
		 * @param $dataMix - значения
		 * @param string $filePath - имя файла дампа
		 * @param bool $append - не удалять предыдущие записи
		 * @param int $autoDeleteOldFile - указать время в секундах, в течении которого старые записи не будут удаляться из файла
		 * @return int
		 */
		function file( $dataMix, $filePath = 'log.html', $append = true, $autoDeleteOldFile = 5 ){
			$filePath = realpath( $filePath );
			if( ! file_exists( dirname( $filePath ) ) ){
				file_put_contents( realpath( 'error.txt' ), dirname( $filePath ) . ' => not exists' );
				return false;
			}
			$returnStr = '<style type="text/css">.sep { border-bottom: 1px dotted #ccc; } .sepLast { margin-bottom: 35px; }</style>';
			$separatorHtml = '<div class="sep"></div>';
			$returnStr .= hiweb()->date()->dateTime() . ' / ' . microtime( true ) . ' / ' . $separatorHtml . $this->getHtml_arrayPrint( $dataMix ) . $separatorHtml;
			$fileContent = '';
			if( file_exists( $filePath ) && is_file( $filePath ) ){
				$time = time();
				$filetime = filemtime( $filePath );
				$timeDelta = $time - $filetime;
				if( $autoDeleteOldFile === false || $timeDelta > $autoDeleteOldFile ){
					unlink( $filePath );
				}else{
					$fileContent = file_get_contents( $filePath );
				}
			}
			///
			$returnStr = ( $append ? $fileContent . $returnStr : $returnStr . $fileContent ) . '<div class="sepLast"></div>';
			return file_put_contents( $filePath, $returnStr );
		}
	}