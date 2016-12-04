<?php
	/*
	Plugin Name: hiWeb Core 2
	Plugin URI: http://plugins.hiweb.moscow/core
	Description: Special framework plugin
	Version: 2.0.0.0
	Author: Den Media
	Author URI: http://hiweb.moscow
	*/
	if( version_compare( PHP_VERSION, '5.4.0' ) >= 0 ){
		require_once 'include/core.php';
		require_once 'include/short_functions.php';
	} else {
		function hw_core_php_version_error(){
			echo '<div class="error notice"><p>hiWeb Core 2 error! Request PHP version great that 5.4.0!</p></div>';
		}
		
		add_action( 'admin_notices', 'hw_core_php_version_error' );
	}
	
	
	
	///todo-
hiweb()->post_type('post')->labels_set('menu_name','Новости')->add_field('test','text','Тестовое поле');