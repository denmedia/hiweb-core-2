<?php


	/**
	 * Created by PhpStorm.
	 * User: hiweb
	 * Date: 30.06.2016
	 * Time: 15:38
	 */
	class hw_path{


		/**
		 * Возвращает текущий адрес URL
		 * @version 1.0.2
		 */
		public function url_full( $trimSlashes = true ){
			$https = ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) || $_SERVER['SERVER_PORT'] == 443;
			return rtrim( 'http' . ( $https ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'], '/' ) . ( $trimSlashes ? rtrim( $_SERVER['REQUEST_URI'], '/\\' ) : $_SERVER['REQUEST_URI'] );
		}


		/**
		 * Возвращает запрошенный GET или POST параметр
		 * @param $key
		 * @param mixed $default
		 * @return mixed
		 */
		public function request( $key, $default = null ){
			$R = $default;
			if( array_key_exists( $key, $_GET ) )
				$R = $_GET[ $key ];
			if( array_key_exists( $key, $_POST ) )
				$R = $_POST[ $key ];
			return $R;
		}


		/**
		 * Возвращает корневой URL
		 * @return string
		 * @version 1.3
		 */
		public function base_url(){
			//if(hiweb()->cacheExists()) return hiweb()->cache();
			$root = ltrim( $this->base_dir(), '/' );
			$query = ltrim( str_replace( '\\', '/', dirname( $_SERVER['PHP_SELF'] ) ), '/' );
			$rootArr = array();
			$queryArr = array();
			foreach( array_reverse( explode( '/', $root ) ) as $dir ){
				$rootArr[] = rtrim( $dir . '/' . end( $rootArr ), '/' );
			}
			foreach( explode( '/', $query ) as $dir ){
				$queryArr[] = ltrim( end( $queryArr ) . '/' . $dir, '/' );
			}
			$rootArr = array_reverse( $rootArr );
			$queryArr = array_reverse( $queryArr );
			$r = '';
			foreach( $queryArr as $dir ){
				foreach( $rootArr as $rootDir ){
					if( $dir == $rootDir ){
						$r = $dir;
						break 2;
					}
				}
			}
			$https = ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) || $_SERVER['SERVER_PORT'] == 443;
			return rtrim( 'http' . ( $https ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'] . '/' . $r, '/' );
		}


		/**
		 * Возвращает корневую папку сайта. Данная функция автоматически определяет корневую папку сайта, отталкиваясь на поиске папок с файлом index.php
		 * @return string
		 * @version 1.4
		 */
		public function base_dir(){
			$full_path = getcwd();
			$ar = explode( "wp-", $full_path );
			return rtrim( $ar[0], '\\/' );
		}


		/**
		 * Возвращает URL с измененным QUERY фрагмнтом
		 * @param null $url
		 * @param array $addData
		 * @param array $removeKeys
		 * @return string
		 * @version 1.4
		 */
		public function query( $url = null, $addData = array(), $removeKeys = array() ){
			if( is_null( $url ) || trim( $url ) == '' ){
				$url = $this->url_full();
			}
			$url = explode( '?', $url );
			$urlPath = array_shift( $url );
			$query = implode( '?', $url );
			///
			$params = explode( '&', $query );
			$paramsPair = array();
			foreach( $params as $param ){
				if( trim( $param ) == '' ){
					continue;
				}
				list( $key, $val ) = explode( '=', $param );
				$paramsPair[ $key ] = $val;
			}
			///Add
			if( is_array( $addData ) ){
				foreach( $addData as $key => $value ){
					$paramsPair[ $key ] = $value;
				}
			}elseif( is_string( $addData ) && trim( $addData ) != '' ){
				$paramsPair[] = $addData;
			}
			///Remove
			if( is_array( $removeKeys ) ){
				foreach( $removeKeys as $key => $value ){
					if( is_string( $key ) && isset( $paramsPair[ $key ] ) ){
						unset( $paramsPair[ $key ] );
					}elseif( isset( $paramsPair[ $value ] ) ){
						unset( $paramsPair[ $value ] );
					}
				}
			}else if( is_string( $removeKeys ) && trim( $removeKeys ) != '' && isset( $paramsPair[ $removeKeys ] ) ){
				unset( $paramsPair[ $removeKeys ] );
			}
			///
			$params = array();
			foreach( $paramsPair as $key => $value ){
				$params[] = ( is_string( $key ) ? $key . '=' : '' ) . htmlentities( $value, ENT_QUOTES, 'UTF-8' );
			}
			///
			return count( $paramsPair ) > 0 ? $urlPath . '?' . implode( '&', $params ) : $urlPath;
		}


		/**
		 * Возвращает расширение файла, уть которого указан в аргументе $path
		 * @param $path
		 * @return string
		 */
		public function extension( $path ){
			$pathInfo = pathinfo( $path );
			return isset( $pathInfo['extension'] ) ? $pathInfo['extension'] : '';
		}


		/**
		 * Конвертирует путь в URL до файла
		 * @param $path
		 * @return mixed
		 */
		public function path_to_url( $path ){
			$path = str_replace( '\\', '/', $this->realpath( $path ) );
			return str_replace( $this->base_dir(), $this->base_url(), $path );
		}


		/**
		 * Конвертирует URL в путь
		 * @param $url
		 * @return mixed
		 */
		public function url_to_path( $url ){
			$url = str_replace( '\\', '/', $url );
			return str_replace( $this->base_url(), $this->base_dir(), $url );
		}


		/**
		 * @param null $url
		 * @return array
		 */
		public function url_info( $url = null ){
			if( is_null( $url ) || trim( $url ) == '' ){
				$url = $this->url_full();
			}
			$urlExplode = explode( '?', $url );
			$urlPath = array_shift( $urlExplode );
			$query = implode( '?', $urlExplode );
			///params
			$paramsPair = array();
			if( trim( $query ) != '' ){
				$params = explode( '&', $query );
				foreach( $params as $param ){
					list( $key, $val ) = explode( '=', $param );
					$paramsPair[ $key ] = $val;
				}
			}
			///
			$baseUrl = $this->base_url();
			$baseUrl = strpos( $url, $baseUrl ) === 0 ? $baseUrl : false;
			$https = ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) || $_SERVER['SERVER_PORT'] == 443;
			$shema = $https ? 'https://' : 'http://';
			$dirs = array();
			$domain = '';
			if( $baseUrl != false ){
				$dirs = explode( '/', trim( str_replace( $shema, '', $urlPath ), '/' ) );
				$domain = array_shift( $dirs );
			}
			return array(
				'url' => $url, 'base_url' => $baseUrl, 'shema' => $shema, 'domain' => $domain, 'dirs' => implode( '/', $dirs ), 'dirs_arr' => $dirs, 'params' => $query, 'params_arr' => $paramsPair
			);
		}


		/**
		 * Возвращает папки или папку(если указать индекс) из URL
		 * @param null $url
		 * @param int $index
		 * @return bool|array|string
		 */
		public function get_dirs_from_url( $url = null, $index = null ){
			$urlArr = $this->url_info( $url );
			return is_int( $index ) ? ( isset( $urlArr['dirs_arr'][ $index ] ) ? $urlArr['dirs_arr'][ $index ] : false ) : $urlArr['dirs_arr'];
		}


		/**
		 * Нормализация URL, так же возвращает парсированный URL
		 * @version 1.2
		 * @param $url
		 * @param null $startUrl
		 * @param bool $returnParseArray
		 * @return bool|array|string
		 */
		public function prepare_url( $url, $startUrl = null, $returnParseArray = false ){
			if( !is_string( $url ) ){
				return false;
			}
			$urlParse = parse_url( trim( $url ) );
			if( !isset( $urlParse['scheme'] ) ){
				if( is_string( $startUrl ) && trim( $startUrl ) != '' ){
					$startUrlParse = parse_url( $startUrl );
					$urlParse['scheme'] = $startUrlParse['scheme'];
					$urlParse['host'] = $startUrlParse['host'];
				}else{
					$urlParse['scheme'] = 'http';
					$urlParse['path'] = explode( '/', $urlParse['path'] );
					$urlParse['host'] = array_shift( $urlParse['path'] );
					$urlParse['path'] = '/' . implode( '/', $urlParse['path'] );
				}
			}
			//if(function_exists('idn_to_utf8')) { $urlParse['host'] = idn_to_utf8($urlParse['host']); }
			if( !isset( $urlParse['path'] ) ){
				$urlParse['path'] = '';
			}
			if( !isset( $urlParse['query'] ) ){
				$urlParse['query'] = '';
			}else{
				$urlParse['query'] = '?' . $urlParse['query'];
			}
			$urlParse['base'] = $urlParse['scheme'] . '://' . $urlParse['host'];
			return $returnParseArray ? $urlParse : $urlParse['scheme'] . '://' . $urlParse['host'] . $urlParse['path'] . $urlParse['query'];
		}


		/**
		 * Возвращает TRUE, если текущая страница являеться домашней
		 * @return bool
		 */
		public function is_home(){
			return $this->base_url() == $this->url_full();
		}


		/**
		 * Возвращает TRUE, если текущая страница соответствует указанному SLUG
		 * @param string $pageSlug
		 * @return bool
		 */
		public function is( $pageSlug = '' ){
			$currentUrl = ltrim( str_replace( $this->base_url(), '', $this->url_full() ), '/\\' );
			$pageSlug = ltrim( $pageSlug, '/\\' );
			return ( strpos( $currentUrl, $pageSlug ) === 0 );
		}


		/**
		 * Возвращает DIRECTORY SEPARATOR, отталкиваясь от данных
		 */
		public function separator(){
			$left = substr_count( $_SERVER['DOCUMENT_ROOT'], '\\' );
			$right = substr_count( $_SERVER['DOCUMENT_ROOT'], '//' );
			return $left > $right ? '\\' : '/';
		}


		/**
		 * Возвращает путь с правильными разделителями
		 * @param $path - исходный путь
		 * @param bool $removeLastSeparators - удалить самый хвостовой сепаратор
		 * @return string | bool
		 * @version 1.1
		 */
		public function prepare_separator( $path, $removeLastSeparators = false ){
			if( !is_string( $path ) ){
				hiweb()->console()->warn( 'Путь должен быть строкой', 1 );
				return false;
			}
			$r = strtr( $path, array( '\\' => $this->separator(), '/' => $this->separator() ) );
			return $removeLastSeparators ? rtrim( $r, $this->separator() ) : $r;
		}


		/**
		 * Конвертация относитльного пути к коневой папке в реальный
		 * @version 1.0.0.0
		 * @param $fileOrDirPath - путь до файла или папки
		 * @return string
		 */
		public function realpath( $fileOrDirPath ){
			$fileOrDirPath = $this->prepare_separator( $fileOrDirPath );
			return ( strpos( $fileOrDirPath, $this->base_dir() ) !== 0 ) ? $this->base_dir() . $this->separator() . $fileOrDirPath : $fileOrDirPath;
		}


		/**
		 * Функция атоматически создает папки
		 * @param $dirPath - путь до папи, которую необходимо создать
		 * @return string
		 */
		public function mkdir( $dirPath ){
			$dirPath = $this->realpath( $dirPath );
			if( @file_exists( $dirPath ) ){
				return is_dir( $dirPath ) ? $dirPath : false;
			}
			$dirPathArr = explode( '/', str_replace( '/', '/', $dirPath ) );
			$newDirArr = array();
			$newDirDoneArr = array();
			foreach( $dirPathArr as $name ){
				$newDirArr[] = $name;
				$newDirStr = implode( '/', $newDirArr );
				@chmod( $newDirStr, 0755 );
				//$stat = @stat( $newDirStr );
				if( !@file_exists( $newDirStr ) || @is_file( $newDirStr ) ){
					$newDirDoneArr[ $name ] = @mkdir( $newDirStr, 0755 );
				}else{
					$newDirDoneArr[ $newDirStr ] = 0;
				}
			}
			return $newDirDoneArr;
		}


		/**
		 * Удалить папку вместе с вложенными папками и файлами
		 * @param $dirPath
		 * @return bool
		 */
		public function rmdir( $dirPath ){
			if( !is_dir( $dirPath ) ){
				return false;
			}
			if( substr( $dirPath, strlen( $dirPath ) - 1, 1 ) != '/' ){
				$dirPath .= '/';
			}
			$files = glob( $dirPath . '*', GLOB_MARK );
			foreach( $files as $file ){
				if( is_dir( $file ) ){
					$this->rmdir( $file );
				}else{
					unlink( $file );
				}
			}
			return rmdir( $dirPath );
		}


		/**
		 * Копирует папку целиком вместе с вложенными файлами и папками
		 * @param $sourcePath - исходная папка
		 * @param $destinationDir - папка назначения
		 * @return bool
		 */
		public function copy_dir( $sourcePath, $destinationDir ){
			$dir = opendir( $sourcePath );
			$this->mkdir( $destinationDir );
			$r = true;
			while( false !== ( $file = readdir( $dir ) ) ){
				if( ( $file != '.' ) && ( $file != '..' ) ){
					if( is_dir( $sourcePath . '/' . $file ) ){
						$r = $r && $this->copy_dir( $sourcePath . '/' . $file, $destinationDir . '/' . $file );
					}else{
						$r = $r && copy( $sourcePath . '/' . $file, $destinationDir . '/' . $file );
					}
				}
			}
			closedir( $dir );
			return $r;
		}


		/**
		 * Возвращает форматированный вид размера файла из байтов
		 * @param $size - INT килобайты
		 * @return string
		 */
		public function size_format( $size ){
			$size = intval( $size );
			if( $size < 1024 ){
				return $size . " bytes";
			}else if( $size < ( 1024 * 1024 ) ){
				$size = round( $size / 1024, 1 );
				return $size . " KB";
			}else if( $size < ( 1024 * 1024 * 1024 ) ){
				$size = round( $size / ( 1024 * 1024 ), 1 );
				return $size . " MB";
			}else{
				$size = round( $size / ( 1024 * 1024 * 1024 ), 1 );
				return $size . " GB";
			}
		}


		/**
		 * Возвращает содержимое папки в массиве
		 * @param $path
		 * @param bool $returnDirs
		 * @param bool $returnFiles
		 * @param bool $getSubDirs
		 * @return array
		 */
		public function scan_directory( $path, $returnDirs = true, $returnFiles = true, $getSubDirs = true ){
			$path = $this->realpath( $path );
			if( !file_exists( $path ) ){
				return array();
			}
			$R = array();
			if( $handle = opendir( $path ) ){
				while( false !== ( $file = readdir( $handle ) ) ){
					$nextpath = $path . '/' . $file;
					if( $file != '.' && $file != '..' && !is_link( $nextpath ) ){
						///
						if( is_dir( $nextpath ) && $returnDirs ){
							$R[ $nextpath ] = pathinfo( $nextpath );
						}elseif( is_file( $nextpath ) && $returnFiles ){
							$R[ $nextpath ] = pathinfo( $nextpath );
						}
						///
						if( $getSubDirs && is_dir( $nextpath ) ){
							$R = $R + $this->scan_directory( $nextpath, $returnDirs, $returnFiles, $getSubDirs );
						}
					}
				}
			}
			closedir( $handle );
			return $R;
		}


		/**
		 * Выполняет архивацию папки в ZIP архив
		 * @param $pathInput
		 * @param string $pathOut
		 * @param string $arhiveName
		 * @param string|bool $baseDirInArhive - базовая папка / путь внутри архива для всех запакованных файлов и папок. Если установить TRUE - в архиве будет корневая папка, которая была указана в качестве исходной.
		 * @param bool $appendToArchive
		 * @return bool|string
		 */
		public function archive( $pathInput, $pathOut = '', $arhiveName = 'arhive.zip', $baseDirInArhive = true, $appendToArchive = false ){
			$pathInput = $this->realpath( $pathInput );
			if( !is_file( $pathOut ) ){
				$this->mkdir( $pathOut );
			}
			$pathOut = $pathOut == '' ? $pathInput : $this->realpath( $pathOut );
			if( !file_exists( $pathInput ) ){
				return false;
			}
			if( $baseDirInArhive === true ){
				$baseDirInArhive = basename( $pathInput ) . '/';
			}
			if( !$appendToArchive && file_exists( $pathOut . '/' . $arhiveName ) ){
				@unlink( $pathOut . '/' . $arhiveName );
			}
			$zip = new ZipArchive; // класс для работы с архивами
			if( $zip->open( $pathOut . '/' . $arhiveName, ZipArchive::CREATE ) === true ){ // создаем архив, если все прошло удачно продолжаем
				$files = $this->scan_directory( $pathInput, false );
				foreach( $files as $path => $fileArr ){
					$zip->addFile( $path, $baseDirInArhive . str_replace( rtrim( $pathInput, '/' ) . '/', '', $path ) );
				}
				$zip->close(); // закрываем архив.
				return $pathOut;
			}else{
				return false;
			}
		}


		/**
		 * Распаковывает ZIP архив
		 * @param $archivePath
		 * @param string $destinationDir
		 * @return bool
		 */
		public function unpack( $archivePath, $destinationDir = '' ){
			$archivePath = $this->realpath( $archivePath );
			if( !file_exists( $archivePath ) ){
				return false;
			}
			if( $destinationDir == '' ){
				$destinationDir = dirname( $archivePath );
			}
			$zip = new ZipArchive();
			if( $zip->open( $archivePath ) === true ){
				if( !$zip->extractTo( $destinationDir ) ){
					return false;
				}
				$zip->close();
				return true;
			}else{
				return false;
			}
		}


		/**
		 * Возвращает расширение файла, уть которого указан в аргументе $path
		 * @param $path
		 * @return string
		 */
		public function file_extension( $path ){
			$pathInfo = pathinfo( $path );
			return isset( $pathInfo['extension'] ) ? $pathInfo['extension'] : '';
		}


		/**
		 * Возвращает содержимое файла PHP, подключая его через INCLUDE
		 * @param $path
		 * @return bool|string
		 */
		public function get_content( $path ){
			$path = $this->realpath( $path );
			if( file_exists( $path ) && is_readable( $path ) ){
				if( function_exists( 'ob_start' ) ){
					ob_start();
					include $path;
					return ob_get_clean();
				}else{
					hiweb()->console()->error('Функции [ob_start] не установлено на сервере', true);
					return false;
				}
			}else{
				hiweb()->console()->error('Файла ['.$path.'] нет', true);
				return false;
			}
		}


	}