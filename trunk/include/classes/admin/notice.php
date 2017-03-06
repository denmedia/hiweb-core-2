<?php


	class hw_admin_notices{

		/** @var hw_admin_notice[] */
		protected $notices = array();

		use hw_hidden_methods_props;


		public function __construct(){
			add_action( 'admin_notices', array( $this, 'add_action_admin_notices' ) );
		}


		protected function add_action_admin_notices(){
			foreach( $this->notices as $notice ){
				$notice->the();
			}
		}


		protected function add( $message, $append_p = true, $is_dismissible = true ){
			$notice = new hw_admin_notice( $message, $append_p, $is_dismissible );
			$this->notices[] = $notice;
			return $notice;
		}


		/**
		 * @param      $message
		 * @param bool $append_p
		 * @param bool $is_dismissible
		 * @return hw_admin_notice
		 */
		public function info( $message, $append_p = true, $is_dismissible = true ){
			return $this->add( $message, $append_p, $is_dismissible )->set_info();
		}


		/**
		 * @param      $message
		 * @param bool $append_p
		 * @param bool $is_dismissible
		 * @return hw_admin_notice
		 */
		public function success( $message, $append_p = true, $is_dismissible = true ){
			return $this->add( $message, $append_p, $is_dismissible )->set_success();
		}


		/**
		 * @param      $message
		 * @param bool $append_p
		 * @param bool $is_dismissible
		 * @return hw_admin_notice
		 */
		public function warning( $message, $append_p = true, $is_dismissible = true ){
			return $this->add( $message, $append_p, $is_dismissible )->set_warning();
		}


		/**
		 * @param      $message
		 * @param bool $append_p
		 * @param bool $is_dismissible
		 * @return hw_admin_notice
		 */
		public function error( $message, $append_p = true, $is_dismissible = true ){
			return $this->add( $message, $append_p, $is_dismissible )->set_error();
		}

	}


	class hw_admin_notice{

		protected $is_dismissible = '';

		protected $class = '';

		protected $message = '';

		protected $append_p = true;


		public function __construct( $message, $append_p = true, $is_dismissible = true ){
			$this->message = $message;
			$this->append_p = $append_p;
			$this->set_dismissible( $is_dismissible );
		}


		/**
		 * Установить класс DISMISSIBLE
		 * @param bool $set
		 */
		public function set_dismissible( $set = true ){
			$this->is_dismissible = (bool)$set ? 'is-dismissible' : '';
		}


		/**
		 * Установить класс INFO
		 * @return hw_admin_notice
		 */
		public function set_info(){
			$this->class = 'notice-info';
			return $this;
		}


		/**
		 * Установить класс SUCCESS
		 * @return hw_admin_notice
		 */
		public function set_success(){
			$this->class = 'notice-success';
			return $this;
		}


		/**
		 * Установить класс WARNING
		 * @return hw_admin_notice
		 */
		public function set_warning(){
			$this->class = 'notice-warning';
			return $this;
		}


		/**
		 * Установить класс ERROR
		 * @return hw_admin_notice
		 */
		public function set_error(){
			$this->class = 'notice-error';
			return $this;
		}


		/**
		 * @param null $message
		 * @return hw_admin_notice|string
		 */
		public function message( $message = null ){
			if( is_null( $message ) ){
				return $this->message;
			} else {
				$this->message = $message;
				return $this;
			}
		}


		/**
		 * Возвращает html NOTICE
		 * @return string
		 */
		public function html(){
			return '<div class="notice ' . $this->class . ' ' . $this->is_dismissible . '">' . ( $this->append_p ? '<p>' : '' ) . $this->message . ( $this->append_p ? '</p>' : '' ) . '</div>';
		}


		/**
		 * Выводит html NOTICE
		 */
		public function the(){
			echo $this->html();
		}

	}