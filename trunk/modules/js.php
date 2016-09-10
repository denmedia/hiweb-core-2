<?php

	/**
	 * Created by PhpStorm.
	 * User: hiweb
	 * Date: 30.06.2016
	 * Time: 22:16
	 */
	class hiweb_js {

		private $files = array();

		public function __construct() {
			add_action('wp_enqueue_scripts', array($this, '_my_wp_enqueue_scripts'));
			add_action('admin_enqueue_scripts', array($this, '_my_wp_enqueue_scripts'));
			add_action('login_enqueue_scripts', array($this, '_my_wp_enqueue_scripts'));
			add_action('wp_footer', array($this, '_my_wp_enqueue_scripts'));
			add_action('admin_footer', array($this, '_my_wp_enqueue_scripts'));
		}

		/**
		 * Поставить в очередь файл JS
		 * @version 1.2
		 * @param $file
		 * @return bool
		 */
		public function enqueue($file, $afterJS = array(), $in_footer = false) {
			$url = '';
			if (strpos($file, '/') === 0) {
				$backtrace = debug_backtrace();
				if(strpos($file, hiweb()->path()->getStr_baseDir()) !== 0){
					$sourceDir = dirname($backtrace[1]['file']);
					$file = $sourceDir . $file;
				}
			}
			$url = hiweb()->path()->getStr_urlFromPath($file);
			if ($url != '') {
				$this->files[md5($url)] = array($url, $afterJS, $in_footer);
				return true;
			} else {
				hiweb()->console(  )->error('hiweb()→js(): файл ['.$file.'] не найден!', true);
				return false;
			}
		}

		function _my_wp_enqueue_scripts() {
			foreach ($this->files as $slug => $script) {
				unset($this->files[$slug]);
				wp_register_script($slug, $script[0], $script[1], false, $script[2]);
				wp_enqueue_script($slug);
			}
		}


	}