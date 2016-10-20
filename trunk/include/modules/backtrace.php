<?php

	/**
	 * Created by PhpStorm.
	 * User: hiweb
	 * Date: 30.06.2016
	 * Time: 17:48
	 */
	class hw_backtrace {


		/**
		 * Возвращает массив
		 *
		 * @param bool $class - возвращать классы className
		 * @param bool $functions - возвращать имена функций functionName
		 * @param bool $files - возвращать имена файлов fileName
		 * @param bool $dirs - возвращать имена папки, из которой вызван файл
		 * @param bool $paths - возвращать путь папки с файлом
		 * @param bool $returnChunkArray - возвращать разбитый массив на ключевые значения array('class' => ..., 'functions' => ..., 'file' => ...)
		 * @param int $minDepth - минимальная глубина
		 * @param int $maxDepth - максимальная глубина
		 * @param string $prepend - добавлять до значения
		 * @param string $append - добавлять после каждого значения
		 * @param bool $args - возвращать аргументы
		 *
		 * @return array
		 *
		 * @version 1.2
		 */
		public function get_byGroup( $class = true, $functions = true, $files = true, $dirs = true, $paths = true, $returnChunkArray = false, $minDepth = 2, $maxDepth = 4, $prepend = '', $append = '', $args = false ) {
			$r   = array();
			$dbt = debug_backtrace();
			$n   = 0;
			foreach ( $dbt as $i ) {
				$n ++;
				if ( $n < $minDepth || $n > $maxDepth ) {
					continue;
				}
				if ( $class && isset( $i['class'] ) ) {
					$r['className'][] = $prepend . $i['class'] . $append;
				}
				if ( $args && isset( $i['args'] ) ) {
					$r['args'][] = $i['args'];
				}
				if ( $functions && isset( $i['function'] ) ) {
					$r['functionName'][] = $prepend . $i['function'] . $append;
				}
				if ( $files && isset( $i['file'] ) ) {
					$r['fileName'][] = $prepend . basename( $i['file'] ) . $append;
				}
				if ( $dirs && isset( $i['file'] ) ) {
					$r['dirName'][] = $prepend . basename( dirname( $i['file'] ) ) . $append;
					if ( $n < $maxDepth ) {
						$r['dirName'][] = $prepend . basename( dirname( dirname( $i['file'] ) ) ) . $append;
					}
				}
				if ( $paths && isset( $i['file'] ) ) {
					$r['path'][] = str_replace( '\\', '/', $prepend . dirname( $i['file'] ) . $append );
					if ( $n < $maxDepth ) {
						$r['path'][] = str_replace( '\\', '/', $prepend . dirname( dirname( $i['file'] ) ) . $append );
					}
				}
			}
			foreach ( $r as $k => $i ) {
				$r[ $k ] = array_unique( $i );
			}
			if ( ! $returnChunkArray ) {
				$r2 = array();
				foreach ( $r as $i ) {
					if ( is_array( $i ) ) {
						$r2 = $r2 + $i;
					}
				}

				return $r2;
			}

			return $r;
		}


		/**
		 * Возвращает путь и строку файла, откуда была запущена функция
		 *
		 * @param int $depth - глубина родительских функций
		 *
		 * @return string
		 *
		 * @version 2.0
		 */
		public function file_locate( $depth = 0 ) {
			$debugBacktrace = debug_backtrace();
			$R              = '';
			if ( hiweb()->arrays()->count( $debugBacktrace ) < $depth ) {
				//hiweb()->console()->warn( 'Слишком глубоко [' . $depth . ']', 1 );
			} else {
				$R = realpath( hiweb()->arrays()->get_byKey( $debugBacktrace, array( $depth, 'file' ), ':файл не найден:' ) ) . ' : ' . hiweb()->arrays()->get_byKey( $debugBacktrace, array( $depth, 'line' ) );
			}

			return $R;
		}

		/**
		 * Возвращает функцию, откуда была запущена текущая функция
		 *
		 * @param int $depth
		 *
		 * @return string
		 */
		public function function_trace( $depth = 0 ) {
			$debugBacktrace = debug_backtrace();
			$class          = hiweb()->arrays()->get_byKey( $debugBacktrace, array( $depth, 'class' ), '' );
			$function       = hiweb()->arrays()->get_byKey( $debugBacktrace, array( $depth, 'function' ), '' );
			$type           = hiweb()->arrays()->get_byKey( $debugBacktrace, array( $depth, 'type' ), '' );
			//Class filter
			if ( strpos( $class, 'hiweb_' ) === 0 && method_exists( hiweb(), substr( $class, 6 ) ) ) {
				$r = 'hiweb->' . substr( $class, 6 );
			} else {
				$r = $class;
			}
			$r .= $type . $function;

			return $r;
		}
	}