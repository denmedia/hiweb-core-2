<?php
	/*
	Plugin Name: hiWeb Core 2
	Plugin URI: http://plugins.hiweb.moscow/core
	Description: Special framework plugin
	Version: 2.7.0.0
	Author: Den Media
	Author URI: http://hiweb.moscow
	*/

	//todo!!!
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );

	require_once 'define.php';
	require_once HIWEB_DIR_INCLUDE . '/core.php';



	$meta = hiweb()->meta_boxes()->get('test')->title('Тестовый мета-бокс')->context('side');
	$meta->add_field( 'test' )->description('Отметьте этот пункт, чтобы проверить его')->title('Тестовое поле');
	$meta->screen()->post_type('')->or_in()->taxonomies();
	//$pt = hiweb()->post_types( 'post' );

	//hiweb()->console( $pt->labels() );
