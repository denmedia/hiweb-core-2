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
		///
		require_once 'include/core.php';
		require_once 'include/short_functions.php';
	} else {
		function hw_core_php_version_error(){
			echo '<div class="error notice"><p>hiWeb Core 2 error! Request PHP version great that 5.4.0!</p></div>';
		}

		add_action( 'admin_notices', 'hw_core_php_version_error' );
	}

	//TODO
	$field = hiweb()->fields()->add_field( 'test', 'image' );
	$field->name( 'Тестовое поле' )->default_value( 'Значение по-умолчанию!' )->description( 'Описание для данного поля' );
	$field->location()->post_type( 'page', 3 );
	$field->location()->post_type( 'post', 4 );
	$field->location()->taxonomy( 'category' );

	$field = hiweb()->field( 'test-2', 'text', 'Проверка имени поля' )->description('Еще одно поле для првоерки достаточно длинного вспомогательного текста')->location()->taxonomy( 'category' );