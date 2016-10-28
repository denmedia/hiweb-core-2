<?php
	/*
	Plugin Name: hiWeb Core 2
	Plugin URI: http://plugins.hiweb.moscow/core
	Description: Special framework plugin
	Version: 2.7.0.0
	Author: Den Media
	Author URI: http://hiweb.moscow
	*/
	
	require_once 'define.php';
	require_once HIWEB_DIR_INCLUDE . '/core.php';

	$option = hiweb()->option( 'test', 'repeat' )->title( '' );
	$option->input()->cols( array(
		hiweb()->input( 'image', 'image' )->width( 10 ), hiweb()->input( 'text' )->width( 90 )
	) );
	$option->page( 'My Options' )->page_title( 'Мои тестовые настройки' );