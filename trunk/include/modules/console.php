<?php


	class hw_console{

		private $_infos = array();
		private $_warns = array();
		private $_errors = array();
		public $mess = array();
		private $_mess = array();


		public function __construct( $info = null ){
			if( !is_null( $info ) ){
				$this->info( $info );
			}
			if( !hiweb()->wp()->is_ajax() )
				add_action( 'shutdown', array( $this, 'echo_footer' ), 10 );
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'echo_footer':
					$this->echo_footer();
					break;
			}
		}


		/**
		 * Вывести информацию в консоль
		 * @param $info - информация
		 * @param bool $debugMod - дополнительная информация
		 */
		public function info( $info, $debugMod = false ){
			//check call from __construct function
			$deb = debug_backtrace();
			$function = isset( $deb[1]['function'] ) ? $deb[1]['function'] : '';
			$class = isset( $deb[1]['class'] ) ? $deb[1]['class'] : '';
			$callFromConstruct = $function == '__construct' && $class == 'hiweb_console';
			//
			$this->_infos[] = $info;
			$this->mess['info'][] = $info;
			$this->_mess[] = array(
				'data' => $info, 'type' => 'info', 'debug' => $debugMod, 'microtime' => microtime( 1 ), 'file' => hiweb()->backtrace()->file_locate( $callFromConstruct ? 3 : 2 ),
				'function' => hiweb()->backtrace()->function_trace( $callFromConstruct ? 3 : 2 )
			);
		}


		/**
		 * Вывести предупреждение в консоль
		 * @param $info - информация
		 * @param bool $debugMod - дополнительная информация
		 */
		public function warn( $info, $debugMod = false ){
			$this->_warns[] = $info;
			$this->mess['warn'][] = $info;
			$this->_mess[] = array(
				'data' => $info, 'type' => 'warn', 'debug' => $debugMod, 'microtime' => microtime( 1 ), 'file' => hiweb()->backtrace()->file_locate( 2 ), 'function' => hiweb()->backtrace()->function_trace( 2 )
			);
		}


		/**
		 * Вывести в консоль ошибку
		 * @param $info - информация
		 * @param bool $debugMod - дополнительная информация
		 * @version 1.1
		 */
		public function error( $info, $debugMod = false ){
			$this->_errors[] = $info;
			$this->mess['error'][] = $info;
			$this->_mess[] = array(
				'data' => $info, 'type' => 'error', 'debug' => $debugMod, 'microtime' => microtime( 1 ), 'file' => hiweb()->backtrace()->file_locate( 2 ), 'function' => hiweb()->backtrace()->function_trace( 2 )
			);
		}


		/**
		 * Форс вывод console.info($info)
		 * @param $info
		 */
		protected function echo_info( $info ){
			echo 'console.info(' . json_encode( $info ) . ');';
		}


		/**
		 * Форс вывод console.warn($info)
		 * @param $info
		 */
		protected function echo_warn( $info ){
			echo 'console.warn(' . json_encode( $info ) . ');';
		}


		/**
		 * Форс вывод console.error($info)
		 * @param $info
		 */
		protected function echo_error( $info ){
			echo 'console.error(' . json_encode( $info ) . ');';
		}


		private function echo_footer(){
			echo '<script>';
			foreach( $this->_mess as $info ){
				$this->{'echo_' . $info['type']}( $info['debug'] ? ( is_array( $info['data'] ) ? array_merge( array(
					'► ' . $info['function'], '► ' . $info['file']
				), $info['data'] ) : $info['function'] . '() : ' . $info['data'] . chr( 13 ) . chr( 10 ) . '► ' . $info['file'] ) : $info['data'] );
			}
			echo '</script>';
		}


	}